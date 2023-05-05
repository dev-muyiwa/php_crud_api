<?php

namespace App\Http\Middleware;

use App\Http\Controllers\Controller;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
        if (!Auth::user()->email_verified_at) {
            return Controller::onError(
                message: "User's email isn't verified. Redirect to the generate OTP endpoint",
                data: Auth::user()->email,
                status: 403
            );
        }
        return $next($request);
    }
}
