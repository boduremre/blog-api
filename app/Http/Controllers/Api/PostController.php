<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Models\Post;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Post::with('category:id,name', 'user:id,name,email')->withCount('comments')->get();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePostRequest $request)
    {
        // Create the post using validated data
        $post = Post::create($request->validated());

        // Load the category and user relationships for the response
        return response()->json([
            'success' => true,
            'message' => 'Post created successfully',
            'data' => $post->load('category:id,name', 'user:id,name,email')
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Post $post)
    {
        // Eager load category and user relationships
        return response()->json($post->load('category:id,name', 'user:id,name,email', 'comments.user:id,name'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePostRequest $request, Post $post)
    {
        $this->authorize('update', $post);

        $post->update($request->validated());
        return response()->json([
            'success' => true,
            'message' => 'Post updated successfully',
            'data' => $post->load('category:id,name', 'user:id,name,email')
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post)
    {
        $this->authorize('delete', $post);

        $post->delete();
        return response()->json([
            'success' => true,
            'message' => 'Post deleted successfully'
        ]);
    }
}
