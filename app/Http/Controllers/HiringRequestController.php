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
        try {
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
            $hiringRequest->load(['client', 'musicianProfile']);

            // 3. Devolver la respuesta exitosa
            return $this->successResponse(
                new HiringRequestResource($hiringRequest),
                'Hiring request created successfully',
                201
            );
            
        } catch (\Exception $e) {
            // 🔥 ESTO NOS DIRÁ EXACTAMENTE QUÉ ESTÁ FALLANDO 🔥
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ], 500);
        }
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