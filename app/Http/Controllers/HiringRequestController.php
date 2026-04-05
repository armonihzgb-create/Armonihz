<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreHiringRequestRequest;
use App\Http\Requests\UpdateHiringRequestStatusRequest;
use App\Http\Resources\HiringRequestResource;
use App\Models\ClientEvent;
use App\Models\HiringRequest;
use Illuminate\Http\Request;
use App\Traits\ApiResponseTrait;

class HiringRequestController extends Controller
{
    use ApiResponseTrait;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $firebaseUid = $request->attributes->get('firebase_uid');
        $cliente = \App\Models\Client::where('firebase_uid', $firebaseUid)->first();

        if (!$cliente) {
            return response()->json(['data' => []], 200); 
        }

        // 1. SOLICITUDES DIRECTAS (Hiring Requests) - Límite: Las 50 más recientes
        $hiringRequests = \App\Models\HiringRequest::with('musicianProfile')
            ->withExists('review as has_review')
            ->where('client_id', $cliente->id)
            ->orderBy('created_at', 'desc') // 🔥 ORDENAMOS EN BASE DE DATOS PRIMERO
            ->take(50)                      // 🔥 LÍMITE DE SEGURIDAD (50 registros)
            ->get()
            ->map(function ($req) {
                return [
                    'id' => $req->id,
                    'type' => 'hiring', 
                    'event_date' => $req->event_date,
                    'end_time' => $req->end_time,
                    'event_location' => $req->event_location,
                    'description' => $req->description,
                    'budget' => $req->budget,
                    'status' => $req->status,
                    'musician_message' => $req->musician_message,
                    'counter_offer' => $req->counter_offer,
                    'musician_profile' => $req->musicianProfile,
                    'has_review' => $req->has_review,
                    'created_at' => $req->created_at,
                ];
            });

        // 2. CASTINGS ACEPTADOS / FINALIZADOS - Límite: Los 50 más recientes
        $castingApps = \App\Models\CastingApplication::whereHas('event', function($q) use ($firebaseUid) {
                $q->where('firebase_uid', $firebaseUid);
            })
            ->whereIn('status', ['accepted', 'completed'])
            ->with(['musician', 'event'])
            ->withExists('review as has_review')
            ->orderBy('created_at', 'desc') // 🔥 ORDENAMOS EN BASE DE DATOS PRIMERO
            ->take(50)                      // 🔥 LÍMITE DE SEGURIDAD (50 registros)
            ->get()
            ->map(function ($app) {
                $start = null;
                $end   = null;

                if ($app->event && $app->event->fecha) {
                    try {
                        [$start, $end] = ClientEvent::parseDateTimeRange(
                            $app->event->fecha,
                            $app->event->duracion
                        );
                    } catch (\Exception $e) {
                        \Log::warning("Could not parse casting date for app {$app->id}: " . $e->getMessage());
                    }
                }

                return [
                    'id'               => $app->id,
                    'type'             => 'casting',
                    'event_date'       => $start ? $start->format('Y-m-d H:i:s') : null,
                    'end_time'         => $end   ? $end->format('Y-m-d H:i:s')   : null,
                    'event_location'   => $app->event->ubicacion ?? 'No especificada',
                    'description'      => "🎤 Casting: " . ($app->event->titulo ?? '') . "\n" . ($app->event->descripcion ?? ''),
                    'budget'           => $app->proposed_price ?? ($app->event->presupuesto ?? 0),
                    'status'           => $app->status,
                    'musician_message' => $app->message,
                    'counter_offer'    => null,
                    'musician_profile' => $app->musician,
                    'has_review'       => $app->has_review,
                    'created_at'       => $app->created_at,
                ];
            });

        // 3. UNIR AMBAS LISTAS Y ORDENAR
        // Volvemos a ordenar la colección fusionada para asegurarnos de que queden
        // perfectamente intercaladas según su fecha de creación.
        $merged = $hiringRequests->concat($castingApps)->sortByDesc('created_at')->values();

        return response()->json(['data' => $merged], 200);
    }
    /**
     * Store a newly created resource in storage.
     */
