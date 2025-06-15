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
        // Get existing country, state, city records to avoid foreign key issues
        $country = Country::inRandomOrder()->first();
        $state = null;
        $city = null;
        
        if ($country) {
            $state = State::where('country_id', $country->id)->inRandomOrder()->first();
            if ($state) {
                $city = City::where('state_id', $state->id)->inRandomOrder()->first();
            }
        }
        
        $startDate = $this->faker->dateTimeBetween('-5 years', '-1 year');
        $currentlyWorking = $this->faker->boolean(30);
        
        return [
            'candidate_id' => Candidate::factory(),
            'experience_title' => $this->faker->jobTitle,
            'company' => $this->faker->company,
            'country_id' => $country?->id ?? 1, // Fallback to first country
            'state_id' => $state?->id ?? 1, // Fallback to first state
            'city_id' => $city?->id ?? 1, // Fallback to first city
            'start_date' => $startDate,
            'end_date' => $currentlyWorking ? null : $this->faker->dateTimeBetween($startDate, 'now'),
            'currently_working' => $currentlyWorking,
            'description' => $this->faker->paragraph,
            'job_level' => $this->faker->randomElement(['Entry', 'Mid', 'Senior', 'Lead', 'Manager']),
            'employment_type' => $this->faker->randomElement(['Full-time', 'Part-time', 'Contract', 'Internship']),
            'salary' => $this->faker->numberBetween(30000, 150000),
            'is_verified' => $this->faker->boolean(60),
        ];
    }
}
