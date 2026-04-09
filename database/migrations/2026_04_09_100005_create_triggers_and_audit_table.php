<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

/**
 * Migración: Tabla de Auditoría + Disparadores (Triggers)
 *
 * Requisito: Implementar Triggers de tipo INSERT, UPDATE y/o DELETE en al menos 2 tablas.
 *
 * TRIGGER 1 — AFTER UPDATE en `hiring_requests`:
 *   Cuando el status de una solicitud de contratación cambia, se guarda el historial
 *   en la tabla `hiring_requests_audit`. Permite rastrear transiciones de estado.
 *
 * TRIGGER 2 — AFTER INSERT en `reviews`:
 *   Al recibir una nueva reseña, actualiza de forma atómica los contadores de la
 *   vista materializada (`mv_musician_monthly_stats`) para el músico calificado.
 *   Esto mantiene el snapshot actualizado sin necesidad de CRON inmediato.
 *
 * BUENAS PRÁCTICAS APLICADAS:
 *   - Se verifica el cambio real (OLD.status != NEW.status) antes de insertar.
 *   - Se usa INSERT ... ON DUPLICATE KEY UPDATE para la vista materializada.
 *   - Las tablas de auditoría son APPEND-ONLY (no se actualizan ni borran).
 */
return new class extends Migration
{
    /**
     * Ejecutar la migración.
     */
    public function up(): void
    {
        // ── TABLA DE AUDITORÍA: hiring_requests_audit ──────────────────────────
        // Verificamos si la tabla ya fue creada en un intento previo que falló a la mitad
        if (!Schema::hasTable('hiring_requests_audit')) {
            Schema::create('hiring_requests_audit', function (Blueprint $table) {
                $table->id();

                // Referencia a la solicitud de contratación afectada
                $table->unsignedBigInteger('hiring_request_id')
                      ->comment('ID de la solicitud de contratación auditada');

                // Estado anterior y nuevo (no usamos FK para preservar historial aunque se borre la solicitud)
                $table->string('old_status', 50)->nullable()->comment('Estado anterior a la modificación');
                $table->string('new_status', 50)->comment('Estado nuevo después de la modificación');

                // Timestamp del cambio (registrado por el trigger, no por Laravel)
                $table->timestamp('changed_at')->useCurrent();

                $table->index('hiring_request_id', 'idx_audit_hiring_request');
            });
        }


        // ── TRIGGER 1: AFTER UPDATE en hiring_requests ─────────────────────────
        DB::unprepared("
            CREATE TRIGGER trg_after_update_hiring_request
            AFTER UPDATE ON hiring_requests
            FOR EACH ROW
            BEGIN
                /*
                 * Disparador: trg_after_update_hiring_request
                 * Tabla:      hiring_requests
                 * Evento:     AFTER UPDATE
                 * Propósito:  Registrar el historial de cambios de estado en hiring_requests_audit.
                 *             Solo se activa cuando el campo `status` realmente cambia.
                 */
                IF OLD.status != NEW.status THEN
                    INSERT INTO hiring_requests_audit
                        (hiring_request_id, old_status, new_status, changed_at)
                    VALUES
                        (OLD.id, OLD.status, NEW.status, NOW());
                END IF;
            END
        ");

        // ── TRIGGER 2: AFTER INSERT en reviews ────────────────────────────────
        DB::unprepared("
            CREATE TRIGGER trg_after_insert_review
            AFTER INSERT ON reviews
            FOR EACH ROW
            BEGIN
                /*
                 * Disparador: trg_after_insert_review
                 * Tabla:      reviews
                 * Evento:     AFTER INSERT
                 * Propósito:  Al insertarse una nueva reseña, actualizar atómicamente
                 *             la vista materializada `mv_musician_monthly_stats`
                 *             para el músico calificado en el mes correspondiente.
                 *             Mantiene el snapshot actualizado sin esperar al CRON.
                 */
                INSERT INTO mv_musician_monthly_stats
                    (musician_profile_id, report_month, total_hired, total_earned, avg_rating, last_updated)
                SELECT
                    NEW.musician_profile_id,
                    DATE_FORMAT(NOW(), '%Y-%m'),
                    -- Mantener el valor actual de contrataciones (no cambia al recibir una review)
                    COALESCE((
                        SELECT total_hired
                        FROM   mv_musician_monthly_stats
                        WHERE  musician_profile_id = NEW.musician_profile_id
                          AND  report_month = DATE_FORMAT(NOW(), '%Y-%m')
                        LIMIT 1
                    ), 0),
                    -- Mantener el valor actual de ganancias
                    COALESCE((
                        SELECT total_earned
                        FROM   mv_musician_monthly_stats
                        WHERE  musician_profile_id = NEW.musician_profile_id
                          AND  report_month = DATE_FORMAT(NOW(), '%Y-%m')
                        LIMIT 1
                    ), 0.00),
                    -- Recalcular el rating promedio usando todas las reviews del músico en el mes
                    COALESCE((
                        SELECT AVG(r.rating)
                        FROM   reviews r
                        WHERE  r.musician_profile_id = NEW.musician_profile_id
                          AND  DATE_FORMAT(r.created_at, '%Y-%m') = DATE_FORMAT(NOW(), '%Y-%m')
                    ), NEW.rating),
                    NOW()
                ON DUPLICATE KEY UPDATE
                    avg_rating   = COALESCE((
                        SELECT AVG(r.rating)
                        FROM   reviews r
                        WHERE  r.musician_profile_id = NEW.musician_profile_id
                          AND  DATE_FORMAT(r.created_at, '%Y-%m') = DATE_FORMAT(NOW(), '%Y-%m')
                    ), NEW.rating),
                    last_updated = NOW();
            END
        ");
    }

    /**
     * Revertir la migración.
     * Eliminamos los triggers antes que las tablas.
     */
    public function down(): void
    {
        DB::unprepared('DROP TRIGGER IF EXISTS trg_after_insert_review');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_after_update_hiring_request');
        Schema::dropIfExists('hiring_requests_audit');
    }
};
