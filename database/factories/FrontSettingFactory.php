<?php

namespace Database\Factories;

use App\Models\FrontSetting;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\FrontSetting>
 */
class FrontSettingFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = FrontSetting::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $keys = [
            'site_title',
            'featured_jobs_enabled',
            'job_alert_enabled',
            'company_registration_enabled',
            'candidate_registration_enabled',
            'job_posting_enabled',
            'resume_upload_enabled',
            'email_verification_enabled',
            'social_login_enabled',
        ];

        return [
            'key' => $this->faker->randomElement($keys),
            'value' => $this->faker->randomElement(['1', '0', $this->faker->sentence(3)]),
        ];
    }

    /**
     * Indicate that the setting is enabled.
     */
    public function enabled(): static
    {
        return $this->state(fn (array $attributes) => [
            'value' => '1',
        ]);
    }

    /**
     * Indicate that the setting is disabled.
     */
    public function disabled(): static
    {
        return $this->state(fn (array $attributes) => [
            'value' => '0',
        ]);
    }
}
