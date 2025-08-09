<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\Job;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Job>
 */
class JobFactory extends Factory
{
    protected $model = Job::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'job_id' => 'JOB'.strtoupper(Str::random(8)),
            'job_title' => fake()->jobTitle(),
            'description' => fake()->text(1000),
            'company_id' => Company::factory(),
            'job_type_id' => 1,
            'job_category_id' => 1,
            'career_level_id' => 1,
            'functional_area_id' => 1,
            'job_shift_id' => 1,
            'degree_level_id' => 1,
            'currency_id' => 1,
            'salary_period_id' => 1,
            'salary_from' => fake()->numberBetween(30000, 50000),
            'salary_to' => fake()->numberBetween(50000, 100000),
            'hide_salary' => fake()->boolean(),
            'no_preference' => fake()->boolean(),
            'is_freelance' => fake()->boolean(),
            'is_featured' => false,
            'is_suspended' => false,
            'is_created_by_admin' => false,
            'status' => Job::STATUS_OPEN,
            'position' => fake()->numberBetween(1, 10),
            'experience' => fake()->numberBetween(0, 10),
            'country_id' => 1,
            'state_id' => 1,
            'city_id' => 1,
            'job_expiry_date' => fake()->dateTimeBetween('now', '+30 days'),
        ];
    }

    public function featured(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_featured' => true,
        ]);
    }

    public function suspended(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_suspended' => true,
        ]);
    }

    public function closed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => Job::STATUS_CLOSED,
        ]);
    }

    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => Job::STATUS_DRAFT,
        ]);
    }

    public function expired(): static
    {
        return $this->state(fn (array $attributes) => [
            'job_expiry_date' => fake()->dateTimeBetween('-30 days', '-1 day'),
        ]);
    }
}
