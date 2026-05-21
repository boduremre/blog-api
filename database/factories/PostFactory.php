<?php

namespace Database\Factories;

use App\Models\Post;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Post>
 */
class PostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(),
            'content' => fake()->paragraph(),
            'slug' => fake()->slug(),
            'user_id' => \App\Models\User::factory(),
            'category_id' => \App\Models\Category::factory(),
            'status' => fake()->randomElement(['draft', 'published']),
            'deleted_at' => null,
        ];
    }
}
