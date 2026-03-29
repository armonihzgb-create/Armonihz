<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Review;
use App\Models\Client;
use App\Models\HiringRequest;
use App\Models\CastingApplication;

class ReviewController extends Controller
{
    public function store(Request $request)
    {
        $firebaseUid = $request->attributes->get('firebase_uid');
        
        if (!$firebaseUid) {
            return response()->json(['success' => false, 'message' => 'No autorizado'], 401);
        }

        // 1. CORRECCIÓN: Buscar directamente en la tabla Client (como en toda tu App Móvil)
        $client = Client::where('firebase_uid', $firebaseUid)->first();
        
        if (!$client) {
            return response()->json(['success' => false, 'message' => 'Usuario no encontrado'], 404);
        }

        $request->validate([
            'musician_profile_id' => 'required|exists:musician_profiles,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
            'hiring_request_id' => 'nullable|exists:hiring_requests,id',
            'casting_application_id' => 'nullable|exists:casting_applications,id',
        ]);

        if (!$request->hiring_request_id && !$request->casting_application_id) {
            return response()->json(['success' => false, 'message' => 'Debe especificar el evento (hiring_request_id o casting_application_id).'], 400);
        }

        if ($request->filled('hiring_request_id')) {
            $hiring = HiringRequest::find($request->hiring_request_id);
            if (!$hiring) {
                return response()->json(['success' => false, 'message' => 'Solicitud de contratación no encontrada.'], 404);
            }
            if ($hiring->status !== 'completed') {
                return response()->json(['success' => false, 'message' => 'Solo se pueden reseñar eventos completados.'], 400);
            }
            // 2. CORRECCIÓN: Validar contra el ID del cliente
            if ($hiring->client_id !== $client->id) {
                return response()->json(['success' => false, 'message' => 'No autorizado.'], 403);
            }
            if (Review::where('hiring_request_id', $hiring->id)->exists()) {
                return response()->json(['success' => false, 'message' => 'Ya has dejado una reseña.'], 400);
            }
        }

        if ($request->filled('casting_application_id')) {
            $application = CastingApplication::with('event.client')->find($request->casting_application_id);
            if ($application->status !== 'completed') {
                return response()->json(['success' => false, 'message' => 'Solo se pueden reseñar eventos completados.'], 400);
            }
            // Asumiendo que el evento del casting tiene la relación con el cliente móvil
            if ($application->event->client_id !== $client->id) {
                return response()->json(['success' => false, 'message' => 'No autorizado.'], 403);
            }
            if (Review::where('casting_application_id', $application->id)->exists()) {
                return response()->json(['success' => false, 'message' => 'Ya has dejado una reseña.'], 400);
            }
        }

        // 3. CORRECCIÓN: Guardar usando el ID de la tabla clients
        $review = Review::create([
            'client_id' => $client->id, 
            'musician_profile_id' => $request->musician_profile_id,
            'hiring_request_id' => $request->hiring_request_id,
            'casting_application_id' => $request->casting_application_id,
            'rating' => $request->rating,
            'comment' => $request->comment,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Reseña enviada exitosamente.',
            'review' => $review
        ], 201);
    }

    public function musicianReviews($id)
    {
        // 4. CORRECCIÓN: Como ahora las reseñas pertenecen a la tabla clients, 
        // la relación en el modelo Review ya no debería ser con 'User', 
        // pero usaremos with('client') asumiendo que tu modelo Review apunta al Client
        $reviews = Review::with('client') 
            ->where('musician_profile_id', $id)
            ->orderBy('created_at', 'desc')
            ->get();
        
        $formattedReviews = $reviews->map(function ($review) {
            $clientProfile = null;
            
            // Leemos directamente del cliente asociado a la reseña
            if ($review->client) {
                $perfil = $review->client;
                $clientProfile = [
                    'id' => $perfil->id,
                    'nombre' => $perfil->nombre,
                    'apellido' => $perfil->apellido,
                    'profile_picture' => $perfil->photo_url ?? $perfil->photoUrl ?? null 
                ];
            }

            return [
                'id' => $review->id,
                'rating' => $review->rating,
                'comment' => $review->comment,
                'response' => $review->response,
                'created_at' => $review->created_at,
                'client' => $clientProfile 
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $formattedReviews
        ], 200);
    }

    public function myReviews(Request $request)
    {
        $firebaseUid = $request->attributes->get('firebase_uid');
        
        // Buscamos al cliente móvil
        $cliente = Client::where('firebase_uid', $firebaseUid)->first();

        if (!$cliente) {
            return response()->json(['success' => false, 'message' => 'Cliente no encontrado'], 404);
        }

        // Buscamos las reseñas creadas usando el ID del CLIENTE
        $reviews = Review::with('musicianProfile')
            ->where('client_id', $cliente->id)
            ->orderBy('created_at', 'desc')
            ->get();

        $formattedReviews = $reviews->map(function ($review) {
            return [
                'id' => $review->id,
                'rating' => $review->rating,
                'comment' => $review->comment,
                'response' => $review->response,
                'created_at' => $review->created_at,
                'musician' => [
                    'id' => $review->musicianProfile->id,
                    'stage_name' => $review->musicianProfile->stage_name,
                    'profile_picture' => $review->musicianProfile->profile_picture,
                ]
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $formattedReviews
        ], 200);
    }
}