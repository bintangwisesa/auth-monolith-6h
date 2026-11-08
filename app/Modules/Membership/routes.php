<?php

use Illuminate\Support\Facades\Route;
use Modules\Membership\Http\Controllers\MemberController;

Route::middleware('api')->prefix('api')->group(function () {
    Route::get('members', [MemberController::class, 'index']);
    Route::delete('members/{id}', [MemberController::class, 'destroy']);
    Route::post('admin/members/{id}/restore', [MemberController::class, 'restore']);
});