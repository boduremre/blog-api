<?php

namespace App\Services;

use App\Models\Post;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class PostService
{
    /**
     * List all posts with their categories and users.
     *
     * @return Collection The list of posts.
     */
    public function list(): Collection
    {
        return Cache::remember("posts.index", now()->addMinutes(10), function () {
            return Post::with('category:id,name', 'user:id,name,email', 'tags:id,name')->withCount('comments')->get();
        });
    }

    /**
     * Find a post by its ID with all related data.
     *
     * @param Post $post The post to find.
     * @return Post The found post with loaded relationships.
     */
    public function find(Post $post): Post
    {
        return Cache::remember("posts.show.{$post->id}", now()->addMinutes(10), function () use ($post) {
            return $post->load('category', 'user', 'tags', 'comments');
        });
    }

    /**
     * Create a new post with the given data and user.
     *
     * @param array $data The validated data for creating the post.
     * @param User $user The user who is creating the post.
     * @return Post The created post with loaded relationships.
     */
    public function create(array $data, User $user): Post
    {
        return DB::transaction(
            function () use ($data, $user) {
                $post = $user->posts()->create([
                    'title' => $data['title'],
                    'content' => $data['content'],
                    'slug' => Str::slug($data['title']),
                    'category_id' => $data['category_id'],
                    'status' => $data['status'] ?? 'draft',
                ]);

                if (!empty($data['tags'])) {

                    $post->tags()->sync(
                        $data['tags']
                    );
                }

                // Clear the cache for the posts index to ensure fresh data
                Cache::forget('posts.index');
                Cache::forget("posts.show.{$post->id}");

                return $post->load([
                    'category:id,name',
                    'tags:id,name',
                    'user:id,name,email'
                ]);
            }
        );
    }

    /**
     * Update an existing post with the given data.
     *
     * @param Post $post The post to update.
     * @param array $data The validated data for updating the post.
     * @return Post The updated post with loaded relationships.
     */
    public function update(Post $post, array $data): Post
    {
        return DB::transaction(
            function () use ($post, $data) {

                $post->update([
                    'title' => $data['title'],
                    'content' => $data['content'],
                    'slug' => Str::slug($data['title']),
                    'category_id' => $data['category_id'],
                    'status' => $data['status'] ?? $post->status,
                ]);

                if (isset($data['tags'])) {
                    $post->tags()->sync(
                        $data['tags']
                    );
                }

                // Clear the cache for the posts index to ensure fresh data is shown
                Cache::forget('posts.index');
                Cache::forget("posts.show.{$post->id}");

                return $post->load([
                    'category:id,name',
                    'tags:id,name',
                    'user:id,name,email'
                ]);
            }
        );
    }

    /**
     * Delete a post.
     *
     * @param Post $post The post to delete.
     * @return bool Whether the post was deleted.
     */
    public function delete(Post $post): bool
    {
        $deleted = $post->delete();

        Cache::forget('posts.index');
        Cache::forget("posts.show.{$post->id}");

        return $deleted;
    }
}
