<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\JobShift>
 */
class JobShiftFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $shifts = [
            'Day Shift',
            'Night Shift', 
            'Morning Shift',
            'Evening Shift',
            'Flexible Hours'
        ];
        
        $shift = $this->faker->unique()->randomElement($shifts);
        
        return [
            'shift' => $shift,
            'description' => 'Work shift: ' . $shift,
            'is_default' => $this->faker->boolean(20)
        ];
    }
}
