<?php

use Illuminate\Support\Facades\Route;
use Modules\Subscription\Http\Controllers\SubscriptionPlanController;
use Modules\Subscription\Http\Controllers\SubscriptionController;

Route::get('plans', [SubscriptionPlanController::class, 'index'])
    ->name('subscription.plans.index');

Route::middleware('auth:sanctum')->group(function () {
    Route::get('subscriptions/current', [SubscriptionController::class, 'current'])
        ->name('subscription.current');

    Route::post('subscriptions', [SubscriptionController::class, 'store'])
        ->name('subscription.store');

    Route::post('subscriptions/{subscription}/cancel', [SubscriptionController::class, 'cancel'])
        ->name('subscription.cancel');

    Route::get('subscriptions/history', [SubscriptionController::class, 'history'])
        ->name('subscription.history');

    Route::post('subscriptions/assign', [SubscriptionController::class, 'assignSubscription'])
        ->middleware('permission:assign subscription')
        ->name('subscription.assign');
});
