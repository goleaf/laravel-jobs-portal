<?php

namespace Database\Factories;

use App\Models\Todo;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TodoFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Todo::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'title' => $this->faker->sentence(4),
            'description' => $this->faker->paragraph(3),
            'due_date' => $this->faker->dateTimeBetween('now', '+30 days'),
            'is_completed' => $this->faker->boolean(20), // 20% will be completed
            'priority' => $this->faker->randomElement(['low', 'medium', 'high']),
        ];
    }

    /**
     * Indicate that the todo is completed.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function completed()
    {
        return $this->state(function (array $attributes) {
            return [
                'is_completed' => true,
            ];
        });
    }

    /**
     * Indicate that the todo is not completed.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function incomplete()
    {
        return $this->state(function (array $attributes) {
            return [
                'is_completed' => false,
            ];
        });
    }

    /**
     * Set the priority to high.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function highPriority()
    {
        return $this->state(function (array $attributes) {
            return [
                'priority' => 'high',
            ];
        });
    }
}
