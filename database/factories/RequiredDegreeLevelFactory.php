<?php

namespace Database\Factories;

use App\Models\RequiredDegreeLevel;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Enhanced Factory for RequiredDegreeLevel
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
     * Define the model's default state using Enhanced patterns.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $degrees = [
            'High School Diploma',
            'Associate Degree',
            'Bachelor\'s Degree',
            'Master\'s Degree',
            'Doctoral Degree (PhD)',
            'Professional Degree',
            'Certificate Program',
            'Diploma',
            'Trade School',
            'No Formal Education Required'
        ];
        
        return [
            'name' => $this->faker->unique()->randomElement($degrees),
            'is_default' => $this->faker->boolean(10) // 10% chance of being default
        ];
    }
}