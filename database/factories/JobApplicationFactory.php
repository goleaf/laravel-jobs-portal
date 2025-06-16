<?php

namespace Database\Factories;

use App\Models\JobApplication;
use App\Models\Job;
use App\Models\Candidate;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\JobApplication>
 */
class JobApplicationFactory extends Factory
{
    protected $model = JobApplication::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'job_id' => Job::factory(),
            'candidate_id' => Candidate::factory(),
            'resume_id' => fake()->numberBetween(1, 100), // Simple resume ID for testing
            'expected_salary' => fake()->numberBetween(40000, 100000),
            'notes' => fake()->text(500),
            'status' => JobApplication::STATUS_PENDING,
        ];
    }

    /**
     * Indicate that the application should be shortlisted.
     */
    public function shortlisted(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => JobApplication::SHORT_LIST,
        ]);
    }

    /**
     * Indicate that the application should be selected.
     */
    public function selected(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => JobApplication::SELECT_STATUS,
        ]);
    }

    /**
     * Indicate that the application should be rejected.
     */
    public function rejected(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => JobApplication::REJECTED,
        ]);
    }

    /**
     * Indicate that the application should be completed.
     */
    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => JobApplication::COMPLETE,
        ]);
    }

    /**
     * Indicate that the application should be declined.
     */
    public function declined(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => JobApplication::REJECTED,
        ]);
    }
}
