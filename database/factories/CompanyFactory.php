<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\Industry;
use App\Models\State;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Company>
 */
class CompanyFactory extends Factory
{
    protected $model = Company::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'ceo' => fake()->name(),
            'no_of_offices' => fake()->numberBetween(1, 10),
            'industry_id' => 1, // Default industry
            'ownership_type_id' => 1, // Default ownership type
            'company_size_id' => 1, // Default company size
            'established_in' => fake()->year(),
            'details' => fake()->text(500),
            'website' => fake()->url(),
            'location' => fake()->address(),
            'is_featured' => false,
            'fax' => fake()->phoneNumber(),
            'facebook_url' => fake()->url(),
            'twitter_url' => fake()->url(),
            'linkedin_url' => fake()->url(),
            'google_plus_url' => fake()->url(),
            'pinterest_url' => fake()->url(),
            'unique_id' => Str::random(10),
        ];
    }

    /**
     * Indicate that the company should be featured.
     */
    public function featured(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_featured' => true,
        ]);
    }

    /**
     * Indicate that the company should be inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
