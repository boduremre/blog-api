<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Models\Post;
use App\Services\PostService;

class PostController extends Controller
{
    public function __construct(private readonly PostService $postService) {}

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json($this->postService->list());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePostRequest $request)
    {
        // Create the post using validated data
        $post = $this->postService->create($request->validated(), $request->user());

        // Load the category and user relationships for the response
        return response()->json([
            'success' => true,
            'message' => 'Post created successfully!',
            'data' => $post
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Post $post)
    {
        // Load the post with its relationships for the response
        return response()->json($this->postService->find($post));
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePostRequest $request, Post $post)
    {
        $this->authorize('update', $post);

        $post = $this->postService->update($post, $request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Post updated successfully!',
            'data' => $post
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post)
    {
        $this->authorize('delete', $post);

        $this->postService->delete($post);
        return response()->json([
            'success' => true,
            'message' => 'Post deleted successfully'
        ]);
    }
}
