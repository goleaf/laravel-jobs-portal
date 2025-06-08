<?php

namespace Database\Factories;

use App\Models\Plan;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Context7 Factory for Plan
 * Generated using Laravel 12 best practices
 *
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Plan>
 */
class PlanFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Plan::class;

    /**
     * Define the model's default state using Context7 patterns.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->words(2, true),
            'stripe_plan_id' => fake()->numberBetween(1, 100),
            'allowed_jobs' => fake()->word(),
            'amount' => fake()->randomFloat(2, 1, 10000),
            'salary_currency_id' => fake()->numberBetween(1, 100),
            'is_trial_plan' => fake()->word()
        ];
    }
}