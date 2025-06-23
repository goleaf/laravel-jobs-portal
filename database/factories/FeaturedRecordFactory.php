<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\FeaturedRecord>
 */
class FeaturedRecordFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'owner_id' => Company::factory(),
            'owner_type' => 'App\\Models\\Company',
            'user_id' => User::factory(),
            'stripe_id' => $this->faker->uuid(),
            'start_time' => $this->faker->dateTimeBetween('-1 month', 'now'),
            'end_time' => $this->faker->dateTimeBetween('now', '+1 month'),
            'meta' => json_encode(['featured_type' => 'company']),
            'is_active' => $this->faker->boolean(80),
            'settings' => json_encode(['display_priority' => $this->faker->numberBetween(1, 10)]),
        ];
    }
}
