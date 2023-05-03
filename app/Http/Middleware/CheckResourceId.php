<?php

namespace App\Http\Middleware;

use Closure;
use http\Env;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PHPUnit\Event\Telemetry\System;
use Symfony\Component\HttpFoundation\Response;

class CheckResourceId
{

    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        $post = $request->route()->parameter("post");

        if ($post->user()->isNot(Auth::user())) {
            return response()->json(['error' => 'You are not authorized to access or modify this resource.'], 403);
        }
        return $next($request);
    }
}
