<?php

use App\Http\Controllers\CartController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DeliveryController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ReviewController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
    ]);
})->name('health');

// Маршрути товарів
Route::prefix('catalog')->name('catalog.')->group(function () {
    Route::get('/', [ProductController::class, 'index'])
        ->name('index');

    Route::post('/', [ProductController::class, 'store'])
        ->middleware('catalog.client')
        ->name('store');

    Route::get('/{id}', [ProductController::class, 'show'])
        ->whereNumber('id')
        ->name('show');

    Route::put('/{id}', [ProductController::class, 'update'])
        ->whereNumber('id')
        ->middleware('catalog.client')
        ->name('update');
});

// Маршрут кошика
Route::get('/cart/summary', [CartController::class, 'summary'])
    ->name('cart.summary');

// Маршрути категорій
Route::prefix('categories')->name('categories.')->group(function () {
    Route::get('/', [CategoryController::class, 'index'])
        ->name('index');

    Route::get('/{id}', [CategoryController::class, 'show'])
        ->whereNumber('id')
        ->name('show');

    Route::post('/', [CategoryController::class, 'store'])
        ->middleware('catalog.client')
        ->name('store');
});

// Маршрути замовлень
Route::prefix('orders')->name('orders.')->group(function () {
    Route::get('/', [OrderController::class, 'index'])
        ->name('index');

    Route::get('/{id}', [OrderController::class, 'show'])
        ->whereNumber('id')
        ->name('show');

    Route::post('/', [OrderController::class, 'store'])
        ->middleware('catalog.client')
        ->name('store');
});

Route::prefix('payments')->name('payments.')->group(function () {
    Route::get('/', [PaymentController::class, 'index'])
        ->name('index');

    Route::get('/{id}', [PaymentController::class, 'show'])
        ->whereNumber('id')
        ->name('show');
});

Route::prefix('deliveries')->name('deliveries.')->group(function () {
    Route::get('/', [DeliveryController::class, 'index'])
        ->name('index');

    Route::get('/{id}', [DeliveryController::class, 'show'])
        ->whereNumber('id')
        ->name('show');
});

Route::prefix('reviews')->name('reviews.')->group(function () {
    Route::get('/', [ReviewController::class, 'index'])
        ->name('index');

    Route::get('/{id}', [ReviewController::class, 'show'])
        ->whereNumber('id')
        ->name('show');

    Route::post('/', [ReviewController::class, 'store'])
        ->middleware('catalog.client')
        ->name('store');
});
