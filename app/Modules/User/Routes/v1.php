<?php

use Illuminate\Support\Facades\Route;
use Modules\User\Http\Controllers\UserController;

Route::middleware('auth:sanctum')->group(function () {
    Route::get('me', [UserController::class, 'me'])
            ->name('user.me');
});