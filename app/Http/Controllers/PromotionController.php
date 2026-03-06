<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePromotionRequest;
use App\Http\Requests\UpdatePromotionRequest;
use App\Http\Resources\PromotionResource;
use App\Models\Promotion;
use Illuminate\Http\Request;
use App\Traits\ApiResponseTrait;

class PromotionController extends Controller
{
    use ApiResponseTrait;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = $request->user('sanctum');

        $query = Promotion::with('musicianProfile');

        if ($user && $user->role === 'musico') {
            $query->whereHas('musicianProfile', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            });
        }
        else {
            $query->where('is_active', true)
                ->where('valid_until', '>', now());
        }

        $promotions = $query->paginate(10);
        return $this->successResponse(
            PromotionResource::collection($promotions)->response()->getData(true),
            'Promotions retrieved successfully'
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePromotionRequest $request)
    {
        $user = $request->user();
        $profile = $user->musicianProfile;

        $promotion = Promotion::create([
            'musician_profile_id' => $profile->id,
            'title' => $request->input('title'),
            'description' => $request->input('description'),
            'valid_from' => now(), // Assume valid from today for MVP
            'valid_until' => $request->input('expires_at'),
            'is_active' => true,
        ]);

        $promotion->load('musicianProfile');

        return $this->successResponse(new PromotionResource($promotion), 'Promotion created successfully', 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $promotion = Promotion::with('musicianProfile')->findOrFail($id);
        return $this->successResponse(new PromotionResource($promotion), 'Promotion retrieved successfully');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePromotionRequest $request, string $id)
    {
        $user = $request->user();
        $promotion = Promotion::with('musicianProfile')->findOrFail($id);

        if ($promotion->musicianProfile->user_id !== $user->id) {
            return $this->errorResponse('Forbidden: You do not own this promotion.', null, 403);
        }

        $updates = $request->validated();

        // Expiration check logic
        if ($promotion->valid_until < now()) {
            $updates['is_active'] = false;
        }

        $promotion->update($updates);

        return $this->successResponse(new PromotionResource($promotion), 'Promotion updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, string $id)
    {
        $user = $request->user();
        $promotion = Promotion::with('musicianProfile')->findOrFail($id);

        if ($promotion->musicianProfile->user_id !== $user->id) {
            return $this->errorResponse('Forbidden: You do not own this promotion.', null, 403);
        }

        // Soft-delete behavior for promotions
        $promotion->update(['is_active' => false]);

        return $this->successResponse(null, 'Promotion deactivated successfully', 200);
    }
}
