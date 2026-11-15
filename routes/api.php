
<?php

use App\Http\Controllers\Api\V1\ApiController;
use App\Http\Controllers\Api\V1\Auth\AuthController;
use App\Http\Controllers\Api\V1\Business\BusinessController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {

    // API Health Check
    Route::get('/', [ApiController::class, 'index']);

    // Public Authentication Routes
    Route::prefix('auth')->group(function () {

        Route::post('/register', [AuthController::class, 'register'])
            ->middleware('throttle:5,1');

        Route::post('/login', [AuthController::class, 'login'])
            ->middleware('throttle:5,1');

    });

    // Protected Routes
    Route::middleware('auth:sanctum')->group(function () {

        // Authentication
        Route::prefix('auth')->group(function () {

            Route::get('/me', [AuthController::class, 'me']);

            Route::post('/logout', [AuthController::class, 'logout']);

        });

        // Business Management
        Route::prefix('businesses')->group(function () {

            Route::post('/', [BusinessController::class, 'store']);

            Route::get('/', [BusinessController::class, 'index']);

            Route::get('/{id}', [BusinessController::class, 'show']);

            Route::put('/{id}', [BusinessController::class, 'update']);

            Route::post('/{id}/switch', [BusinessController::class, 'switch']);

        });

        // Active Business
        Route::get('/active-business', [BusinessController::class, 'active'])
            ->middleware('active.business');

    });

});

// API Fallback
Route::fallback(function () {

    return response()->json([
        'success' => false,
        'message' => 'API endpoint not found.',
        'error' => 'The requested API route does not exist.',
    ], 404);

});
