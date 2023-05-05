<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{
    /**
     * Checks if there is a query attached to the url.
     * If true, it returns a list of posts based on that query.
     * Else, it returns all the posts.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getAllPosts(Request $request): JsonResponse
    {
        $query = $request->input('q');
//        try {
        if ($query) {
            $posts = Post::where('title', 'like', '%' . $query . '%')->get();
        } else {
            $posts = Post::all();
        }

        return response()->json($posts, 200);
//        } catch (Exception $e) {
//            if ($query == null)
//            return $this->onError(message: "Invalid query type.");
//        }
    }


    /**
     * Returns a post.
     *
     * @param Post $post
     * @return JsonResponse
     */
    public function getPost(Post $post): JsonResponse
    {
        return $this->onSuccess(data: $post, message: "Post has been retrieved.");
    }

    /**
     * Creates a new post that corresponds to a particular user.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function createPost(Request $request): JsonResponse
    {
        $id = Auth::id();
        $user = User::findOrFail($id);
        $post = $user->posts()->create($request->all());
        return self::onSuccess(data: $post, message: "Post created successfully", status: 201);
    }

    /**
     * Modifies the content of a post.
     *
     * @param Request $request
     * @param Post $post
     * @return JsonResponse
     */
    public function updatePost(Request $request, Post $post): JsonResponse
    {
        $post->update($request->all());
        return self::onSuccess(data: $post, message: "Post updated successfully");
    }

    /**
     * Delete post.
     *
     * @param User $user
     * @param Post $post
     * @return JsonResponse
     */
    public function deletePost(User $user, Post $post): JsonResponse
    {
        $post->delete();
        return response()->json("Post " . $post->id . " deleted successfully.");
    }
}
