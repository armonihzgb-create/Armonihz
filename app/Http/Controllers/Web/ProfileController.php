<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\ChangePasswordRequest;
use App\Http\Requests\UpdateMusicianProfileRequest;
use App\Models\Genre;
use App\Models\GroupType;
use App\Models\EventType;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    /**
     * Show the public-facing profile of a musician.
     */
    public function showPublic($id)
    {
        $user = User::findOrFail($id);
        $profile = $user->musicianProfile()->with(['genres', 'groupTypes', 'eventTypes'])->firstOrFail();
        $media = $profile->media()->orderBy('is_featured', 'desc')->orderBy('created_at', 'desc')->get();

        return view('profile-public', compact('user', 'profile', 'media'));
    }

    /**
     * Show the musician's own profile with edit form data.
     */
    public function edit(Request $request)
    {
        $user = $request->user();
        $profile = $user->musicianProfile()->with(['genres', 'groupTypes', 'eventTypes'])->first();
        $genres = Genre::orderBy('name')->get();
        $groupTypes = GroupType::orderBy('id')->get();
        $eventTypes = EventType::orderBy('id')->get();
        $completion = $this->calcCompletion($profile);

        // --- Stats ---
        $acceptedRequests = 0;
        if ($profile) {
            $acceptedRequests = $profile->hiringRequests()->where('status', 'accepted')->count();
        }

        // --- Media ---
        $media = [];
        if ($profile) {
            $media = $profile->media()->orderBy('created_at', 'desc')->get();
        }

        return view('profile', compact('user', 'profile', 'genres', 'groupTypes', 'eventTypes', 'completion', 'acceptedRequests', 'media'));
    }

    /**
     * Save profile changes (text fields + photo + genres).
     */
    public function update(UpdateMusicianProfileRequest $request)
    {
        $user    = $request->user();
        $profile = $user->musicianProfile;

        // ── Photo upload ──────────────────────────────────────────────────
        $picturePath = $profile->profile_picture;

        if ($request->hasFile('profile_picture')) {
            if ($picturePath && Storage::disk('public')->exists($picturePath)) {
                Storage::disk('public')->delete($picturePath);
            }
            $picturePath = $request->file('profile_picture')
                ->store('profiles', 'public');
        }

        // ── Text fields ─────────────────────────────────────────────────
        $profile->update([
            'stage_name'     => $request->stage_name,
            'bio'            => $request->bio,
            'location'       => $request->location,
            'hourly_rate'    => $request->hourly_rate,
            'phone'          => $request->phone,
            'instagram'      => $request->instagram,
            'facebook'       => $request->facebook,
            'youtube'        => $request->youtube,
            'tiktok'         => $request->tiktok,
            'spotify'        => $request->spotify,
            'coverage_notes' => $request->coverage_notes,
            'profile_picture'=> $picturePath,
        ]);

        // ── Genres, Groups & Events (pivot sync) ──────────────────────────
        $profile->genres()->sync($request->input('genres', []));
        $profile->groupTypes()->sync($request->input('group_types', []));
        $profile->eventTypes()->sync($request->input('event_types', []));

        return redirect()->route('profile')
            ->with('success', '¡Perfil actualizado correctamente!');
    }

    public function multimedia(Request $request)
    {
        $user = $request->user();
        $profile = $user->musicianProfile;
        
        $media = [];
        if ($profile) {
            $media = $profile->media()->orderBy('created_at', 'desc')->get();
        }

        return view('multimedia', array_merge(compact('user', 'profile'), ['media' => $media]));
    }

    public function availability(Request $request)
    {
        $user = $request->user();
        return view('availability', compact('user'));
    }

    /**
     * Delete the user's account and profile permanently.
     */
    public function destroy(Request $request)
    {
        $user = clone $request->user();
        $profile = $user->musicianProfile;

        // Perform cleanup of local files if any
        if ($profile && $profile->profile_picture && \Illuminate\Support\Str::startsWith($profile->profile_picture, 'profiles/')) {
            Storage::disk('public')->delete($profile->profile_picture);
        }

        // Delete the user (this cascades to musician_profiles and hiring_requests based on DB constraints)
        // Note: For now we just delete the user.
        $user->delete();

        // Invalidate session
        \Illuminate\Support\Facades\Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('status', 'Tu cuenta ha sido eliminada exitosamente. Esperamos verte pronto.');
    }

    /**
     * Change user's password.
     *
     * Authorization (Google-only accounts) and all validation
     * are handled by ChangePasswordRequest.
     */
    public function changePassword(ChangePasswordRequest $request)
    {
        $request->user()->update([
            'password' => Hash::make($request->password),
        ]);

        return back()->with('success', 'Contraseña actualizada correctamente.');
    }

    // ── Private ───────────────────────────────────────────────────────────────

    private function calcCompletion($profile): int
    {
        if (!$profile)
            return 0;

        $fields = ['stage_name', 'bio', 'location', 'hourly_rate', 'profile_picture', 'phone'];
        $filled = collect($fields)->filter(fn($f) => !empty($profile->$f))->count();
        $hasGenres = $profile->genres()->exists();
        $total = count($fields) + 1; // +1 para géneros

        return (int)round((($filled + ($hasGenres ? 1 : 0)) / $total) * 100);
    }
}
