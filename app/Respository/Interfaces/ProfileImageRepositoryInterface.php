<?php

namespace App\Respository\Interfaces;

interface ProfileImageRepositoryInterface {
    public function getImageById($id);
    public function getImageByUser($userId);
    public function getAllImageByUser($userId);
    public function createImage(array $data, $userId);
    public function updateImage(array $data, $userId, $imageId);
    public function deleteImage($imageId);
}
