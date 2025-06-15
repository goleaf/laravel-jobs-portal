<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class CustomMediaFactory extends Factory
{
    public function definition(): array
    {
        return [
            'model_type' => fake()->word(),
            'model_id' => fake()->numberBetween(1, 100),
            'uuid' => fake()->uuid(),
            'collection_name' => fake()->word(),
            'name' => fake()->word(),
            'file_name' => fake()->word() . '.' . fake()->fileExtension(),
            'mime_type' => fake()->mimeType(),
            'disk' => 'public',
            'conversions_disk' => 'public',
            'size' => fake()->numberBetween(1000, 1000000),
            'manipulations' => json_encode([]),
            'custom_properties' => json_encode([]),
            'generated_conversions' => json_encode([]),            'responsive_images' => json_encode([]),            'order_column' => fake()->numberBetween(1, 100),
        ];
    }
}
