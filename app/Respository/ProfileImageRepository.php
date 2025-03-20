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

    public function getImageByUser($userId) {
        $profile = Profile::where('user_id', $userId)->first();
        return $profile?->image;
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

    public function updateImage(array $data, $userId, $imageId) {
        $image = Image::where('imageable_id', $userId)
            ->where('imageable_type', 'App\Models\Profile')
            ->where('id', $data['image_id'] ?? null)
            ->first();

        if (!$image) return null;

        Storage::disk('public')->delete($image->path);
        $path = $data['image']->store('profile_images', 'public');

        $image->update(['path' => $path]);
        return $image;
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
