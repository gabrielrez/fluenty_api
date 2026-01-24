<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\LessonController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/ping', fn() => response()->json('pong'));

Route::group(['prefix' => 'auth'], function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/auth/check', fn() => response()->json(true));

    Route::get('/lessons', [LessonController::class, 'index']);
    Route::post('/lessons/{lesson}/start', [LessonController::class, 'start']);
    Route::post('/lessons/{lesson}/toggle-complete', [LessonController::class, 'toggleComplete']);

    Route::get('/users/profile', [UserController::class, 'profile']);
});
