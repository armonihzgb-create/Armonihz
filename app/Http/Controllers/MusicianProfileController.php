<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateMusicianProfileRequest;
use App\Http\Resources\MusicianProfileResource;
use App\Models\MusicianProfile;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use App\Traits\ApiResponseTrait;

class MusicianProfileController extends Controller
{
    use ApiResponseTrait;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = MusicianProfile::with(['user:id,name', 'genres'])
           // ->where('is_verified', true)
            ->withExists(['promotions as has_active_promotion' => function ($query) {
            $query->where('is_active', true)->where('valid_until', '>', now());
        }])
            ->orderByDesc('has_active_promotion');

        $query->when($request->filled('search'), function (Builder $q) use ($request) {
            $search = $request->input('search');
            $q->where(function ($query) use ($search) {
                    $query->where('stage_name', 'like', '%' . $search . '%')
                        ->orWhereHas('user', function ($uq) use ($search) {
                    $uq->where('name', 'like', '%' . $search . '%');
                }
                );
            }
            );
        });

        $query->when($request->filled('genre'), function (Builder $q) use ($request) {
            $q->whereHas('genres', function ($gq) use ($request) {
                    $gq->where('genres.id', $request->input('genre'));
                }
                );
            });

        $query->when($request->filled('location'), function (Builder $q) use ($request) {
            $q->where('location', 'like', '%' . $request->input('location') . '%');
        });

        $musicians = $query->paginate(10);

        return $this->successResponse(
            MusicianProfileResource::collection($musicians)->response()->getData(true),
            'Musicians retrieved successfully'
        );
    }

    /**
     * Display the specified resource.
     */
   public function show(Request $request, string $id)
    {
        $profile = MusicianProfile::with(['user:id,name', 'genres', 'media', 'promotions' => function ($query) {
            $query->where('is_active', true);
        }])->findOrFail($id);

        $isFavorite = false;

        // Extraemos el token manualmente por si esta ruta es pública y no usa el middleware
        $authHeader = $request->header('Authorization');
        
        if ($authHeader && str_starts_with($authHeader, 'Bearer ')) {
            try {
                $idToken = substr($authHeader, 7);
                // Llamamos a tu servicio de Firebase
                $firebaseService = app(\App\Services\FirebaseService::class);
                $decodedToken = $firebaseService->verifyIdToken($idToken);
                $uid = $decodedToken->claims()->get('sub');

                // Buscamos al cliente
                $client = \App\Models\Client::where('firebase_uid', $uid)->first();
                
                // Si existe, verificamos si este músico es su favorito
                if ($client) {
                    $isFavorite = $client->favoriteMusicians()
                                         ->where('musician_profile_id', $id)
                                         ->exists();
                }
            } catch (\Throwable $e) {
                // Si el token es inválido o no hay sesión, simplemente se queda en false
            }
        }

        // Le inyectamos el atributo al modelo
        $profile->setAttribute('is_favorite', $isFavorite);

        return $this->successResponse(new MusicianProfileResource($profile), 'Musician profile retrieved successfully');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateMusicianProfileRequest $request, string $id)
    {
        $user = $request->user();

        if ($user->role !== 'musico') {
            return $this->errorResponse('Forbidden: Only musicians can update profiles.', null, 403);
        }

        $profile = MusicianProfile::findOrFail($id);

        if ($user->id !== $profile->user_id) {
            return $this->errorResponse('Forbidden: You do not own this profile.', null, 403);
        }

        $profile->update($request->validated());

        return $this->successResponse(
            new MusicianProfileResource($profile->load(['user:id,name', 'genres'])),
            'Profile updated successfully'
        );
    }

   public function getAvailability($id)
    {
        $profile = \App\Models\MusicianProfile::findOrFail($id);
        $busyDates = [];

        // 1. Bloqueos manuales del músico
        $manualEvents = $profile->calendarEvents()->get();
        foreach ($manualEvents as $ev) {
            $busyDates[] = [
                'start' => $ev->start->format('Y-m-d H:i:s'),
                'end' => $ev->end->format('Y-m-d H:i:s'),
            ];
        }

        // 2. Contrataciones ya aceptadas
        $hiringRequests = $profile->hiringRequests()->where('status', 'accepted')->get();
        foreach ($hiringRequests as $hr) {
            $busyDates[] = [
                'start' => $hr->event_date->format('Y-m-d H:i:s'),
                // Asumimos 3 horas de evento por defecto si no hay campo de duración
                'end' => $hr->end_time ? \Carbon\Carbon::parse($hr->end_time)->format('Y-m-d H:i:s') : $hr->event_date->copy()->addHours(3)->format('Y-m-d H:i:s'), 
            ];
        }

        // 🔥 NUEVO: 3. Castings Aceptados 🔥
        $castingApps = $profile->castingApplications()->where('status', 'accepted')->with('event')->get();
        foreach ($castingApps as $app) {
            if ($app->event && $app->event->fecha) {
                try {
                    $fechaString = trim($app->event->fecha);
                    try {
                        $start = \Carbon\Carbon::createFromFormat('d/m/Y', $fechaString);
                    } catch (\Exception $e) {
                        $start = \Carbon\Carbon::parse($fechaString);
                    }
                    $end = clone $start;

                    $duracionString = trim($app->event->duracion);
                    if (str_contains($duracionString, ' a ')) {
                        $parts = explode(' a ', $duracionString);
                        $start->setTimeFromTimeString(trim($parts[0]));
                        $end->setTimeFromTimeString(trim($parts[1]));
                        if ($end->lessThan($start)) {
                            $end->addDay();
                        }
                    } else {
                        $start->startOfDay();
                        $duration = (int) $duracionString ?: 3;
                        $end->startOfDay()->addHours($duration);
                    }

                    // Lo agregamos a la lista de ocupados para la app móvil
                    $busyDates[] = [
                        'start' => $start->format('Y-m-d H:i:s'),
                        'end' => $end->format('Y-m-d H:i:s'),
                    ];
                } catch (\Exception $e) {
                    \Log::error("Error en getAvailability parseando casting: " . $e->getMessage());
                }
            }
        }

        return response()->json([
            'success' => true,
            'data' => $busyDates
        ]);
    }
}
