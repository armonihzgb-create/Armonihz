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
        // 1. Optimizamos relaciones: Solo traemos los datos mínimos del usuario y de los géneros
        // Only APPROVED musicians are visible in the mobile app.
        $query = MusicianProfile::approved()
            ->with(['user:id,name', 'genres:id,name'])
            ->withExists([
                'promotions as has_active_promotion' => function ($query) {
                    $query->where('is_active', true)->where('valid_until', '>', now());
                }
            ])
            ->withAvg('reviews', 'rating')
            ->orderByDesc('has_active_promotion')
            ->orderByDesc('id');

        $query->when($request->filled('search'), function (Builder $q) use ($request) {
            $search = $request->input('search');
            $q->where(
                function ($query) use ($search) {
                    $query->where('stage_name', 'like', '%' . $search . '%')
                        ->orWhereHas(
                            'user',
                            function ($uq) use ($search) {
                                $uq->where('name', 'like', '%' . $search . '%');
                            }
                        );
                }
            );
        });

        $query->when($request->filled('genre'), function (Builder $q) use ($request) {
            $q->whereHas(
                'genres',
                function ($gq) use ($request) {
                    $gq->where('genres.id', $request->input('genre'));
                }
            );
        });

        $query->when($request->filled('location'), function (Builder $q) use ($request) {
            $q->where('location', 'like', '%' . $request->input('location') . '%');
        });

        $page = $request->header('X-Page', 1);

        // 🔥 LA MAGIA DE LA OPTIMIZACIÓN: 
        // Le decimos a Laravel exactamente qué columnas sacar de la base de datos
        $columnas = [
            'id', 
            'user_id', 
            'stage_name', 
            'location', 
            'hourly_rate', 
            'rating_average',
            'profile_picture', 
            'is_verified',
            'tiktok',
            'spotify',
            'has_active_promotion'
        ];

        // Pasamos el arreglo de columnas como segundo parámetro al paginate
        $musicians = $query->paginate(10, $columnas, 'page', $page);

        return $this->successResponse(
            MusicianProfileResource::collection($musicians)->response()->getData(true),
            'Musicians retrieved successfully'
        );
    }

    /**
     * Display the specified resource.
     * Only approved profiles are publicly accessible.
     * The profile owner (authenticated via Sanctum) can always see their own profile.
     */
    public function show(Request $request, string $id)
    {
        $profile = MusicianProfile::with([
            'user:id,name',
            'genres',
            'media',
            'promotions' => function ($query) {
                $query->where('is_active', true);
            }
        ])->findOrFail($id);

        // Gate: block non-approved profiles unless the requester is the owner
        if ($profile->verification_status !== MusicianProfile::STATUS_APPROVED) {
            // Check if the requesting user is the profile owner (Sanctum auth)
            $sanctumUser = $request->user('sanctum');
            $isOwner = $sanctumUser && $sanctumUser->id === $profile->user_id;

            if (!$isOwner) {
                return $this->errorResponse(
                    'Este perfil no está disponible porque aún no ha sido verificado.',
                    null,
                    404
                );
            }
        }

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
                'start' => $ev->start->format('Y-m-d\TH:i:s'), // <-- Agregada la \T
                'end' => $ev->end->format('Y-m-d\TH:i:s'),   // <-- Agregada la \T
            ];
        }

        // 2. Contrataciones ya aceptadas o en estado de contraoferta
        $hiringRequests = $profile->hiringRequests()->whereIn('status', ['accepted', 'counter_offer'])->get();
        foreach ($hiringRequests as $hr) {
            $busyDates[] = [
                'start' => $hr->event_date->format('Y-m-d\TH:i:s'), // <-- Agregada la \T
                'end' => $hr->end_time ? \Carbon\Carbon::parse($hr->end_time)->format('Y-m-d\TH:i:s') : $hr->event_date->copy()->addHours(3)->format('Y-m-d\TH:i:s'),
            ];
        }

        // 3. Castings Aceptados (o completados)
        $castingApps = $profile->castingApplications()->whereIn('status', ['accepted', 'completed'])->with('event')->get();

        foreach ($castingApps as $app) {
            if ($app->event && $app->event->fecha) {
                try {
                    // Usamos la función global a prueba de balas
                    [$start, $end] = \App\Models\ClientEvent::parseDateTimeRange(
                        $app->event->fecha,
                        $app->event->duracion
                    );

                    $busyDates[] = [
                        'start' => $start->format('Y-m-d\TH:i:s'), // <-- Agregada la \T vital para Android
                        'end'   => $end->format('Y-m-d\TH:i:s'),   // <-- Agregada la \T
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

    /**
     * Registra una vista al perfil proveniente de la app móvil
     */
    public function recordMobileView($id)
    {
        $profile = \App\Models\MusicianProfile::findOrFail($id);
        
        // Incrementa el contador de vistas. 
        // Cambia 'mobile_views' por el nombre exacto de tu columna en la base de datos
        $profile->increment('profile_views'); 

        return response()->json([
            'success' => true,
            'message' => 'Vista registrada correctamente'
        ]);
    }
}