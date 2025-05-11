<?php

declare(strict_types=1);

use App\Http\Controllers\Api\V1\Auth\RefreshTokenController;
use App\Http\Controllers\Api\V1\Auth\SignInController;
use App\Http\Controllers\Api\V1\Auth\SignOutController;
use App\Http\Controllers\Api\V1\Auth\SignUpController;
use App\Http\Controllers\Api\V1\Manage\OrderController;
use App\Http\Controllers\Api\V1\Manage\ProductController;
use App\Http\Controllers\Api\V1\Manage\StockChangeController;
use App\Http\Controllers\Api\V1\Manage\WarehouseController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'auth', 'throttle:10,1'], function (): void {
    Route::group(['middleware' => ['guest:api']], function (): void {
        Route::post('/register', SignUpController::class)->name('auth.register');
        Route::post('/login', SignInController::class)->name('auth.login');
    });

    Route::middleware(['jwt'])->group(function (): void {
        Route::post('/logout', SignOutController::class)->name('auth.logout');
        Route::post('/token/refresh', RefreshTokenController::class)->name('auth.refresh');
    });
});

Route::group([
    'prefix' => 'manage',
    // 'middleware' => ['jwt', 'throttle:60,1'],
], function (): void {
    Route::get('/warehouses', WarehouseController::class);
    Route::get('/products', ProductController::class);
    Route::get('/stock-changes', StockChangeController::class);

    Route::group(['prefix' => 'orders'], function (): void {
        Route::get('/', [OrderController::class, 'index']);
        Route::post('/', [OrderController::class, 'store']);
        Route::put('/{order}', [OrderController::class, 'update']);
        Route::post('/{order}/complete', [OrderController::class, 'complete']);
        Route::post('/{order}/cancel', [OrderController::class, 'cancel']);
        Route::post('/{order}/activate', [OrderController::class, 'activate']);
    });
});
