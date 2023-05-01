<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckUserId
{

    /**
     * Restricts user from accessing unauthorised routes.
     *
     * @param Request $request
     * @param Closure $next
     * @return Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        $pathId = $request->route()->parameter("user")->id;
        $userId = Auth::id();

        if ($pathId != $userId) {
            return response()->json(['error' => 'You are not authorized to access this resource.'], 403);
        }
        return $next($request);
    }
}
