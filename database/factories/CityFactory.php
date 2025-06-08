<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\City>
 */
class CityFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Use existing state IDs from our StatesSeeder
        $validStateIds = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 50, 51, 52, 53, 54, 60, 80, 81, 82, 83, 84, 85, 90, 91, 92, 93, 94, 95, 96, 97, 98, 99];
        
        return [
            'name' => $this->faker->city,
            'state_id' => $this->faker->randomElement($validStateIds),
        ];
    }
}
