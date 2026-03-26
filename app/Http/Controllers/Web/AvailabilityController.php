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
        if (!$profile) return response()->json([]);

        $events = [];

        // 1. Eventos manuales (disponibilidad / ocupado)
        foreach ($profile->calendarEvents as $ev) {
            // Detectar si es día completo: hora 00:00 inicio y 23:59 fin
            $isAllDay = ($ev->start->hour === 0 && $ev->start->minute === 0
                      && ($ev->end->hour === 23 || $ev->end->hour === 0)
                      && ($ev->end->minute === 59 || $ev->end->minute === 0));

            if ($isAllDay) {
                $events[] = [
                    'id'              => 'manual_' . $ev->id,
                    'real_id'         => $ev->id,
                    'title'           => $ev->title,
                    'start'           => $ev->start->format('Y-m-d'),
                    'end'             => $ev->end->addDay()->format('Y-m-d'), // FullCalendar end exclusive
                    'allDay'          => true,
                    'backgroundColor' => $ev->color ?? '#dc2626',
                    'borderColor'     => 'transparent',
                    'extendedProps'   => ['source' => 'manual', 'type' => $ev->type, 'real_id' => $ev->id],
                    'event_source'    => 'manual',
                    'event_type'      => $ev->type,
                ];
            } else {
                $events[] = [
                    'id'              => 'manual_' . $ev->id,
                    'real_id'         => $ev->id,
                    'title'           => $ev->title,
                    'start'           => $ev->start->format('Y-m-d\TH:i:s'),
                    'end'             => $ev->end->format('Y-m-d\TH:i:s'),
                    'allDay'          => false,
                    'backgroundColor' => '#ef4444',
                    'borderColor'     => 'transparent',
                    'extendedProps'   => ['source' => 'manual', 'type' => $ev->type, 'real_id' => $ev->id],
                    'event_source'    => 'manual',
                    'event_type'      => $ev->type,
                ];
            }
        }

        // 2. Contrataciones Directas Aceptadas
        $hiringRequests = $profile->hiringRequests()->where('status', 'accepted')->get();
        foreach ($hiringRequests as $hr) {
            $events[] = [
                'id'              => 'hiring_' . $hr->id,
                'title'           => '💍 Evento Privado',
                'start'           => $hr->event_date->toIso8601String(),
                'end'             => $hr->event_date->addHours(3)->toIso8601String(),
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
                    $start = Carbon::parse($app->event->fecha);
                    $events[] = [
                        'id'              => 'casting_' . $app->id,
                        'title'           => '🎤 Casting: ' . $app->event->titulo,
                        'start'           => $start->toIso8601String(),
                        'end'             => $start->addHours(3)->toIso8601String(),
                        'backgroundColor' => '#9333ea',
                        'borderColor'     => 'transparent',
                        'extendedProps'   => ['source' => 'system', 'description' => 'Casting aceptado.'],
                        'real_id'         => $app->event->id,
                        'event_source'    => 'casting',
                        'event_type'      => 'busy',
                    ];
                } catch (\Exception $e) { /* ignore */ }
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
            'end'   => 'required|string',
            'type'  => 'required|in:available,busy',
        ]);

        $profile = Auth::user()->musicianProfile;
        if (!$profile) abort(403);

        // Detectar si es día completo (sin hora "T") o rango de horas
        $isAllDay = !str_contains($request->start, 'T');

        if ($isAllDay) {
            $start = Carbon::parse($request->start)->startOfDay();       // 00:00:00
            $end   = Carbon::parse($request->end)->setTime(23, 59, 59);  // 23:59:59
        } else {
            $start = Carbon::parse($request->start);
            $end   = Carbon::parse($request->end);
        }

        if ($start->isPast() && !$start->isToday()) {
            return response()->json(['success' => false, 'message' => 'No puedes crear bloques en fechas pasadas.'], 422);
        }

        $ev = MusicianCalendarEvent::create([
            'musician_profile_id' => $profile->id,
            'title'               => $request->title,
            'start'               => $start,
            'end'                 => $end,
            'type'                => $request->type,
            'color'               => '#dc2626',
        ]);

        return response()->json([
            'success'      => true,
            'event'        => $ev,
            'event_source' => 'manual',
            'event_type'   => $ev->type,
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
            $end   = Carbon::parse($request->end)->subDay()->setTime(23, 59, 59); // end is exclusive in FC
        } else {
            $start = Carbon::parse($request->start);
            $end   = Carbon::parse($request->end ?: $request->start);
        }

        if ($start->isPast() && !$start->isToday()) {
            return response()->json(['success' => false, 'message' => 'No puedes mover bloques a fechas pasadas.'], 422);
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
