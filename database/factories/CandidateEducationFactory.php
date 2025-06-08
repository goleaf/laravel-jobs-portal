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
            'candidate_id' => \App\Models\Candidate::factory(),
            'degree_level_id' => random_int(1, 10),
            'degree_title' => $this->faker->jobTitle,
            'institute' => $this->faker->company,
            'result' => $this->faker->randomElement(['A', 'B', 'C', 'D', 'Pass', 'Distinction']),
            'year' => $this->faker->year,
            'country_id' => random_int(1, 50),
            'state_id' => random_int(1, 100),
            'city_id' => random_int(1, 200),
        ];
    }
}
