<?php

namespace App\Http\Controllers;

use App\Events\UserRegistered;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    public function register(Request $request) {
        $validateData = $request->validate([
            'username' => ['required', 'string', 'max:255', 'unique:users'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password'=> ['required', 'confirmed', 'min:6'],
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'gender' => ['required', 'in:male,female,other'],
            'dob' => ['required', 'date', 'before:today'],
        ]);


        $userData = [
                'username' => $validateData['username'],
                'email' => $validateData['email'],
                'password' => $validateData['password'],
            ];
        $user = User::create($userData);

        if ($user) {
            Profile::create([
                "user_id" => $user->id,
                "first_name" => $validateData['first_name'],
                'last_name'=> $validateData['last_name'],
                'gender'=> $validateData['gender'],
                "dob" => $validateData['dob']
            ]);
        }

        $token = $user->createToken($user->username)->plainTextToken;

        return $this->success(
            data: [
                'token' => $token,
                'user' => $user
            ],
            message: "User registered successfully",
            statusCode: JsonResponse::HTTP_CREATED
        );
    }

    public function login(Request $request) {
        $validate = $request->validate([
            'username'=> ['required', 'string', 'max:255', 'exists:users'],
            'password'=> ['required', 'min:6'],
        ]);

        $user = User::where('username', $request->username)->first();

        if(empty($user) || !Hash::check($request->password, $user->password)) {
            return $this->error(
                error: 'Invalid credentials',
                message: "The provided credentials are incorrect",
                statusCode: JsonResponse::HTTP_UNAUTHORIZED
            );
        }

        $token = $user->createToken($user->username)->plainTextToken;

        return $this->success(
            data: [
                'token' => $token,
                'user' => $user
            ],
            message: "User logged in successfully",
            statusCode: JsonResponse::HTTP_OK
        );
    }

    public function logout(Request $request) {
        $request->user()->tokens()->delete();
        return $this->success(
            data: [],
            message: "User logged out successfully",
            statusCode: 200
        );
    }
}
