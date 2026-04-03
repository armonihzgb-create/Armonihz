<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
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
            if (Auth::user()->role === 'cliente') {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                return back()->withErrors([
                    'auth' => 'Esta plataforma web es exclusiva para Músicos y Admins. Para usar tu cuenta de cliente, por favor espera el lanzamiento de la App Móvil de Armonihz.',
                ])->onlyInput('email');
            }

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

    public function showClientRegister()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return view('auth.register-client');
    }

    public function registerClient(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:100',
            'apellido' => 'required|string|max:100',
            'email' => 'required|email|max:255|unique:users',
            'telefono' => 'required|string|max:20',
            'password' => 'required|string|min:8|confirmed',
        ], [
            'nombre.required' => 'El nombre es obligatorio.',
            'apellido.required' => 'El apellido es obligatorio.',
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'Ingresa un correo electrónico válido.',
            'email.unique' => 'Este correo ya está registrado.',
            'telefono.required' => 'El número de teléfono es obligatorio.',
            'password.required' => 'La contraseña es obligatoria.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
        ]);

        $user = User::create([
            'name' => trim($request->nombre . ' ' . $request->apellido),
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'cliente',
            'is_active' => true,
            'is_verified' => false,
        ]);

        \App\Models\Client::create([
            'user_id' => $user->id,
            'nombre' => $request->nombre,
            'apellido' => $request->apellido,
            'email' => $request->email,
            'telefono' => $request->telefono,
        ]);

        return back()->with('status', '¡Registro completado exitosamente! Hemos guardado tus datos. Te notificaremos cuando la App Móvil esté disponible para que inicies sesión.');
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
            'email.email'    => 'Ingresa un correo electrónico válido.',
        ]);

        // Generate a 6-digit OTP code
        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $email = strtolower(trim($request->email));
        $cacheKey = 'pwd_otp_' . md5($email);

        // Store code for 15 minutes (regardless of whether email exists — prevents enumeration)
        Cache::put($cacheKey, $code, now()->addMinutes(15));

        // Send the email only if the user actually exists
        $user = User::where('email', $email)->first();
        if ($user) {
            Mail::send('emails.reset-code', ['code' => $code, 'user' => $user], function ($m) use ($user) {
                $m->to($user->email)
                  ->subject('Código de verificación - Armonihz');
            });
        }

        return redirect()->route('password.verify.form', ['email' => $email])
            ->with('otp_email', $email)
            ->with('status', "Hemos enviado un código de 6 dígitos a <strong>{$email}</strong>. Revisa tu bandeja de entrada.");
    }

    public function showVerifyCodeForm(Request $request)
    {
        // Email comes from URL query param (persists on reload) or session flash fallback
        // Strip anything after '?' to avoid proxy-caused duplication (e.g. email?email=...)
        $raw   = $request->query('email', session('otp_email', ''));
        $email = explode('?', $raw)[0];
        return view('auth.verify-code', compact('email'));
    }

    public function verifyResetCode(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'code'  => 'required|string|size:6',
        ], [
            'code.required' => 'El código es obligatorio.',
            'code.size'     => 'El código debe tener exactamente 6 dígitos.',
        ]);

        $email    = strtolower(trim($request->email));
        $cacheKey = 'pwd_otp_' . md5($email);
        $stored   = Cache::get($cacheKey);

        if (!$stored || $stored !== $request->code) {
            return back()
                ->withInput()
                ->withErrors(['code' => 'El código es incorrecto o ya expiró. Solicita uno nuevo.']);
        }

        // Code is valid — consume it
        Cache::forget($cacheKey);

        // Generate a real Laravel password reset token for the existing reset form
        $user = User::where('email', $email)->first();
        if (!$user) {
            return back()->withErrors(['code' => 'No encontramos una cuenta con ese correo.']);
        }

        $token = Password::createToken($user);

        // Redirect to existing reset form (same tab, no email links needed)
        return redirect(route('password.reset', ['token' => $token]) . '?email=' . urlencode($email));
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
            'token'    => 'required',
            'email'    => 'required|email',
            'password' => 'required|string|min:8|confirmed',
        ], [
            'email.required'    => 'El correo electrónico es obligatorio.',
            'email.email'       => 'Ingresa un correo electrónico válido.',
            'password.required' => 'La contraseña es obligatoria.',
            'password.min'      => 'La contraseña debe tener al menos 8 caracteres.',
            'password.confirmed'=> 'Las contraseñas no coinciden.',
        ]);

        // Check that the new password is different from the current one
        $user = User::where('email', $request->email)->first();
        if ($user && $user->password && Hash::check($request->password, $user->password)) {
            return back()->withErrors([
                'password' => 'La nueva contraseña no puede ser igual a la contraseña actual.',
            ])->withInput($request->except('password', 'password_confirmation'));
        }

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
            $user->forceFill([
                'password'       => Hash::make($password),
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
                ->with('status', 'Contraseña actualizada correctamente. Ya puedes iniciar sesión con tu nueva contraseña.');
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

        // Mark as verified (idempotent if already verified)
        if (!$user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
        }

        // Log in the verified user (in case this tab has a different session)
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        Auth::login($user);
        $request->session()->regenerate();

        // Return a tiny self-closing page — the original tab's polling
        // will detect verification and redirect to Dashboard automatically.
        return response(<<<HTML
        <!DOCTYPE html>
        <html lang="es">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Verificado ✅</title>
            <style>
                body { margin:0; display:flex; flex-direction:column; align-items:center; justify-content:center;
                       min-height:100vh; font-family:'Inter',sans-serif; background:#f0fdf4; color:#15803d; gap:16px; }
                .icon { font-size:56px; animation: pop .4s cubic-bezier(.4,2,.6,1); }
                h2 { margin:0; font-size:20px; }
                p  { margin:0; font-size:14px; color:#4b7a60; }
                @keyframes pop { 0%{transform:scale(0)} 100%{transform:scale(1)} }
            </style>
        </head>
        <body>
            <div class="icon">✅</div>
            <h2>¡Correo verificado!</h2>
            <p>Puedes cerrar esta pestaña.</p>
            <script>
                // Close this tab — works when opened by email client same-browser
                setTimeout(() => { window.close(); }, 1800);
            </script>
        </body>
        </html>
        HTML);
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

    public function googleCallback(Request $request)
    {
        $request->validate(['credential' => 'required|string']);

        $tokenStr = $request->credential;
        $decoded = null;

        // Validar Token con Google Identity Services (Web)
        $response = Http::get('https://oauth2.googleapis.com/tokeninfo', [
            'id_token' => $tokenStr,
        ]);

        if ($response->successful()) {
            $googleDecoded = $response->json();
            $expectedClientId = config('services.google.client_id', env('GOOGLE_CLIENT_ID'));

            // Validar Audience para Web
            if (isset($googleDecoded['aud']) && $expectedClientId && $googleDecoded['aud'] === $expectedClientId) {
                $decoded = $googleDecoded;
            }
        }

        if (!$decoded) {
            \Illuminate\Support\Facades\Log::error('Google Auth Error: Token no válido o aud key mismatch.');
            return back()->withErrors(['auth' => 'No se pudo validar el Token con Google.']);
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
