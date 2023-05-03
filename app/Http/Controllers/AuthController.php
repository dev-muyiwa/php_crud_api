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

        // Add a middleware that checks if the user email is verified

        // Redirect to the OTP route

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
            'user' => $user,
            'token' => $token
        ];

        return response()->json($response, 200);
    }

    public function generateOtp(User $user): JsonResponse
    {
//        $user = User::findOrFail($request->user);
        $otp = rand(100_000, 999_999);
//        Mail::to($user)->send(new NewOtpNotification($otp));
        // Generate a new otp every 5 minute.
        $test = $user->otp()->create(["otp" => $otp]);
        // Store the otp to the database
        return self::onSuccess(data: $test, message: "OTP has been generated and sent to the user", status: 201);
//        return redirect()->route("verify-otp");
    }

    public function verifyOtp(Request $request)
    {
        // Check if the otp from the query is equal to that from the db
        return response()->json($request->otp);
        // return the auth token
    }

    public function logout(Request $request): JsonResponse
    {
        auth()->user()->tokens()->delete();
        return response()->json([
            'message' => 'Logged out.'
        ]);
    }
}
