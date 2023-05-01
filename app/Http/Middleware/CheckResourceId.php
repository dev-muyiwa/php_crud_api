<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckResourceId
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $pathId = $request->route()->parameter("post")->user_id;
        $userId = Auth::id();

        if ($pathId != $userId) {
            return response()->json(['error' => 'You are not authorized to access this resource.'], 403);
        }
        return $next($request);
    }
}
