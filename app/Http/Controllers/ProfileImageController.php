<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreImageRequest;
use App\Http\Requests\UpdateImageRequest;
use App\Models\Profile;
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

    public function getImageByUserId() {
        try {
            $userId = Auth::id();
            $image = $this->profileImageService->getImageByUser($userId);
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

    public function createProfilePicture(StoreImageRequest $request) {
        try {
            $userId = Auth::id();
            $data = $request->validated();
            $data['imageable_id'] = $userId;
            $data['imageable_type'] = "App\Models\Profile";

            $image = $this->profileImageService->storeImage($data, $userId);
            return $this->success($image, "Profile picture created successfully");
        } catch (\Exception $exception) {
            return $this->error($exception, $exception->getMessage(), $exception->getCode() ?: 500);
        }
    }

    public function updateProfilePicture(UpdateImageRequest $request, $imageId) {
        try {
            $userId = Auth::id();
            $data = $request->validated();
            $data['imageable_id'] = $userId;
            $data['imageable_type'] = 'App\Models\Profile';

            $image = $this->profileImageService->updateImage($data, $userId, $imageId);
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
