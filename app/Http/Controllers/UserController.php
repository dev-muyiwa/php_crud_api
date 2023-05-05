<?php

namespace App\Http\Controllers;

use App\Mail\NewOtpNotification;
use App\Notifications\ExampleNotification;
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

    public function generateOtp(): JsonResponse
    {
        $user = Auth::user();
        $otp = rand(100_000, 999_999);
        Mail::to($user)->send(new NewOtpNotification($otp));
        $user->otp()->updateOrCreate(["user_id" => $user->id], ["otp" => $otp]);
        return self::onSuccess(
            data: $user->id,
            message: "OTP has been generated and sent to the user",
            status: 201);
    }

    public function verifyOtp(Request $request): JsonResponse
    {
        $user = Auth::user();
        $otp = $request->otp;
        $userOtp = Auth::user()->otp;
        if ($otp != $userOtp->otp) {
            return self::onError(message: "Invalid OTP", status: 401);
        }
        if (Carbon::now()->timestamp >= $userOtp->expires_at) {
            return self::onError(message: "OTP provided has expired. Generate a new OTP", status: 401);
        }
        $user->markEmailAsVerified();
        $user->otp()->delete();
        $token = $user->createToken('app_token')->plainTextToken;
        $user->save();
        return self::onSuccess(data: $token, message: "Email verified successfully");
    }

    public function sendTestNotification()
    {
        $user = Auth::user();
        $user->notify(new ExampleNotification());
        return self::onSuccess(data: $user, message: "Email notification sent.");
    }
}
