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
        $fields = $request->validate([
            'name' => 'required|string',
            'email' => 'required|string|unique:users,email',
            'password' => 'required|string'
        ]);
        $user = User::create([
            'name' => $fields['name'],
            'email' => $fields['email'],
            'password' => bcrypt($fields['password'])
        ]);
        $token = $user->createToken('app_token')->plainTextToken;

        $response = [
            'user' => $user,
            'token' => $token
        ];

        return response()
            ->json($response, 201);
    }

    public function login(Request $request): JsonResponse
    {
        $fields = $request->validate([
            'email' => 'required|string|exists:users,email',
            'password' => 'required|string'
        ]);
        $user = User::where('email', $fields['email'])->firstOrFail();

        if (!$user || !Hash::check($fields['password'], $user->password)) {
            return response()->json([
                "code" => "401",
                "message" => "Invalid credentials."
            ], 401);
        }
        $token = $user->createToken('app_token')->plainTextToken;
        $user->save();

        $response = [
            'code' => '201',
            'user' => $user,
            'token' => $token
        ];

        return response()->json($response, 200);
    }

    public function logout(Request $request): JsonResponse
    {
        auth()->user()->tokens()->delete();
        return response()->json([
            'message' => 'Logged out.'
        ]);
    }
}
