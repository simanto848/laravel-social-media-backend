<?php

namespace App\Http\Controllers;

use App\Services\ProfileImageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileImageController extends Controller
{
    protected $profileImageService;

    public function __construct(ProfileImageService $profileImageService) {
        $this->profileImageService = $profileImageService;
        $this->middleware('auth:sanctum');
    }

    public function getSingleImage($imageId) {
        try {
            $image = $this->profileImageService->getSingleImage($imageId);
            if (!$image) {
                return $this->error(null, "Image not found", 404);
            }
            return $this->success($image, "Image retrieved successfully");
        } catch (\Exception $exception) {
            return $this->error($exception, $exception->getMessage());
        }
    }

    public function getCurrentProfileImage() {
        $userId = Auth::id();
        dd($userId);
        try {
            $image = $this->profileImageService->getCurrentProfileImage($userId);
            return $this->success($image, "Current profile image retrieved successfully");
        } catch (\Exception $exception) {
            return $this->error($exception, $exception->getMessage());
        }
    }

    public function getAllImageOfUser() {
        try {
            $userId = Auth::id();
            $images = $this->profileImageService->getAllImage($userId);
            return $this->success($images, "Images retrieved successfully");
        } catch (\Exception $exception) {
            return $this->error($exception, $exception->getMessage());
        }
    }

    public function createProfilePicture(Request $request) {
        try {
            $data = $request->validate([
                'image' => ['required', 'image', 'max:10240'],
            ]);
            $userId = Auth::id();
            $data['imageable_id'] = $userId;
            $data['imageable_type'] = "App\Models\Profile";

            $image = $this->profileImageService->storeImage($data, $userId);
            return $this->success($image, "Profile picture created successfully");
        } catch (\Exception $exception) {
            return $this->error($exception, $exception->getMessage(), $exception->getCode() ?: 500);
        }
    }

    public function updateProfilePicture($imageId) {
        try {
            $userId = Auth::id();

            $image = $this->profileImageService->updateImage($userId, $imageId);
            return $this->success($image, "Profile picture updated successfully");
        } catch (\Exception $exception) {
            return $this->error($exception, $exception->getMessage(), $exception->getCode() ?: 500);
        }
    }

    public function deleteProfilePicture($imageId) {
        try {
            $userId = Auth::id();
            $this->profileImageService->deleteImage($imageId, $userId);
            return $this->success(null, "Profile picture deleted successfully");
        } catch (\Exception $exception) {
            return $this->error($exception, $exception->getMessage(), $exception->getCode() ?: 500);
        }
    }
}
