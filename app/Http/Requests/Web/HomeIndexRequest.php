<?php

declare(strict_types=1);

namespace App\Http\Requests\Web;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class HomeIndexRequest
 * Enterprise-grade validation for Web Home index operations
 * Handles homepage display with personalization and analytics
 */
class HomeIndexRequest extends FormRequest
{
    private const MAX_FEATURED_JOBS = 20;
    private const MAX_FEATURED_COMPANIES = 15;
    private const MAX_JOB_CATEGORIES = 12;

    public function authorize(): bool
    {
        return true; // Public access for homepage
    }

    public function rules(): array
    {
        return [
            // Personalization Parameters
            'personalized' => [
                'sometimes',
                'boolean',
            ],
            'location_based' => [
                'sometimes',
                'boolean',
            ],
            'user_location' => [
                'sometimes',
                'string',
                'max:200',
                'regex:/^[\p{L}\p{N}\s\-_\.,]+$/u',
            ],
            'detected_location' => [
                'sometimes',
                'string',
                'max:200',
                'regex:/^[\p{L}\p{N}\s\-_\.,]+$/u',
            ],

            // Content Limits
            'featured_jobs_limit' => [
                'sometimes',
                'integer',
                'min:1',
                'max:'.self::MAX_FEATURED_JOBS,
            ],
            'featured_companies_limit' => [
                'sometimes',
                'integer',
                'min:1',
                'max:'.self::MAX_FEATURED_COMPANIES,
            ],
            'job_categories_limit' => [
                'sometimes',
                'integer',
                'min:1',
                'max:'.self::MAX_JOB_CATEGORIES,
            ],
            'latest_jobs_limit' => [
                'sometimes',
                'integer',
                'min:1',
                'max:20',
            ],

            // Search Parameters (from homepage search)
            'quick_search' => [
                'sometimes',
                'string',
                'min:1',
                'max:200',
                'regex:/^[\p{L}\p{N}\s\-_\.@&,\(\)]+$/u',
            ],
            'search_location' => [
                'sometimes',
                'string',
                'max:100',
                'regex:/^[\p{L}\p{N}\s\-_\.,]+$/u',
            ],

            // Display Preferences
            'layout' => [
                'sometimes',
                'string',
                'in:default,compact,detailed,mobile',
            ],
            'theme' => [
                'sometimes',
                'string',
                'in:light,dark,auto',
            ],
            'show_stats' => [
                'sometimes',
                'boolean',
            ],
            'show_testimonials' => [
                'sometimes',
                'boolean',
            ],
            'show_blog' => [
                'sometimes',
                'boolean',
            ],
            'show_newsletter' => [
                'sometimes',
                'boolean',
            ],

            // Analytics & Tracking
            'session_id' => [
                'sometimes',
                'string',
                'max:100',
                'regex:/^[a-zA-Z0-9\-_]+$/',
            ],
            'visitor_id' => [
                'sometimes',
                'string',
                'max:100',
                'regex:/^[a-zA-Z0-9\-_]+$/',
            ],
            'ref' => [
                'sometimes',
                'string',
                'max:200',
                'regex:/^[a-zA-Z0-9\-_\.\/\?=&]+$/',
            ],
            'utm_source' => [
                'sometimes',
                'string',
                'max:100',
                'regex:/^[a-zA-Z0-9\-_\.]+$/',
            ],
            'utm_medium' => [
                'sometimes',
                'string',
                'max:100',
                'regex:/^[a-zA-Z0-9\-_\.]+$/',
            ],
            'utm_campaign' => [
                'sometimes',
                'string',
                'max:100',
                'regex:/^[a-zA-Z0-9\-_\.]+$/',
            ],

            // Device & Browser Context
            'device_type' => [
                'sometimes',
                'string',
                'in:desktop,tablet,mobile',
            ],
            'browser' => [
                'sometimes',
                'string',
                'max:50',
                'regex:/^[a-zA-Z0-9\s\-_\.]+$/',
            ],
            'screen_resolution' => [
                'sometimes',
                'string',
                'regex:/^\d{3,4}x\d{3,4}$/',
            ],

            // Language & Localization
            'locale' => [
                'sometimes',
                'string',
                'size:2',
                'regex:/^[a-z]{2}$/',
            ],
            'timezone' => [
                'sometimes',
                'string',
                'max:50',
                'timezone',
            ],
            'currency' => [
                'sometimes',
                'string',
                'size:3',
                'regex:/^[A-Z]{3}$/',
            ],

            // Performance & UX
            'lazy_load' => [
                'sometimes',
                'boolean',
            ],
            'preload_images' => [
                'sometimes',
                'boolean',
            ],
            'animation_enabled' => [
                'sometimes',
                'boolean',
            ],
            'reduced_motion' => [
                'sometimes',
                'boolean',
            ],

            // Content Filtering
            'hide_expired' => [
                'sometimes',
                'boolean',
            ],
            'featured_only' => [
                'sometimes',
                'boolean',
            ],
            'recent_only' => [
                'sometimes',
                'boolean',
            ],

            // A/B Testing
            'variant' => [
                'sometimes',
                'string',
                'max:50',
                'regex:/^[a-zA-Z0-9\-_]+$/',
            ],
            'experiment_id' => [
                'sometimes',
                'string',
                'max:100',
                'regex:/^[a-zA-Z0-9\-_]+$/',
            ],

            // Social Integration
            'social_login' => [
                'sometimes',
                'boolean',
            ],
            'social_platform' => [
                'sometimes',
                'string',
                'in:google,facebook,linkedin,twitter,github',
            ],

            // Newsletter & Marketing
            'newsletter_popup' => [
                'sometimes',
                'boolean',
            ],
            'marketing_consent' => [
                'sometimes',
                'boolean',
            ],
            'cookie_consent' => [
                'sometimes',
                'boolean',
            ],
        ];
    }

