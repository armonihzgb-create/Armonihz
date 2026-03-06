<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Genre;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    /**
     * Show the public-facing profile of a musician.
     */
    public function showPublic($id)
    {
        $user = User::findOrFail($id);
        $profile = $user->musicianProfile()->with('genres')->firstOrFail();

        return view('profile-public', compact('user', 'profile'));
    }

    /**
     * Show the musician's own profile with edit form data.
     */
    public function edit(Request $request)
    {
        $user = $request->user();
        $profile = $user->musicianProfile()->with('genres')->first();
        $genres = Genre::orderBy('name')->get();
        $completion = $this->calcCompletion($profile);

        // --- Stats ---
        $acceptedRequests = 0;
        if ($profile) {
            $acceptedRequests = $profile->hiringRequests()->where('status', 'accepted')->count();
        }

        return view('profile', compact('user', 'profile', 'genres', 'completion', 'acceptedRequests'));
    }

    /**
     * Save profile changes (text fields + photo + genres).
     */
    public function update(Request $request)
    {
        $user = $request->user();
        $profile = $user->musicianProfile;

        $request->validate([
            'stage_name' => 'required|string|max:255',
            'bio' => 'nullable|string|max:2000',
            'location' => 'nullable|string|max:255',
            'hourly_rate' => 'nullable|numeric|min:0',
            'phone' => 'nullable|string|max:30',
            'instagram' => 'nullable|string|max:255',
            'facebook' => 'nullable|string|max:255',
            'youtube' => 'nullable|string|max:255',
            'coverage_notes' => 'nullable|string|max:500',
            'profile_picture' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:3072',
            'genres' => 'nullable|array',
            'genres.*' => 'integer|exists:genres,id',
        ]);

        // ── Photo upload ──────────────────────────────────────────────────────
        $picturePath = $profile->profile_picture;

        if ($request->hasFile('profile_picture')) {
            // Delete old picture if exists
            if ($picturePath && Storage::disk('public')->exists($picturePath)) {
                Storage::disk('public')->delete($picturePath);
            }
            $picturePath = $request->file('profile_picture')
                ->store('profiles', 'public');
        }

        // ── Text fields ───────────────────────────────────────────────────────
        $profile->update([
            'stage_name' => $request->stage_name,
            'bio' => $request->bio,
            'location' => $request->location,
            'hourly_rate' => $request->hourly_rate,
            'phone' => $request->phone,
            'instagram' => $request->instagram,
            'facebook' => $request->facebook,
            'youtube' => $request->youtube,
            'coverage_notes' => $request->coverage_notes,
            'profile_picture' => $picturePath,
        ]);

        // ── Genres (pivot sync) ───────────────────────────────────────────────
        $profile->genres()->sync($request->input('genres', []));

        return redirect()->route('profile')
            ->with('success', '¡Perfil actualizado correctamente!');
    }

    public function multimedia(Request $request)
    {
        $user = $request->user();
        $profile = $user->musicianProfile;
        return view('multimedia', array_merge(compact('user', 'profile'), ['media' => []]));
    }

    public function availability(Request $request)
    {
        $user = $request->user();
        return view('availability', compact('user'));
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
