<?php

namespace App\Http\Controllers;

use App\Models\ClientEvent;
use App\Models\CastingApplication;
use App\Models\MusicianProfile;
use Illuminate\Http\Request;

class ClientEventController extends Controller
{
    // Obtener los eventos del cliente logueado
   // Obtener los eventos del cliente logueado
// Obtener los eventos del cliente logueado
    public function index(Request $request)
    {
        $firebaseUid = $request->attributes->get('firebase_uid');

        // 🔵 1. Cargamos las relaciones 'genre', 'client' y AHORA 'applications'
        // Cargar 'applications' aquí hace que todo sea 10x más rápido (Evita el problema de N+1 queries)
        $eventos = ClientEvent::with(['genre', 'client', 'applications']) 
            ->where('firebase_uid', $firebaseUid)
            ->orderBy('created_at', 'desc')
            ->get();

        $formattedEvents = $eventos->map(function ($evento) {

            // 🔥 LA MAGIA: Buscamos si hay una propuesta ganadora y revisamos su estado
            $winningApp = $evento->applications->whereIn('status', ['accepted', 'completed'])->first();
            $finalStatus = $evento->status;

            // Si el músico ya le dio a "Finalizar" a su propuesta, forzamos el estado del evento a 'completed'
            if ($winningApp && $winningApp->status === 'completed') {
                $finalStatus = 'completed';
            }

            return [
                'id' => $evento->id,
                'titulo' => $evento->titulo,
                'tipoMusica' => $evento->genre ? $evento->genre->name : $evento->tipo_musica,
                'fecha' => $evento->fecha,
                'ubicacion' => $evento->ubicacion,
                'status' => $finalStatus, // 👈 Enviamos el estado corregido a Android
                'duracion' => $evento->duracion,
                'descripcion' => $evento->descripcion,
                'presupuesto' => (float) $evento->presupuesto,
                
                // Le quitamos los paréntesis a count() porque ya cargamos la relación arriba
                'propuestas' => $evento->applications->count(), 
                
                // 🔵 2. Agregamos el nombre y contacto del cliente
                'nombre_cliente' => $evento->client->nombre ?? 'Usuario Anónimo',
                'email' => $evento->email ?? ($evento->client->email ?? null),
                'telefono' => $evento->telefono ?? ($evento->client->telefono ?? null)
            ];
        });

        return response()->json($formattedEvents);
    }

    /**
     * Get all applications for a specific client event.
     */
    public function getApplications(Request $request, $id)
    {
        $firebaseUid = $request->attributes->get('firebase_uid');
        $event = ClientEvent::where('id', $id)->where('firebase_uid', $firebaseUid)->firstOrFail();

        $applications = CastingApplication::where('client_event_id', $event->id)
            ->with('musician.user')
            ->get()
            ->map(function ($app) {
            $musician = $app->musician;
            return [
            'id' => $app->id,
            'status' => $app->status,
            'proposed_price' => $app->proposed_price,
            'message' => $app->message,
            'created_at' => $app->created_at,
            'musician' => [
            'id' => $musician->id,
            'stage_name' => $musician->stage_name,
            'location' => $musician->location,
            'profile_picture' => $musician->profile_picture,
            'hourly_rate' => $musician->hourly_rate,
            ],
            ];
        });

        return response()->json(['event_id' => $event->id, 'applications' => $applications]);
    }

    /**
     * Accept an application — closes the event and marks the musician as hired.
     */
    public function acceptApplication(Request $request, $eventId, $appId)
    {
        $firebaseUid = $request->attributes->get('firebase_uid');
        $event = ClientEvent::where('id', $eventId)->where('firebase_uid', $firebaseUid)->firstOrFail();

        if ($event->status !== 'open') {
            return response()->json(['error' => 'El evento ya no está abierto.'], 409);
        }

        $app = CastingApplication::where('id', $appId)
            ->where('client_event_id', $eventId)
            ->firstOrFail();

        // Accept this application
        $app->update(['status' => 'accepted']);

        // Reject all other applications for this event
        CastingApplication::where('client_event_id', $eventId)
            ->where('id', '!=', $appId)
            ->update(['status' => 'rejected']);

        // Close the event
        $event->update(['status' => 'closed']);

        return response()->json(['message' => 'Músico contratado exitosamente.', 'application_id' => $app->id]);
    }

