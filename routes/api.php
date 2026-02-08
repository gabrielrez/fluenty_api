<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\LessonController;
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
    Route::get('/lessons/{lesson}', [LessonController::class, 'show']);
    Route::post('/lessons/{lesson}/start', [LessonController::class, 'start']);
    Route::post('/lessons/{lesson}/toggle-complete', [LessonController::class, 'toggleComplete']);

    Route::get('/words', [WordController::class, 'index']);
    Route::get('/words/{word}', [WordController::class, 'show']);
    Route::post('/words/toggle', [WordController::class, 'toggleSave']);
    Route::post('/words/save', [WordController::class, 'store']);
    Route::delete('/words/{savedWord}', [WordController::class, 'delete']);

    Route::get('/users/profile', [UserController::class, 'profile']);
});
