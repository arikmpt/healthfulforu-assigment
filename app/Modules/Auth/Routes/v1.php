<?php

use Illuminate\Support\Facades\Route;
use Modules\Auth\Http\Controllers\AuthController;

Route::post('register', [AuthController::class, 'register'])
    ->middleware('throttle:auth')
    ->name('auth.register');
Route::post('login', [AuthController::class, 'login'])
    ->middleware('throttle:auth')
    ->name('auth.login');

Route::middleware('auth:sanctum')->group(function () {
    Route::post('logout', [AuthController::class, 'logout'])
        ->name('auth.logout');
    
    Route::post('logout-device', [AuthController::class, 'logoutFromDevice'])
        ->name('auth.logout-device');
    
    Route::post('refresh', [AuthController::class, 'refresh'])
        ->name('auth.refresh');
});