<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migración: Columnas Generadas (Generated Columns)
 *
 * Agrega columnas calculadas automáticamente por el motor de base de datos
 * en al menos 2 tablas, sin necesidad de lógica adicional en el backend.
 *
 * TABLA 1 — client_events:
 *   `presupuesto_con_impuesto` (VIRTUAL): Calcula el presupuesto con IVA del 16% al vuelo.
 *   - Tipo VIRTUAL: no ocupa espacio en disco; se calcula al leerlo.
 *
 * TABLA 2 — hiring_requests:
 *   `commission_amount` (STORED): Calcula el 10% de comisión de la plataforma sobre el budget.
 *   - Tipo STORED: se persiste en disco al insertar/actualizar, ideal para queries frecuentes.
 */
return new class extends Migration
{
    /**
     * Ejecutar la migración.
     */
    public function up(): void
    {
        // ── TABLA 1: client_events ──────────────────────────────────────────────
        Schema::table('client_events', function (Blueprint $table) {
            /**
             * Columna VIRTUAL: Presupuesto con IVA (16%)
             * Formula: presupuesto * 1.16
             * Al ser VIRTUAL no ocupa espacio en disco; el valor se recalcula en cada lectura.
             * Útil para mostrar el costo total estimado al cliente en la UI sin procesamiento en PHP.
             */
            $table->decimal('presupuesto_con_impuesto', 12, 2)
                  ->nullable()
                  ->virtualAs('presupuesto * 1.16')
                  ->after('presupuesto')
                  ->comment('Columna generada virtual: presupuesto con IVA del 16%');
        });

        // ── TABLA 2: hiring_requests ────────────────────────────────────────────
        Schema::table('hiring_requests', function (Blueprint $table) {
            /**
             * Columna STORED: Comisión de plataforma (10% del budget acordado)
             * Formula: budget * 0.10
             * Al ser STORED, se guarda en disco. Ventaja frente a VIRTUAL:
             * permite indexar la columna y hacer búsquedas/reportes eficientes.
             */
            $table->decimal('commission_amount', 10, 2)
                  ->nullable()
                  ->storedAs('budget * 0.10')
                  ->after('budget')
                  ->comment('Columna generada stored: 10% de comisión de la plataforma sobre el budget');
        });
    }

    /**
     * Revertir la migración.
     */
    public function down(): void
    {
        Schema::table('client_events', function (Blueprint $table) {
            $table->dropColumn('presupuesto_con_impuesto');
        });

        Schema::table('hiring_requests', function (Blueprint $table) {
            $table->dropColumn('commission_amount');
        });
    }
};
