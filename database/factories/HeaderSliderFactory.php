<?php

namespace Database\Factories;

use App\Models\HeaderSlider;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\HeaderSlider>
 */
class HeaderSliderFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = HeaderSlider::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(),
            'sub_title' => $this->faker->sentence(),
            'description' => $this->faker->paragraph(),
            'button_text' => $this->faker->words(2, true),
            'button_url' => $this->faker->url(),
            'image_url' => $this->faker->imageUrl(),
            'is_active' => $this->faker->boolean(),
            'is_featured' => $this->faker->boolean(),
            'sort_order' => $this->faker->numberBetween(1, 100),
            'target' => $this->faker->randomElement(['_self', '_blank', '_parent', '_top']),
            'css_class' => $this->faker->word(),
            'metadata' => [],
            'published_at' => $this->faker->dateTimeBetween('-1 month', 'now'),
            'expires_at' => $this->faker->dateTimeBetween('now', '+1 month'),
        ];
    }

    /**
     * Indicate that the header slider is active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => true,
        ]);
    }

    /**
     * Indicate that the header slider is featured.
     */
    public function featured(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_featured' => true,
        ]);
    }
}
