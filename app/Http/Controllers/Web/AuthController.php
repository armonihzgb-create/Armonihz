<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
use App\Models\User;
use App\Models\MusicianProfile;
use App\Models\Genre;
use App\Services\FirebaseService;

class AuthController extends Controller
{
    // ── LOGIN ─────────────────────────────────────────────────────────────────

    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return view('auth.login');
    }

    public function showRegister()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        $genres = Genre::orderBy('name')->get();
        return view('auth.register', compact('genres'));
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ], [
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'Ingresa un correo electrónico válido.',
            'password.required' => 'La contraseña es obligatoria.',
        ]);

        $user = User::where('email', $request->email)->first();

        if ($user && !$user->is_active) {
            return back()->withErrors([
                'auth' => 'Tu cuenta ha sido suspendida. Contacta al soporte.',
            ])->onlyInput('email');
        }

        if (Auth::attempt($request->only('email', 'password'), $request->boolean('remember'))) {
            $request->session()->regenerate();

            if (Auth::user()->role === 'admin') {
                return redirect()->intended(route('admin.dashboard'));
            }

            return redirect()->intended(route('dashboard'));
        }

        return back()->withErrors([
            'auth' => 'El correo o la contraseña son incorrectos.',
        ])->onlyInput('email');
    }

    // ── REGISTER ──────────────────────────────────────────────────────────────

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'city' => 'required|string|max:255',
        ], [
            'name.required' => 'El nombre artístico es obligatorio.',
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'Ingresa un correo electrónico válido.',
            'email.unique' => 'Este correo ya está registrado.',
            'password.required' => 'La contraseña es obligatoria.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
            'city.required' => 'La ciudad es obligatoria.',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'musico',
            'is_active' => true,
            'is_verified' => false,
        ]);

        $profile = MusicianProfile::create([
            'user_id' => $user->id,
            'stage_name' => $request->name,
            'location' => $request->city ?? '',
            'bio' => null,
            'is_verified' => false,
        ]);

        // Attach genre if one was selected and it exists in DB
        if ($request->filled('genre_id')) {
            $genre = Genre::find($request->genre_id);
            if ($genre) {
                $profile->genres()->attach($genre->id);
            }
        }

        // Invalidate any previous session completely before logging in the new user
        if (Auth::check()) {
            Auth::logout();
        }
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        Auth::login($user);
        $request->session()->regenerate();

        // Fire the Registered event — triggers the verification email
        event(new Registered($user));

        return redirect()->route('verification.notice')
            ->with('status', '¡Cuenta creada! Revisa tu correo para verificar tu dirección.');
    }

    // ── LOGOUT ────────────────────────────────────────────────────────────────

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }

    // ── FORGOT PASSWORD ───────────────────────────────────────────────────────

    public function showForgotForm()
    {
        return view('auth.forgot');
    }

    public function sendResetLink(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
        ], [
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'Ingresa un correo electrónico válido.',
        ]);

        $status = Password::sendResetLink($request->only('email'));

        // Always return success message (security: don't reveal if email exists)
        return back()->with('status', 'Si tu correo está registrado, recibirás un enlace en breve para restablecer tu contraseña.');
    }

    // ── RESET PASSWORD ────────────────────────────────────────────────────────

    public function showResetForm(Request $request, string $token)
    {
        return view('auth.reset', [
            'token' => $token,
            'email' => $request->query('email', ''),
        ]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|string|min:8|confirmed',
        ], [
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'Ingresa un correo electrónico válido.',
            'password.required' => 'La contraseña es obligatoria.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
            $user->forceFill([
                'password' => Hash::make($password),
                'remember_token' => Str::random(60),
            ])->save();
            event(new PasswordReset($user));
        }
        );

        if ($status === Password::PASSWORD_RESET) {
            // Log out any currently active session (could be a different user)
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')
                ->with('status', '✅ Contraseña actualizada correctamente. Ya puedes iniciar sesión con tu nueva contraseña.');
        }

        return back()->withErrors([
            'email' => 'El enlace de restablecimiento no es válido o ha expirado. Solicita uno nuevo.',
        ]);
    }

    // ── EMAIL VERIFICATION ────────────────────────────────────────────────────

    public function showVerifyNotice()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }
        if (Auth::user()->hasVerifiedEmail()) {
            return redirect()->intended(route('dashboard'));
        }
        return view('auth.verify');
    }

    public function verifyEmail(Request $request, $id, $hash)
    {
        $user = User::findOrFail($id);

        if (!hash_equals((string)$hash, sha1($user->getEmailForVerification()))) {
            abort(403, 'Enlace de verificación inválido.');
        }

        if ($user->hasVerifiedEmail()) {
            // Log in as the correct user anyway and go to dashboard
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            Auth::login($user);
            $request->session()->regenerate();
            return redirect()->route('dashboard')
                ->with('status', 'Tu correo ya había sido verificado. Bienvenido.');
        }

        $user->markEmailAsVerified();

        // Log out whoever was in session and log in the correct user
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('dashboard')
            ->with('status', '✅ ¡Correo verificado! Bienvenido a Armonihz.');
    }

    public function resendVerification(Request $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->route('dashboard');
        }

        $request->user()->sendEmailVerificationNotification();

        return back()->with('status', 'Se ha reenviado el enlace de verificación. Revisa tu bandeja de entrada.');
    }
    // ── GOOGLE SIGN-IN (Firebase) ─────────────────────────────────────────────

    public function googleCallback(Request $request, FirebaseService $firebaseService)
    {
        $request->validate(['credential' => 'required|string']);

        $tokenStr = $request->credential;
        $decoded = null;
        $isFirebase = false;

        // Try 1: Google Identity Services (Web)
        $response = Http::get('https://oauth2.googleapis.com/tokeninfo', [
            'id_token' => $tokenStr,
        ]);

        if ($response->successful()) {
            $googleDecoded = $response->json();
            $expectedClientId = config('services.google.client_id', env('GOOGLE_CLIENT_ID'));

            // Validate Audience for Web
            if (isset($googleDecoded['aud']) && $expectedClientId && $googleDecoded['aud'] === $expectedClientId) {
                $decoded = $googleDecoded;
            }
        }

        // Try 2: Firebase (Android App)
        if (!$decoded) {
            try {
                $firebaseDecoded = $firebaseService->verifyIdToken($tokenStr);
                $decoded = $firebaseDecoded->claims()->all();
                $isFirebase = true;
            }
            catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::error('Google Auth Error (Firebase Fallback): ' . $e->getMessage());
                return back()->withErrors(['auth' => 'Token de Google o Firebase inválido. Intenta de nuevo.']);
            }
        }

        if (!$decoded) {
            return back()->withErrors(['auth' => 'No se pudo validar el Token con Google ni con Firebase.']);
        }

        // Extract consistent fields from either payload
        $email = $decoded['email'] ?? null;
        $name = $decoded['name'] ?? 'Usuario';
        $uid = $decoded['sub'] ?? null;
        $picture = $decoded['picture'] ?? null;

        if (!$email || !$uid) {
            return back()->withErrors(['auth' => 'No se pudo obtener la cuenta de Google (faltan datos).']);
        }

        $user = User::firstOrCreate(
        ['email' => $email],
        [
            'name' => $name,
            'google_id' => $uid,
            'firebase_uid' => $uid,
            'role' => 'musico',
            'password' => bcrypt(Str::random(24)),
            'is_active' => true,
            'is_verified' => false,
            'email_verified_at' => now(),
        ]
        );

        // If the user was just created, we MUST create their MusicianProfile
        // Otherwise they will get "Attempt to read property 'instagram' on null" on the dashboard/profile
        if ($user->wasRecentlyCreated && $user->role === 'musico') {
            MusicianProfile::create([
                'user_id' => $user->id,
                'stage_name' => $name,
                'location' => '',
                'bio' => null,
                'profile_picture' => $picture, // Save Google Picture!
                'is_verified' => false,
            ]);
        }

        // Link google_id & firebase_uid if the user already existed with email+password
        if (!$user->google_id) {
            $user->update([
                'google_id' => $uid,
                'firebase_uid' => $uid,
                'email_verified_at' => $user->email_verified_at ?? now(),
            ]);
        }

        if (!$user->is_active) {
            return back()->withErrors(['auth' => 'Tu cuenta está suspendida. Contacta al soporte.']);
        }

        Auth::login($user, true);
        $request->session()->regenerate();

        if ($user->role === 'admin') {
            return redirect()->intended(route('admin.dashboard'));
        }

        return redirect()->intended(route('dashboard'));
    }
}
