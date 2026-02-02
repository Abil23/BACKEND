<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\GameController;
use App\Http\Controllers\ScoreController;
use App\Http\Controllers\UserController; // <--- JANGAN LUPA INI

Route::prefix('v1')->group(function () {
    
    // Auth
    Route::post('auth/signup', [AuthController::class, 'signup']);
    Route::post('auth/signin', [AuthController::class, 'signin']);

    // Public Games & Score
    Route::get('games', [GameController::class, 'index']);
    Route::get('games/{slug}', [GameController::class, 'show']);
    Route::get('games/{slug}/scores', [ScoreController::class, 'index']);

    // Protected (Login Required)
    Route::middleware('auth:sanctum')->group(function () {
        
        Route::post('auth/signout', [AuthController::class, 'signout']);
        
        // Game Management
        Route::post('games', [GameController::class, 'store']);
        Route::put('games/{slug}', [GameController::class, 'update']);
        Route::delete('games/{slug}', [GameController::class, 'destroy']);
        Route::post('games/{slug}/upload', [GameController::class, 'uploadVersion']);
        
        // Score
        Route::post('games/{slug}/scores', [ScoreController::class, 'store']);

        // User Management (NEW)
        Route::get('users', [UserController::class, 'index']);      // List Users
        Route::put('users/{id}', [UserController::class, 'update']); // Edit User
        Route::delete('users/{id}', [UserController::class, 'destroy']); // Hapus User
    });

});