<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CareerLevel>
 */
class CareerLevelFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $careerLevels = [
            'Entry Level',
            'Junior Level',
            'Mid Level',
            'Senior Level', 
            'Lead Level',
            'Principal Level',
            'Manager Level',
            'Director Level'
        ];
        
        return [
            'level_name' => $this->faker->unique()->randomElement($careerLevels),
        ];
    }
}
