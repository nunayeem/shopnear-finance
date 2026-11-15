<?php

use App\Http\Controllers\Api\V1\Auth\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {

    Route::get('/', function () {
        return response()->json([
            'success' => true,
            'message' => 'API v1 is working.',
        ]);
    });

    // Protected routes
    Route::middleware('auth:sanctum')->group(function () {

        Route::get('/auth/me', function (Request $request) {
            return response()->json([
                'success' => true,
                'message' => 'Authenticated user retrieved successfully.',
                'data' => $request->user(),
            ]);
        });

    });


    Route::prefix('auth')->group(function () {

        // Public routes
        Route::post('/register', [AuthController::class, 'register'])
            ->middleware('throttle:5,1');

        // Web authentication
        Route::post('/web/login', [AuthController::class, 'webLogin'])
            ->middleware('throttle:5,1');

        // Mobile authentication
        Route::post('/mobile/login', [AuthController::class, 'mobileLogin'])
            ->middleware('throttle:5,1');

        // Protected routes
        Route::middleware('auth:sanctum')->group(function () {

            Route::get('/me', [AuthController::class, 'me']);

            Route::post('/web/logout', [AuthController::class, 'webLogout']);

            Route::post('/mobile/logout', [AuthController::class, 'mobileLogout']);

        });
    });

});
