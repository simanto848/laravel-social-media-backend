<?php

namespace App\Http\Controllers;

use App\Services\ProfileImageService;
use App\Models\User;
use Illuminate\Http\Request;

class ProfileImageController extends Controller
{
    protected $profileImageService;

    public function __construct(ProfileImageService $profileImageService)
    {
        $this->profileImageService = $profileImageService;
    }

    public function getAllImageOfUser(Request $request)
    {
        try {
            $username = $request->query('username');
            $userId = $username ? User::where('username', $username)->firstOrFail()->id : auth()->id();
            $images = $this->profileImageService->getAllImage($userId);
            return $this->success($images, "Images retrieved successfully");
        } catch (\Exception $exception) {
            return $this->error($exception, $exception->getMessage(), $exception->getCode() ?: 500);
        }
    }

    public function getCurrentProfileImage()
    {
        try {
            $image = $this->profileImageService->getCurrentProfileImage(auth()->id());
            return $this->success($image, "Current profile image retrieved successfully");
        } catch (\Exception $exception) {
            return $this->error($exception, $exception->getMessage(), $exception->getCode() ?: 500);
        }
    }

    public function getSingleImage($imageId)
    {
        try {
            $image = $this->profileImageService->getSingleImage($imageId);
            if (!$image) {
                throw new \Exception("Image not found", 404);
            }
            return $this->success($image, "Image retrieved successfully");
        } catch (\Exception $exception) {
            return $this->error($exception, $exception->getMessage(), $exception->getCode() ?: 500);
        }
    }

    public function createProfilePicture(Request $request)
    {
        try {
            $data = $request->validate([
                'image' => 'required|image|max:2048',
                'imageable_id' => 'required|integer',
                'imageable_type' => 'required|string',
            ]);
            $image = $this->profileImageService->storeImage($data, auth()->id());
            return $this->success($image, "Profile picture created successfully", 201);
        } catch (\Exception $exception) {
            return $this->error($exception, $exception->getMessage(), $exception->getCode() ?: 500);
        }
    }

    public function updateProfilePicture($imageId)
    {
        try {
            $image = $this->profileImageService->updateImage(auth()->id(), $imageId);
            return $this->success($image, "Profile picture updated successfully");
        } catch (\Exception $exception) {
            return $this->error($exception, $exception->getMessage(), $exception->getCode() ?: 500);
        }
    }

    public function deleteProfilePicture($imageId)
    {
        try {
            $this->profileImageService->deleteImage($imageId, auth()->id());
            return $this->success(null, "Profile picture deleted successfully");
        } catch (\Exception $exception) {
            return $this->error($exception, $exception->getMessage(), $exception->getCode() ?: 500);
        }
    }
}
