<?php

namespace Database\Factories;

use App\Models\Candidate;
// Users/auth removed
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Candidate>
 */
class CandidateFactory extends Factory
{
    protected $model = Candidate::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => null,
            'unique_id' => 'CND-'.fake()->unique()->numberBetween(100000, 999999),
            'father_name' => fake()->name('male'),
            'marital_status_id' => null, // Nullable to avoid foreign key constraints
            'nationality' => fake()->country(),
            'national_id_card' => fake()->numerify('##########'),
            'experience' => fake()->numberBetween(0, 15),
            'career_level_id' => null, // Nullable to avoid foreign key constraints
            'industry_id' => null, // Nullable to avoid foreign key constraints
            'functional_area_id' => null, // Nullable to avoid foreign key constraints
            'current_salary' => fake()->numberBetween(30000, 80000),
            'expected_salary' => fake()->numberBetween(40000, 100000),
            'immediate_available' => fake()->boolean(),
        ];
    }

    /**
     * Indicate that the candidate should be immediately available.
     */
    public function immediatelyAvailable(): static
    {
        return $this->state(fn (array $attributes) => [
            'immediate_available' => true,
        ]);
    }

    /**
     * Indicate that the candidate should be experienced.
     */
    public function experienced(): static
    {
        return $this->state(fn (array $attributes) => [
            'experience' => fake()->numberBetween(5, 15),
        ]);
    }

    /**
     * Indicate that the candidate should be a fresher.
     */
    public function fresher(): static
    {
        return $this->state(fn (array $attributes) => [
            'experience' => 0,
        ]);
    }

    /**
     * Indicate that the candidate should not be immediately available.
     */
    public function notImmediatelyAvailable(): static
    {
        return $this->state(fn (array $attributes) => [
            'immediate_available' => false,
        ]);
    }
}
