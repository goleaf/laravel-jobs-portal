<?php

namespace Database\Factories;

use App\Models\Job;
// Users/auth removed
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\FavouriteJob>
 */
class FavouriteJobFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => null,
            'job_id' => Job::factory(),
            'is_active' => $this->faker->boolean(90),
        ];
    }
}
