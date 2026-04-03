<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\MusicianProfileController;
use App\Http\Controllers\Api\ProfileViewController;
use App\Http\Controllers\ClientController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ClientEventController;
use App\Http\Controllers\GenreController;
use App\Http\Controllers\HiringRequestController;

Route::prefix('v1')->group(function () {

    Route::get('/test', function () {
            return response()->json([
            'message' => 'API OK'
            ]);
        }
        );

        // ── Public routes ─────────────────────────────
        Route::post('/register', [AuthController::class , 'register']);
        Route::post('/login', [AuthController::class , 'login'])->middleware('throttle:login');
        Route::post('/firebase-login', [AuthController::class , 'firebaseLogin']);

        Route::get('/musicians', [MusicianProfileController::class , 'index'])->middleware('throttle:public-api');
        Route::get('/musicians/{id}', [MusicianProfileController::class , 'show'])->middleware('throttle:public-api');
        Route::get('/musicians/{id}/availability', [App\Http\Controllers\MusicianProfileController::class, 'getAvailability']);
        Route::get('/musicians/{id}/reviews', [\App\Http\Controllers\Api\ReviewController::class, 'musicianReviews']);

        Route::get('/promotions', [App\Http\Controllers\PromotionController::class , 'index'])->middleware('throttle:public-api');
        Route::get('/test-notification', [ClientController::class, 'testNotification']);
        Route::get('/genres', [GenreController::class, 'index']);

        // ── Authenticated routes (Sanctum) ────────────
        Route::middleware('auth:sanctum')->group(function () {

            Route::post('/logout', [AuthController::class , 'logout']);
            Route::get('/me', [AuthController::class , 'me']);

            Route::post('/musicians/{id}/view', [ProfileViewController::class , 'record']);
            Route::put('/musicians/{id}', [MusicianProfileController::class , 'update']);

            Route::post('promotions', [App\Http\Controllers\PromotionController::class , 'store'])->middleware('throttle:promotions');
            Route::apiResource('promotions', App\Http\Controllers\PromotionController::class)->except(['index', 'store']);

            Route::get('/notifications', [App\Http\Controllers\NotificationController::class , 'index']);
            Route::patch('/notifications/{id}/read', [App\Http\Controllers\NotificationController::class , 'markAsRead']);
        }
        );

        // ── Firebase protected routes ────────────────
        Route::middleware('firebase.auth')->group(function () {
            Route::post('/client/foto', [ClientController::class , 'uploadFotoPerfil']);
            Route::delete('/client/foto', [ClientController::class , 'deleteFotoPerfil']);
            Route::get('/client/profile', [ClientController::class , 'profile']);
            Route::put('/client/profile', [ClientController::class , 'updateProfile']);
            Route::post('/client/sync-google-photo', [ClientController::class , 'syncGooglePhoto']);
            Route::post('/client/sync', [ClientController::class , 'syncClient']);
            Route::delete('/client/account', [ClientController::class , 'deleteAccount']);
            Route::get('/client/events', [ClientEventController::class , 'index']);
            Route::post('/client/events', [ClientEventController::class , 'store']);
            Route::get('/client/events/{id}/applications', [ClientEventController::class , 'getApplications']);
            Route::post('/client/events/{eventId}/applications/{appId}/accept', [ClientEventController::class , 'acceptApplication']);
            Route::post('/client/events/{eventId}/applications/{appId}/cancel', [ClientEventController::class , 'cancelApplication']);

            Route::put('/client/events/{id}', [ClientEventController::class, 'update']);
            Route::delete('/client/events/{id}', [ClientEventController::class, 'destroy']);
            Route::post('/client/fcm-token', [ClientController::class, 'updateFcmToken']);
            Route::post('/client/favorites/{id}', [App\Http\Controllers\FavoriteController::class, 'addFavorite']);
            Route::delete('/client/favorites/{id}', [App\Http\Controllers\FavoriteController::class, 'removeFavorite']);
            Route::get('/client/favorites', [App\Http\Controllers\FavoriteController::class, 'index']);
            Route::get('/client/reviews', [\App\Http\Controllers\Api\ReviewController::class, 'myReviews']);

            // 👇 AHORA SÍ, LAS RUTAS DE HIRING-REQUESTS ESTÁN PROTEGIDAS POR FIREBASE 👇
            Route::post('hiring-requests', [HiringRequestController::class , 'store']);
            
            // Reviews
            Route::post('/reviews', [\App\Http\Controllers\Api\ReviewController::class, 'store']);
            Route::apiResource('hiring-requests', HiringRequestController::class)->only(['index', 'show']);
            Route::patch('hiring-requests/{id}/status', [HiringRequestController::class , 'updateStatus']);
            Route::post('hiring-requests/{id}/respond', [HiringRequestController::class, 'respondToCounterOffer']);
            // En routes/api.php (dentro de middleware 'firebase.auth')
            Route::post('/musicians/{id}/report', [\App\Http\Controllers\Api\ReportController::class, 'store']);
            
        }
        );
    });