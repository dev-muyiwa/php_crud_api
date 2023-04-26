<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function index(): JsonResponse
    {
        $posts = Post::all();
        return response()->json($posts);
    }

    public function store(Request $request)
    {
        $request->validate([
            'author' => 'required',
            'content' => 'required'
        ]);
        $post = Post::create($request->all());
        return response()->json($post, 201);
    }

    public function show($id)
    {
        $post = Post::find($id);
        return response()->json($post);
    }

    public function update(Request $request, $id)
    {
        $post = Post::find($id);
        $post->update($request->all());
        return response()->json($post);
    }

    public function search($author) {
        return Post::where('author', 'like', '%'.$author.'%')->get();
    }

    public function destroy($id)
    {
        Post::destroy($id);
        return response()->json("Post deleted successfully.", 204);
    }
}
