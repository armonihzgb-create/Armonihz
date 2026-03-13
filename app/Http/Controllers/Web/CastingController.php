<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\ClientEvent;
use App\Models\CastingApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Notifications\ProposalReceivedNotification; 

class CastingController extends Controller
{
    /**
     * Listado de castings abiertos con puntuación inteligente.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $profile = $user->musicianProfile;

        // Géneros del músico para el matching
        $musicianGenres = $profile
            ? $profile->genres->pluck('name')->map(fn($n) => strtolower($n))->toArray()
            : [];

        $musicianLocation = $profile ? strtolower($profile->location ?? '') : '';

        // Obtener eventos abiertos
        $events = ClientEvent::where('status', 'open')
            ->orderByDesc('created_at')
            ->get();

        // Calcular puntaje de coincidencia y verificar si ya postuló
        $myApplicationIds = $profile
            ? CastingApplication::where('musician_profile_id', $profile->id)
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

        $types = ClientEvent::where('status', 'open')->distinct()->pluck('tipo_musica')->sort()->values();

        $filterType = $request->get('type', 'all');
        if ($filterType !== 'all') {
            $events = $events->filter(fn($e) => strtolower($e->tipo_musica) === strtolower($filterType))->values();
        }

        return view('castings.index', compact('events', 'types', 'filterType', 'profile'));
    }

    /**
     * Detalle de un casting individual.
     */
    public function show($id)
    {
        $event = ClientEvent::findOrFail($id);
        $user = Auth::user();
        $profile = $user->musicianProfile;

        $myApplication = $profile
            ? CastingApplication::where('client_event_id', $id)
            ->where('musician_profile_id', $profile->id)
            ->first()
            : null;

        $totalApplications = CastingApplication::where('client_event_id', $id)->count();

        return view('castings.show', compact('event', 'myApplication', 'totalApplications', 'profile'));
    }

    /**
     * Lógica para postularse y enviar notificación PUSH al cliente.
     */
    public function apply(Request $request, $id)
    {
        $event = ClientEvent::findOrFail($id);
        $user = Auth::user();
        $profile = $user->musicianProfile;

        // Validaciones previas
        if (!$profile) {
            return redirect()->route('profile')->withErrors(['error' => 'Completa tu perfil de músico antes de postularte.']);
        }

        if ($event->status !== 'open') {
            return redirect()->route('castings.show', $id)->withErrors(['error' => 'Este evento ya no acepta propuestas.']);
        }

        $exists = CastingApplication::where('client_event_id', $id)
            ->where('musician_profile_id', $profile->id)
            ->exists();

        if ($exists) {
            return redirect()->route('castings.show', $id)->withErrors(['error' => 'Ya te has postulado a este evento.']);
        }

        $request->validate([
            'proposed_price' => ['required', 'numeric', 'min:0'],
            'message' => ['required', 'string', 'max:800'],
        ], [
            'proposed_price.required' => 'Debes ingresar un precio.',
            'message.required' => 'El mensaje es obligatorio.',
        ]);

        // 1. Crear la postulación
        $application = CastingApplication::create([
            'client_event_id' => $event->id,
            'musician_profile_id' => $profile->id,
            'proposed_price' => $request->proposed_price,
            'message' => $request->message,
            'status' => 'pending',
        ]);

        // 2. ENCONTRAR AL DESTINATARIO (User con fcm_token)
        // Buscamos al usuario que creó el evento
        $clientUser = $event->user; 

        // Si el evento está vinculado a un "Client", buscamos al "User" dueño de ese cliente
        if (!$clientUser && $event->client) {
            $clientUser = $event->client->user; 
        }

        // 3. ENVIAR NOTIFICACIÓN
        if ($clientUser) {
            // Laravel detectará automáticamente si tiene fcm_token para enviar el Push
            $clientUser->notify(new ProposalReceivedNotification($application));
        }

        return redirect()->route('castings.show', $id)->with('success', '¡Postulación enviada! El cliente ha sido notificado en su celular.');
    }

    /**
     * Historial de postulaciones del músico.
     */
    public function myApplications()
    {
        $user = Auth::user();
        $profile = $user->musicianProfile;

        $applications = $profile
            ? CastingApplication::where('musician_profile_id', $profile->id)
            ->with('event')
            ->orderByDesc('created_at')
            ->get()
            : collect();

        return view('castings.my-applications', compact('applications'));
    }
}