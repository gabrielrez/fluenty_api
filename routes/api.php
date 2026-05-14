<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\LessonController;
use App\Http\Controllers\StatisticsController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WordController;
use Illuminate\Support\Facades\Route;

Route::get('/ping', fn() => response()->json('pong'));

Route::group(['prefix' => 'auth'], function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
});

Route::post('/words/translate', [WordController::class, 'translate']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/auth/check', fn() => response()->json(true));

    Route::get('/categories', [CategoryController::class, 'index']);

    Route::get('/lessons', [LessonController::class, 'index']);

    Route::middleware('subscribed')->group(function () {
        Route::get('/lessons/{lesson}', [LessonController::class, 'show']);
        Route::post('/lessons/{lesson}/start', [LessonController::class, 'start']);
        Route::post('/lessons/{lesson}/toggle-complete', [LessonController::class, 'toggleComplete']);
        Route::post('/subscription/billing-portal', [SubscriptionController::class, 'billingPortal']);
    });

    Route::get('/subscription/plans', [SubscriptionController::class, 'plans']);
    Route::post('/subscription/checkout', [SubscriptionController::class, 'checkout']);
    Route::get('/subscription/status', [SubscriptionController::class, 'status']);

    Route::get('/statistics', [StatisticsController::class, 'index']);

    Route::get('/words', [WordController::class, 'index']);
    Route::get('/words/{word}', [WordController::class, 'show']);
    Route::post('/words/toggle', [WordController::class, 'toggleSave']);
    Route::post('/words/save', [WordController::class, 'store']);
    Route::delete('/words/{savedWord}', [WordController::class, 'delete']);

    Route::get('/me', [UserController::class, 'profile']);
    Route::put('/me/update', [UserController::class, 'update']);

    Route::prefix('admin')->middleware('admin')->group(function () {
        Route::post('/lessons', [LessonController::class, 'store']);
    });
});
