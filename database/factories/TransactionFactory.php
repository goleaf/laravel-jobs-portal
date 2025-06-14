<?php

namespace Database\Factories;

use App\Models\Transaction;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Enhanced Factory for Transaction
 * Generated using Laravel 12 best practices
 *
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Transaction>
 */
class TransactionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Transaction::class;

    /**
     * Define the model's default state using Enhanced patterns.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => fake()->numberBetween(1, 3), // Use existing user IDs
            'subscription_id' => fake()->numberBetween(1, 10),
            'invoice_id' => fake()->uuid(),
            'amount' => fake()->randomFloat(2, 10, 1000),
            'status' => fake()->randomElement(['pending', 'completed', 'failed', 'cancelled']),
            'is_approved' => fake()->boolean(),
            'approved_id' => fake()->optional()->numberBetween(1, 3),
            'plan_currency_id' => fake()->numberBetween(1, 3) // Use existing currency IDs
        ];
    }
}