<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\FunctionalArea>
 */
class FunctionalAreaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $functionalAreas = [
            'Software Engineering', 'IT Support', 'Human Resources Management', 'Digital Marketing', 'Sales Operations',
            'Financial Analysis', 'Business Operations', 'Customer Success', 'Product Strategy', 'UX/UI Design',
            'Technical Engineering', 'Quality Control', 'Research & Innovation', 'Legal Affairs', 'Office Administration',
            'Project Coordination', 'Business Development', 'Strategic Consulting', 'Learning & Development', 'Logistics Management',
            'Production Management', 'Health Services', 'Educational Services', 'Communications', 'Property Management'
        ];
        
        return [
            'name' => $this->faker->unique()->randomElement($functionalAreas),
            'is_default' => $this->faker->boolean(30), // 30% chance of being default
        ];
    }
} 