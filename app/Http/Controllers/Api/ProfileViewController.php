<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MusicianProfile;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;

/**
 * POST /api/v1/musicians/{id}/view
 *
 * Called by the Android app whenever a cliente opens a musician's profile.
 * Requires Sanctum token so only authenticated users can trigger views.
 * A musician viewing their OWN profile does NOT count.
 */
class ProfileViewController extends Controller
{
    use ApiResponseTrait;

    public function record(Request $request, int $id)
    {
        $profile = MusicianProfile::findOrFail($id);

        // Prevent self-views: do not count if the viewer is the musician owner
        if ($request->user()->id === $profile->user_id) {
            return $this->successResponse(
            ['profile_views' => $profile->profile_views],
                'Self-view not counted',
                200
            );
        }

        // Atomic increment to avoid race conditions
        $profile->increment('profile_views');

        return $this->successResponse(
        ['profile_views' => $profile->fresh()->profile_views],
            'View recorded',
            200
        );
    }
}
