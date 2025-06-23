<?php

namespace Database\Factories;

use App\Models\OwnerShipType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Enhanced Factory for OwnerShipType
 * Generated using Laravel 12 best practices.
 *
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\OwnerShipType>
 */
class OwnerShipTypeFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = OwnerShipType::class;

    /**
     * Define the model's default state using Enhanced patterns.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->words(2, true),
            'description' => fake()->paragraph(),
        ];
    }
}
