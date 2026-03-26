<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreHiringRequestRequest;
use App\Http\Requests\UpdateHiringRequestStatusRequest;
use App\Http\Resources\HiringRequestResource;
use App\Models\HiringRequest;
use Illuminate\Http\Request;
use App\Traits\ApiResponseTrait;
use App\Notifications\HiringRequestCreatedNotification;
use App\Notifications\HiringRequestStatusUpdatedNotification;

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
        $user = $request->user();

        // 1. Guardar en la base de datos
        $hiringRequest = HiringRequest::create(array_merge(
            $request->validated(),
        [
            'client_id' => $user->id,
            'status' => 'pending'
        ]
        ));

        // 2. Cargar relaciones
        $hiringRequest->load(['client', 'musicianProfile', 'musicianProfile.user']);

        // 3. Intentar enviar la notificación de forma segura
        try {
            if ($hiringRequest->musicianProfile && $hiringRequest->musicianProfile->user) {
                $hiringRequest->musicianProfile->user->notify(new HiringRequestCreatedNotification($hiringRequest));
            }
        } catch (\Exception $e) {
            // Si falla el correo/notificación, lo registramos en los logs de Laravel pero NO detenemos la app
            \Illuminate\Support\Facades\Log::error('Error enviando notificación de contratación: ' . $e->getMessage());
        }

        // 4. Devolver la respuesta exitosa a la app móvil
        return $this->successResponse(
            new HiringRequestResource($hiringRequest),
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

        if ($hiringRequest->client) {
            $hiringRequest->client->notify(new HiringRequestStatusUpdatedNotification($hiringRequest));
        }

        return $this->successResponse(new HiringRequestResource($hiringRequest), 'Status updated successfully');
    }
}
