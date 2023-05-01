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
     * Returns a list of Post associated to a user.
     *
     * @param User $user
     * @return JsonResponse
     */
    public function getAllPosts(): JsonResponse
    {
        $posts = Post::all();
        return response()->json($posts, 200);
    }


    /**
     * Returns a post.
     *
     * @param User $user
     * @param Post $post
     * @return JsonResponse
     */
    public function getPost(User $user, Post $post): JsonResponse
    {
        return response()->json($post);
    }


    /**
     * Returns a list of posts based on the search query.
     *
     * @param Request $request
     * @param User $user
     * @return JsonResponse
     */
    public function searchPostsByTitle(Request $request, User $user): JsonResponse
    {
        $search = $request->input('search');

        $posts = $user->posts()
            ->when($search, function ($query, $search) {
                return $query->where('title', 'LIKE', '%' . $search . '%');
            })
            ->get();

        return response()->json($posts);
    }

    /**
     * Creates a new post that corresponds to a particular user.
     *
     * @param Request $request
     * @param User $user
     * @return JsonResponse
     */
    public function createPost(Request $request): JsonResponse
    {
        $id = Auth::id();
        $user = User::findOrFail($id);
        $post = $user->posts()->create($request->all());
        return response()->json($post, 201);
    }

    /**
     * Modifies the content of a post.
     *
     * @param Request $request
     * @param User $user
     * @param Post $post
     * @return JsonResponse
     */
    public function updatePost(Request $request, User $user, Post $post): JsonResponse
    {
        $post->update($request->all());
        return response()->json($post);
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