public function store(StoreHiringRequestRequest $request)
    {
        $firebaseUid = $request->attributes->get('firebase_uid');
        $cliente = \App\Models\Client::where('firebase_uid', $firebaseUid)->first();

        if (!$cliente) {
            return response()->json(['success' => false, 'message' => 'Cliente no encontrado.'], 404);
        }

        // Se crea la solicitud en la base de datos
        $hiringRequest = HiringRequest::create(array_merge(
            $request->validated(),
            [
                'client_id' => $cliente->id, 
                'status' => 'pending'
            ]
        ));

        // Cargamos las relaciones (cliente, perfil del músico, y el usuario del músico para sacar su email)
        $hiringRequest->load(['client', 'musicianProfile.user']);

        // 🔥 LA MAGIA AQUÍ: Enviar el correo al músico
        try {
            $musicianEmail = $hiringRequest->musicianProfile->user->email;
            $musicianName = $hiringRequest->musicianProfile->stage_name;
            $clientName = $cliente->nombre . ' ' . $cliente->apellido;

            \Illuminate\Support\Facades\Mail::to($musicianEmail)
                ->send(new \App\Mail\NewHiringRequestEmail($musicianName, $clientName, $hiringRequest->event_date));
        } catch (\Exception $e) {
            \Log::error('No se pudo enviar el correo al músico: ' . $e->getMessage());
            // Si el correo falla por alguna razón de red, no rompemos el proceso, la solicitud se guarda igual
        }

        return $this->successResponse(
            new \App\Http\Resources\HiringRequestResource($hiringRequest),
            'Hiring request created successfully',
            201
        );
    }

    /**
     * Display the specified resource.
     *
     * The route is protected by firebase.auth, so $request->user() returns
     * the Client model resolved by FirebaseGuard.
     */
    public function show(Request $request, string $id)
    {
        $hiringRequest = HiringRequest::with(['client', 'musicianProfile'])->findOrFail($id);

        // The authenticated identity on firebase.auth routes is always a Client
        $client = $request->user(); // Client model via FirebaseGuard

        if (!$client || $hiringRequest->client_id !== $client->id) {
            return $this->errorResponse('Forbidden: You are not authorized to view this request.', null, 403);
        }

        return $this->successResponse(new HiringRequestResource($hiringRequest), 'Hiring request retrieved successfully');
    }

    /**
     * Update the hiring request status (musician side, Sanctum-protected).
     *
     * NOTE: This action is called by musicians via the web portal (Sanctum auth),
     * NOT by Firebase clients. The musician's User model is resolved by $request->user().
     */
    public function updateStatus(UpdateHiringRequestStatusRequest $request, string $id)
    {
        $hiringRequest = HiringRequest::with(['client', 'musicianProfile'])->findOrFail($id);
        $musicianUser  = $request->user(); // User model (Sanctum / web session)

        if (!$musicianUser || $hiringRequest->musicianProfile->user_id !== $musicianUser->id) {
            return $this->errorResponse('Forbidden: You do not own this request destination.', null, 403);
        }

        if ($hiringRequest->status !== 'pending') {
            return $this->errorResponse('Bad Request: Only pending requests can have their status changed.', null, 400);
        }

        $hiringRequest->update([
            'status' => $request->validated()['status']
        ]);

        return $this->successResponse(new HiringRequestResource($hiringRequest), 'Status updated successfully');
    }

    public function respondToCounterOffer(Request $request, $id)
    {
        // Resolved by FirebaseGuard via firebase.auth middleware
        $client = $request->user();

        if (!$client) {
            return response()->json(['success' => false, 'message' => 'No autenticado.'], 401);
        }

        // Scope the lookup to this client's requests only
        $hiringRequest = HiringRequest::where('id', $id)
            ->where('client_id', $client->id)
            ->firstOrFail();

        $request->validate([
            'status' => 'required|in:accepted,rejected'
        ]);

        $hiringRequest->status = $request->status;

        // If the client accepts, the counter offer becomes the official budget
        if ($request->status === 'accepted' && $hiringRequest->counter_offer) {
            $hiringRequest->budget = $hiringRequest->counter_offer;
        }

        $hiringRequest->save();

        return response()->json([
            'success' => true,
            'message' => 'Respuesta guardada correctamente'
        ]);
    }
}