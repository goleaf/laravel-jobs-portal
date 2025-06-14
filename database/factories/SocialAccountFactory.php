<?php

namespace Database\Factories;

use App\Models\SocialAccount;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Enhanced Factory for SocialAccount
 * Generated using Laravel 12 best practices
 *
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SocialAccount>
 */
class SocialAccountFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = SocialAccount::class;

    /**
     * Define the model's default state using Enhanced patterns.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => fake()->numberBetween(1, 3), // Use existing user IDs from our seeder
            'provider' => fake()->randomElement(['google', 'facebook', 'twitter', 'linkedin', 'github']),
            'provider_id' => fake()->uuid(), // Use UUID for provider ID
        ];
    }
}