<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Industry>
 */
class IndustryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $industries = [
            'Technology', 'Healthcare', 'Finance', 'Education', 'Manufacturing',
            'Retail', 'Consulting', 'Media', 'Real Estate', 'Hospitality',
            'Automotive', 'Energy', 'Construction', 'Agriculture', 'Transportation',
            'Telecommunications', 'Aerospace', 'Biotechnology', 'Entertainment', 'Food & Beverage',
            'Pharmaceuticals', 'Insurance', 'Banking', 'E-commerce', 'Gaming',
            'Fashion', 'Sports', 'Travel', 'Logistics', 'Mining',
            'Chemical', 'Textile', 'Furniture', 'Electronics', 'Software Development',
            'Cybersecurity', 'Artificial Intelligence', 'Renewable Energy', 'Oil & Gas', 'Publishing',
        ];

        return [
            'name' => $this->faker->unique()->randomElement($industries),
            'description' => $this->faker->paragraph(3),
            'is_default' => $this->faker->boolean(20), // 20% chance of being default
        ];
    }
}
