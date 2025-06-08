<?php

namespace Database\Factories;

use App\Models\Transaction;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Context7 Factory for Transaction
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
     * Define the model's default state using Context7 patterns.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => fake()->numberBetween(1, 100),
            'owner_id' => fake()->numberBetween(1, 100),
            'owner_type' => fake()->word(),
            'amount' => fake()->randomFloat(2, 1, 10000),
            'invoice_id' => fake()->numberBetween(1, 100),
            'status' => fake()->randomElement(['active', 'inactive']),
            'is_approved' => fake()->word(),
            'approved_id' => fake()->numberBetween(1, 100),
            'plan_currency_id' => fake()->numberBetween(1, 100)
        ];
    }
}