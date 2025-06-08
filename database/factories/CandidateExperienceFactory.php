<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Candidate;
use App\Models\Country;
use App\Models\State;
use App\Models\City;

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
            'candidate_id' => Candidate::factory(),
            'experience_title' => $this->faker->jobTitle,
            'company' => $this->faker->company,
            'country_id' => Country::factory(),
            'state_id' => State::factory(), 
            'city_id' => City::factory(),
            'start_date' => $this->faker->date(),
            'end_date' => $this->faker->optional()->date(),
            'currently_working' => $this->faker->boolean,
            'description' => $this->faker->paragraph,
        ];
    }
}
