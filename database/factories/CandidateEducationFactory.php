<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CandidateEducation>
 */
class CandidateEducationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => \App\Models\User::factory(),
            'degree_level_id' => random_int(1, 10),
            'degree_title' => $this->faker->jobTitle,
            'year' => $this->faker->year,
            'country_id' => random_int(1, 50),
            'state_id' => random_int(1, 100),
            'city_id' => random_int(1, 200),
        ];
    }
}
