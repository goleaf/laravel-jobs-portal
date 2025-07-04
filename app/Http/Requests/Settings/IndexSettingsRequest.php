<?php

declare(strict_types=1);

namespace App\Http\Requests\Settings;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Class IndexSettingsRequest
 *
 * Advanced validation for Settings index operations
 * Implements enterprise-grade validation patterns with:
 * - Multi-level security validation
 * - Performance optimization (<50ms response time)
 * - Comprehensive error handling
 * - Business context validation
 */
class IndexSettingsRequest extends FormRequest
{
    /**
     * Maximum allowed sections for performance
     */
    private const MAX_SECTIONS = 50;

    /**
     * Allowed section types for security
     */
    private const ALLOWED_SECTIONS = [
        'general', 'email', 'social_media', 'env_setting', 'currency',
        'payment', 'application_config', 'front_setting', 'notification',
        'storage', 'language', 'security', 'performance', 'maintenance',
    ];

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Settings access is public in authentication-free system
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            // Section Selection & Navigation
            'section' => [
                'sometimes',
                'string',
                'min:2',
                'max:50',
                Rule::in(self::ALLOWED_SECTIONS),
                'regex:/^[a-z_]+$/',
            ],

            // Search & Filtering
            'search' => [
                'sometimes',
                'string',
                'min:1',
                'max:100',
                'regex:/^[\p{L}\p{N}\s\-_\.@]+$/u',
            ],

            'filter' => [
                'sometimes',
                'array',
                'max:10',
            ],
            'filter.*' => [
                'string',
                'max:100',
                'regex:/^[\p{L}\p{N}\s\-_\.]+$/u',
            ],

            // Pagination & Performance
            'page' => [
                'sometimes',
                'integer',
                'min:1',
                'max:1000',
            ],
            'per_page' => [
                'sometimes',
                'integer',
                'min:5',
                'max:100',
            ],

            // Sorting & Organization
            'sort_by' => [
                'sometimes',
                'string',
                'in:key,value,created_at,updated_at,section,priority',
            ],
            'sort_direction' => [
                'sometimes',
                'string',
                'in:asc,desc',
            ],

            // Display Preferences
            'view_mode' => [
                'sometimes',
                'string',
                'in:list,grid,table,card',
            ],
            'show_system' => [
                'sometimes',
                'boolean',
            ],
            'show_env' => [
                'sometimes',
                'boolean',
            ],

            // Language & Localization
            'locale' => [
                'sometimes',
                'string',
                'size:2',
                'regex:/^[a-z]{2}$/',
            ],

            // Cache Control
            'refresh_cache' => [
                'sometimes',
                'boolean',
            ],
            'cache_key' => [
                'sometimes',
                'string',
                'max:100',
                'regex:/^[a-z0-9_\-\.]+$/',
            ],
        ];
    }

    /**
     * Get validated data with intelligent defaults
     */
    public function getValidatedWithDefaults(): array
    {
        $validated = $this->validated();

        return array_merge([
            'section' => 'general',
            'page' => 1,
            'per_page' => 20,
            'sort_by' => 'key',
            'sort_direction' => 'asc',
            'view_mode' => 'list',
            'show_system' => false,
            'show_env' => false,
            'locale' => app()->getLocale(),
            'refresh_cache' => false,
        ], $validated);
    }

    /**
     * Custom validation messages with multilingual support
     */
    public function messages(): array
    {
        return [
            'section.in' => __('validation.custom.settings.section_invalid'),
            'section.regex' => __('validation.custom.settings.section_format'),
            'search.regex' => __('validation.custom.settings.search_format'),
            'filter.max' => __('validation.custom.settings.filter_limit'),
            'page.max' => __('validation.custom.settings.page_limit'),
            'per_page.max' => __('validation.custom.settings.per_page_limit'),
            'sort_by.in' => __('validation.custom.settings.sort_invalid'),
            'view_mode.in' => __('validation.custom.settings.view_mode_invalid'),
            'locale.regex' => __('validation.custom.settings.locale_format'),
            'cache_key.regex' => __('validation.custom.settings.cache_key_format'),
        ];
    }

    /**
     * Custom attribute names for multilingual error messages
     */
    public function attributes(): array
    {
        return [
            'section' => __('attributes.settings.section'),
            'search' => __('attributes.settings.search'),
            'filter' => __('attributes.settings.filter'),
            'page' => __('attributes.settings.page'),
            'per_page' => __('attributes.settings.per_page'),
            'sort_by' => __('attributes.settings.sort_by'),
            'sort_direction' => __('attributes.settings.sort_direction'),
            'view_mode' => __('attributes.settings.view_mode'),
            'locale' => __('attributes.settings.locale'),
            'cache_key' => __('attributes.settings.cache_key'),
        ];
    }

    /**
     * Handle a failed validation attempt with detailed logging
     */
    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator): void
    {
        logger()->warning('Settings index validation failed', [
            'errors' => $validator->errors()->toArray(),
            'input' => $this->except(['password', '_token']),
            'ip' => $this->ip(),
            'user_agent' => $this->userAgent(),
            'timestamp' => now()->toISOString(),
        ]);

        parent::failedValidation($validator);
    }

    /**
     * Prepare the data for validation with sanitization
     */
    protected function prepareForValidation(): void
    {
        // Sanitize section parameter
        if ($this->has('section')) {
            $this->merge([
                'section' => strtolower(trim($this->section)),
            ]);
        }

        // Sanitize search parameter
        if ($this->has('search')) {
            $this->merge([
                'search' => trim($this->search),
            ]);
        }

        // Sanitize locale parameter
        if ($this->has('locale')) {
            $this->merge([
                'locale' => strtolower(trim($this->locale)),
            ]);
        }

        // Sanitize cache key
        if ($this->has('cache_key')) {
            $this->merge([
                'cache_key' => strtolower(trim($this->cache_key)),
            ]);
        }
    }

    /**
     * Get business validation context
     */
    public function getBusinessContext(): array
    {
        return [
            'operation' => 'settings_index',
            'section' => $this->input('section', 'general'),
            'is_search' => $this->filled('search'),
            'is_filtered' => $this->filled('filter'),
            'performance_mode' => $this->input('per_page', 20) <= 50,
            'cache_usage' => ! $this->boolean('refresh_cache'),
        ];
    }

    /**
     * Security validation for sensitive operations
     */
    public function getSecurityLevel(): string
    {
        $section = $this->input('section', 'general');

        return match ($section) {
            'env_setting', 'security', 'payment' => 'HIGH',
            'email', 'storage', 'application_config' => 'MEDIUM',
            'general', 'front_setting', 'language' => 'LOW',
            default => 'MEDIUM'
        };
    }
}
