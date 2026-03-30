<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\MusicianCalendarEvent;
use Carbon\Carbon;

class AvailabilityController extends Controller
{
    /**
     * Devuelve todos los eventos en formato JSON para FullCalendar.
     */
    public function getEvents()
    {
        $profile = Auth::user()->musicianProfile;
        if (!$profile)
            return response()->json([]);

        $events = [];

        // 1. Eventos manuales (disponibilidad / ocupado)
        foreach ($profile->calendarEvents as $ev) {
            // Detectar si es día completo: hora 00:00 inicio y 23:59 fin
            $isAllDay = ($ev->start->hour === 0 && $ev->start->minute === 0
                && ($ev->end->hour === 23 || $ev->end->hour === 0)
                && ($ev->end->minute === 59 || $ev->end->minute === 0));

            if ($isAllDay) {
                $events[] = [
                    'id' => 'manual_' . $ev->id,
                    'real_id' => $ev->id,
                    'title' => $ev->title,
                    'start' => $ev->start->format('Y-m-d'),
                    'end' => $ev->end->addDay()->format('Y-m-d'), // FullCalendar end exclusive
                    'allDay' => true,
                    'backgroundColor' => $ev->color ?? '#dc2626',
                    'borderColor' => 'transparent',
                    'extendedProps' => ['source' => 'manual', 'type' => $ev->type, 'real_id' => $ev->id],
                    'event_source' => 'manual',
                    'event_type' => $ev->type,
                ];
            }
            else {
                $events[] = [
                    'id' => 'manual_' . $ev->id,
                    'real_id' => $ev->id,
                    'title' => $ev->title,
                    'start' => $ev->start->format('Y-m-d\TH:i:s'),
                    'end' => $ev->end->format('Y-m-d\TH:i:s'),
                    'allDay' => false,
                    'backgroundColor' => '#ef4444',
                    'borderColor' => 'transparent',
                    'extendedProps' => ['source' => 'manual', 'type' => $ev->type, 'real_id' => $ev->id],
                    'event_source' => 'manual',
                    'event_type' => $ev->type,
                ];
            }
        }

        // 2. Contrataciones Directas Aceptadas
       $hiringRequests = $profile->hiringRequests()->where('status', 'accepted')->get();
        foreach ($hiringRequests as $hr) {
            
            // 1. Nos aseguramos de manejar las fechas con Carbon
            $start = \Carbon\Carbon::parse($hr->event_date);
            $end = $hr->end_time ? \Carbon\Carbon::parse($hr->end_time) : $start->copy()->addHours(3);
            
            // 2. Armamos el texto bonito del horario (Ej: 20:00 a 01:00)
            $horario = $start->format('H:i') . ' a ' . $end->format('H:i');

            $events[] = [
                'id'              => 'hiring_' . $hr->id,
                // 3. Metemos el horario directamente en el título
                'title'           => '💍 ' . $horario . ' - Evento Privado', 
                'start'           => $start->toIso8601String(),
                'end'             => $end->toIso8601String(),
                'backgroundColor' => '#4f46e5',
                'borderColor'     => 'transparent',
                'extendedProps'   => ['source' => 'system', 'description' => 'Contratación directa.'],
                'real_id'         => $hr->id,
                'event_source'    => 'hiring',
                'event_type'      => 'busy',
            ];
        }

       // 3. Castings Aceptados
        $castingApps = $profile->castingApplications()->where('status', 'accepted')->with('event')->get();
        foreach ($castingApps as $app) {
            if ($app->event && $app->event->fecha) {
                try {
                    // 1. Parseamos el día (Ej: "15/04/2026")
                    $fechaString = trim($app->event->fecha);
                    
                    try {
                        $start = \Carbon\Carbon::createFromFormat('d/m/Y', $fechaString);
                    } catch (\Exception $e) {
                        $start = \Carbon\Carbon::parse($fechaString);
                    }

                    $end = clone $start;

                    // 2. Parseamos la hora desde DURACION (Ej: "20:30 a 22:30")
                    $duracionString = trim($app->event->duracion);

                    if (str_contains($duracionString, ' a ')) {
                        $parts = explode(' a ', $duracionString);
                        $startTimeString = trim($parts[0]); // "20:30"
                        $endTimeString = trim($parts[1]);   // "22:30"

                        // Le aplicamos las horas exactas al día que ya teníamos
                        $start->setTimeFromTimeString($startTimeString);
                        $end->setTimeFromTimeString($endTimeString);

                        // Lógica por si el evento cruza la medianoche (Ej: "23:00 a 02:00")
                        if ($end->lessThan($start)) {
                            $end->addDay();
                        }
                    } else {
                        // Plan B por si un evento viejo solo dice "3" en duración
                        $start->startOfDay();
                        $duration = (int) $duracionString ?: 3;
                        $end->startOfDay()->addHours($duration);
                    }

                    $events[] = [
                        'id' => 'casting_' . $app->id,
                        'title' => '🎤 Casting: ' . $app->event->titulo,
                        // toIso8601String() ya lleva la zona horaria del servidor, así que FullCalendar no lo moverá
                      //  'start' => $start->toIso8601String(),
                      //  'end' => $end->toIso8601String(),
                        'start' => $start->format('Y-m-d\TH:i:s'),
                        'end' => $end->format('Y-m-d\TH:i:s'),
                        'backgroundColor' => '#9333ea',
                        'borderColor' => 'transparent',
                        'extendedProps' => ['source' => 'system', 'description' => 'Casting aceptado.'],
                        'real_id' => $app->event->id,
                        'event_source' => 'casting',
                        'event_type' => 'busy',
                    ];
                }
                catch (\Exception $e) {
                    \Log::error("Error parseando fecha de casting ID {$app->id}: " . $e->getMessage());
                }
            }
        }

        return response()->json($events);
    }

