<?php

namespace Database\Factories;

use App\Models\PostComment;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PostComment>
 */
class PostCommentFactory extends Factory
{
    protected $model = PostComment::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->safeEmail(),
            'comment' => fake()->paragraph(),
            'post_id' => fake()->numberBetween(1, 10), // Assuming we have some posts
            'user_id' => fake()->optional()->numberBetween(1, 3), // Optional user association
        ];
    }
}