    public function getValidatedWithDefaults(): array
    {
        $validated = $this->validated();

        return array_merge([
            'personalized' => false,
            'location_based' => false,
            'featured_jobs_limit' => 6,
            'featured_companies_limit' => 8,
            'job_categories_limit' => 8,
            'latest_jobs_limit' => 4,
            'layout' => 'default',
            'theme' => 'light',
            'show_stats' => true,
            'show_testimonials' => true,
            'show_blog' => true,
            'show_newsletter' => true,
            'device_type' => 'desktop',
            'locale' => app()->getLocale(),
            'lazy_load' => true,
            'animation_enabled' => true,
            'hide_expired' => true,
            'featured_only' => false,
            'recent_only' => false,
            'newsletter_popup' => true,
        ], $validated);
    }

    public function messages(): array
    {
        return [
            'user_location.regex' => __('validation.custom.home.location_format'),
            'quick_search.regex' => __('validation.custom.home.search_format'),
            'featured_jobs_limit.max' => __('validation.custom.home.featured_jobs_limit'),
            'layout.in' => __('validation.custom.home.layout_invalid'),
            'theme.in' => __('validation.custom.home.theme_invalid'),
            'device_type.in' => __('validation.custom.home.device_type_invalid'),
            'locale.regex' => __('validation.custom.home.locale_format'),
            'currency.regex' => __('validation.custom.home.currency_format'),
            'screen_resolution.regex' => __('validation.custom.home.resolution_format'),
        ];
    }

    protected function prepareForValidation(): void
    {
        // Sanitize text inputs
        foreach (['quick_search', 'search_location', 'user_location'] as $field) {
            if ($this->has($field)) {
                $this->merge([$field => trim($this->input($field))]);
            }
        }

        // Normalize locale and currency
        if ($this->has('locale')) {
            $this->merge(['locale' => strtolower(trim($this->locale))]);
        }
        if ($this->has('currency')) {
            $this->merge(['currency' => strtoupper(trim($this->currency))]);
        }

        // Auto-detect device type if not provided
        if (! $this->has('device_type')) {
            $userAgent = $this->userAgent();
            $deviceType = 'desktop';
            if (preg_match('/Mobile|Android|iPhone/', $userAgent)) {
                $deviceType = 'mobile';
            } elseif (preg_match('/iPad|Tablet/', $userAgent)) {
                $deviceType = 'tablet';
            }
            $this->merge(['device_type' => $deviceType]);
        }

        // Auto-detect reduced motion preference
        if (! $this->has('reduced_motion') && $this->hasHeader('Sec-CH-Prefers-Reduced-Motion')) {
            $this->merge(['reduced_motion' => $this->header('Sec-CH-Prefers-Reduced-Motion') === 'reduce']);
        }
    }

    public function getPersonalizationContext(): array
    {
        return [
            'is_personalized' => $this->boolean('personalized'),
            'location_based' => $this->boolean('location_based'),
            'user_location' => $this->input('user_location'),
            'device_type' => $this->input('device_type', 'desktop'),
            'locale' => $this->input('locale', app()->getLocale()),
            'has_utm_data' => $this->hasUtmData(),
        ];
    }

    public function getDisplayPreferences(): array
    {
        return [
            'layout' => $this->input('layout', 'default'),
            'theme' => $this->input('theme', 'light'),
            'content_limits' => [
                'featured_jobs' => $this->input('featured_jobs_limit', 6),
                'featured_companies' => $this->input('featured_companies_limit', 8),
                'job_categories' => $this->input('job_categories_limit', 8),
                'latest_jobs' => $this->input('latest_jobs_limit', 4),
            ],
            'sections' => [
                'stats' => $this->boolean('show_stats', true),
                'testimonials' => $this->boolean('show_testimonials', true),
                'blog' => $this->boolean('show_blog', true),
                'newsletter' => $this->boolean('show_newsletter', true),
            ],
            'ux_preferences' => [
                'lazy_load' => $this->boolean('lazy_load', true),
                'animations' => $this->boolean('animation_enabled', true),
                'reduced_motion' => $this->boolean('reduced_motion', false),
            ],
        ];
    }

    private function hasUtmData(): bool
    {
        return $this->filled(['utm_source', 'utm_medium', 'utm_campaign']);
    }
}
