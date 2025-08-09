<?php

namespace Database\Factories;

use App\Models\Candidate;
use App\Models\City;
use App\Models\Country;
use App\Models\RequiredDegreeLevel;
use App\Models\State;
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

        $degreeLevelId = RequiredDegreeLevel::inRandomOrder()->value('id') ?? 1;

        return [
            'candidate_id' => Candidate::factory(),
            'degree_level_id' => $degreeLevelId,
            'degree_title' => $this->faker->jobTitle.' Degree',
            'institute' => $this->faker->company.' University',
            'result' => $this->faker->randomElement(['A', 'B', 'C', 'D', 'Pass', 'Distinction', 'First Class', 'Second Class']),
            'year' => $this->faker->numberBetween(2010, 2023),
            'country_id' => $country?->id ?? 1, // Fallback to first country
            'state_id' => $state?->id ?? 1, // Fallback to first state
            'city_id' => $city?->id ?? 1, // Fallback to first city
            'grade_percentage' => $this->faker->randomFloat(2, 60, 100),
            'field_of_study' => $this->faker->randomElement([
                'Computer Science',
                'Engineering',
                'Business Administration',
                'Medicine',
                'Law',
                'Arts',
                'Mathematics',
                'Physics',
            ]),
            'description' => $this->faker->text(200),
            'is_verified' => $this->faker->boolean(70),
        ];
    }
}
