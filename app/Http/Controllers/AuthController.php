<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\JsonResponse;

class AuthController extends Controller
{
    public function register(Request $request) {
        $validateData = $request->validate([
            'username' => ['required', 'string', 'max:255', 'unique:users'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password'=> ['required', 'confirmed', 'min:6'],
        ]);

        $user = User::create($validateData);

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
