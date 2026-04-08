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

Route::get('/app/descargar', function () {
    $apkPath = storage_path('app/public/apps/Armonihz.apk');
    if (!file_exists($apkPath)) {
        return response('El archivo Armonihz.apk no se encuentra en el servidor (Easypanel). Por favor, sube físicamente el archivo a: ' . $apkPath, 404);
    }
    return response()->download($apkPath, 'Armonihz.apk', [
        'Content-Type' => 'application/vnd.android.package-archive',
    ]);
})->name('app.download');

Route::get('/test', function () {
    return view('test');
});

// --- AUTHENTICATION ROUTES (Guest only) ---
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class , 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class , 'login']);

    Route::get('/register', [AuthController::class , 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class , 'register'])->name('register.submit');

    // App Client pre-registration
    Route::get('/app-registro', [AuthController::class , 'showClientRegister'])->name('register.client');
    Route::post('/app-registro', [AuthController::class , 'registerClient'])->name('register.client.submit');
    Route::get('/app-registro2', [AuthController::class , 'showClientRegister'])->name('register.client2');
    Route::post('/app-registro2', [AuthController::class , 'registerClient'])->name('register.client.submit2')->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class]);

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

    // Rutas de verificación de identidad (músicos)
    Route::get('/verificar-identidad', [\App\Http\Controllers\Web\VerificationController::class, 'notice'])->name('id_verification.notice');
    Route::post('/verificar-identidad', [\App\Http\Controllers\Web\VerificationController::class, 'upload'])->name('id_verification.upload');

    Route::middleware(['verified_musician'])->group(function () {
        // Common dashboard (accessible to all authenticated roles)
        Route::get('/dashboard', [DashboardController::class , 'index'])->name('dashboard');

        // Common reviews (accessible to all authenticated users)
        Route::get('/reviews', [\App\Http\Controllers\Web\ReviewController::class, 'index'])->name('reviews.index');
        Route::post('/reviews/{id}/respond', [\App\Http\Controllers\Web\ReviewController::class, 'respond'])->name('reviews.respond');

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

            Route::get('/requests/{status?}', [RequestController::class , 'index'])
                ->where('status', 'pending|accepted|rejected|completed|counter_offer')
                ->name('requests.index');
            Route::get('/requests/{id}', [RequestController::class , 'show'])->name('requests.show');

            Route::patch('/requests/{id}/status', [RequestController::class , 'updateStatus'])->name('requests.update-status');

            

    // Castings / Oportunidades (Músicos)
    Route::get('/castings', [CastingController::class, 'index'])->name('castings.index');
    Route::get('/castings/mis-postulaciones', [CastingController::class, 'myApplications'])->name('castings.my-applications');
    Route::get('/castings/{id}', [CastingController::class, 'show'])->name('castings.show');
    Route::post('/castings/{id}/apply', [CastingController::class, 'apply'])->name('castings.apply');
    Route::put('/castings/applications/{id}', [CastingController::class, 'update'])->name('castings.update');
    Route::delete('/castings/applications/{id}', [CastingController::class, 'destroy'])->name('castings.destroy');
    Route::put('/castings/applications/{id}/complete', [CastingController::class, 'complete'])->name('castings.complete');


            Route::get('/promote', [PromotionController::class , 'create'])->name('promotions.create');
            Route::get('/my-promotions', [PromotionController::class , 'index'])->name('promotions.index');
        }
        );

        // --- ADMIN ROUTES ---
        Route::middleware(['role:admin'])->prefix('admin')->group(function () {
            Route::get('/', [\App\Http\Controllers\Web\AdminController::class, 'index'])->name('admin.dashboard');
            Route::get('/search/{search?}', [\App\Http\Controllers\Web\AdminController::class, 'index'])->name('admin.dashboard.search');

            // Verificación de Músicos
            Route::get('/musicians/verification/{id}', [\App\Http\Controllers\Web\AdminController::class, 'verifyMusicianView'])->name('admin.musicians.verify');
            Route::post('/musicians/verification/{id}', [\App\Http\Controllers\Web\AdminController::class, 'verifyMusicianAction'])->name('admin.musicians.verify.action');
            Route::get('/musicians/verification/{id}/document', [\App\Http\Controllers\Web\AdminController::class, 'streamDocument'])->name('admin.musicians.document');

                Route::get('/castings', [\App\Http\Controllers\Web\AdminController::class, 'castingsIndex'])->name('admin.castings.index');
                Route::get('/castings/{id}', [\App\Http\Controllers\Web\AdminController::class, 'showCasting'])->name('admin.castings.show');
                Route::patch('/castings/{id}/status', [\App\Http\Controllers\Web\AdminController::class, 'updateCastingStatus'])->name('admin.castings.status');
                Route::delete('/castings/{id}', [\App\Http\Controllers\Web\AdminController::class, 'destroyCasting'])->name('admin.castings.destroy');

                Route::get('/promotions', function () {
                    return view('admin.promotions.index');
                }
                )->name('admin.promotions.index');

                Route::get('/musicians/{status?}/{search?}', [\App\Http\Controllers\Web\AdminController::class, 'musiciansIndex'])
                    ->where('status', 'pending|approved|rejected|unverified')
                    ->name('admin.musicians.index');

                Route::get('/settings', function () {
                    return view('admin.settings.index');
                })->name('admin.settings.index');

                // Gestión de reportes
                Route::get('/reports/{status?}', [\App\Http\Controllers\Web\AdminController::class, 'reportsIndex'])
                    ->where('status', 'pending|reviewed|resolved')
                    ->name('admin.reports.index');
                Route::patch('/reports/{id}/status', [\App\Http\Controllers\Web\AdminController::class, 'updateReportStatus'])->name('admin.reports.status');
        });
    }); // End verified_musician middleware
}); // End auth middleware

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

