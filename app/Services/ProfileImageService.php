<?php

namespace App\Services;

use App\Models\Profile;
use App\Respository\ProfileImageRepository;

class ProfileImageService {
    protected $profileImageRepository;

    public function __construct(ProfileImageRepository $profileImageRepository) {
        $this->profileImageRepository = $profileImageRepository;
    }

    public function getSingleImage($id) {
        return $this->profileImageRepository->getImageById($id);
    }

    public function getImageByUser($userId) {
        return $this->profileImageRepository->getImageByUser($userId);
    }

    public function getAllImage($userId) {
        return $this->profileImageRepository->getAllImageByUser($userId);
    }

    public function storeImage(array $data, $userId) {
        $image = $this->profileImageRepository->createImage($data, $userId);

        // Update profile with the new image if it doesn't have one
        $profile = Profile::where('user_id', $userId)->first();
        if ($profile && !$profile->profile_image_id) {
            $profile->update(['profile_image_id' => $image->id]);
        }
        return $image;
    }

    public function updateImage(array $data, $userId, $imageId) {
        $image = $this->profileImageRepository->updateImage($data, $userId, $imageId);

        if (!$image) {
            throw new \Exception("Image not found or not authorized", 404);
        }

        return $image;
    }

    public function deleteImage($imageId, $userId) {
        $image = $this->profileImageRepository->getImageById($imageId);

        // Check if the image exists and belongs to the user
        if (!$image || $image->imageable_id !== $userId) {
            throw new \Exception("Image not found or not authorized", 404);
        }

        $deleted = $this->profileImageRepository->deleteImage($imageId);

        if (!$deleted) {
            throw new \Exception("Failed to delete image");
        }

        // Update profile with the next available image
        $profile = Profile::where('user_id', $userId)->first();
        if ($profile && $profile->profile_image_id === $imageId) {
            $nextImage = $this->profileImageRepository->getImageByUser($userId);
            $profile->update(['profile_image_id' => $nextImage?->id]);
        }

        return true;
    }
}
