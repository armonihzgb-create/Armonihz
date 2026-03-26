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
        $user = $request->user();

        $query = HiringRequest::with(['client', 'musicianProfile']);

        if ($user->role === 'cliente') {
            $query->where('client_id', $user->id);
        }
        elseif ($user->role === 'musico') {
            $query->whereHas('musicianProfile', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            });
        }

        $requests = $query->paginate(10);
        return $this->successResponse(
            HiringRequestResource::collection($requests)->response()->getData(true),
            'Hiring requests retrieved successfully'
        );
    }

    /**
     * Store a newly created resource in storage.
     */
 public function store(StoreHiringRequestRequest $request)
    {
        // 1. Recuperamos el UID de Firebase que tu middleware inyectó
        $firebaseUid = $request->attributes->get('firebase_uid');

        // 2. Buscamos al cliente. 
        // Nota: Si tus contrataciones (client_id) apuntan a la tabla "users" en lugar de "clients", 
        // cambia "Client" por "User" en la siguiente línea.
        $cliente = \App\Models\Client::where('firebase_uid', $firebaseUid)->first();

        if (!$cliente) {
            return response()->json(['success' => false, 'message' => 'Cliente no encontrado'], 404);
        }

        // 3. Guardar en la base de datos
        $hiringRequest = HiringRequest::create(array_merge(
            $request->validated(),
        [
            'client_id' => $cliente->id, // Usamos el ID del cliente de tu BD
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
}