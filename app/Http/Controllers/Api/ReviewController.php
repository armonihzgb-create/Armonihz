<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Review;
use App\Models\Client;
use App\Models\User;
use App\Models\HiringRequest;
use App\Models\CastingApplication;

class ReviewController extends Controller
{
    public function store(Request $request)
    {
        $firebaseUid = $request->attributes->get('firebase_uid');
        if (!$firebaseUid) {
            return response()->json(['message' => 'No autorizado'], 401);
        }

        $user = User::where('firebase_uid', $firebaseUid)->first();
        if (!$user) {
            return response()->json(['message' => 'Usuario no encontrado'], 404);
        }

        $request->validate([
            'musician_profile_id' => 'required|exists:musician_profiles,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
            'hiring_request_id' => 'nullable|exists:hiring_requests,id',
            'casting_application_id' => 'nullable|exists:casting_applications,id',
        ]);

        if (!$request->hiring_request_id && !$request->casting_application_id) {
            return response()->json(['message' => 'Debe especificar el evento (hiring_request_id o casting_application_id).'], 400);
        }

        if ($request->filled('hiring_request_id')) {
            $hiring = HiringRequest::find($request->hiring_request_id);
            if (!$hiring) {
                return response()->json(['message' => 'Solicitud de contratación no encontrada.'], 404);
            }
            if ($hiring->status !== 'completed') {
                return response()->json(['message' => 'Solo se pueden reseñar eventos completados.'], 400);
            }
            $client = Client::where('firebase_uid', $firebaseUid)->first();
            if (!$client || $hiring->client_id !== $client->id) {
                return response()->json(['message' => 'No autorizado.'], 403);
            }
            if (Review::where('hiring_request_id', $hiring->id)->exists()) {
                return response()->json(['message' => 'Ya has dejado una reseña.'], 400);
            }
        }

        if ($request->filled('casting_application_id')) {
            $application = CastingApplication::with('event.client')->find($request->casting_application_id);
            if ($application->status !== 'completed') {
                return response()->json(['message' => 'Solo se pueden reseñar eventos completados.'], 400);
            }
            $client = Client::where('firebase_uid', $firebaseUid)->first();
            if (!$client || $application->event->firebase_uid !== $client->firebase_uid) {
                return response()->json(['message' => 'No autorizado.'], 403);
            }
            if (Review::where('casting_application_id', $application->id)->exists()) {
                return response()->json(['message' => 'Ya has dejado una reseña.'], 400);
            }
        }

        $review = Review::create([
            'client_id' => $user->id,
            'musician_profile_id' => $request->musician_profile_id,
            'hiring_request_id' => $request->hiring_request_id,
            'casting_application_id' => $request->casting_application_id,
            'rating' => $request->rating,
            'comment' => $request->comment,
        ]);

        return response()->json([
            'message' => 'Reseña enviada exitosamente.',
            'review' => $review
        ], 201);
    }

    public function musicianReviews($id)
    {
        // 1. Traemos las reseñas con la relación anidada (user -> client)
        $reviews = Review::with('client.client') 
            ->where('musician_profile_id', $id)
            ->orderBy('created_at', 'desc')
            ->get();
        
        // 2. Formateamos los datos para ocultar emails/contraseñas y darle a Android la estructura plana que espera
        $formattedReviews = $reviews->map(function ($review) {
            $clientProfile = null;
            
            // Verificamos que la relación del cliente exista
            if ($review->client && $review->client->client) {
                $perfil = $review->client->client;
                $clientProfile = [
                    'id' => $perfil->id,
                    'nombre' => $perfil->nombre,
                    'apellido' => $perfil->apellido,
                    // OJO: Ajusta 'photo_url' según cómo se llame la columna de la foto en tu tabla clients
                    'profile_picture' => $perfil->photo_url ?? $perfil->photoUrl ?? null 
                ];
            }

            return [
                'id' => $review->id,
                'rating' => $review->rating,
                'comment' => $review->comment,
                'response' => $review->response,
                'created_at' => $review->created_at,
                'client' => $clientProfile // Entregamos el objeto ya simplificado y seguro
            ];
        });

        // 3. Devolvemos el envoltorio (wrapper) exacto que espera Kotlin (success y data)
        return response()->json([
            'success' => true,
            'data' => $formattedReviews
        ], 200);
    }
}
