<?php

use App\Http\Controllers\CartController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DeliveryController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\ShopCartController;
use App\Http\Controllers\ShopCategoryController;
use App\Http\Controllers\ShopDeliveryController;
use App\Http\Controllers\ShopOrderController;
use App\Http\Controllers\ShopPaymentController;
use App\Http\Controllers\ShopProductController;
use App\Http\Controllers\ShopReviewController;
use App\Http\Controllers\ShopUserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home');
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

Route::prefix('shop/catalog')
    ->name('shop.catalog.')
    ->group(function () {
        Route::get('/', [ShopProductController::class, 'index'])
            ->name('index');

        Route::get('/create', [ShopProductController::class, 'create'])
            ->name('create');

        Route::post('/', [ShopProductController::class, 'store'])
            ->name('store');

        Route::get('/{id}/edit', [ShopProductController::class, 'edit'])
            ->whereNumber('id')
            ->name('edit');

        Route::put('/{id}', [ShopProductController::class, 'update'])
            ->whereNumber('id')
            ->name('update');

        Route::delete('/{id}', [ShopProductController::class, 'destroy'])
            ->whereNumber('id')
            ->name('destroy');

        Route::get('/{id}', [ShopProductController::class, 'show'])
            ->whereNumber('id')
            ->name('show');
    });

Route::prefix('shop/orders')
    ->name('shop.orders.')
    ->group(function () {
        Route::get('/', [ShopOrderController::class, 'index'])
            ->name('index');

        Route::get('/create', [ShopOrderController::class, 'create'])
            ->name('create');

        Route::post('/', [ShopOrderController::class, 'store'])
            ->name('store');

        Route::get('/{id}/edit', [ShopOrderController::class, 'edit'])
            ->whereNumber('id')
            ->name('edit');

        Route::put('/{id}', [ShopOrderController::class, 'update'])
            ->whereNumber('id')
            ->name('update');

        Route::delete('/{id}', [ShopOrderController::class, 'destroy'])
            ->whereNumber('id')
            ->name('destroy');
    });

Route::prefix('shop/categories')
    ->name('shop.categories.')
    ->group(function () {
        Route::get('/', [ShopCategoryController::class, 'index'])
            ->name('index');

        Route::get('/create', [ShopCategoryController::class, 'create'])
            ->name('create');

        Route::post('/', [ShopCategoryController::class, 'store'])
            ->name('store');

        Route::get('/{id}/edit', [ShopCategoryController::class, 'edit'])
            ->whereNumber('id')
            ->name('edit');

        Route::put('/{id}', [ShopCategoryController::class, 'update'])
            ->whereNumber('id')
            ->name('update');

        Route::delete('/{id}', [ShopCategoryController::class, 'destroy'])
            ->whereNumber('id')
            ->name('destroy');
    });

Route::prefix('shop/carts')
    ->name('shop.carts.')
    ->group(function () {
        Route::get('/', [ShopCartController::class, 'index'])
            ->name('index');

        Route::get('/{id}', [ShopCartController::class, 'show'])
            ->whereNumber('id')
            ->name('show');

        Route::post('/{id}/items', [ShopCartController::class, 'storeItem'])
            ->whereNumber('id')
            ->name('items.store');

        Route::put('/{id}/items/{itemId}', [ShopCartController::class, 'updateItem'])
            ->whereNumber(['id', 'itemId'])
            ->name('items.update');

        Route::delete('/{id}/items/{itemId}', [ShopCartController::class, 'destroyItem'])
            ->whereNumber(['id', 'itemId'])
            ->name('items.destroy');
    });

Route::prefix('shop/reviews')
    ->name('shop.reviews.')
    ->group(function () {
        Route::get('/', [ShopReviewController::class, 'index'])
            ->name('index');

        Route::get('/create', [ShopReviewController::class, 'create'])
            ->name('create');

        Route::post('/', [ShopReviewController::class, 'store'])
            ->name('store');

        Route::get('/{id}/edit', [ShopReviewController::class, 'edit'])
            ->whereNumber('id')
            ->name('edit');

        Route::put('/{id}', [ShopReviewController::class, 'update'])
            ->whereNumber('id')
            ->name('update');

        Route::delete('/{id}', [ShopReviewController::class, 'destroy'])
            ->whereNumber('id')
            ->name('destroy');
    });

Route::prefix('shop/users')
    ->name('shop.users.')
    ->group(function () {
        Route::get('/', [ShopUserController::class, 'index'])
            ->name('index');

        Route::get('/create', [ShopUserController::class, 'create'])
            ->name('create');

        Route::post('/', [ShopUserController::class, 'store'])
            ->name('store');

        Route::get('/{id}/edit', [ShopUserController::class, 'edit'])
            ->whereNumber('id')
            ->name('edit');

        Route::put('/{id}', [ShopUserController::class, 'update'])
            ->whereNumber('id')
            ->name('update');

        Route::delete('/{id}', [ShopUserController::class, 'destroy'])
            ->whereNumber('id')
            ->name('destroy');
    });

Route::prefix('shop/payments')
    ->name('shop.payments.')
    ->group(function () {
        Route::get('/', [ShopPaymentController::class, 'index'])
            ->name('index');

        Route::get('/create', [ShopPaymentController::class, 'create'])
            ->name('create');

        Route::post('/', [ShopPaymentController::class, 'store'])
            ->name('store');

        Route::get('/{id}/edit', [ShopPaymentController::class, 'edit'])
            ->whereNumber('id')
            ->name('edit');

        Route::put('/{id}', [ShopPaymentController::class, 'update'])
            ->whereNumber('id')
            ->name('update');

        Route::delete('/{id}', [ShopPaymentController::class, 'destroy'])
            ->whereNumber('id')
            ->name('destroy');
    });

Route::prefix('shop/deliveries')
    ->name('shop.deliveries.')
    ->group(function () {
        Route::get('/', [ShopDeliveryController::class, 'index'])
            ->name('index');

        Route::get('/create', [ShopDeliveryController::class, 'create'])
            ->name('create');

        Route::post('/', [ShopDeliveryController::class, 'store'])
            ->name('store');

        Route::get('/{id}/edit', [ShopDeliveryController::class, 'edit'])
            ->whereNumber('id')
            ->name('edit');

        Route::put('/{id}', [ShopDeliveryController::class, 'update'])
            ->whereNumber('id')
            ->name('update');

        Route::delete('/{id}', [ShopDeliveryController::class, 'destroy'])
            ->whereNumber('id')
            ->name('destroy');
    });
