<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    public function getAllComments(Post $post): JsonResponse
    {
        return response()->json($post->comments()->get(), 200);
    }

    public function createComment(Request $request, Post $post): JsonResponse
    {
        $user = Auth::user();
        $comment = $post->comments()->create([
            "comment" => $request->comment,
            "user_id" => $user->id
        ]);

        return response()->json($comment, 201);
    }
}
