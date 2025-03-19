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
        $this->middleware('auth:sanctum');
    }

    public function getProfile(): JsonResponse
    {
        try {
            $userId = Auth::id();
            $profile = $this->profileService->getProfileByUserId($userId);
            return $this->success($profile, "Profile retrieved successfully");
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), "Failed to retrieve profile", $e->getCode() ?: JsonResponse::HTTP_NOT_FOUND);
        }
    }

    public function getProfileByUsernameOrEmail(Request $request): JsonResponse
    {
        try {
            $usernameOrEmail = $request->input('username_or_email');
            $profile = $this->profileService->getProfileByUsernameOrEmail($usernameOrEmail);
            return $this->success($profile, "Profile retrieved successfully");
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), "Failed to retrieve profile", $e->getCode() ?: JsonResponse::HTTP_NOT_FOUND);
        }
    }

    public function updateNames(Request $request): JsonResponse
    {
        try {
            $userId = Auth::id();
            $data = $request->validate([
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
            ]);
            $profile = $this->profileService->updateNames($userId, $data);
            return $this->success($profile, "Profile names updated successfully");
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), "Failed to update profile names", JsonResponse::HTTP_BAD_REQUEST);
        }
    }

    public function updateOthers(Request $request): JsonResponse
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
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), "Failed to update profile details", JsonResponse::HTTP_BAD_REQUEST);
        }
    }

    public function updateUserInfo(Request $request): JsonResponse
    {
        try {
            $userId = Auth::id();
            $data = $request->validate([
                'username' => 'required|string|max:255|unique:users,username,' . $userId,
                'email' => 'required|string|email|max:255|unique:users,email,' . $userId,
            ]);
            $user = $this->profileService->updateUserInfo($userId, $data);
            return $this->success($user, "User info updated successfully");
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), "Failed to update user info", JsonResponse::HTTP_BAD_REQUEST);
        }
    }

    public function updatePassword(Request $request): JsonResponse
    {
        try {
            $userId = Auth::id();
            $request->validate([
                'password' => 'required|string|min:8|confirmed',
            ]);
            $user = $this->profileService->updatePassword($userId, $request->password);
            return $this->success($user, "Password updated successfully");
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), "Failed to update password", JsonResponse::HTTP_BAD_REQUEST);
        }
    }

    public function deleteProfile(): JsonResponse
    {
        try {
            $userId = Auth::id();
            $this->profileService->destroyProfile($userId);
            return $this->success(null, "Profile deleted successfully");
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), "Failed to delete profile", JsonResponse::HTTP_BAD_REQUEST);
        }
    }

    public function deleteUser(): JsonResponse
    {
        try {
            $userId = Auth::id();
            $this->profileService->destroyUser($userId);
            return $this->success(null, "User deleted successfully");
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), "Failed to delete user", JsonResponse::HTTP_BAD_REQUEST);
        }
    }
}
