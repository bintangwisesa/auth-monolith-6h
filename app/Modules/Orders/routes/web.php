<?php

use Illuminate\Support\Facades\Route;
use Modules\Orders\Controllers\OrderController;

Route::middleware(['web', 'auth'])->group(function (): void {
    Route::resource('orders', OrderController::class)
        ->only(['index', 'create', 'store', 'show']);

    Route::patch('orders/{order}/status', [OrderController::class, 'updateStatus'])
        ->name('orders.status.update');
});
