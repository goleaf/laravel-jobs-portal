<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\User;
use App\Models\Country;
use App\Models\State;
use App\Models\City;
use App\Models\Industry;
use App\Models\OwnerShipType;
use App\Models\CompanySize;
use Illuminate\Database\Eloquent\Factories\Factory;

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
            'name' => fake()->company(),
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->phoneNumber(),
            'website' => fake()->url(),
            'location' => fake()->address(),
            'details' => fake()->text(500),
            'industry_id' => 1, // Default industry
            'ownership_type_id' => 1, // Default ownership type
            'company_size_id' => 1, // Default company size
            'country_id' => 1, // Default country
            'state_id' => 1, // Default state  
            'city_id' => 1, // Default city
            'established_in' => fake()->year(),
            'ceo' => fake()->name(),
            'is_active' => true,
            'is_featured' => false,
            'twitter_url' => fake()->url(),
            'facebook_url' => fake()->url(),
            'linkedin_url' => fake()->url(),
            'google_plus_url' => fake()->url(),
            'pinterest_url' => fake()->url(),
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