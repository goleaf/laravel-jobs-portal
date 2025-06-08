<?php

namespace Database\Factories;

use App\Models\JobType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\JobType>
 */
class JobTypeFactory extends Factory
{
    protected $model = JobType::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $jobTypes = [
            'Full Time',
            'Part Time',
            'Contract',
            'Temporary',
            'Internship',
            'Freelance',
            'Remote',
        ];

        return [
            'name' => fake()->unique()->randomElement($jobTypes),
            'description' => fake()->sentence(),
            'is_default' => false,
        ];
    }

    /**
     * Indicate that the job type should be default.
     */
    public function default(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_default' => true,
        ]);
    }

    /**
     * Indicate that the job type should be inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
} 