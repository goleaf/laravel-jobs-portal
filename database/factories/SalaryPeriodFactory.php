<?php

namespace Database\Factories;

use App\Models\SalaryPeriod;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Context7 Factory for SalaryPeriod
 * Generated using Laravel 12 best practices
 *
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SalaryPeriod>
 */
class SalaryPeriodFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = SalaryPeriod::class;

    /**
     * Define the model's default state using Context7 patterns.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $periods = [
            ['period' => 'Hourly', 'description' => 'Payment calculated per hour worked'],
            ['period' => 'Daily', 'description' => 'Payment calculated per day worked'],
            ['period' => 'Weekly', 'description' => 'Payment calculated per week worked'],
            ['period' => 'Monthly', 'description' => 'Payment calculated per month worked'],
            ['period' => 'Yearly', 'description' => 'Payment calculated per year worked'],
            ['period' => 'Project-based', 'description' => 'Payment calculated per project completion']
        ];
        
        $period = $this->faker->unique()->randomElement($periods);
        
        return [
            'period' => $period['period'],
            'description' => $period['description'],
            'is_default' => $this->faker->boolean(10) // 10% chance of being default
        ];
    }
}