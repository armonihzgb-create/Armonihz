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

        // --- Media ---
        $media = [];
        if ($profile) {
            $media = $profile->media()->orderBy('created_at', 'desc')->get();
        }

        return view('profile', compact('user', 'profile', 'genres', 'completion', 'acceptedRequests', 'media'));
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
            'phone' => [
                'nullable',
                'string',
                'regex:/^\+?[0-9\s\-]*$/',
                function ($attribute, $value, $fail) {
                    $digitsCount = preg_match_all('/[0-9]/', $value);
                    if ($digitsCount > 10) {
                        $fail('El número de teléfono no debe tener más de 10 números.');
                    }
                },
            ],
            'instagram' => 'nullable|string|max:255',
            'facebook' => 'nullable|string|max:255',
            'youtube' => 'nullable|string|max:255',
            'coverage_notes' => 'nullable|string|max:500',
            'profile_picture' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:3072',
            'genres' => 'nullable|array',
            'genres.*' => 'integer|exists:genres,id',
        ], [
            'phone.regex' => 'El teléfono solo puede contener números, espacios, guiones y un símbolo + opcional al inicio.',
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
     */
    public function changePassword(Request $request)
    {
        $user = clone $request->user();

        // If the user is exclusively a Google user (no local password), shouldn't reach here normally, 
        // but we protect against it.
        if ($user->google_id && !$user->password) {
            return back()->withErrors(['password' => 'Tu cuenta está vinculada a Google.'])->withFragment('password-section');
        }

        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => [
                'required',
                'string',
                'min:8',
                'regex:/[a-z]/',
                'regex:/[A-Z]/',
                'regex:/[0-9]/',
                'regex:/[@$!%*#?&]/',
                'confirmed',
                'different:current_password'
            ],
        ], [
            'current_password.required' => 'Debes ingresar tu contraseña actual.',
            'current_password.current_password' => 'La contraseña actual es incorrecta.',
            'password.required' => 'La nueva contraseña es obligatoria.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'password.regex' => 'La contraseña debe contener al menos una mayúscula, un número y un carácter especial.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
            'password.different' => 'La nueva contraseña no puede ser igual a la actual.',
        ]);

        $request->user()->update([
            'password' => \Illuminate\Support\Facades\Hash::make($request->password),
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
