<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\EmailTemplate>
 */
class EmailTemplateFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $attributes = [
            'template_name' => $this->faker->unique()->word(),
            'subject' => $this->faker->sentence(),
            'body' => $this->faker->paragraphs(3, true),
            'variables' => json_encode([
                'user_name' => '{{user_name}}',
                'job_title' => '{{job_title}}',
                'company_name' => '{{company_name}}',
                'application_date' => '{{application_date}}'
            ]),
        ];

        // Use Laravel 12.16 Arr::hasAll() to validate required fields
        if (!Arr::hasAll($attributes, ['template_name', 'subject', 'body', 'variables'])) {
            throw new \InvalidArgumentException('EmailTemplateFactory: Missing required fields');
        }

        return $attributes;
    }

    /**
     * Create template with specific variables
     */
    public function withVariables(array $variables): static
    {
        return $this->state(fn (array $attributes) => [
            'variables' => json_encode($variables),
        ]);
    }

    /**
     * Create job application template
     */
    public function jobApplication(): static
    {
        return $this->state(fn (array $attributes) => [
            'template_name' => 'job_application_confirmation',
            'subject' => 'Your Application for {{job_title}} at {{company_name}}',
            'body' => 'Dear {{user_name}}, Thank you for applying to {{job_title}} at {{company_name}}. We have received your application on {{application_date}}.',
            'variables' => json_encode([
                'user_name' => '{{user_name}}',
                'job_title' => '{{job_title}}',
                'company_name' => '{{company_name}}',
                'application_date' => '{{application_date}}'
            ]),
        ]);
    }
} 