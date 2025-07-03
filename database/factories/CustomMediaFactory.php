<?php

namespace Database\Factories;

use App\Models\CustomMedia;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;

class CustomMediaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $attributes = [
            'model_type' => fake()->word(),
            'model_id' => fake()->numberBetween(1, 100),
            'uuid' => fake()->uuid(),
            'collection_name' => fake()->word(),
            'name' => fake()->word(),
            'file_name' => fake()->word().'.'.fake()->fileExtension(),
            'mime_type' => fake()->mimeType(),
            'disk' => 'public',
            'conversions_disk' => 'public',
            'size' => fake()->numberBetween(1000, 1000000),
            'manipulations' => json_encode([]),
            'custom_properties' => json_encode([
                'alt_text' => fake()->sentence(),
                'caption' => fake()->sentence(),
                'description' => fake()->paragraph(),
            ]),
            'generated_conversions' => json_encode([]),
            'order_column' => fake()->numberBetween(1, 100),
        ];

        // Use Laravel 12.16 Arr::hasAll() to validate factory data
        if (! Arr::hasAll($attributes, ['name', 'file_name', 'mime_type', 'collection_name', 'size'])) {
            throw new \InvalidArgumentException('CustomMediaFactory: Missing required fields');
        }

        return $attributes;
    }

    /**
     * Create a media item with enhanced validation
     */
    public function withValidation(): static
    {
        return $this->afterMaking(function (CustomMedia $media) {
            $attributes = $media->getAttributes();

            // Validate using our enhanced model method
            CustomMedia::validateFactoryData($attributes);
        });
    }

    /**
     * Create media with specific collection
     */
    public function forCollection(string $collection): static
    {
        return $this->state(fn (array $attributes) => [
            'collection_name' => $collection,
        ]);
    }

    /**
     * Create image media
     */
    public function image(): static
    {
        return $this->state(fn (array $attributes) => [
            'mime_type' => fake()->randomElement([
                'image/jpeg',
                'image/png',
                'image/gif',
                'image/webp',
            ]),
            'file_name' => fake()->word().'.'.fake()->randomElement(['jpg', 'png', 'gif', 'webp']),
        ]);
    }

    /**
     * Create document media
     */
    public function document(): static
    {
        return $this->state(fn (array $attributes) => [
            'mime_type' => fake()->randomElement([
                'application/pdf',
                'application/msword',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'application/vnd.ms-excel',
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ]),
            'file_name' => fake()->word().'.'.fake()->randomElement(['pdf', 'doc', 'docx', 'xls', 'xlsx']),
        ]);
    }

    /**
     * Create video media
     */
    public function video(): static
    {
        return $this->state(fn (array $attributes) => [
            'mime_type' => fake()->randomElement([
                'video/mp4',
                'video/avi',
                'video/quicktime',
                'video/x-msvideo',
            ]),
            'file_name' => fake()->word().'.'.fake()->randomElement(['mp4', 'avi', 'mov', 'wmv']),
        ]);
    }
}
