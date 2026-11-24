<?php

use App\Http\Controllers\CartController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

// Головна сторінка Laravel
Route::get('/', function () {
    return view('welcome');
})->name('home');

// Перевірка працездатності системи
Route::get('/health', function () {
    return response()->json([
        'status' => 'OK',
        'application' => 'Clothing & Accessories Store',
        'timestamp' => now()->toIso8601String(),
    ]);
})->name('health');

// Маршрути каталогу товарів
Route::prefix('catalog')
    ->name('catalog.')
    ->group(function () {
        Route::get('/', [
            ProductController::class,
            'index',
        ])->name('index');

        Route::post('/', [
            ProductController::class,
            'store',
        ])
            ->middleware('catalog.client')
            ->name('store');

        Route::get('/{id}', [
            ProductController::class,
            'show',
        ])
            ->whereNumber('id')
            ->name('show');
    });

// Демонстраційний кошик
Route::get('/cart/summary', [
    CartController::class,
    'summary',
])->name('cart.summary');
