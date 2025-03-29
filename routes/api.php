<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\FriendController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProfileImageController;
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

    Route::prefix('profile-image')->group(function () {
        // Get all images for the authenticated user
        Route::get('/all', [ProfileImageController::class, 'getAllImageOfUser']);

        // Create a new profile picture
        Route::post('/create', [ProfileImageController::class, 'createProfilePicture']);

        // Get the current user's profile image
        Route::get('/current', [ProfileImageController::class, 'getCurrentProfileImage']); # Not working

        // Get a single image by ID
        Route::get('/{imageId}', [ProfileImageController::class, 'getSingleImage']);

        // Update an existing profile picture
        Route::put('/{imageId}', [ProfileImageController::class, 'updateProfilePicture']);

        // Delete a profile picture
        Route::delete('/{imageId}', [ProfileImageController::class, 'deleteProfilePicture']);
    });

    // Friend Routes
    Route::prefix("/friends")->group(function () {
        // Send Friend Request
        Route::post('/send-request/{friendId}', [FriendController::class, 'sendFriendRequest']);

        // Accept Friend Request Route
        Route::put("/accept/{friendShipId}", [FriendController::class, "acceptFriendRequest"]);

        // Reject Friend Request
        Route::delete("/reject/{friendShipId}", [FriendController::class, "rejectFriendRequest"]);

        // Suggest Friend for sending friend request
        Route::get("/suggest", [FriendController::class, "suggestFriends"]);
    });
});
