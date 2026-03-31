<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreHiringRequestRequest;
use App\Http\Requests\UpdateHiringRequestStatusRequest;
use App\Http\Resources\HiringRequestResource;
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

        // 1. SOLICITUDES DIRECTAS (Hiring Requests)
        $hiringRequests = \App\Models\HiringRequest::with('musicianProfile')
            ->withExists('review as has_review')
            ->where('client_id', $cliente->id)
            ->get()
            ->map(function ($req) {
                return [
                    'id' => $req->id,
                    'type' => 'hiring', // 👈 Identificador
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

        // 2. CASTINGS ACEPTADOS / FINALIZADOS
        $castingApps = \App\Models\CastingApplication::whereHas('event', function($q) use ($firebaseUid) {
                $q->where('firebase_uid', $firebaseUid);
            })
            ->whereIn('status', ['accepted', 'completed']) // Solo los aprobados o ya terminados
            ->with(['musician', 'event'])
            ->withExists('review as has_review')
            ->get()
            ->map(function ($app) {
                // Formateamos las fechas para que Android no falle
                $start = null;
                $end = null;
                if ($app->event && $app->event->fecha) {
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
                }

                return [
                    'id' => $app->id,
                    'type' => 'casting', // 👈 Identificador
                    'event_date' => $start ? $start->format('Y-m-d H:i:s') : null,
                    'end_time' => $end ? $end->format('Y-m-d H:i:s') : null,
                    'event_location' => $app->event->ubicacion ?? 'No especificada',
                    // Le ponemos un emoji de micrófono para distinguirlo
                    'description' => "🎤 Casting: " . ($app->event->titulo ?? '') . "\n" . ($app->event->descripcion ?? ''),
                    'budget' => $app->proposed_price ?? ($app->event->presupuesto ?? 0),
                    'status' => $app->status,
                    'musician_message' => $app->message,
                    'counter_offer' => null,
                    'musician_profile' => $app->musician,
                    'has_review' => $app->has_review,
                    'created_at' => $app->created_at,
                ];
            });

        // 3. UNIR AMBAS LISTAS Y ORDENAR
        $merged = $hiringRequests->concat($castingApps)->sortByDesc('created_at')->values();

        return response()->json(['data' => $merged], 200);
    }
    /**
     * Store a newly created resource in storage.
     */
 public function store(StoreHiringRequestRequest $request)
    {
        // 1. Recuperamos el UID de Firebase que inyectó el middleware
        $firebaseUid = $request->attributes->get('firebase_uid');

        // 2. Buscamos al cliente en el modelo correcto (Client, no User)
        $cliente = \App\Models\Client::where('firebase_uid', $firebaseUid)->first();

        // Verificamos que el cliente realmente exista en la BD antes de continuar
        if (!$cliente) {
            return response()->json(['success' => false, 'message' => 'Cliente no encontrado. UID: ' . $firebaseUid], 404);
        }

        // 3. Guardar en la base de datos usando el ID correcto
        $hiringRequest = HiringRequest::create(array_merge(
            $request->validated(),
        [
            'client_id' => $cliente->id, 
            'status' => 'pending'
        ]
        ));

        // 4. Cargar relaciones
        $hiringRequest->load(['client', 'musicianProfile']);

        return $this->successResponse(
            new \App\Http\Resources\HiringRequestResource($hiringRequest),
            'Hiring request created successfully',
            201
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, string $id)
    {
        $hiringRequest = HiringRequest::with(['client', 'musicianProfile'])->findOrFail($id);
        $user = $request->user();

        $isClient = $user->role === 'cliente' && $hiringRequest->client_id === $user->id;
        $isMusician = $user->role === 'musico' && $hiringRequest->musicianProfile->user_id === $user->id;

        if (!$isClient && !$isMusician) {
            return $this->errorResponse('Forbidden: You are not authorized to view this request.', null, 403);
        }

        return $this->successResponse(new HiringRequestResource($hiringRequest), 'Hiring request retrieved successfully');
    }

    /**
     * Update the hiring request status.
     */
    public function updateStatus(UpdateHiringRequestStatusRequest $request, string $id)
    {
        $hiringRequest = HiringRequest::with(['client', 'musicianProfile'])->findOrFail($id);
        $user = $request->user();

        // Ensure only the assigned musician can update this request
        if ($hiringRequest->musicianProfile->user_id !== $user->id) {
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
        $firebaseUid = $request->attributes->get('firebase_uid');
        $cliente = \App\Models\Client::where('firebase_uid', $firebaseUid)->first();

        // Buscamos la solicitud y nos aseguramos de que sea de este cliente
        $hiringRequest = HiringRequest::where('id', $id)
            ->where('client_id', $cliente->id)
            ->firstOrFail();

        $request->validate([
            'status' => 'required|in:accepted,rejected'
        ]);

        $hiringRequest->status = $request->status;
        
        // Si acepta, el nuevo precio se convierte en el oficial
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