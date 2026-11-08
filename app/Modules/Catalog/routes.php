<?php

use Illuminate\Support\Facades\Route;
use Modules\Catalog\Http\Controllers\BookController;

Route::middleware('api')->prefix('api')->group(function () {
    Route::get('books', [BookController::class, 'index']);
    Route::get('books/{id}', [BookController::class, 'show']);
    Route::delete('books/{id}', [BookController::class, 'destroy']);

    // Khusus admin: pasang middleware auth/admin dari pertemuan sebelumnya
    Route::post('admin/books/{id}/restore', [BookController::class, 'restore']);
});