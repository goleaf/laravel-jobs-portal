<?php

namespace Database\Factories;

use App\Models\JobApplication;
use App\Models\JobStage;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\JobApplicationSchedule>
 */
class JobApplicationScheduleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'job_application_id' => JobApplication::factory(),
            'stage_id' => JobStage::factory(),
            'time' => $this->faker->time(),
            'date' => $this->faker->date(),
            'notes' => $this->faker->optional()->paragraph(),
            'status' => $this->faker->numberBetween(0, 3),
            'batch' => $this->faker->optional()->numberBetween(1, 10),
            'rejected_slot_notes' => $this->faker->optional()->text(),
            'employer_cancel_slot_notes' => $this->faker->optional()->text(),
        ];
    }
}
