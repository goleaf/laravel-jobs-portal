<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CompanySize>
 */
class CompanySizeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $sizes = [
            'Startup (1-10 employees)',
            'Small (11-50 employees)',
            'Medium (51-200 employees)',
            'Large (201-1000 employees)',
            'Enterprise (1000+ employees)',
            'Micro (1-5 employees)',
            'Mid-size (101-500 employees)',
            'Corporation (500+ employees)',
        ];

        return [
            'size' => $this->faker->unique()->randomElement($sizes),
            'is_active' => $this->faker->boolean(80),
        ];
    }
}
