<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Response;

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

        return Response::json([
            'token'=> $token,
            'user'=> $user
        ]);
    }

    public function login(Request $request) {
        $validate = $request->validate([
            'username'=> ['required', 'string', 'max:255', 'exists:users'],
            'password'=> ['required', 'min:6'],
        ]);

        $user = User::where('username', $request->username)->first();

        if(empty($user) || !Hash::check($request->password, $user->password)) {
            return Response::json([
                'error'=> 'Invalid credentials',
            ]);
        }

        $token = $user->createToken($user->username)->plainTextToken;

        return Response::json([
            'token'=> $token,
            'user'=> $user
        ]);
    }

    public function logout(Request $request) {
        $request->user()->tokens()->delete();
        return Response::json("Logged out successfully");
    }
}
