<?php

namespace Database\Factories;

use App\Models\Testimonial;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Enhanced Factory for Testimonial
 * Generated using Laravel 12 best practices
 *
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Testimonial>
 */
class TestimonialFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Testimonial::class;

    /**
     * Define the model's default state using Enhanced patterns.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'customer_name' => fake()->name(),
            'customer_title' => fake()->jobTitle(),
            'customer_company' => fake()->company(),
            'customer_email' => fake()->email(),
            'description' => fake()->paragraph(),
            'rating' => fake()->numberBetween(1, 5),
            'is_active' => fake()->boolean(80),
            'is_featured' => fake()->boolean(20),
            'is_verified' => fake()->boolean(70),
            'location' => fake()->city(),
            'project_type' => fake()->randomElement(['web_development', 'mobile_development', 'design', 'consulting', 'other']),
            'sort_order' => fake()->numberBetween(1, 100),
            'testimonial_date' => fake()->dateTimeBetween('-2 years', 'now'),
        ];
    }

    /**
     * Make a featured testimonial.
     */
    public function featured(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_featured' => true,
            'is_active' => true,
            'is_verified' => true,
        ]);
    }

    /**
     * Make an active testimonial.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => true,
        ]);
    }

    /**
     * Make an inactive testimonial.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}