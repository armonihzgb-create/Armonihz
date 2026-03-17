<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\ProfileController;
use App\Http\Controllers\Web\RequestController;
use App\Http\Controllers\Web\PromotionController;
use App\Http\Controllers\Web\CastingController;
use App\Http\Controllers\Web\MultimediaController;

// --- PUBLIC ROUTES ---
Route::get('/', function () {
    return view('welcome');
});

Route::get('/test', function () {
    return view('test');
});

// --- AUTHENTICATION ROUTES (Guest only) ---
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class , 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class , 'login']);

    Route::get('/register', [AuthController::class , 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class , 'register'])->name('register.submit');

    // Google Sign-In via Firebase
    Route::post('/auth/google/callback', [AuthController::class , 'googleCallback'])->name('auth.google.callback');

    // Forgot password (only guests need to request a link)
    Route::get('/password/forgot', [AuthController::class , 'showForgotForm'])->name('password.request');
    Route::post('/password/forgot', [AuthController::class , 'sendResetLink'])->name('password.email');
});


// OTP code verification (public — user is not logged in yet)
Route::get('/password/verify-code', [AuthController::class , 'showVerifyCodeForm'])->name('password.verify.form');
Route::post('/password/verify-code', [AuthController::class , 'verifyResetCode'])->name('password.verify.code');

// Password reset form & submit — public (works even when logged in as another user)
Route::get('/password/reset/{token}', [AuthController::class , 'showResetForm'])->name('password.reset');
Route::post('/password/reset', [AuthController::class , 'resetPassword'])->name('password.update');

Route::post('/logout', [AuthController::class , 'logout'])->name('logout');

// --- EMAIL VERIFICATION ROUTES ---
Route::middleware('auth')->group(function () {
    Route::get('/email/verify', [AuthController::class , 'showVerifyNotice'])->name('verification.notice');
    Route::post('/email/resend', [AuthController::class , 'resendVerification'])
        ->middleware('throttle:6,1')
        ->name('verification.send');
    // Polling endpoint — JS on verify page calls this every 4s
    Route::get('/email/check-status', function () {
        return response()->json([
            'verified' => auth()->user()?->hasVerifiedEmail() ?? false,
        ]);
    })->name('verification.check');
});

// Verification link (signed URL — no auth required; controller handles login)
Route::get('/email/verify/{id}/{hash}', [AuthController::class , 'verifyEmail'])
    ->middleware(['signed', 'throttle:6,1'])
    ->name('verification.verify');

// --- PUBLIC MUSICIAN PROFILE ---
Route::get('/musico/{id}', [ProfileController::class , 'showPublic'])->name('profile.public');

// --- AUTHENTICATED ROUTES ---
Route::middleware(['auth', 'verified'])->group(function () {

    // Common dashboard (accessible to all authenticated roles)
    Route::get('/dashboard', [DashboardController::class , 'index'])->name('dashboard');

    // Common reviews (accessible to all authenticated users)
    Route::get('/reviews', function () {
            return view('reviews');
        }
        )->name('reviews.index');

        // --- MÚSICO specific routes ---
        Route::middleware(['role:musico'])->group(function () {
            Route::get('/profile', [ProfileController::class , 'edit'])->name('profile');
            Route::put('/profile', [ProfileController::class , 'update'])->name('profile.update');
            Route::put('/profile/password', [ProfileController::class , 'changePassword'])->name('profile.password');
            Route::delete('/profile', [ProfileController::class , 'destroy'])->name('profile.destroy');
            Route::get('/multimedia', [ProfileController::class , 'multimedia'])->name('multimedia');
            Route::post('/multimedia/upload', [MultimediaController::class, 'upload'])->name('multimedia.upload');
            Route::delete('/multimedia/{media}', [MultimediaController::class, 'destroy'])->name('multimedia.destroy');
            Route::patch('/multimedia/{media}/feature', [MultimediaController::class, 'setFeatured'])->name('multimedia.feature');

            Route::get('/availability', [ProfileController::class , 'availability'])->name('availability');

            Route::get('/requests', [RequestController::class , 'index'])->name('requests.index');
            Route::get('/requests/{id}', [RequestController::class , 'show'])->name('requests.show');

    // Castings / Oportunidades (Músicos)
    Route::get('/castings', [CastingController::class, 'index'])->name('castings.index');
    Route::get('/castings/mis-postulaciones', [CastingController::class, 'myApplications'])->name('castings.my-applications');
    Route::get('/castings/{id}', [CastingController::class, 'show'])->name('castings.show');
    Route::post('/castings/{id}/apply', [CastingController::class, 'apply'])->name('castings.apply');
    Route::put('/castings/applications/{id}', [CastingController::class, 'update'])->name('castings.update');
    Route::delete('/castings/applications/{id}', [CastingController::class, 'destroy'])->name('castings.destroy');

            Route::get('/promote', [PromotionController::class , 'create'])->name('promotions.create');
            Route::get('/my-promotions', [PromotionController::class , 'index'])->name('promotions.index');
        }
        );

        // --- ADMIN ROUTES ---
        Route::middleware(['role:admin'])->prefix('admin')->group(function () {
            Route::get('/', function () {
                    return view('admin');
                }
                )->name('admin.dashboard');

                Route::get('/castings', function () {
                    return view('admin.castings.index');
                }
                )->name('admin.castings.index');

                Route::get('/promotions', function () {
                    return view('admin.promotions.index');
                }
                )->name('admin.promotions.index');

                Route::get('/musicians', function () {
                    return view('admin.musicians.index');
                }
                )->name('admin.musicians.index');

                Route::get('/settings', function () {
                    return view('admin.settings.index');
                }
                )->name('admin.settings.index');
            }
            );
        });