// --- FIX PARA IMÁGENES Y VIDEOS EN ENTORNO LOCAL/PRODUCCIÓN ---
// Usamos /file/ para evitar conflictos con la carpeta /public/storage/ existente
// --- FIX PARA IMÁGENES Y VIDEOS (STREAMING MANUAL PARA ANDROID) ---
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

    if (!$fullPath || !str_starts_with($fullPath, $base) || !file_exists($fullPath)) {
        abort(404);
    }

    $mimeType = mime_content_type($fullPath);

    // Si es un VIDEO, forzamos el streaming por rangos a mano, pero al estilo Laravel
    if (str_starts_with($mimeType, 'video/')) {
        $size = filesize($fullPath);
        $start = 0;
        $end = $size - 1;
        $length = $size;
        $status = 200;
        
        $headers = [
            'Content-Type' => $mimeType,
            'Accept-Ranges' => 'bytes',
            'Cache-Control' => 'max-age=86400, public',
        ];

        // Android envía HTTP_RANGE cuando quiere adelantar o leer la duración
        if (request()->server('HTTP_RANGE')) {
            $range = request()->server('HTTP_RANGE');
            list($param, $range) = explode('=', $range, 2);
            
            if (strtolower(trim($param)) !== 'bytes') {
                return response('Requested Range Not Satisfiable', 416)
                    ->header('Content-Range', "bytes $start-$end/$size");
            }
            
            $range = explode(',', $range)[0];
            $range = explode('-', $range);
            $c_start = $range[0];
            $c_end = (isset($range[1]) && is_numeric($range[1])) ? $range[1] : $size - 1;

            $c_end = ($c_end > $end) ? $end : $c_end;
            if ($c_start > $c_end || $c_start > $size - 1 || $c_end >= $size) {
                return response('Requested Range Not Satisfiable', 416)
                    ->header('Content-Range', "bytes $start-$end/$size");
            }

            $start = $c_start;
            $end = $c_end;
            $length = $end - $start + 1;
            $status = 206; // 206 Partial Content (Obligatorio para Android)
            $headers['Content-Range'] = "bytes $start-$end/$size";
        }

        $headers['Content-Length'] = $length;

        // Usamos response()->stream() para enviar los fragmentos sin usar exit;
        return response()->stream(function () use ($fullPath, $start, $length) {
            $file = fopen($fullPath, 'rb');
            fseek($file, $start);
            $bufferSize = 8192; // Fragmentos de 8KB
            $bytesLeft = $length;
            
            while ($bytesLeft > 0 && !feof($file)) {
                $read = ($bytesLeft > $bufferSize) ? $bufferSize : $bytesLeft;
                $bytesLeft -= $read;
                echo fread($file, $read);
                flush();
            }
            fclose($file);
        }, $status, $headers);
    }

    // Si es imagen u otro archivo, lo mandamos normal
    return response()->file($fullPath, [
        'Content-Type' => $mimeType,
        'Cache-Control' => 'max-age=86400, public',
    ]);
})->where('path', '.*');