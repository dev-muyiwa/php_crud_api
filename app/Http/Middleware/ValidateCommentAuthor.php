<?php

namespace App\Http\Middleware;

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
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $commentId = $request->route()->parameter("comment");
        $authorId = Comment::findOrFail($commentId)->commenter_id;
        $userId = Auth::id();

        if ($authorId != $userId) {
            return response()->json(['error' => 'You are not authorized to access this resource.'], 403);
        }
        return $next($request);
    }
}
