<?php

use Illuminate\Support\Facades\Route;
use Modules\Content\Http\Controllers\ContentController;
use Modules\Content\Http\Controllers\TopicController;
use Modules\Content\Http\Controllers\ContentInteractionController;
use Modules\Content\Http\Controllers\UserPreferenceController;

Route::get('contents', [ContentController::class, 'index'])
    ->name('content.index');

Route::middleware('auth:sanctum')->group(function () {
    Route::get('contents/recommended', [ContentController::class, 'recommended'])
        ->name('content.recommended');

    Route::get('contents/{slug}', [ContentController::class, 'show'])
        ->middleware('premium.access')
        ->name('content.show');

    Route::middleware('permission:create content')->group(function () {
        Route::post('contents', [ContentController::class, 'store'])
            ->name('content.store');
    });

    Route::middleware('permission:update content')->group(function () {
        Route::put('contents/{content}', [ContentController::class, 'update'])
            ->name('content.update');
    });

    Route::middleware('permission:delete content')->group(function () {
        Route::delete('contents/{content}', [ContentController::class, 'destroy'])
            ->name('content.destroy');
    });

    Route::prefix('contents/{content}')->group(function () {
        Route::post('like', [ContentInteractionController::class, 'toggleLike'])
            ->name('content.like');
        Route::post('bookmark', [ContentInteractionController::class, 'toggleBookmark'])
            ->name('content.bookmark');
        Route::post('share', [ContentInteractionController::class, 'share'])
            ->name('content.share');
    });
    Route::get('topics', [TopicController::class, 'index'])
        ->name('topics.index');

    Route::get('topics/{topic}', [TopicController::class, 'show'])
        ->name('topics.show');

    Route::middleware('permission:create content')->group(function () {
        Route::post('topics', [TopicController::class, 'store'])
            ->name('topics.store');
    });

    Route::middleware('permission:update content')->group(function () {
        Route::put('topics/{topic}', [TopicController::class, 'update'])
            ->name('topics.update');
    });

    Route::middleware('permission:delete content')->group(function () {
        Route::delete('topics/{topic}', [TopicController::class, 'destroy'])
            ->name('topics.destroy');
    });

    Route::get('preferences', [UserPreferenceController::class, 'index'])
        ->name('preferences.index');
    Route::post('preferences', [UserPreferenceController::class, 'store'])
        ->name('preferences.store');
    Route::delete('preferences/topics/{topic}', [UserPreferenceController::class, 'destroy'])
        ->name('preferences.destroy');
});
