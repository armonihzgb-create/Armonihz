<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

/**
 * Migración: Índices Individuales y Compuestos Adicionales
 *
 * Requisito: Índices individuales en al menos 2 tablas.
 *            Índices compuestos en al menos 2 tablas.
 *
 * ÍNDICES INDIVIDUALES (Nuevos):
 *   1. client_events.firebase_uid  — Búsquedas por usuario de Firebase (flujo Android/Web).
 *   2. reviews.rating              — Filtros de "Top músicos" o "peor calificados".
 *   3. musician_profiles.slug      — Búsquedas por URL amigable (ya creado como UNIQUE,
 *                                    pero se documenta explícitamente su valor como índice).
 *
 * ÍNDICES COMPUESTOS (Nuevos):
 *   1. reviews (musician_profile_id, rating) — Para el cálculo eficiente del promedio de un músico.
 *   2. hiring_requests (musician_profile_id, status) — Para listar contrataciones por estado de un músico.
 *
 * NOTA: Los índices en columnas FK (musician_profile_id, client_id) son creados automáticamente
 *       por InnoDB en columnas con FOREIGN KEY constraint. Se indexan adicionalmente campos
 *       de búsqueda frecuente que NO son FK para completar la estrategia.
 *
 * BUENAS PRÁCTICAS:
 *   - No se indexan columnas de baja cardinalidad (booleanos) de forma individual.
 *   - Se evita sobre-indexar para no afectar el rendimiento de INSERT/UPDATE.
 */
return new class extends Migration
{
    /**
     * Ejecutar la migración.
     */
    public function up(): void
    {
        // ── ÍNDICES INDIVIDUALES ───────────────────────────────────────────────

        Schema::table('client_events', function (Blueprint $table) {
            /**
             * Índice individual en firebase_uid.
             * Justificación: La app Android consulta los eventos filtrando por firebase_uid.
             * Sin este índice, cada consulta haría un Full Table Scan sobre client_events.
             */
            $table->index('firebase_uid', 'idx_client_events_firebase_uid');
        });

        Schema::table('reviews', function (Blueprint $table) {
            /**
             * Índice individual en rating.
             * Justificación: El catálogo filtra/ordena músicos por rating promedio.
             * También usado en queries del tipo WHERE rating >= 4 ORDER BY rating DESC.
             */
            $table->index('rating', 'idx_reviews_rating');
        });

        // ── ÍNDICES COMPUESTOS ─────────────────────────────────────────────────

        Schema::table('reviews', function (Blueprint $table) {
            /**
             * Índice compuesto (musician_profile_id, rating).
             * Justificación: Optimiza la subconsulta de AVG(rating) por músico que se usa
             * en la vista vw_musico_reputacion y en los triggers AFTER INSERT.
             * MySQL puede resolver la consulta usando solo el índice (Index-Only Scan).
             */
            $table->index(['musician_profile_id', 'rating'], 'idx_reviews_musician_rating');
        });

        Schema::table('hiring_requests', function (Blueprint $table) {
            /**
             * Índice compuesto (musician_profile_id, status).
             * Justificación: Consultas frecuentes del tipo:
             *   WHERE musician_profile_id = ? AND status = 'completed'
             * que se usan en el panel del músico y en la vista materializada.
             */
            $table->index(['musician_profile_id', 'status'], 'idx_hiring_requests_musician_status');
        });
    }

    /**
     * Revertir la migración.
     */
    public function down(): void
    {
        Schema::table('client_events', function (Blueprint $table) {
            $table->dropIndex('idx_client_events_firebase_uid');
        });

        Schema::table('reviews', function (Blueprint $table) {
            $table->dropIndex('idx_reviews_rating');
            $table->dropIndex('idx_reviews_musician_rating');
        });

        Schema::table('hiring_requests', function (Blueprint $table) {
            $table->dropIndex('idx_hiring_requests_musician_status');
        });
    }
};
