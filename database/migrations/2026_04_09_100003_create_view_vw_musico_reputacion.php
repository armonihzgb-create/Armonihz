<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Migración: Vista Regular (DATABASE VIEW)
 *
 * Crea la vista `vw_musico_reputacion` que combina:
 * - INNER JOIN entre `musician_profiles` y `users` para obtener datos básicos del músico.
 * - Subconsultas anidadas (correlated subqueries) en el SELECT para calcular:
 *     * rating_promedio: Promedio de todas las calificaciones recibidas.
 *     * total_resenas: Número de reseñas recibidas.
 *     * total_contrataciones: Número de contratos con status 'completed'.
 *
 * JUSTIFICACIÓN:
 *   - La vista abstrae la complejidad de la consulta del backend.
 *   - Eloquent puede consultar esta vista como si fuera una tabla.
 *   - Evita SELECT * innecesarios: solo expone los campos útiles para el listado.
 */
return new class extends Migration
{
    /**
     * Ejecutar la migración.
     */
    public function up(): void
    {
        DB::unprepared("
            CREATE OR REPLACE VIEW vw_musico_reputacion AS
            /*
             * Vista: vw_musico_reputacion
             * Propósito: Proveer un resumen de la reputación y actividad de cada músico.
             * Combina: INNER JOIN + subconsultas anidadas.
             */
            SELECT
                mp.id                   AS musician_id,
                mp.slug,
                mp.stage_name,
                mp.location,
                mp.hourly_rate,
                mp.profile_picture,
                mp.is_verified,
                mp.profile_views,
                u.email                 AS user_email,

                -- Subconsulta 1: Promedio de calificaciones (COALESCE evita NULL si no hay reseñas)
                (
                    SELECT COALESCE(AVG(r.rating), 0)
                    FROM   reviews r
                    WHERE  r.musician_profile_id = mp.id
                ) AS rating_promedio,

                -- Subconsulta 2: Total de reseñas recibidas
                (
                    SELECT COUNT(r.id)
                    FROM   reviews r
                    WHERE  r.musician_profile_id = mp.id
                ) AS total_resenas,

                -- Subconsulta 3: Total de contrataciones completadas
                (
                    SELECT COUNT(hr.id)
                    FROM   hiring_requests hr
                    WHERE  hr.musician_profile_id = mp.id
                      AND  hr.status = 'completed'
                ) AS total_contrataciones_completadas

            FROM musician_profiles mp
            -- INNER JOIN: solo músicos que tienen cuenta de usuario asociada
            INNER JOIN users u ON mp.user_id = u.id
            WHERE u.is_active = 1
        ");
    }

    /**
     * Revertir la migración.
     */
    public function down(): void
    {
        DB::unprepared('DROP VIEW IF EXISTS vw_musico_reputacion');
    }
};
