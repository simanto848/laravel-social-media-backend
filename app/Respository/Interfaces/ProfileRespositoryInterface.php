<?php

namespace App\Respository\Interfaces;

use App\Models\Profile;
use App\Models\User;

interface ProfileRespositoryInterface {
    public function getProfileByUserId(int $userId);
    public function getProfileByUsername(string $username);
    public function updateNames(Profile $profile, array $data);
    public function updateOthers(Profile $profile, array $data);
    public function updateUserInfo(User $user, array $data); # Update [username, email]
    public function updatePassword(User $user, string $password);
    public function destroyProfile(Profile $profile);
    public function destroyUser(User $user);

}
