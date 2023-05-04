<?php

namespace App\Http\Controllers;

use App\Mail\NewOtpNotification;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class UserController extends Controller
{
    /**
     * Returns the currently authenticated user
     *
     * @return JsonResponse
     */
    public function getUser(): JsonResponse
    {
        $user = Auth::user();
        return response()->json($user, 200);
    }

    public function updateUserCredentials(Request $request): RedirectResponse
    {
        $user = Auth::user();
        $user->update($request->all());
        return redirect()->route("user-profile");
    }

    public function generateOtp(User $user): JsonResponse
    {
        $otp = rand(100_000, 999_999);
        //Send OTP to user email
        Mail::to($user)->send(new NewOtpNotification($otp));
        // Store the otp to the database
        $user->otp()->updateOrCreate(["user_id" => $user->id], ["otp" => $otp]);
        return self::onSuccess(
            data: ["user_id" => $user->id],
            message: "OTP has been generated and sent to the user",
            status: 201);
    }

    public function verifyOtp(Request $request, User $user): JsonResponse
    {
        $otp = $request->otp;
        $userOtp = $user->otp;
        if ($otp != $userOtp->otp) {
            return self::onError(message: "Invalid OTP", status: 401);
        }
        if (Carbon::now()->timestamp >= $userOtp->expires_at) {
            return self::onError(message: "OTP provided has expired. Generate a new OTP", status: 401);
        }
        $user->markEmailAsVerified();
        $user->otp()->delete();
        // Generate a new auth token
        $token = $user->createToken('app_token')->plainTextToken;
        $user->save();
        return self::onSuccess(data: $token, message: "Email verified successfully");
//        return response()->json($user);
        // return the auth token
    }
}
