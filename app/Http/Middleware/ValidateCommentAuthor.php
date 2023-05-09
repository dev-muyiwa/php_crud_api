<?php

namespace App\Http\Middleware;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ValidateCommentAuthor
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
        $commentId = $request->route()->parameter("comment");
        $comment = Comment::find($commentId);
        if ($comment == null) {
            return Controller::onError(message: "Comment doesn't exist.", status: 404);
        }
        $authorId = $comment->commenter_id;

        if (Auth::id() != $authorId) {
            return Controller::onError(message: "You are not authorized to modify this resource.", status: 403);
        }
        return $next($request);
    }
}
