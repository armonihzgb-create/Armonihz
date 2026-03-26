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
        // 1. Obtenemos el cliente usando el UID de Firebase (igual que en el método store)
        $firebaseUid = $request->attributes->get('firebase_uid');
        $cliente = \App\Models\Client::where('firebase_uid', $firebaseUid)->first();

        if (!$cliente) {
            return response()->json(['data' => []], 200); // Si no hay cliente, devolvemos lista vacía
        }

        // 2. Buscamos todas sus solicitudes y traemos también los datos del músico
        $requests = HiringRequest::with('musicianProfile')
            ->where('client_id', $cliente->id)
            ->orderBy('created_at', 'desc')
            ->get();

        // 3. Devolvemos la respuesta en formato JSON
        return response()->json(['data' => $requests], 200);
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