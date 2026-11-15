
<?php

use App\Http\Controllers\Api\V1\Auth\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {

    // API Health Check
    Route::get('/', [App\Http\Controllers\Api\V1\ApiController::class, 'index']);

    // Public Authentication Routes
    Route::prefix('auth')->group(function () {

        Route::post('/register', [AuthController::class, 'register'])
            ->middleware('throttle:5,1');

        Route::post('/login', [AuthController::class, 'login'])
            ->middleware('throttle:5,1');

    });

    // Protected Routes
    Route::middleware('auth:sanctum')->group(function () {

        Route::get('/auth/me', function (Request $request) {
            return response()->json([
                'success' => true,
                'message' => 'Authenticated user retrieved successfully.',
                'data' => $request->user(),
            ]);
        });

        Route::post('/auth/logout', [AuthController::class, 'logout']);

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
