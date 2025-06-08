<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Candidate;
use App\Models\RequiredDegreeLevel;
use App\Models\Country;
use App\Models\State;
use App\Models\City;

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
            'candidate_id' => Candidate::factory(),
            'degree_level_id' => RequiredDegreeLevel::factory(),
            'degree_title' => $this->faker->jobTitle,
            'institute' => $this->faker->company,
            'result' => $this->faker->randomElement(['A', 'B', 'C', 'D', 'Pass', 'Distinction']),
            'year' => $this->faker->year,
            'country_id' => Country::factory(),
            'state_id' => State::factory(),
            'city_id' => City::factory(),
        ];
    }
}
