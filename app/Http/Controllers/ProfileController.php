<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProfileRequest;
use App\Http\Requests\UpdateProfileRequest;
use App\Models\Profile;
use App\Services\ProfileService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    protected $profileService;

    public function __construct(ProfileService $profileService){
        $this->profileService = $profileService;
        // $this->middleware('auth:sanctum');
    }

    public function getProfile($userId)
    {
        try {
            $profile = $this->profileService->getProfileByUserId($userId);
            return $this->success($profile, "Profile retrieved successfully");
        } catch (\Exception $exception) {
            return $this->error($exception, $exception->getMessage(), $exception->getCode() ?: 500);
        }
    }

    public function getProfileByUsername(Request $request, $username)
    {
        try {
            $profile = $this->profileService->getProfileByUsername($username);
            return $this->success($profile, "Profile retrieved successfully");
        } catch (\Exception $exception) {
            return $this->error($exception, $exception->getMessage(), $exception->getCode() ?: 500);
        }
    }

    public function updateNames(Request $request)
    {
        try {
            $userId = Auth::id();
            $data = $request->validate([
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
            ]);
            $profile = $this->profileService->updateNames($userId, $data);
            return $this->success($profile, "Profile names updated successfully");
        } catch (\Exception $exception) {
            return $this->error($exception, $exception->getMessage(), $exception->getCode() ?: 500);
        }
    }

    public function updateOthers(Request $request)
    {
        try {
            $userId = Auth::id();
            $data = $request->validate([
                'phone_number' => ['nullable', 'string', 'max:25', 'regex:/^([0-9\s\-\+\(\)]*)$/', 'unique:profiles,phone_number,' . $userId],
                'bio' => ['nullable', 'string', 'max:255'],
                'gender' => ['required', 'string', 'in:male,female,other'],
                'dob' => ['required', 'date', 'before:today'],
            ]);
            $profile = $this->profileService->updateOthers($userId, $data);
            return $this->success($profile, "Profile details updated successfully");
        } catch (\Exception $exception) {
            return $this->error($exception, $exception->getMessage(), $exception->getCode() ?: 500);
        }
    }

    public function updateUserInfo(Request $request)
    {
        try {
            $userId = Auth::id();
            $data = $request->validate([
                'username' => 'required|string|max:255|unique:users,username,' . $userId,
                'email' => 'required|string|email|max:255|unique:users,email,' . $userId,
            ]);
            $user = $this->profileService->updateUserInfo($userId, $data);
            return $this->success($user, "User info updated successfully");
        } catch (\Exception $exception) {
            return $this->error($exception, $exception->getMessage(), $exception->getCode() ?: 500);
        }
    }

    public function updatePassword(Request $request)
    {
        try {
            $userId = Auth::id();
            $validateData = $request->validate([
                'current_password' => ['required', 'string'],
                'new_password' => ['required', 'string', 'min:6', 'confirmed'],
            ]);
            $user = $this->profileService->updatePassword($userId, $validateData);
            return $this->success($user, "Password updated successfully");
        } catch (\Exception $exception) {
            return $this->error($exception, $exception->getMessage(), $exception->getCode() ?: 500);
        }
    }

    public function deleteProfile()
    {
        try {
            $userId = Auth::id();
            $this->profileService->destroyProfile($userId);
            return $this->success(null, "Profile deleted successfully");
        } catch (\Exception $exception) {
            return $this->error($exception, $exception->getMessage(), $exception->getCode() ?: 500);
        }
    }

    public function deleteUser()
    {
        try {
            $userId = Auth::id();
            $this->profileService->destroyUser($userId);
            return $this->success(null, "User deleted successfully");
        } catch (\Exception $exception) {
            return $this->error($exception, $exception->getMessage(), $exception->getCode() ?: 500);
        }
    }
}
