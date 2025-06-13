<?php

namespace Database\Factories;

use App\Models\JobType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\JobType>
 */
class JobTypeFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = JobType::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $jobTypes = [
            'Full-Time',
            'Part-Time',
            'Contract',
            'Temporary',
            'Internship',
            'Freelance',
            'Remote',
            'Hybrid',
            'On-Site',
            'Consultant',
            'Volunteer',
            'Seasonal'
        ];

        $colors = [
            '#3B82F6', // Blue
            '#10B981', // Green
            '#F59E0B', // Yellow
            '#EF4444', // Red
            '#8B5CF6', // Purple
            '#06B6D4', // Cyan
            '#F97316', // Orange
            '#84CC16', // Lime
            '#EC4899', // Pink
            '#6B7280', // Gray
        ];

        $icons = [
            'clock',
            'briefcase',
            'user-tie',
            'laptop',
            'home',
            'building',
            'users',
            'star',
            'globe',
            'calendar'
        ];

        return [
            'name' => $this->faker->unique()->randomElement($jobTypes),
            'description' => $this->faker->sentence(10),
            'is_default' => $this->faker->boolean(20), // 20% chance of being default
            'is_active' => $this->faker->boolean(85), // 85% chance of being active
            'is_featured' => $this->faker->boolean(30), // 30% chance of being featured
            'sort_order' => $this->faker->numberBetween(1, 100),
            'icon' => $this->faker->randomElement($icons),
            'color' => $this->faker->randomElement($colors),
            'slug' => $this->faker->unique()->slug(2),
            'meta_title' => $this->faker->sentence(3),
            'meta_description' => $this->faker->sentence(8),
            'created_at' => $this->faker->dateTimeBetween('-2 years', 'now'),
            'updated_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
        ];
    }

    /**
     * Indicate that the job type is active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => true,
        ]);
    }

    /**
     * Indicate that the job type is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    /**
     * Indicate that the job type is default.
     */
    public function default(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_default' => true,
        ]);
    }

    /**
     * Indicate that the job type is custom.
     */
    public function custom(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_default' => false,
        ]);
    }

    /**
     * Indicate that the job type is featured.
     */
    public function featured(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_featured' => true,
        ]);
    }

    /**
     * Create a full-time job type.
     */
    public function fullTime(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Full-Time',
            'description' => 'Standard full-time employment position',
            'icon' => 'briefcase',
            'color' => '#3B82F6',
            'is_default' => true,
        ]);
    }

    /**
     * Create a part-time job type.
     */
    public function partTime(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Part-Time',
            'description' => 'Part-time employment position',
            'icon' => 'clock',
            'color' => '#10B981',
            'is_default' => true,
        ]);
    }

    /**
     * Create a contract job type.
     */
    public function contract(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Contract',
            'description' => 'Contract-based employment',
            'icon' => 'user-tie',
            'color' => '#F59E0B',
            'is_default' => true,
        ]);
    }

    /**
     * Create a remote job type.
     */
    public function remote(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Remote',
            'description' => 'Work from home or remote location',
            'icon' => 'home',
            'color' => '#8B5CF6',
            'is_default' => true,
        ]);
    }

    /**
     * Create an internship job type.
     */
    public function internship(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Internship',
            'description' => 'Internship or training position',
            'icon' => 'star',
            'color' => '#EC4899',
            'is_default' => true,
        ]);
    }
} 