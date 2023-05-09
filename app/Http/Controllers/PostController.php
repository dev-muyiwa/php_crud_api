<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\User;
use Exception;
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
        $key = key($request->query());
        try {
            if ($key == "q") {
                $query = $request->input('q');
                $posts = Post::where('title', 'like', '%' . $query . '%')->get();
            } elseif ($key == "") {
                $posts = Post::all();
            } else {
                throw new Exception(message: "Invalid query name =>" . key($request->query()), code: 400);
            }

            return $this::onSuccess(data: $posts, message: "Post(s) retrieved.");
        } catch (Exception $e) {
            return $this::onError(message: $e->getMessage(), status: $e->getCode());
        }
    }

    /**
     * Returns a post.
     *
     * @param Post $post
     * @return JsonResponse
     */
    public
    function getPost(Post $post): JsonResponse
    {
        return $this::onSuccess(data: $post, message: "Post has been retrieved.");
    }

    /**
     * Creates a new post that corresponds to a particular user.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public
    function createPost(Request $request): JsonResponse
    {
        $user = Auth::user();
        $post = $user->posts()->create($request->all());
        return self::onSuccess(data: $post, message: "Post created successfully.", status: 201);
    }

    /**
     * Modifies the content of a post.
     *
     * @param Request $request
     * @param Post $post
     * @return JsonResponse
     */
    public
    function updatePost(Request $request, Post $post): JsonResponse
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
    public
    function deletePost(User $user, int $post_id): JsonResponse
    {
        try {
            $post = Post::find($post_id);
            $post->delete();
            return self::onSuccess(data: null, message: "Post deleted successfully.");
        } catch (Exception $e) {
            return self::onError($e->getMessage(), $e->getCode());
        }
    }
}
