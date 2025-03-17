<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get("/user", function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Auth Routes
Route::middleware('guest')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
});

Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    // Profile Routes
    Route::prefix('profile')->group(function () {
        // Retrieve profile
        Route::get('/', [ProfileController::class, 'getProfile']);
        Route::get('/lookup', [ProfileController::class, 'getProfileByUsernameOrEmail']);

        // Update profile
        Route::put('/names', [ProfileController::class, 'updateNames']);
        Route::put('/details', [ProfileController::class, 'updateOthers']);

        // Update user info
        Route::put('/user-info', [ProfileController::class, 'updateUserInfo']);
        Route::put('/password', [ProfileController::class, 'updatePassword']);

        // Delete profile/user
        Route::delete('/', [ProfileController::class, 'deleteProfile']);
        Route::delete('/user', [ProfileController::class, 'deleteUser']);
    });
});
