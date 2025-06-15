<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Language>
 */
class LanguageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $languages = [
            ['code' => 'en', 'name' => 'English'],
            ['code' => 'es', 'name' => 'Spanish'],
            ['code' => 'fr', 'name' => 'French'],
            ['code' => 'de', 'name' => 'German'],
            ['code' => 'it', 'name' => 'Italian'],
            ['code' => 'pt', 'name' => 'Portuguese'],
            ['code' => 'ru', 'name' => 'Russian'],
            ['code' => 'zh', 'name' => 'Chinese'],
            ['code' => 'ja', 'name' => 'Japanese'],
            ['code' => 'ko', 'name' => 'Korean'],
            ['code' => 'ar', 'name' => 'Arabic'],
            ['code' => 'hi', 'name' => 'Hindi'],
            ['code' => 'tr', 'name' => 'Turkish'],
            ['code' => 'nl', 'name' => 'Dutch'],
            ['code' => 'sv', 'name' => 'Swedish'],
        ];

        $language = $this->faker->unique()->randomElement($languages);

        return [
            'language' => $language['name'],
            'iso_code' => $language['code'],
            'is_default' => $this->faker->boolean(10),
        ];
    }
}
