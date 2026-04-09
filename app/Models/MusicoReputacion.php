<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modelo de la Vista: vw_musico_reputacion
 *
 * Permite consultar la vista creada en la base de datos como si fuera una tabla.
 * La vista expone: datos del músico, rating promedio, total de reseñas y contrataciones.
 *
 * Uso en controlador:
 *   $top = MusicoReputacion::orderByDesc('rating_promedio')->limit(10)->get();
 *   $musico = MusicoReputacion::where('musician_id', $id)->first();
 *
 * IMPORTANTE: Esta vista es de solo lectura. No permite INSERT, UPDATE ni DELETE.
 */
class MusicoReputacion extends Model
{
    /**
     * Nombre de la vista en la base de datos.
     */
    protected $table = 'vw_musico_reputacion';

    /**
     * La vista no tiene columna `id` propia como primary key en el sentido convencional;
     * usamos el musician_id de musician_profiles.
     */
    protected $primaryKey = 'musician_id';

    /**
     * Una vista no gestiona timestamps propios.
     */
    public $timestamps = false;

    /**
     * La vista es de solo lectura.
     */
    protected static function booted(): void
    {
        // Prevenir escrituras accidentales en la vista
        static::creating(fn() => false);
        static::updating(fn() => false);
        static::deleting(fn() => false);
    }
}