    /**
     * Muestra la vista del calendario
     */
    public function index()
    {
        return view('availability');
    }

    /**
     * Guarda un bloque manual (día completo o rango de horas)
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:120',
            'start' => 'required|string',
            'end' => 'required|string',
            'type' => 'required|in:available,busy',
        ]);

        $profile = Auth::user()->musicianProfile;
        if (!$profile)
            abort(403);

        // Detectar si es día completo (sin hora "T") o rango de horas
        $isAllDay = !str_contains($request->start, 'T');

        if ($isAllDay) {
            $start = Carbon::parse($request->start)->startOfDay(); // 00:00:00
            $end = Carbon::parse($request->end)->setTime(23, 59, 59); // 23:59:59
        }
        else {
            $start = Carbon::parse($request->start);
            $end = Carbon::parse($request->end);

            // Lógica Cross-Midnight: si el fin es anterior o igual al inicio (ej. 22:00 a 01:00)
            // se asume que el fin es del día siguiente.
            if ($end->lessThanOrEqualTo($start)) {
                $end->addDay();
            }
        }

        if ($start->isPast() && !$start->isToday()) {
            return response()->json(['success' => false, 'message' => 'No puedes crear bloques en fechas pasadas.'], 422);
        }

        // Check for existing overlapping manual blocks
        $overlap = MusicianCalendarEvent::where('musician_profile_id', $profile->id)
            ->where(function ($q) use ($start, $end) {
            // If it's allDay (00:00 to 23:59), an exact start match is enough for broad "already blocked" check
            $q->where(function ($q2) use ($start, $end) {
                    $q2->where('start', '<', $end)
                        ->where('end', '>', $start);
                }
                );
            })->exists();

        if ($overlap) {
            return response()->json(['success' => false, 'message' => 'Ya existe un bloqueo que se cruza con este horario.'], 422);
        }

        $ev = MusicianCalendarEvent::create([
            'musician_profile_id' => $profile->id,
            'title' => $request->title,
            'start' => $start,
            'end' => $end,
            'type' => $request->type,
            'color' => '#dc2626',
        ]);

        return response()->json([
            'success' => true,
            'event' => $ev,
            'event_source' => 'manual',
            'event_type' => $ev->type,
        ]);
    }

    /**
     * Actualiza fechas (arrastrar y soltar)
     */
    public function update(Request $request, $id)
    {
        $profile = Auth::user()->musicianProfile;
        $ev = MusicianCalendarEvent::where('id', $id)
            ->where('musician_profile_id', $profile->id)
            ->firstOrFail();

        $isAllDay = !str_contains($request->start, 'T');

        if ($isAllDay) {
            $start = Carbon::parse($request->start)->startOfDay();
            $end = Carbon::parse($request->end)->subDay()->setTime(23, 59, 59); // end is exclusive in FC
        }
        else {
            $start = Carbon::parse($request->start);
            $end = Carbon::parse($request->end ?: $request->start);

            // Lógica Cross-Midnight para actualización
            if ($end->lessThanOrEqualTo($start)) {
                $end->addDay();
            }
        }

        if ($start->isPast() && !$start->isToday()) {
            return response()->json(['success' => false, 'message' => 'No puedes mover bloques a fechas pasadas.'], 422);
        }

        // Check for existing overlapping manual blocks (excluding self)
        $overlap = MusicianCalendarEvent::where('musician_profile_id', $profile->id)
            ->where('id', '!=', $id)
            ->where(function ($q) use ($start, $end) {
            $q->where('start', '<', $end)
                ->where('end', '>', $start);
        })->exists();

        if ($overlap) {
            return response()->json(['success' => false, 'message' => 'Ya existe un bloqueo en este horario.'], 422);
        }

        $ev->update(['start' => $start, 'end' => $end]);

        return response()->json(['success' => true]);
    }

    /**
     * Elimina evento manual
     */
    public function destroy($id)
    {
        $profile = Auth::user()->musicianProfile;
        $ev = MusicianCalendarEvent::where('id', $id)
            ->where('musician_profile_id', $profile->id)
            ->firstOrFail();

        $ev->delete();
        return response()->json(['success' => true]);
    }
}
