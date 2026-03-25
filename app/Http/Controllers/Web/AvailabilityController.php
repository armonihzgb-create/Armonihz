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

        // 1. Eventos Manuales (Disponibilidad / Ocupado)
        foreach ($profile->calendarEvents as $ev) {
            $events[] = [
                'id' => 'manual_' . $ev->id,
                'real_id' => $ev->id,
                'title' => $ev->title,
                'start' => $ev->start->format('Y-m-d'),
                'end'   => $ev->end->format('Y-m-d'),
                'backgroundColor' => $ev->color ?? ($ev->type === 'busy' ? '#dc2626' : '#22c55e'),
                'borderColor' => 'transparent',
                'extendedProps' => [
                    'source' => 'manual',
                    'type' => $ev->type
                ],
                // Estructura limpia para consumo de API Móvil
                'event_source' => 'manual',
                'event_type' => $ev->type,
            ];
        }

        // 2. Contrataciones Directas Aceptadas (HiringRequests)
        $hiringRequests = $profile->hiringRequests()->where('status', 'accepted')->get();
        foreach ($hiringRequests as $hr) {
            $events[] = [
                'id' => 'hiring_' . $hr->id,
                'title' => '💍 Evento Privado',
                'start' => $hr->event_date->toIso8601String(),
                'end' => $hr->event_date->addHours(3)->toIso8601String(), // asume 3 hrs aprox
                'backgroundColor' => '#4f46e5', // Indigo
                'borderColor' => 'transparent',
                'extendedProps' => [
                    'source' => 'system',
                    'description' => 'Contratación directa.'
                ],
                // Estructura limpia para consumo de API Móvil
                'real_id' => $hr->id,
                'event_source' => 'hiring',
                'event_type' => 'busy',
            ];
        }

        // 3. Castings Aceptados (CastingApplications)
        $castingApps = $profile->castingApplications()->where('status', 'accepted')->with('event')->get();
        foreach ($castingApps as $app) {
            if ($app->event && $app->event->fecha) {
                try {
                    // Try to parse string date
                    $start = Carbon::parse($app->event->fecha);
                    $events[] = [
                        'id' => 'casting_' . $app->id,
                        'title' => '🎤 Casting: ' . $app->event->titulo,
                        'start' => $start->toIso8601String(),
                        'end' => $start->addHours(3)->toIso8601String(),
                        'backgroundColor' => '#9333ea', // Purple
                        'borderColor' => 'transparent',
                        'extendedProps' => [
                            'source' => 'system',
                            'description' => 'Casting aceptado.'
                        ],
                        // Estructura limpia para consumo de API Móvil
                        'real_id' => $app->event->id,
                        'event_source' => 'casting',
                        'event_type' => 'busy',
                    ];
                } catch (\Exception $e) {
                    // Ignore unparseable dates
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
     * Guarda un bloque manual
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
            'start' => 'required|date',
            'end' => 'required|date',
            'type' => 'required|in:available,busy'
        ]);

        $profile = Auth::user()->musicianProfile;
        if (!$profile) abort(403);

        $color = $request->type === 'busy' ? '#dc2626' : '#22c55e';

        $ev = MusicianCalendarEvent::create([
            'musician_profile_id' => $profile->id,
            'title' => $request->title,
            'start' => Carbon::parse($request->start),
            'end' => Carbon::parse($request->end),
            'type' => $request->type,
            'color' => $color,
        ]);

        return response()->json(['success' => true, 'event' => $ev]);
    }

    /**
     * Actualiza las fechas (arrastrar y soltar)
     */
    public function update(Request $request, $id)
    {
        $profile = Auth::user()->musicianProfile;
        $ev = MusicianCalendarEvent::where('id', $id)->where('musician_profile_id', $profile->id)->firstOrFail();

        $ev->update([
            'start' => Carbon::parse($request->start),
            'end' => Carbon::parse($request->end),
        ]);

        return response()->json(['success' => true]);
    }

    /**
     * Elimina evento manual
     */
    public function destroy($id)
    {
        $profile = Auth::user()->musicianProfile;
        $ev = MusicianCalendarEvent::where('id', $id)->where('musician_profile_id', $profile->id)->firstOrFail();
        
        $ev->delete();
        return response()->json(['success' => true]);
    }
}
