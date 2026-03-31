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

    // 1. CARGA LA RELACIÓN 'genre' AQUÍ CON with()
   $events = ClientEvent::with(['genre', 'client']) // 🔵 Agregamos 'client'
        ->where('status', 'open')
        ->orderByDesc('created_at')
        ->get();

    $myApplicationIds = $profile
        ? CastingApplication::where('musician_profile_id', $profile->id)
        ->pluck('client_event_id')
        ->toArray()
        : [];

    $events = $events->map(function ($event) use ($musicianGenres, $musicianLocation, $myApplicationIds) {
        $score = 0;
        
        $eventGenreName = $event->genre ? strtolower($event->genre->name) : strtolower($event->tipo_musica);
        
        foreach ($musicianGenres as $g) {
            if (str_contains($eventGenreName, $g) || str_contains($g, $eventGenreName)) {
                $score += 2;
            }
        }

        if ($musicianLocation && str_contains(strtolower($event->ubicacion), $musicianLocation)) {
            $score += 1;
        }

        $event->match_score = $score;
        $event->already_applied = in_array($event->id, $myApplicationIds);
        $event->applications_count = CastingApplication::where('client_event_id', $event->id)->count();
        
        // 🔵 ASIGNAMOS EL NOMBRE DEL CLIENTE AL EVENTO
        $event->nombre_cliente = $event->client ? $event->client->nombre : 'Usuario Anónimo';
        
        return $event;
    })->sortByDesc('match_score')->values();

    $types = \App\Models\Genre::orderBy('name')->pluck('name')->values();

    $rawType = $request->get('type', 'all');
    $filterType = explode('?', $rawType)[0];

    if ($filterType !== 'all') {
        // 3. FILTRO AJUSTADO PARA COMPARAR CONTRA EL NOMBRE
        $events = $events->filter(function($e) use ($filterType) {
            $currentName = $e->genre ? $e->genre->name : $e->tipo_musica;
            return strtolower($currentName) === strtolower($filterType);
        })->values();
    }

    return view('castings.index', compact('events', 'types', 'filterType', 'profile'));
}

    /**
     * Detalle de un casting individual.
     */
   public function show($id)
{
    // Cargamos la relación genre para que en la vista de detalle también salga el nombre
    $event = ClientEvent::with(['genre', 'client'])->findOrFail($id);

    // 🔵 Le asignamos la variable para la vista
    $event->nombre_cliente = $event->client ? $event->client->nombre : 'Usuario Anónimo';
    
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
        return redirect()->route('profile')->withErrors([
            'error' => 'Completa tu perfil de músico antes de postularte.'
        ]);
    }

    if ($event->status !== 'open') {
        return redirect()->route('castings.show', $id)->withErrors([
            'error' => 'Este evento ya no acepta propuestas.'
        ]);
    }

    $exists = CastingApplication::where('client_event_id', $id)
        ->where('musician_profile_id', $profile->id)
        ->exists();

    if ($exists) {
        return redirect()->route('castings.show', $id)->withErrors([
            'error' => 'Ya te has postulado a este evento.'
        ]);
    }

    $request->validate([
        'proposed_price' => ['required', 'numeric', 'min:0'],
        'message' => ['required', 'string', 'max:800'],
    ], [
        'proposed_price.required' => 'El precio propuesto es obligatorio.',
        'proposed_price.numeric' => 'El precio debe ser un número.',
        'proposed_price.min' => 'El precio no puede ser negativo.',
        'message.required' => 'El mensaje es obligatorio.',
        'message.max' => 'El mensaje no puede superar los 800 caracteres.',
    ]);

    // 1️⃣ Crear la postulación
    $application = CastingApplication::create([
        'client_event_id' => $event->id,
        'musician_profile_id' => $profile->id,
        'proposed_price' => $request->proposed_price,
        'message' => $request->message,
        'status' => 'pending',
    ]);

    // 2️⃣ Obtener cliente dueño del evento
    $client = $event->client;

    if (!$client) {
        \Log::warning('Evento sin cliente asociado', ['event_id' => $event->id]);
    }

    // 3️⃣ Enviar notificación PUSH
    if ($client && $client->fcm_token) {

        try {

            $fcm = app(\App\Services\FirebaseNotificationService::class);

            $fcm->send(
                $client->fcm_token,
                "Nueva propuesta 🎵",
                $user->name . " envió una propuesta para tu evento"
            );

            \Log::info("Notificación enviada al cliente", [
                'client_id' => $client->id,
                'event_id' => $event->id
            ]);

        } catch (\Throwable $e) {

            \Log::error("Error enviando notificación", [
                'error' => $e->getMessage()
            ]);

        }
    }

    return redirect()
        ->route('castings.show', $id)
        ->with('success', '¡Postulación enviada! El cliente ha sido notificado en su celular.');
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

    /**
     * Actualiza una propuesta enviada.
     */
    public function update(Request $request, $id)
    {
        $app = CastingApplication::findOrFail($id);
        $user = Auth::user();

        if ($app->musician_profile_id !== $user->musicianProfile->id) {
            abort(403);
        }

        if ($app->status !== 'pending') {
            return back()->withErrors(['error' => 'No puedes editar una propuesta que ya fue respondida.']);
        }

        $request->validate([
            'proposed_price' => ['required', 'numeric', 'min:0'],
            'message' => ['required', 'string', 'max:800'],
        ], [
            'proposed_price.required' => 'El precio propuesto es obligatorio.',
            'proposed_price.numeric' => 'El precio debe ser un número.',
            'proposed_price.min' => 'El precio no puede ser negativo.',
            'message.required' => 'El mensaje es obligatorio.',
            'message.max' => 'El mensaje no puede superar los 800 caracteres.',
        ]);

        $app->update([
            'proposed_price' => $request->proposed_price,
            'message' => $request->message,
        ]);

        return back()->with('success', 'Tu propuesta ha sido actualizada correctamente.');
    }

    /**
     * Cancela (elimina) una propuesta enviada.
     */
    public function destroy($id)
    {
        $app = CastingApplication::findOrFail($id);
        $user = Auth::user();

        if ($app->musician_profile_id !== $user->musicianProfile->id) {
            abort(403);
        }

        // Si ya fue aceptada, probablemente no deba borrarse así de fácil
        if ($app->status === 'accepted') {
            return back()->withErrors(['error' => 'Ya te han aceptado para este evento. Contacta soporte para cancelar.']);
        }

        $app->delete();

        return redirect()->route('castings.index')->with('success', 'Has cancelado tu postulación a este casting.');
    }

    /**
     * Marca un casting aceptado como finalizado (completed).
     */
    public function complete($id)
    {
        $app = CastingApplication::findOrFail($id);
        $user = Auth::user();

        // Verificar que el músico que lo quiere cerrar sea el dueño de la postulación
        if ($app->musician_profile_id !== $user->musicianProfile->id) {
            abort(403);
        }

        // Solo se pueden finalizar castings que ya estaban aceptados
        if ($app->status !== 'accepted') {
            return back()->withErrors(['error' => 'Solo puedes finalizar eventos que ya han sido aceptados.']);
        }

        // Cambiamos el estado a completed
        $app->update([
            'status' => 'completed'
        ]);

        return redirect()->route('castings.show', $app->client_event_id)
            ->with('success', '¡Has finalizado el evento! El cliente ahora podrá dejarte una reseña.');
    }
}