    /**
     * Cancel a previously accepted application — re-opens the event so new proposals can be accepted.
     */
    public function cancelApplication(Request $request, $eventId, $appId)
    {
        $firebaseUid = $request->attributes->get('firebase_uid');

        // Ensure the event belongs to this client
        $event = ClientEvent::where('id', $eventId)
            ->where('firebase_uid', $firebaseUid)
            ->firstOrFail();

        // Find the application that was accepted
        $app = CastingApplication::where('id', $appId)
            ->where('client_event_id', $eventId)
            ->firstOrFail();

        if ($app->status !== 'accepted') {
            return response()->json([
                'error' => 'Solo puedes cancelar una propuesta que haya sido aceptada previamente.',
            ], 409);
        }

        // Mark the accepted application as cancelled
        $app->update(['status' => 'cancelled']);

        // Re-open the rejected applications so the client can accept another one
        CastingApplication::where('client_event_id', $eventId)
            ->where('id', '!=', $appId)
            ->where('status', 'rejected')
            ->update(['status' => 'pending']);

        // Re-open the event so the client can accept a different musician
        $event->update(['status' => 'open']);

        return response()->json([
            'message' => 'Contratación cancelada. El evento está abierto de nuevo.',
        ]);
    }

    // Guardar un nuevo evento
public function store(Request $request)
    {
        $validated = $request->validate([
            'titulo'      => 'required|string',
            'genre_id'    => 'required|exists:genres,id', 
            'fecha'       => 'required|string',
            'duracion'    => 'required|string',
            'ubicacion'   => 'required|string',
            'descripcion' => 'nullable|string',
            'presupuesto' => 'required|numeric',
            'email'       => 'nullable|email',  // NUEVO
            'telefono'    => 'nullable|string', // NUEVO
        ]);

        $firebaseUid = $request->attributes->get('firebase_uid');

        $evento = ClientEvent::create([
            'firebase_uid' => $firebaseUid,
            'titulo'       => $validated['titulo'],
            'tipo_musica'  => $validated['genre_id'], 
            'fecha'        => $validated['fecha'],
            'duracion'     => $validated['duracion'],
            'ubicacion'    => $validated['ubicacion'],
            'descripcion'  => $validated['descripcion'],
            'presupuesto'  => $validated['presupuesto'],
            'email'        => $validated['email'] ?? null,    // NUEVO
            'telefono'     => $validated['telefono'] ?? null, // NUEVO
        ]);

        return response()->json([
            'message' => 'Evento creado con éxito',
            'evento' => $evento
        ], 201);
    }
    // Actualizar un evento existente
    public function update(Request $request, $id)
{
    $firebaseUid = $request->attributes->get('firebase_uid');

    // 1. Buscamos el evento
    $evento = ClientEvent::where('id', $id)
        ->where('firebase_uid', $firebaseUid)
        ->firstOrFail();

    if ($evento->status !== 'open') {
        return response()->json([
            'error' => 'No puedes editar un evento que ya está cerrado o en proceso.'
        ], 403);
    }

    // 2. Validamos usando 'genre_id' (que es lo que manda Android)
    $validated = $request->validate([
        'titulo'      => 'required|string',
        'genre_id'    => 'required|exists:genres,id', 
        'fecha'       => 'required|string',
        'duracion'    => 'required|string',
        'ubicacion'   => 'required|string',
        'descripcion' => 'nullable|string',
        'presupuesto' => 'required|numeric',
        'email'       => 'nullable|email',  // 👇 Nuevo
        'telefono'    => 'nullable|string', // 👇 Nuevo
    ]);

    // 3. Actualizamos la base de datos mapeando genre_id a tipo_musica
    $evento->update([
        'titulo'      => $validated['titulo'],
        'tipo_musica' => $validated['genre_id'], // Aquí estaba el error
        'fecha'       => $validated['fecha'],
        'duracion'    => $validated['duracion'],
        'ubicacion'   => $validated['ubicacion'],
        'descripcion' => $validated['descripcion'],
        'presupuesto' => $validated['presupuesto'],
        'email'       => $validated['email'] ?? $evento->email,
        'telefono'    => $validated['telefono'] ?? $evento->telefono,
    ]);

    return response()->json([
        'message' => 'Evento actualizado con éxito',
        'evento'  => $evento->load('genre') // Cargamos la relación para que el móvil reciba el nombre
    ], 200);
}

    /**
     * Delete an event from the client app.
     */
    public function destroy(Request $request, $id)
    {
        $firebaseUid = $request->attributes->get('firebase_uid');

        // Find the event ensuring it belongs to this client
        $event = ClientEvent::where('id', $id)
            ->where('firebase_uid', $firebaseUid)
            ->firstOrFail();

        // Delete the event (cascade will handle applications if DB is set up that way, otherwise they get deleted/orphaned)
        $event->delete();

        return response()->json([
            'message' => 'Evento eliminado exitosamente',
        ]);
    }
}