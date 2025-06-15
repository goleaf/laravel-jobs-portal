<?php

namespace Database\Factories;

use App\Models\Plan;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Enhanced Factory for Plan
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
     * Define the model's default state using Enhanced patterns.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $planNames = ['Basic', 'Premium', 'Enterprise', 'Starter', 'Professional', 'Business'];
        
        return [
            'name' => fake()->unique()->randomElement($planNames),
            'stripe_plan_id' => fake()->optional(0.7)->numerify('plan_#####'),
            'allowed_jobs' => fake()->randomElement([5, 10, 25, 50, 100, -1]), // -1 for unlimited
            'amount' => fake()->randomFloat(2, 0, 999.99),
            'salary_currency_id' => fake()->numberBetween(1, 10), // Assuming 10 currencies exist
            'is_trial_plan' => fake()->boolean(20), // 20% chance of being trial
            'is_active' => fake()->boolean(90), // 90% chance of being active
            'is_featured' => fake()->boolean(30), // 30% chance of being featured
            'priority_support' => fake()->boolean(40), // 40% chance of priority support
            'analytics_access' => fake()->boolean(60), // 60% chance of analytics access
            'max_featured_jobs' => fake()->randomElement([0, 1, 3, 5, 10]),
            'duration_days' => fake()->randomElement([30, 90, 180, 365]), // Monthly, quarterly, bi-annual, yearly
        ];
    }

    /**
     * Indicate that the plan is a trial plan.
     */
    public function trial(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_trial_plan' => true,
            'amount' => 0,
            'duration_days' => 30,
            'priority_support' => false,
            'analytics_access' => false,
        ]);
    }

    /**
     * Indicate that the plan is featured.
     */
    public function featured(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_featured' => true,
            'priority_support' => true,
            'analytics_access' => true,
        ]);
    }

    /**
     * Indicate that the plan is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}