// --- TEMPORARY ROUTE FOR IONOS HOSTING ---
// Visita /setup-storage en tu dominio para crear el enlace simbólico sin SSH
Route::get('/setup-storage', function () {
    try {
        if (file_exists(public_path('storage'))) {
            return 'El enlace "storage" ya existe en la carpeta public. Si las imágenes no cargan, bórralo manualmente mediante FTP o el administrador de archivos y vuelve a visitar esta ruta.';
        }
        \Illuminate\Support\Facades\Artisan::call('storage:link');
        return '¡Enlace de almacenamiento creado con éxito! Tus imágenes y videos ahora deberían ser visibles.';
    } catch (\Exception $e) {
        return 'Error al intentar crear el enlace: ' . $e->getMessage();
    }
});

// --- FIX PARA IMÁGENES EN ENTORNO LOCAL (WINDOWS / XAMPP) ---
// Usamos /file/ para evitar conflictos con la carpeta /public/storage/ existente
Route::get('/file/{path}', function ($path) {
    if (str_starts_with($path, 'profiles/')) {
        $fullPath = storage_path('app/public/' . $path);
    } else if (str_starts_with($path, 'musician_media/')) {
        $fullPath = storage_path('app/public/' . $path);
    } else {
        $fullPath = storage_path('app/public/' . $path);
    }

    $base = realpath(storage_path('app/public'));
    $fullPath = realpath($fullPath);

    // Ensure the resolved path is inside the allowed base directory (prevents path traversal)
    if (!$fullPath || !str_starts_with($fullPath, $base)) {
        abort(404);
    }

    if (!file_exists($fullPath)) {
        abort(404);
    }

    $mimeType = mime_content_type($fullPath);
    // If it's a video, serve it with proper ranges for seeking support
    if (str_starts_with($mimeType, 'video/')) {
        $size = filesize($fullPath);
        $length = $size;
        $start = 0;
        $end = $size - 1;

        $headers = [
            'Content-Type' => $mimeType,
            'Cache-Control' => 'max-age=86400, public',
            'Accept-Ranges' => 'bytes',
        ];

        if (isset($_SERVER['HTTP_RANGE'])) {
            $c_start = $start;
            $c_end = $end;
            list(, $range) = explode('=', $_SERVER['HTTP_RANGE'], 2);
            if (strpos($range, ',') !== false) {
                header('HTTP/1.1 416 Requested Range Not Satisfiable');
                header("Content-Range: bytes $start-$end/$size");
                exit;
            }
            if ($range == '-') {
                $c_start = $size - substr($range, 1);
            } else {
                $range = explode('-', $range);
                $c_start = $range[0];
                $c_end = (isset($range[1]) && is_numeric($range[1])) ? $range[1] : $size;
            }
            $c_end = ($c_end > $end) ? $end : $c_end;
            if ($c_start > $c_end || $c_start > $size - 1 || $c_end >= $size) {
                header('HTTP/1.1 416 Requested Range Not Satisfiable');
                header("Content-Range: bytes $start-$end/$size");
                exit;
            }
            $start = $c_start;
            $end = $c_end;
            $length = $end - $start + 1;
            $headers['Content-Range'] = "bytes $start-$end/$size";
            return response()->file($fullPath, $headers)->setStatusCode(206);
        }
        
        return response()->file($fullPath, $headers);
    }

    return response()->file($fullPath, [
        'Content-Type' => $mimeType,
        'Cache-Control' => 'max-age=86400, public',
    ]);
})->where('path', '.*');
