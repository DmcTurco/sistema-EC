<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\GeneralController;
use App\Http\Controllers\Api\FeedController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\PostStatsController;

Route::prefix('v1')->group(function () {
    // AUTH
    Route::prefix('auth')->group(function () {
        Route::post('register', [GeneralController::class, 'register']);
        Route::post('login', [GeneralController::class, 'login']);

        Route::middleware('auth:sanctum')->group(function () {
            Route::post('logout', [GeneralController::class, 'logout']);
            Route::get('me', [GeneralController::class, 'me']);
        });
    });

    // USER (Requiere auth)
    Route::prefix('user')
        ->middleware('auth:sanctum')
        ->group(function () {
            Route::put('profile', [GeneralController::class, 'updateProfile']);
            Route::put('change-password', [GeneralController::class, 'changePassword']);
            Route::get('orders', [GeneralController::class, 'orders']);
            Route::get('coupons', [GeneralController::class, 'coupons']);
            Route::post('coupons/{userCouponId}/mark-used', [GeneralController::class, 'markCouponAsUsed']);
        });

    // FEED (Público)
    Route::prefix('feed')->group(function () {
        Route::get('/', [FeedController::class, 'index']);
        Route::get('{id}', [FeedController::class, 'show']);
    });

    // PRODUCTS & STORES (Público)
    Route::get('products', [FeedController::class, 'products']);
    Route::get('stores', [GeneralController::class, 'stores']);

    // ORDERS
    Route::prefix('orders')->group(function () {
        Route::post('/', [OrderController::class, 'store']);
        Route::get('number/{orderNumber}', [OrderController::class, 'showByNumber']);

        Route::middleware('auth:sanctum')->group(function () {
            Route::get('/', [OrderController::class, 'index']);
            Route::get('{id}', [OrderController::class, 'show']);
            Route::put('{id}/status', [OrderController::class, 'updateStatus']);
        });
    });

    // POSTS (Público)
    Route::prefix('posts')->group(function () {
        Route::post('{id}/view', [PostStatsController::class, 'incrementViews']);
        Route::get('{id}/stats', [PostStatsController::class, 'stats']);
    });
});
