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

        // Buscamos estrictamente en la tabla CLIENTS
        $client = Client::where('firebase_uid', $firebaseUid)->first();
        
        if (!$client) {
            return response()->json(['success' => false, 'message' => 'Cliente no encontrado en la app móvil'], 404);
        }

        $request->validate([
            'musician_profile_id' => 'required|exists:musician_profiles,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
            'hiring_request_id' => 'nullable|exists:hiring_requests,id',
            'casting_application_id' => 'nullable|exists:casting_applications,id',
        ]);

        if (!$request->hiring_request_id && !$request->casting_application_id) {
            return response()->json(['success' => false, 'message' => 'Debe especificar el evento.'], 400);
        }

        if ($request->filled('hiring_request_id')) {
            $hiring = HiringRequest::find($request->hiring_request_id);
            if (!$hiring) {
                return response()->json(['success' => false, 'message' => 'Solicitud no encontrada.'], 404);
            }
            if ($hiring->status !== 'completed') {
                return response()->json(['success' => false, 'message' => 'Solo se pueden reseñar eventos completados.'], 400);
            }
            
            // Validamos contra el ID de la tabla CLIENTS
            if ($hiring->client_id !== $client->id) {
                return response()->json(['success' => false, 'message' => 'No tienes permiso para reseñar este evento.'], 403);
            }
            
            if (Review::where('hiring_request_id', $hiring->id)->exists()) {
                return response()->json(['success' => false, 'message' => 'Ya has dejado una reseña.'], 400);
            }
        }

        // Guardamos la reseña con el ID de la tabla CLIENTS
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
        // Relación directa al cliente móvil
        $reviews = Review::with('client') 
            ->where('musician_profile_id', $id)
            ->orderBy('created_at', 'desc')
            ->get();
        
        $formattedReviews = $reviews->map(function ($review) {
            $clientProfile = null;
            
            if ($review->client) {
                $perfil = $review->client;
                
                // --- LÓGICA PARA ARMAR LA URL DE LA FOTO ---
                $photoUrl = null;
                
                if (!empty($perfil->fotoPerfil)) {
                    // 1. Si subió una foto personalizada
                    if (str_starts_with($perfil->fotoPerfil, 'http')) {
                        $photoUrl = $perfil->fotoPerfil;
                    } else {
                        $photoUrl = url('file/' . $perfil->fotoPerfil);
                    }
                } elseif (!empty($perfil->google_picture)) {
                    // 2. 🔥 PLAN B: Si no tiene foto personalizada, usamos la de Google
                    $photoUrl = $perfil->google_picture;
                }

                $clientProfile = [
                    'id' => $perfil->id,
                    'nombre' => $perfil->nombre,
                    'apellido' => $perfil->apellido,
                    'photoUrl' => $photoUrl 
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
        
        // Buscamos estrictamente en la tabla CLIENTS
        $cliente = Client::where('firebase_uid', $firebaseUid)->first();

        if (!$cliente) {
            return response()->json(['success' => false, 'message' => 'Cliente no encontrado'], 404);
        }

        // Obtenemos el historial usando el ID del CLIENTE
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