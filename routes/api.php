<?php

use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\Category\CategoryController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\Payment\PaymentCallbackController;
use App\Http\Controllers\Api\ProductController;
use Illuminate\Support\Facades\Route;
// ===============================
// PUBLIC ROUTES
// ===============================

Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/categories/{category:slug}', [CategoryController::class, 'show']);

Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/{product:slug}', [ProductController::class, 'show']);

// Midtrans callback tetap public
Route::post('payment/callback', [PaymentCallbackController::class, 'callback']);


// ===============================
// AUTH ROUTES
// ===============================

Route::prefix('auth')->group(function() {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:10,1');
});


// ===============================
// PROTECTED ROUTES
// ===============================

Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('auth/logout', [AuthController::class, 'logout']);

    Route::get('cart', [CartController::class, 'index']);
    Route::post('cart', [CartController::class, 'store']);
    Route::delete('cart/{cart}', [CartController::class, 'destroy']);

    Route::get('orders', [OrderController::class, 'index']);
    Route::post('checkout', [OrderController::class, 'store']);
    Route::get('/orders/{order_number}', [OrderController::class, 'showByNumber']);
    Route::post('/orders/{order}/cancel', [OrderController::class, 'cancel']);

    Route::middleware(['role:admin'])->group(function () {
        Route::post('/categories', [CategoryController::class, 'store']);
        Route::put('/categories/{category}', [CategoryController::class, 'update']);
        Route::delete('/categories/{category}', [CategoryController::class, 'destroy']);

        Route::post('/products', [ProductController::class, 'store']);
        Route::match(['post', 'put'], '/products/{product}', [ProductController::class, 'update']);
        Route::delete('/products/{product}', [ProductController::class, 'destroy']);
    });
});