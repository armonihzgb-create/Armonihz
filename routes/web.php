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

            // Availability
            Route::get('/availability', [\App\Http\Controllers\Web\AvailabilityController::class, 'index'])->name('availability');
            Route::get('/availability/events', [\App\Http\Controllers\Web\AvailabilityController::class, 'getEvents'])->name('availability.events');
            Route::post('/availability', [\App\Http\Controllers\Web\AvailabilityController::class, 'store'])->name('availability.store');
            Route::put('/availability/{id}', [\App\Http\Controllers\Web\AvailabilityController::class, 'update'])->name('availability.update');
            Route::delete('/availability/{id}', [\App\Http\Controllers\Web\AvailabilityController::class, 'destroy'])->name('availability.destroy');

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

// --- FIX DEFINITIVO PARA IMÁGENES Y VIDEOS (STREAMING REAL) ---
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

    // Seguridad: evitar Path Traversal
    if (!$fullPath || !str_starts_with($fullPath, $base) || !file_exists($fullPath)) {
        abort(404);
    }

    $mimeType = mime_content_type($fullPath);
    $size = filesize($fullPath);

    // --- LÓGICA DE STREAMING PARA VIDEOS ---
    if (str_starts_with($mimeType, 'video/')) {
        $start = 0;
        $end = $size - 1;
        $length = $size;
        $status = 200;

        $headers = [
            'Content-Type' => $mimeType,
            'Cache-Control' => 'no-cache, private',
            'Accept-Ranges' => 'bytes',
        ];

        // Procesar la cabecera Range (que envía Android)
        if (isset($_SERVER['HTTP_RANGE'])) {
            preg_match('/bytes=(\d+)-(\d+)?/', $_SERVER['HTTP_RANGE'], $matches);
            $start = isset($matches[1]) ? intval($matches[1]) : 0;
            $end = isset($matches[2]) ? intval($matches[2]) : $size - 1;

            if ($end >= $size) {
                $end = $size - 1;
            }

            $length = ($end - $start) + 1;
            $status = 206; // 206 Partial Content (CLAVE PARA ANDROID)
            
            $headers['Content-Range'] = "bytes $start-$end/$size";
        }

        $headers['Content-Length'] = $length;

        // Limpiar buffers de salida previos
        while (ob_get_level() > 0) {
            ob_end_clean();
        }

        // Devolver una respuesta Streamed de Laravel
        return response()->stream(function () use ($fullPath, $start, $length) {
            $stream = fopen($fullPath, 'rb');
            fseek($stream, $start);
            
            $bufferSize = 8192; // 8KB chunks
            $bytesSent = 0;

            while (!feof($stream) && $bytesSent < $length) {
                $bytesToRead = min($bufferSize, $length - $bytesSent);
                echo fread($stream, $bytesToRead);
                flush();
                $bytesSent += $bytesToRead;
            }
            fclose($stream);
        }, $status, $headers);
    }

    // --- Para imágenes y otros archivos normales ---
    return response()->file($fullPath, [
        'Content-Type' => $mimeType,
        'Cache-Control' => 'max-age=86400, public',
    ]);
})->where('path', '.*');