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
        auth()->user()->email;
        $email = $request->email;
        $user = User::where('email', $email)->first();
        if (!$user->email_verified_at) {
            return Controller::onError(
                message: "User's email isn't verified. Redirect to the generate OTP endpoint",
                data: $email,
                status: 403
            );
        }
        return $next($request);
    }
}
