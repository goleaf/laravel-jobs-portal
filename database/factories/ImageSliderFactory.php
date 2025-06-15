<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ImageSlider>
 */
class ImageSliderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'description' => $this->faker->paragraph(2),
            'is_active' => $this->faker->boolean(80),
            'image_path' => $this->faker->imageUrl(800, 600, 'business'),
            'settings' => json_encode([
                'title' => $this->faker->sentence(3),
                'link_url' => $this->faker->url(),
                'target' => $this->faker->randomElement(['_self', '_blank', '_parent']),
                'sort_order' => $this->faker->numberBetween(1, 100),
            ]),
        ];
    }
} 