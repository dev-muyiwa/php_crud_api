<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request): JsonResponse
    {
        if (empty($request->name) || empty($request->email) || empty($request->password)) {
            return self::onError(message: "Invalid credentials. Try again.", status: 401);
        }
        $user = User::where("email", $request->email)->first();
        if ($user) {
            return self::onError(message: "User already exists.", status: 409);
        }

        $fields = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string'
        ]);
        $user = User::create([
            'name' => $fields['name'],
            'email' => $fields['email'],
            'password' => bcrypt($fields['password'])
        ]);
//        $token = $user->createToken('app_token')->plainTextToken;
//        $user->save();

        $response = ["user" => $user];

        return self::onSuccess(
            data: $response,
            message: "Account created successfully.",
            status: 201);
    }

    public function login(Request $request): JsonResponse
    {
        if (empty($request->email) || empty($request->password)) {
            return self::onError(message: "Invalid credentials. Try again.", status: 401);
        }

        $user = User::where("email", $request->email)->first();

        if (!$user){
            return self::onError(message: "Login failed. Invalid username or password.", status: 401);
        }

        if (!Hash::check($request->password, $user->password)) {
            return self::onError(message: "Incorrect credentials. Try again.", status: 401);
        }

        $token = $user->createToken('app_token')->plainTextToken;
        $user->save();
        $response = ["token" => $token];

        return self::onSuccess(data: $response, message: "Login successful.");
    }

    public function logout(): JsonResponse
    {
        auth()->user()->tokens()->delete();
        return self::onSuccess(data: null, message: "User logged out successfully.");
    }
}
