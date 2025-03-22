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

    public function getCurrentProfileImage($userId) {
        $image = $this->profileImageRepository->getAllImageByUser($userId);

        if (!$image) {
            throw new \Exception("Image nai not found", 404);
        }
        return $image;
    }

    public function getAllImage($userId) {
        return $this->profileImageRepository->getAllImageByUser($userId);
    }

    public function storeImage(array $data, $userId) {
        // Update profile with the new image if it doesn't have one
        $profile = Profile::where('user_id', $userId)->first();
        if ($profile) {
            $image = $this->profileImageRepository->createImage($data, $userId);
            $profile->update(['profile_image_id' => $image->id]);
        }
        return $image;
    }

    public function updateImage($userId, $imageId) {
        $image = $this->profileImageRepository->getImageById($imageId);

        if (!$image) {
            throw new \Exception("Image not found or not authorized", 404);
        }
        if($image->imageable_id !== $userId) {
            throw new \Exception("You are not authorized to update this image", 403);
        }

        Profile::where('user_id', $userId)->update(['profile_image_id' => $imageId]);
        return $image;

    }

    public function deleteImage($imageId, $userId) {
        $image = $this->profileImageRepository->getImageById($imageId);

        if (!$image) {
            throw new \Exception("Image not found!", 404);
        }
        if($image->imageable_id !== $userId) {
            throw new \Exception("You are not authorized to update this image!", 403);
        }

        $profile = Profile::where('user_id', $userId)->first();

        if($profile->profile_image_id == $image->id) {
            $deleted = $this->profileImageRepository->deleteImage($imageId);

            if( $deleted ) {
                $remainingImages = $this->profileImageRepository->getAllImageByUser($userId)
                ->where('id', '!=', $imageId);

                // Get the most recent image from the remaining ones
                $nextImage = $remainingImages->sortByDesc('created_at')->first();

                if ($nextImage) {
                    $this->updateImage($userId, $nextImage->id);
                }
            }
        }

        return true;
    }
}
