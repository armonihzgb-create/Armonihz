<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Artisan Command: stats:refresh-mv
 *
 * Refresca las tablas de vistas materializadas simuladas:
 *   - mv_musician_monthly_stats: Estadísticas de músicos por mes.
 *   - mv_client_spending_stats : Estadísticas de clientes por mes.
 *
 * Se puede ejecutar manualmente o programar en el Kernel (CRON) del sistema.
 *
 * Uso manual:
 *   php artisan stats:refresh-mv
 *   php artisan stats:refresh-mv --months=6   (últimos 6 meses, por defecto 3)
 *
 * En producción, programar en App\Console\Kernel o routes/console.php:
 *   Schedule::command('stats:refresh-mv')->daily();
 */
class RefreshMaterializedViews extends Command
{
    /**
     * Firma del comando con opción para definir el número de meses a procesar.
     */
    protected $signature = 'stats:refresh-mv {--months=3 : Número de meses hacia atrás a procesar}';

    /**
     * Descripción corta del comando.
     */
    protected $description = 'Refresca las vistas materializadas simuladas (mv_musician_monthly_stats y mv_client_spending_stats)';

    /**
     * Ejecutar el comando.
     */
    public function handle(): int
    {
        $months = (int) $this->option('months');

        $this->info("Refrescando vistas materializadas para los últimos {$months} mes(es)...");

        // ── VISTA MATERIALIZADA 1: Músicos ─────────────────────────────────────
        $this->line('→ Actualizando mv_musician_monthly_stats...');

        DB::unprepared("
            INSERT INTO mv_musician_monthly_stats
                (musician_profile_id, report_month, total_hired, total_earned, avg_rating, last_updated)
            SELECT
                base.musician_profile_id,
                base.report_month,
                base.total_hired,
                base.total_earned,
                COALESCE((
                    SELECT AVG(r.rating)
                    FROM   reviews r
                    WHERE  r.musician_profile_id = base.musician_profile_id
                      AND  DATE_FORMAT(r.created_at, '%Y-%m') = base.report_month
                ), 0) AS avg_rating,
                NOW()
            FROM (
                SELECT
                    hr.musician_profile_id,
                    DATE_FORMAT(hr.created_at, '%Y-%m') AS report_month,
                    COUNT(hr.id)                         AS total_hired,
                    COALESCE(SUM(hr.budget), 0.00)       AS total_earned
                FROM   hiring_requests hr
                WHERE  hr.status = 'completed'
                  AND  hr.created_at >= DATE_SUB(NOW(), INTERVAL {$months} MONTH)
                GROUP BY hr.musician_profile_id, DATE_FORMAT(hr.created_at, '%Y-%m')
            ) AS base
            ON DUPLICATE KEY UPDATE
                total_hired  = VALUES(total_hired),
                total_earned = VALUES(total_earned),
                avg_rating   = VALUES(avg_rating),
                last_updated = NOW()
        ");

        $this->info('  ✓ mv_musician_monthly_stats actualizada.');

        // ── VISTA MATERIALIZADA 2: Clientes ────────────────────────────────────
        $this->line('→ Actualizando mv_client_spending_stats...');

        DB::unprepared("
            INSERT INTO mv_client_spending_stats
                (firebase_uid, report_month, total_events_created, total_spent, last_updated)
            SELECT
                ce.firebase_uid,
                DATE_FORMAT(ce.created_at, '%Y-%m')     AS report_month,
                COUNT(ce.id)                             AS total_events_created,
                COALESCE(SUM(ce.presupuesto), 0.00)      AS total_spent,
                NOW()
            FROM   client_events ce
            WHERE  ce.deleted_at IS NULL
              AND  ce.created_at >= DATE_SUB(NOW(), INTERVAL {$months} MONTH)
            GROUP BY
                ce.firebase_uid,
                DATE_FORMAT(ce.created_at, '%Y-%m')
            ON DUPLICATE KEY UPDATE
                total_events_created = VALUES(total_events_created),
                total_spent          = VALUES(total_spent),
                last_updated         = NOW()
        ");

        $this->info('  ✓ mv_client_spending_stats actualizada.');

        $this->info('✔ Refresco de vistas materializadas completado.');

        return Command::SUCCESS;
    }
}
