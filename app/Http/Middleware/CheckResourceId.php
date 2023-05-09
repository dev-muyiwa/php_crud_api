<?php

namespace App\Http\Middleware;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
        $post_id = $request->route()->parameter("post");
        $post = Post::find($post_id);
        if ($post == null) {
            return Controller::onError(message: "Resource doesn't exist.", status: 404);
        }

        if ($post->user()->isNot(Auth::user())) {
            return Controller::onError(message: "You are not authorised to make changes to this resource.", status: 403);
        }
        return $next($request);
    }
}
