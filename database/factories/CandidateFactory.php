<?php

namespace Database\Factories;

use App\Models\Candidate;
use App\Models\User;
use App\Models\CareerLevel;
use App\Models\FunctionalArea;
use App\Models\Industry;
use App\Models\MaritalStatus;
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
            'user_id' => User::factory(),
            'career_level_id' => 1, // Default career level
            'industry_id' => 1, // Default industry
            'functional_area_id' => 1, // Default functional area
            'current_salary' => fake()->numberBetween(30000, 80000),
            'expected_salary' => fake()->numberBetween(40000, 100000),
            'salary_currency' => 'USD',
            'salary_period' => 'Monthly',
            'immediate_available' => fake()->boolean(),
            'experience' => fake()->numberBetween(0, 15),
            'phone' => fake()->phoneNumber(),
            'marital_status_id' => 1, // Default marital status
            'nationality' => fake()->country(),
            'national_id_card' => fake()->numerify('##########'),
            'is_immediate_available' => fake()->boolean(),
            'is_active' => true,
            'is_verified' => true,
            'video_link' => fake()->url(),
        ];
    }

    /**
     * Indicate that the candidate should be immediately available.
     */
    public function immediatelyAvailable(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_immediate_available' => true,
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
     * Indicate that the candidate should be inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    /**
     * Indicate that the candidate should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_verified' => false,
        ]);
    }
} 