<?php

use Illuminate\Support\Facades\Route;
use Modules\Circulation\Http\Controllers\LoanController;

Route::middleware('api')->prefix('api')->group(function () {
    Route::get('loans', [LoanController::class, 'index']);
    Route::post('loans', [LoanController::class, 'store']);
    Route::post('loans/{id}/return', [LoanController::class, 'return']);
    Route::delete('loans/{id}', [LoanController::class, 'destroy']);
    Route::post('admin/loans/{id}/restore', [LoanController::class, 'restore']);
});