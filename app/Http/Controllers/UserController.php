<?php

namespace App\Http\Controllers;

use App\Notifications\EmailVerificationWithOtp;
use App\Notifications\ExampleNotification;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        return self::onSuccess(data: $user, message: "User credentials obtained.");
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
        $user->notify(new EmailVerificationWithOtp($otp));
        $user->otp()->updateOrCreate(["otp" => $otp]);
        return self::onSuccess(
            data: $user->id,
            message: "OTP has been generated and sent to the user",
            status: 201);
    }

    public function verifyOtp(Request $request): JsonResponse
    {
        try {
            $user = Auth::user();
            $otp = $request->otp;
            $userOtp = Auth::user()->otp;

            if ($user->hasVerifiedEmail()) {
                throw new Exception(message: "User is already verified", code: 403);
            }

            if ($otp != $userOtp->otp) {
                throw new Exception(message: "Invalid OTP", code: 401);
            }

            if (Carbon::now()->timestamp >= $userOtp->expires_at) {
                throw new Exception(message: "OTP provided has expired. Generate a new OTP", code: 401);
            }

            $user->markEmailAsVerified();
            $user->otp()->delete();
            $token = $user->createToken('app_token')->plainTextToken;
            $user->save();

            return self::onSuccess(data: $token, message: "Email verified successfully");
        } catch (Exception $e) {
            return self::onError($e->getMessage(), status: $e->getCode());
        }
    }

    public function sendTestNotification()
    {
        $user = Auth::user();
        $user->notify(new ExampleNotification());
        return self::onSuccess(data: $user, message: "Email notification sent.");
    }
}
