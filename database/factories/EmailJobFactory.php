<?php

namespace Database\Factories;

use App\Models\Job;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\EmailJob>
 */
class EmailJobFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'job_id' => Job::factory(),
            'job_url' => $this->faker->url(),
            'friend_name' => $this->faker->name(),
            'friend_email' => $this->faker->email(),
            'is_active' => true,
            'is_sent' => false,
            'status' => 'pending',
            'open_count' => 0,
            'click_count' => 0,
        ];
    }
}
