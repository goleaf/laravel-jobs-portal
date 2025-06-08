<?php

namespace Database\Factories;

use App\Models\SalaryCurrency;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Context7 Factory for SalaryCurrency
 * Generated using Laravel 12 best practices
 *
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SalaryCurrency>
 */
class SalaryCurrencyFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = SalaryCurrency::class;

    /**
     * Define the model's default state using Context7 patterns.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'currency_name' => fake()->word(),
            'is_default' => fake()->word(),
            'currency_code' => fake()->word(),
            'currency_icon' => fake()->word()
        ];
    }
}