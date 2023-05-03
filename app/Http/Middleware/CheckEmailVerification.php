<?php

namespace App\Http\Middleware;

use App\Http\Controllers\Controller;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckEmailVerification
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure(Request): (Response) $next
     * @return Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        $email = $request->email;
        $user = User::where('email', $email)->first();
        if (!$user->verified) {
//            return \response()->json($user->name);
            // Redirect to the OTP route
//            return redirect()->route("get-otp", ["user" => $user]);
            return Controller::onError(
                message: "User's email isn't verified. Redirect to the generate OTP endpoint",
                status: 403
            );
        }
        return $next($request);
    }
}
