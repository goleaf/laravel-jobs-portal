<?php

namespace Database\Factories;

use App\Models\JobCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\JobCategory>
 */
class JobCategoryFactory extends Factory
{
    protected $model = JobCategory::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $categories = [
            'Information Technology',
            'Marketing & Sales',
            'Human Resources',
            'Finance & Accounting',
            'Engineering',
            'Healthcare',
            'Education',
            'Design & Creative',
            'Customer Service',
            'Operations',
            'Legal',
            'Research & Development',
        ];

        return [
            'name' => fake()->unique()->randomElement($categories),
            'description' => fake()->sentence(),
            'is_featured' => false,
        ];
    }

    /**
     * Indicate that the job category should be featured.
     */
    public function featured(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_featured' => true,
        ]);
    }

    /**
     * Indicate that the job category should be inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
} 