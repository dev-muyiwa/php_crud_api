<?php

namespace App\Http\Controllers;

use App\Mail\NewCommentNotification;
use App\Models\Comment;
use App\Models\Post;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class CommentController extends Controller
{
    public function getAllComments(Post $post): JsonResponse
    {
        return self::onSuccess(data: $post->comments, message: "All comments have been retrieved.");
    }

    public function createComment(Request $request, Post $post): JsonResponse|\Illuminate\Foundation\Application|Redirector|RedirectResponse|Application
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
        Mail::to($author)->send(new NewCommentNotification($comment));

        return self::onSuccess(
            data: $comment->commenter_id,
            message: "Comment has been created successfully.",
            status: 201);
    }

    public function deleteComment(Post $post, int $comment): JsonResponse
    {
        Comment::findOrFail($comment)->delete();
        return self::onSuccess(data: "", message: "Post deleted successfully");
    }
}
