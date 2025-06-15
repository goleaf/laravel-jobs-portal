<?php

namespace Database\Factories;

use App\Models\ReportedJob;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Enhanced Factory for ReportedJob
 * Generated using Laravel 12 best practices.
 *
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ReportedJob>
 */
class ReportedJobFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ReportedJob::class;

    /**
     * Define the model's default state using Enhanced patterns.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => fake()->numberBetween(1, 3), // Use existing user IDs
            'job_id' => fake()->numberBetween(1, 2), // Use existing job IDs
            'note' => fake()->word(),
        ];
    }
}
