<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\FriendController;
use App\Http\Controllers\NotificationController;
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

    // Public Profile Routes (read-only)
    Route::prefix('profile')->group(function () {
        Route::get('/{userId}', [ProfileController::class, 'getProfile']);
        Route::get('/username/{username}', [ProfileController::class, 'getProfileByUsername']);
    });
});

Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    // Profile Routes
    Route::prefix('profile')->middleware('profile.owner')->group(function () {
        // Restricted routes (only for profile owner)
        Route::put('/names', [ProfileController::class, 'updateNames']);
        Route::put('/details', [ProfileController::class, 'updateOthers']);
        Route::put('/user-info', [ProfileController::class, 'updateUserInfo']);
        Route::put('/password', [ProfileController::class, 'updatePassword']);
        Route::delete('/', [ProfileController::class, 'deleteProfile']);
        Route::delete('/user', [ProfileController::class, 'deleteUser']);
    });

    Route::prefix('profile-image')->group(function () {
        // Get all images for a user (publicly accessible, defaults to authenticated user if no username provided)
        Route::get('/all', [ProfileImageController::class, 'getAllImageOfUser']);

        // Restricted routes (only for profile owner)
        Route::middleware('profile.owner')->group(function () {
            Route::post('/create', [ProfileImageController::class, 'createProfilePicture']);
            Route::put('/{imageId}', [ProfileImageController::class, 'updateProfilePicture']);
            Route::delete('/{imageId}', [ProfileImageController::class, 'deleteProfilePicture']);
        });

        // Other read-only routes
        Route::get('/current', [ProfileImageController::class, 'getCurrentProfileImage']);
        Route::get('/{imageId}', [ProfileImageController::class, 'getSingleImage']);
    });

    // Friend Routes
    Route::prefix("/friends")->group(function () {
        // Send Friend Request
        Route::post('/send-request/{friendId}', [FriendController::class, 'sendFriendRequest']);

        // Accept Friend Request Route
        Route::put("/accept-request/{friendShipId}", [FriendController::class, "acceptFriendRequest"]);

        // Reject Friend Request
        Route::delete("/reject-request/{friendShipId}", [FriendController::class, "rejectFriendRequest"]);

        // Suggest Friend for sending friend request
        Route::get("/suggest", [FriendController::class, "suggestFriends"]);

        // Get friendship
        Route::get("/friendship/{friendId}", [FriendController::class, "getFriendship"]);

        // Get Friend List
        Route::get("/list", [FriendController::class, "getFriendList"]);

        // Unfriend a user
        Route::delete("/unfriend/{friendId}", [FriendController::class, "unFriend"]);

        // Get Friend Request List
        Route::get("/requests", [FriendController::class, "getFriendRequestList"]);
    });

    // Notification Routes
    Route::prefix("notifications")->group(function () {
        Route::get('/', [NotificationController::class, 'notifications']);
        Route::get('/unread-count', [NotificationController::class, 'unreadCount']);
        Route::post('/mark-as-read', [NotificationController::class, 'markAsRead']);
        Route::delete('/{notificationId}', [NotificationController::class, 'deleteNotification']);
    });
});
