<?php

declare(strict_types=1);

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\HealthController;
use App\Http\Controllers\Api\ProductApiController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    // Публічні маршрути
    Route::get('/health', [HealthController::class, 'index']);

    Route::post('/register', [AuthController::class, 'register'])
        ->middleware('throttle:6,1');

    Route::post('/login', [AuthController::class, 'login'])
        ->middleware('throttle:6,1');

    Route::get('/products', [ProductApiController::class, 'index']);

    Route::get('/products/{id}', [ProductApiController::class, 'show'])
        ->whereNumber('id');

    // Маршрути з авторизацією Sanctum
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/me', [AuthController::class, 'me']);

        Route::post('/logout', [AuthController::class, 'logout']);

        Route::post('/products', [ProductApiController::class, 'store']);

        Route::put('/products/{id}', [ProductApiController::class, 'update'])
            ->whereNumber('id');

        Route::delete('/products/{id}', [ProductApiController::class, 'destroy'])
            ->whereNumber('id');
    });
});
