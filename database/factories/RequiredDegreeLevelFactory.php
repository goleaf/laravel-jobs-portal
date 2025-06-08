<?php

namespace Database\Factories;

use App\Models\RequiredDegreeLevel;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Context7 Factory for RequiredDegreeLevel
 * Generated using Laravel 12 best practices
 *
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\RequiredDegreeLevel>
 */
class RequiredDegreeLevelFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = RequiredDegreeLevel::class;

    /**
     * Define the model's default state using Context7 patterns.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->words(2, true),
            'is_default' => fake()->word()
        ];
    }
}