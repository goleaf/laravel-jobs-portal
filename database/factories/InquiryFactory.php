<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Inquiry>
 */
class InquiryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->email(),
            'phone_no' => $this->faker->phoneNumber(),
            'subject' => $this->faker->sentence(),
            'message' => $this->faker->text(),
            'is_active' => true,
            'is_read' => false,
            'is_resolved' => false,
            'status' => $this->faker->randomElement(['pending', 'in_progress', 'resolved']),
            'priority' => $this->faker->numberBetween(1, 4),
            'category' => $this->faker->randomElement(['general', 'technical', 'billing', 'support']),
        ];
    }
}
