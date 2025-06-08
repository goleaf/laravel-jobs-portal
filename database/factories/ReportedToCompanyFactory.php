<?php

namespace Database\Factories;

use App\Models\ReportedToCompany;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Context7 Factory for ReportedToCompany
 * Generated using Laravel 12 best practices
 *
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ReportedToCompany>
 */
class ReportedToCompanyFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ReportedToCompany::class;

    /**
     * Define the model's default state using Context7 patterns.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => fake()->numberBetween(1, 3), // Use existing user IDs
            'company_id' => fake()->numberBetween(1, 3), // Use existing company IDs
            'note' => fake()->word()
        ];
    }
}