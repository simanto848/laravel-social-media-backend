<?php

namespace App\Respository\Interfaces;

interface ProfileImageRepositoryInterface {
    public function getImageById($id);
    public function getCurrentProfileImage($userId);
    public function getAllImageByUser($userId);
    public function createImage(array $data, $userId);
    public function deleteImage($imageId);
}
