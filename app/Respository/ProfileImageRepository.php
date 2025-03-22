<?php

namespace App\Respository;

use App\Models\Image;
use App\Models\Profile;
use App\Respository\Interfaces\ProfileImageRepositoryInterface;
use Illuminate\Support\Facades\Storage;

class ProfileImageRepository implements ProfileImageRepositoryInterface {
    public function getImageById($id) {
        return Image::find($id);
    }

    public function getCurrentProfileImage($userId) {
        $profile = Profile::where("user_id", $userId)->first();
        if ($profile && $profile->profile_image_id) {
            return Image::find($profile->profile_image_id);
        }
        return null;
        // $profile = Profile::where("user_id", $userId)
        // ->with('image')
        // ->first();
        // return $profile?->image;
    }

    public function getAllImageByUser($userId) {
        return Image::where("imageable_id", $userId)
            ->where('imageable_type', 'App\Models\Profile')
            ->get();
    }

    public function createImage(array $data, $userId) {
        $path = $data['image']->store('profile_images', 'public');

        return Image::create([
            'path' => $path,
            'imageable_id' => $data['imageable_id'],
            'imageable_type' => $data['imageable_type']
        ]);
    }

    public function deleteImage($imageId) {
        $image = Image::find($imageId);

        if($image) {
            Storage::disk('public')->delete($image->path);
            return $image->delete();
        }
        return false;
    }
}
