<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CustomMedia>
 */
class CustomMediaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'model_type' => $this->faker->word,
            'model_id' => random_int(1, 100),
            'uuid' => $this->faker->uuid,
            'collection_name' => $this->faker->word,
            'name' => $this->faker->word,
            'file_name' => $this->faker->word . "." . $this->faker->fileExtension,
            'mime_type' => $this->faker->mimeType,
            'disk' => "public",
            'conversions_disk' => "public",
            'size' => $this->faker->numberBetween(1000, 1000000),
        ];
    }
}
