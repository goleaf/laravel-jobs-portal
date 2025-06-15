<?php

namespace Database\Factories;

use App\Models\Application;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Application>
 */
class ApplicationFactory extends Factory
{
    protected $model = Application::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'job_id' => fake()->numberBetween(1, 10), // Will be overridden by seeder
            'candidate_id' => fake()->numberBetween(1, 10), // Will be overridden by seeder
            'resume_id' => 1, // Default resume ID
            'expected_salary' => fake()->numberBetween(40000, 120000),
            'notes' => fake()->optional(0.7)->paragraph(),
            'status' => fake()->randomElement([0, 1, 2, 3, 4]), // Integer status values
        ];
    }

    /**
     * Indicate that the application should be pending.
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 0,
        ]);
    }

    /**
     * Indicate that the application should be shortlisted.
     */
    public function shortlisted(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 1,
        ]);
    }

    /**
     * Indicate that the application should be selected.
     */
    public function selected(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 2,
        ]);
    }

    /**
     * Indicate that the application should be rejected.
     */
    public function rejected(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 3,
        ]);
    }

    /**
     * Indicate that the application should be completed.
     */
    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 4,
        ]);
    }
}
