<?php

namespace App\Respository;

use App\Models\Profile;
use App\Models\User;
use App\Respository\Interfaces\ProfileRespositoryInterface;
use Illuminate\Support\Facades\Hash;

class ProfileRepository implements ProfileRespositoryInterface {
    public function getProfileByUserId(int $userId) {
        $profile = Profile::where("user_id", $userId)->first();
        return $profile;
    }

    public function getProfileByUsernameOrEmail(string $usernameOrEmail) {
        $profile = Profile::where("username", $usernameOrEmail)->orWhere("email", $usernameOrEmail)->first();
        return $profile;
    }

    // Update first name and last name
    public function updateNames(Profile $profile, array $data) {
        $profile->update($data);
        return $profile->fresh();
    }

    // Update phone, gender, dob, bio
    public function updateOthers(Profile $profile, array $data) {
        $profile->update($data);
        return $profile->fresh();
    }

    // User email and username
    public function updateUserInfo(User $user, array $data) {
        $user->update($data);
        return $user->fresh();
    }

    public function updatePassword(User $user, string $password) {
        $user->update([
            'password' => Hash::make($password)
        ]);
        return $user->fresh();
    }

    public function destroyProfile(Profile $profile) {
        $profile->delete();
    }

    public function destroyUser(User $user) {
        $user->delete();
    }
}
