<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CandidateExperience>
 */
class CandidateExperienceFactory extends Factory
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
            'experience_title' => $this->faker->jobTitle,
            'company' => $this->faker->company,
            'country_id' => random_int(1, 50),
            'state_id' => random_int(1, 100),
            'city_id' => random_int(1, 200),
            'start_date' => $this->faker->date,
            'end_date' => $this->faker->date,
            'currently_working' => $this->faker->boolean,
            'description' => $this->faker->paragraph,
        ];
    }
}
