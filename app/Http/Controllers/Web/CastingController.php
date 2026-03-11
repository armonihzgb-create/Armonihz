<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\ClientEvent;
use App\Models\CastingApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CastingController extends Controller
{
    /**
     * List open castings, smartly sorted by genre/location match.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $profile = $user->musicianProfile;

        // Get musician's genre names for smart matching
        $musicianGenres = $profile
            ? $profile->genres->pluck('name')->map(fn($n) => strtolower($n))->toArray()
            : [];

        $musicianLocation = $profile ? strtolower($profile->location ?? '') : '';

        // Fetch all open events
        $events = ClientEvent::where('status', 'open')
            ->orderByDesc('created_at')
            ->get();

        // Add match score and already_applied flag to each event
        $myApplicationIds = $profile
            ?CastingApplication::where('musician_profile_id', $profile->id)
            ->pluck('client_event_id')
            ->toArray()
            : [];

        $events = $events->map(function ($event) use ($musicianGenres, $musicianLocation, $myApplicationIds) {
            $score = 0;
            $eventGenre = strtolower($event->tipo_musica);
            foreach ($musicianGenres as $g) {
                if (str_contains($eventGenre, $g) || str_contains($g, $eventGenre)) {
                    $score += 2;
                }
            }
            if ($musicianLocation && str_contains(strtolower($event->ubicacion), $musicianLocation)) {
                $score += 1;
            }
            $event->match_score = $score;
            $event->already_applied = in_array($event->id, $myApplicationIds);
            $event->applications_count = CastingApplication::where('client_event_id', $event->id)->count();
            return $event;
        })->sortByDesc('match_score')->values();

        // Get unique types for filter tabs
        $types = ClientEvent::where('status', 'open')->distinct()->pluck('tipo_musica')->sort()->values();

        $filterType = $request->get('type', 'all');
        if ($filterType !== 'all') {
            $events = $events->filter(fn($e) => strtolower($e->tipo_musica) === strtolower($filterType))->values();
        }

        return view('castings.index', compact('events', 'types', 'filterType', 'profile'));
    }

    /**
     * Show a single casting's detail page.
     */
    public function show($id)
    {
        $event = ClientEvent::findOrFail($id);
        $user = Auth::user();
        $profile = $user->musicianProfile;

        // Find existing application if any
        $myApplication = $profile
            ?CastingApplication::where('client_event_id', $id)
            ->where('musician_profile_id', $profile->id)
            ->first()
            : null;

        $totalApplications = CastingApplication::where('client_event_id', $id)->count();

        return view('castings.show', compact('event', 'myApplication', 'totalApplications', 'profile'));
    }

    /**
     * Submit a casting application.
     */
    public function apply(Request $request, $id)
    {
        $event = ClientEvent::findOrFail($id);
        $user = Auth::user();
        $profile = $user->musicianProfile;

        if (!$profile) {
            return redirect()->route('profile')->withErrors(['error' => 'Completa tu perfil antes de postularte.']);
        }

        if ($event->status !== 'open') {
            return redirect()->route('castings.show', $id)->withErrors(['error' => 'Este evento ya no está disponible.']);
        }

        // Check duplicate
        $exists = CastingApplication::where('client_event_id', $id)
            ->where('musician_profile_id', $profile->id)
            ->exists();

        if ($exists) {
            return redirect()->route('castings.show', $id)->withErrors(['error' => 'Ya te postulaste a este evento.']);
        }

        $request->validate([
            'proposed_price' => ['required', 'numeric', 'min:0'],
            'message' => ['required', 'string', 'max:800'],
        ], [
            'proposed_price.required' => 'Ingresa tu precio propuesto.',
            'proposed_price.numeric' => 'El precio debe ser un número.',
            'message.required' => 'Escribe un mensaje para el cliente.',
            'message.max' => 'El mensaje no puede superar los 800 caracteres.',
        ]);

        CastingApplication::create([
            'client_event_id' => $event->id,
            'musician_profile_id' => $profile->id,
            'proposed_price' => $request->proposed_price,
            'message' => $request->message,
            'status' => 'pending',
        ]);

        return redirect()->route('castings.show', $id)->with('success', '¡Te postulaste exitosamente! El cliente podrá ver tu propuesta.');
    }

    /**
     * Show the musician's own application history.
     */
    public function myApplications()
    {
        $user = Auth::user();
        $profile = $user->musicianProfile;

        $applications = $profile
            ?CastingApplication::where('musician_profile_id', $profile->id)
            ->with('event')
            ->orderByDesc('created_at')
            ->get()
            : collect();

        return view('castings.my-applications', compact('applications'));
    }
}
