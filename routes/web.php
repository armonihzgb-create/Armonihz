<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\ProfileController;
use App\Http\Controllers\Web\RequestController;
use App\Http\Controllers\Web\PromotionController;

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

    // Forgot password (only guests need to request a link)
    Route::get('/password/forgot', [AuthController::class , 'showForgotForm'])->name('password.request');
    Route::post('/password/forgot', [AuthController::class , 'sendResetLink'])->name('password.email');
});

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
            Route::get('/multimedia', [ProfileController::class , 'multimedia'])->name('multimedia');
            Route::get('/availability', [ProfileController::class , 'availability'])->name('availability');

            Route::get('/requests', [RequestController::class , 'index'])->name('requests.index');
            Route::get('/requests/{id}', [RequestController::class , 'show'])->name('requests.show');

            Route::get('/castings', function () {
                    return view('castings.index');
                }
                )->name('castings.index');
                Route::get('/castings/{id}', function ($id) {
                    return view('castings.show', ['id' => $id]);
                }
                )->name('castings.show');

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

// --- FIX PARA IMÁGENES EN ENTORNO LOCAL (WINDOWS / XAMPP) ---
// Sirve archivos directamente desde storage/app/public sin depender del symlink.
Route::get('/storage/{path}', function ($path) {
    $base = realpath(storage_path('app/public'));
    $fullPath = realpath($base . '/' . $path);

    // Ensure the resolved path is inside the allowed base directory (prevents path traversal)
    if (!$fullPath || !str_starts_with($fullPath, $base)) {
        abort(404);
    }

    if (!file_exists($fullPath)) {
        abort(404);
    }

    $mimeType = mime_content_type($fullPath);
    return response()->file($fullPath, [
    'Content-Type' => $mimeType,
    'Cache-Control' => 'max-age=86400, public',
    ]);
})->where('path', '.*');
