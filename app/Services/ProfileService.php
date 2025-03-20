<?php

namespace App\Services;

use App\Models\Profile;
use App\Models\User; // Add this missing import
use App\Respository\ProfileRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileService {
    protected $profileRepository;

    public function __construct(ProfileRepository $profileRepository) {
        $this->profileRepository = $profileRepository;
    }

    public function getProfileByUserId($userId) {
        $authUserId = Auth::id();

        if($authUserId !== $userId) {
            throw new \Exception("Unauthorized access to profile", 403);
        }

        $profile = $this->profileRepository->getProfileByUserId($userId);

        if(!$profile) {
            throw new \Exception("Profile Not Found!",404);
        }
        return $profile;
    }

    public function getProfileByUsernameOrEmail($usernameOrEmail) {
        $profile = $this->profileRepository->getProfileByUsernameOrEmail($usernameOrEmail);
        if(!$profile) {
            throw new \Exception("Profile Not Found!",404);
        }
        return $profile;
    }

    // Update first name and last name
    public function updateNames($userId, array $data) {
        $profile = $this->getProfileByUserId($userId);
        return $this->profileRepository->updateNames($profile, $data);
    }

    // Update phone, gender, dob, bio
    public function updateOthers($userId, array $data) {
        $profile = $this->getProfileByUserId($userId);
        return $this->profileRepository->updateOthers($profile, $data);
    }

    // User email and username
    public function updateUserInfo($userId, array $data){
        $user = Auth::user();

        if($user->id !== $userId) {
            throw new \Exception("Unauthorized access to update user info!",403);
        }
        return $this->profileRepository->updateUserInfo($user, $data);
    }

    public function updatePassword($userId, array $passwordData) {
        $user = Auth::user();

        if (!$user) {
            throw new \Exception("No authenticated user found!", 401);
        }

        if (!$user instanceof User) {
            throw new \Exception("Authenticated user is not an instance of App\Models\User!", 500);
        }

        if ($user->id !== $userId) {
            throw new \Exception("You are Unauthorized to update password", 403);
        }

        if(!Hash::check($passwordData['current_password'], $user->password)) {
            throw new \Exception('Current password is incorrect',401);
        }

        return $this->profileRepository->updatePassword($user, $passwordData['new_password']);
    }

    public function destroyProfile($userId) {
        $profile = $this->getProfileByUserId($userId);
        return $this->profileRepository->destroyProfile($profile);
    }

    public function destroyUser($userId) {
        $user = Auth::user();

        if( $user->id !== $userId) {
            throw new \Exception("You are Unauthorized to delete user",403);
        }
        return $this->profileRepository->destroyUser($user);
    }

}
