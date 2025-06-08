<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MaritalStatus>
 */
class MaritalStatusFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'marital_status' => $this->faker->unique()->randomElement(["Single", "Married", "Divorced", "Widowed", "Separated", "In a relationship"]),
            'description' => $this->faker->sentence(),
            'is_default' => $this->faker->boolean(20),
        ];
    }
}
