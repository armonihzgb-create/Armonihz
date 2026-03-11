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
    public function show(string $id)
    {
        $profile = MusicianProfile::with(['user:id,name', 'genres', 'media', 'promotions' => function ($query) {
            $query->where('is_active', true);
        }])->findOrFail($id);

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
}
