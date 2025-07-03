<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\File>
 */
class FileFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'model_type' => 'App\\Models\\User',
            'model_id' => User::factory(),
            'collection_name' => $this->faker->randomElement(['documents', 'images', 'avatars']),
            'name' => $this->faker->word(),
            'file_name' => $this->faker->uuid().'.pdf',
            'mime_type' => $this->faker->randomElement(['application/pdf', 'image/jpeg', 'image/png']),
            'disk' => 'public',
            'path' => 'uploads/'.$this->faker->uuid(),
            'size' => $this->faker->numberBetween(1024, 5242880),
            'order_column' => $this->faker->numberBetween(1, 100),
            'custom_properties' => json_encode([]),
            'responsive_images' => json_encode([]),
            'is_active' => $this->faker->boolean(90),
            'is_public' => $this->faker->boolean(30),
            'is_temporary' => $this->faker->boolean(10),
        ];
    }
}
