<?php

namespace Database\Factories;

use App\Models\Subscription;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Subscription>
 */
class SubscriptionFactory extends Factory
{
    protected $model = Subscription::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => fake()->numberBetween(1, 3), // Use existing user IDs
            'name' => fake()->randomElement(['default', 'premium', 'basic']),
            'stripe_id' => 'sub_'.fake()->regexify('[A-Za-z0-9]{40}'),
            'stripe_status' => fake()->randomElement(['active', 'inactive', 'canceled', 'trialing']),
            'stripe_plan' => fake()->randomElement(['plan_basic', 'plan_premium', 'plan_enterprise']),
            'stripe_price' => fake()->randomElement(['price_basic', 'price_premium', 'price_enterprise']),
            'plan_id' => fake()->numberBetween(1, 3),
            'trial_ends_at' => fake()->optional()->dateTimeBetween('now', '+30 days'),
            'ends_at' => fake()->optional()->dateTimeBetween('+1 month', '+1 year'),
            'current_period_start' => fake()->dateTimeBetween('-1 month', 'now'),
            'current_period_end' => fake()->dateTimeBetween('now', '+1 month'),
            'cancellation_reason' => fake()->optional()->sentence(),
            'type' => fake()->randomElement(['default', 'premium', 'basic']),
            'paypal_payment_id' => fake()->optional()->regexify('[A-Z0-9]{17}'),
        ];
    }
}
