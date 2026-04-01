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
     * The 'fecha' column stores dates as 'd/m/Y' strings.
     * The 'duracion' column stores either:
     *   - A range like "20:30 a 22:30" (preferred), or
     *   - A bare hour count like "3" (legacy).
     *
     * Handles midnight-crossing events (e.g. 23:00 a 01:00) automatically.
     *
     * @param  string  $fecha     Raw value from ClientEvent::$fecha
     * @param  string  $duracion  Raw value from ClientEvent::$duracion
     * @return array{0: Carbon, 1: Carbon}  [$start, $end]
     */
    public static function parseDateTimeRange(string $fecha, string $duracion): array
    {
        // — Parse the date ———————————————————————————————
        try {
            $start = Carbon::createFromFormat('d/m/Y', trim($fecha));
        } catch (\Exception) {
            $start = Carbon::parse(trim($fecha));
        }
        $end = clone $start;

        // — Parse the time range ————————————————————————————
        $duracion = trim($duracion);

        if (str_contains($duracion, ' a ')) {
            [$startTime, $endTime] = explode(' a ', $duracion);
            $start->setTimeFromTimeString(trim($startTime));
            $end->setTimeFromTimeString(trim($endTime));

            // Handle midnight-crossing (e.g. 23:00 → 01:00)
            if ($end->lessThan($start)) {
                $end->addDay();
            }
        } else {
            // Legacy format: bare number = duration in hours
            $start->startOfDay();
            $hours = (int) $duracion ?: 3;
            $end->startOfDay()->addHours($hours);
        }

        return [$start, $end];
    }
}