<?php

namespace Database\Factories;

use App\Models\Job;
use App\Models\Company;
use App\Models\JobType;
use App\Models\JobCategory;
use App\Models\CareerLevel;
use App\Models\FunctionalArea;
use App\Models\JobShift;
use App\Models\RequiredDegreeLevel;
use App\Models\SalaryCurrency;
use App\Models\SalaryPeriod;
use Illuminate\Database\Eloquent\Factories\Factory;

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
            'job_id' => 'JOB' . fake()->unique()->numerify('######'),
            'job_title' => fake()->jobTitle(),
            'description' => fake()->text(1000),
            'company_id' => Company::factory(),
            'job_type_id' => 1, // Default job type
            'job_category_id' => 1, // Default job category
            'career_level_id' => 1, // Default career level
            'functional_area_id' => 1, // Default functional area
            'job_shift_id' => 1, // Default job shift
            'degree_level_id' => 1, // Default degree level
            'currency_id' => 1, // Default currency
            'salary_period_id' => 1, // Default salary period
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
            'country_id' => 1, // Default country
            'state_id' => 1, // Default state
            'city_id' => 1, // Default city
            'job_expiry_date' => fake()->dateTimeBetween('now', '+30 days'),
        ];
    }

    /**
     * Indicate that the job should be featured.
     */
    public function featured(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_featured' => true,
        ]);
    }

    /**
     * Indicate that the job should be suspended.
     */
    public function suspended(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_suspended' => true,
        ]);
    }

    /**
     * Indicate that the job should be closed.
     */
    public function closed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => Job::STATUS_CLOSED,
        ]);
    }

    /**
     * Indicate that the job should be a draft.
     */
    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => Job::STATUS_DRAFT,
        ]);
    }

    /**
     * Indicate that the job should be expired.
     */
    public function expired(): static
    {
        return $this->state(fn (array $attributes) => [
            'job_expiry_date' => fake()->dateTimeBetween('-30 days', '-1 day'),
        ]);
    }
} 