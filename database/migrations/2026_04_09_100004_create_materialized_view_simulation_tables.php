<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

/**
 * Migración: Vistas Materializadas Simuladas (Materialized View Simulation)
 *
 * MySQL/MariaDB no soporta MATERIALIZED VIEWS de forma nativa.
 * Se simulan usando tablas físicas de resumen (snapshot tables) que almacenan
 * resultados pre-calculados de consultas pesadas.
 *
 * Ventajas:
 *   - Lecturas del Dashboard en O(1) en lugar de hacer JOINs/AVGs en tiempo real.
 *   - Se pueden actualizar periódicamente (CRON, Artisan Command) o mediante Triggers.
 *   - Equivalente funcional a vistas materializadas en PostgreSQL/Oracle.
 *
 * TABLA 1 — mv_musician_monthly_stats:
 *   Estadísticas mensuales de contrataciones y ganancias por músico.
 *
 * TABLA 2 — mv_client_spending_stats:
 *   Estadísticas mensuales de eventos creados y presupuesto invertido por cliente.
 */
return new class extends Migration
{
    /**
     * Ejecutar la migración.
     */
    public function up(): void
    {
        // ── TABLA MATERIALIZADA 1: Estadísticas Mensuales del Músico ───────────
        Schema::create('mv_musician_monthly_stats', function (Blueprint $table) {
            $table->id();

            // Referencia al músico (FK con cascade para mantener integridad)
            $table->foreignId('musician_profile_id')
                  ->constrained('musician_profiles')
                  ->onDelete('cascade');

            // Periodo de reporte en formato YYYY-MM (ej. "2026-04")
            $table->string('report_month', 7)->comment('Periodo de reporte: formato YYYY-MM');

            // Métricas pre-calculadas (actualizadas por Artisan Command o Trigger)
            $table->integer('total_hired')->default(0)->comment('Total de contrataciones completadas en el mes');
            $table->decimal('total_earned', 12, 2)->default(0.00)->comment('Suma de ingresos del músico en el mes');
            $table->decimal('avg_rating', 4, 2)->default(0.00)->comment('Rating promedio del músico en el mes');

            // Timestamp de la última actualización del snapshot
            $table->timestamp('last_updated')->useCurrent()->useCurrentOnUpdate();

            $table->timestamps();

            // Índice compuesto: garantiza un único registro por músico por mes
            $table->unique(['musician_profile_id', 'report_month'], 'mv_musician_month_unique');
        });

        // ── TABLA MATERIALIZADA 2: Estadísticas Mensuales del Cliente ─────────
        // NOTA: client_events identifica clientes por firebase_uid (string), no por integer FK.
        // Por ello, esta tabla materializada también usa firebase_uid como clave de agrupación.
        Schema::create('mv_client_spending_stats', function (Blueprint $table) {
            $table->id();

            // Identificador Firebase del cliente (mismo campo que client_events.firebase_uid)
            $table->string('firebase_uid')->comment('Firebase UID del cliente');

            // Periodo de reporte en formato YYYY-MM
            $table->string('report_month', 7)->comment('Periodo de reporte: formato YYYY-MM');

            // Métricas pre-calculadas
            $table->integer('total_events_created')->default(0)->comment('Total de eventos creados por el cliente en el mes');
            $table->decimal('total_spent', 12, 2)->default(0.00)->comment('Suma de presupuestos de eventos creados en el mes');

            // Timestamp de la última actualización del snapshot
            $table->timestamp('last_updated')->useCurrent()->useCurrentOnUpdate();

            $table->timestamps();

            // Índice compuesto: garantiza un único registro por cliente por mes
            $table->unique(['firebase_uid', 'report_month'], 'mv_client_month_unique');
            // Índice individual para búsquedas por client
            $table->index('firebase_uid', 'idx_mv_client_firebase_uid');
        });

        // Poblar el snapshot inicial de músicos (últimos 3 meses)
        // Usamos un derived table (subquery FROM) para agrupar primero y luego calcular el avg_rating
        // sin violar sql_mode=only_full_group_by
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
                    COALESCE(SUM(hr.budget), 0)          AS total_earned
                FROM   hiring_requests hr
                WHERE  hr.status = 'completed'
                  AND  hr.created_at >= DATE_SUB(NOW(), INTERVAL 3 MONTH)
                GROUP BY hr.musician_profile_id, DATE_FORMAT(hr.created_at, '%Y-%m')
            ) AS base
            ON DUPLICATE KEY UPDATE
                total_hired  = VALUES(total_hired),
                total_earned = VALUES(total_earned),
                avg_rating   = VALUES(avg_rating),
                last_updated = NOW()
        ");
    }

    /**
     * Revertir la migración.
     */
    public function down(): void
    {
        Schema::dropIfExists('mv_client_spending_stats');
        Schema::dropIfExists('mv_musician_monthly_stats');
    }
};
