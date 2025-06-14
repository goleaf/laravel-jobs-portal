<?php

namespace Database\Factories;

use App\Models\Notification;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Enhanced Factory for Notification
 * Generated using Laravel 12 best practices
 *
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Notification>
 */
class NotificationFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Notification::class;

    /**
     * Define the model's default state using Enhanced patterns.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'type' => fake()->word(),
            'notification_for' => fake()->word(),
            'user_id' => fake()->numberBetween(1, 3), // Use existing user IDs
            'title' => fake()->sentence(3),
            'text' => fake()->word(),
            'meta' => fake()->word(),
            'read_at' => fake()->dateTimeBetween('-1 year', 'now')
        ];
    }
}