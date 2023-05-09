<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use App\Notifications\NewComment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    public function getAllComments(Post $post): JsonResponse
    {
        return self::onSuccess(data: $post->comments, message: "All comments have been retrieved.");
    }

    public function createComment(Request $request, Post $post): JsonResponse
    {
        if (empty($request->comment)) {
            return self::onError(message: "Comment cannot be empty. Try again.", status: 401);
        }
        $user = Auth::user();
        $comment = $post->comments()->create([
            "comment" => $request->comment,
            "commenter_id" => $user->id
        ]);

        $author = $comment->post->user;
        $author->notify(new NewComment($comment));

        $response = ["comment" => $comment];

        return self::onSuccess(
            data: $response,
            message: "Comment has been created successfully.",
            status: 201);
    }

    public function deleteComment(Post $post, int $comment): JsonResponse
    {
        Comment::find($comment)->delete();
        return self::onSuccess(data: null, message: "Post deleted successfully");
    }
}
