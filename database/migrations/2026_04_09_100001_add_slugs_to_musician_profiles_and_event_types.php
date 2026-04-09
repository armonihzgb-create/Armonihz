<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Migración: Implementación de Slugs
 *
 * Agrega columnas de tipo slug a las tablas `musician_profiles` y `event_types`.
 * Los slugs permiten construir URLs amigables para SEO, por ejemplo:
 *   - /musicos/juan-perez-band en lugar de /musicos/42
 *   - /tipo-evento/bodas en lugar de /tipo-evento/3
 *
 * La columna es UNIQUE para garantizar que cada recurso tenga un identificador único en la URL.
 * Se llena automáticamente para los registros existentes usando el stage_name / name del registro.
 */
return new class extends Migration
{
    /**
     * Ejecutar la migración.
     * Se utiliza nullable() para no romper registros existentes al agregar la columna.
     * Después se rellena y se aplica el índice UNIQUE.
     */
    public function up(): void
    {
        // ── TABLA 1: musician_profiles ──────────────────────────────────────────
        Schema::table('musician_profiles', function (Blueprint $table) {
            // Slug para URL amigable del perfil del músico (ej. "juan-perez-band")
            // nullable() permite correr sin error en registros existentes
            $table->string('slug')->nullable()->unique()->after('stage_name');
        });

        // Rellenar slugs para registros existentes de músicos
        // Se usa Str::slug() equivalente a nivel SQL para garantizar compatibilidad
        DB::table('musician_profiles')->orderBy('id')->each(function ($profile) {
            $baseSlug = Str::slug($profile->stage_name, '-');
            $slug     = $baseSlug;
            $counter  = 1;

            // Garantizar unicidad en caso de stage_names duplicados
            while (DB::table('musician_profiles')->where('slug', $slug)->where('id', '!=', $profile->id)->exists()) {
                $slug = $baseSlug . '-' . $counter++;
            }

            DB::table('musician_profiles')->where('id', $profile->id)->update(['slug' => $slug]);
        });

        // ── TABLA 2: event_types ────────────────────────────────────────────────
        Schema::table('event_types', function (Blueprint $table) {
            // Slug para URL amigable del tipo de evento (ej. "boda", "concierto")
            $table->string('slug')->nullable()->unique()->after('name');
        });

        // Rellenar slugs para registros existentes de tipos de evento
        DB::table('event_types')->orderBy('id')->each(function ($type) {
            $baseSlug = Str::slug($type->name, '-');
            $slug     = $baseSlug;
            $counter  = 1;

            while (DB::table('event_types')->where('slug', $slug)->where('id', '!=', $type->id)->exists()) {
                $slug = $baseSlug . '-' . $counter++;
            }

            DB::table('event_types')->where('id', $type->id)->update(['slug' => $slug]);
        });
    }

    /**
     * Revertir la migración.
     */
    public function down(): void
    {
        Schema::table('musician_profiles', function (Blueprint $table) {
            $table->dropColumn('slug');
        });

        Schema::table('event_types', function (Blueprint $table) {
            $table->dropColumn('slug');
        });
    }
};
