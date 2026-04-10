<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class ClientEvent extends Model
{
    use HasFactory, SoftDeletes;

    // Los campos que se pueden llenar de forma masiva
    protected $fillable = [
        'firebase_uid',
        'titulo',
        'tipo_musica',
        'fecha',
        'duracion',
        'ubicacion',
        'descripcion',
        'presupuesto',
        'status',
        'email',
        'telefono',
    ];

    protected $casts = [
        'presupuesto' => 'float',
    ];

    public function applications()
    {
        return $this->hasMany(CastingApplication::class , 'client_event_id');
    }
    public function client()
{
    return $this->belongsTo(Client::class, 'firebase_uid', 'firebase_uid');
}

public function genre()
    {
        // 'tipo_musica' es la llave foránea en esta tabla (ClientEvent)
        // que apunta al 'id' de la tabla Genre
      return $this->belongsTo(Genre::class, 'tipo_musica');
    }

   // ────────────────────────────────────────────────────────────────────────
    // Shared date/time parsing helper (DRY)
    // ────────────────────────────────────────────────────────────────────────

    /**
     * Parse the event's fecha + duracion strings into Carbon start/end objects.
     *
     * @param  string  $fecha   Raw value from ClientEvent::$fecha
     * @param  string  $duracion Raw value from ClientEvent::$duracion
     * @return array{0: Carbon, 1: Carbon}  [$start, $end]
     */
    public static function parseDateTimeRange(string $fecha, string $duracion): array
    {
        // — 1. Parse the date ———————————————————————————————
        // Tu app móvil ahora guarda en 'Y-m-d' (Ej: 2026-04-26) pero antes
        // guardaba en 'd/m/Y'. Carbon::parse() maneja automáticamente 'Y-m-d'.
        // Lo dejamos preparado para ambos casos por si tienes eventos antiguos.
        try {
            if (str_contains($fecha, '/')) {
                $start = Carbon::createFromFormat('d/m/Y', trim($fecha));
            } else {
                $start = Carbon::parse(trim($fecha)); 
            }
        } catch (\Exception $e) {
            $start = Carbon::now()->startOfDay(); // Fallback seguro
        }
        
        $end = clone $start;

        // — 2. Parse the time range ————————————————————————————
        // Limpiamos cualquier tipo de separador y lo convertimos a un guion simple.
        // Esto cubre: "16:00 a 20:00", "16:00 - 20:00", "16:00 – 20:00" y "16:00 — 20:00"
        $duracionLimpia = str_replace([' a ', ' al ', '–', '—'], '-', trim($duracion));

        if (str_contains($duracionLimpia, '-')) {
            $partes = explode('-', $duracionLimpia);
            
            $startTime = trim($partes[0]);
            $endTime = isset($partes[1]) ? trim($partes[1]) : '23:59';

            $start->setTimeFromTimeString($startTime);
            $end->setTimeFromTimeString($endTime);

            // Handle midnight-crossing (e.g. 23:00 → 01:00)
            if ($end->lessThan($start)) {
                $end->addDay();
            }
        } else {
            // Legacy format: bare number = duration in hours
            $start->startOfDay();
            $hours = (int) $duracionLimpia ?: 3;
            $end->startOfDay()->addHours($hours);
        }

        return [$start, $end];
    }
}