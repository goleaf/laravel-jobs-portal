<?php

declare(strict_types=1);

namespace App\Http\Requests\Web;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Class JobDetailsRequest
 * Enterprise-grade validation for Web Job details operations
 * Handles individual job viewing with access control and analytics
 */
class JobDetailsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Public access for job details
    }

    public function rules(): array
    {
        return [
            // Job Identification
            'uniqueJobId' => [
                'sometimes',
                'string',
                'min:8',
                'max:50',
                'regex:/^[a-zA-Z0-9\-_]+$/',
                Rule::exists('jobs', 'job_id')->where('status', '!=', 'draft'),
            ],

            // Tracking & Analytics
            'ref' => [
                'sometimes',
                'string',
                'max:50',
                'regex:/^[a-zA-Z0-9\-_\.]+$/',
            ],
            'source' => [
                'sometimes',
                'string',
                'max:100',
                'in:search,category,company,featured,related,social,email,direct',
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

            // Sharing Parameters
            'share_platform' => [
                'sometimes',
                'string',
                'in:facebook,twitter,linkedin,email,whatsapp,telegram',
            ],

            // User Interaction Context
            'view_context' => [
                'sometimes',
                'string',
                'in:guest,candidate,employer,admin',
            ],
            'device_type' => [
                'sometimes',
                'string',
                'in:desktop,tablet,mobile',
            ],

            // Related Jobs Parameters
            'show_related' => [
                'sometimes',
                'boolean',
            ],
            'related_limit' => [
                'sometimes',
                'integer',
                'min:1',
                'max:20',
            ],

            // Social Integration
            'social_login' => [
                'sometimes',
                'boolean',
            ],
            'from_social' => [
                'sometimes',
                'string',
                'in:google,facebook,linkedin,twitter',
            ],

            // Application Intent Tracking
            'intent' => [
                'sometimes',
                'string',
                'in:view,apply,save,share,report',
            ],
            'step' => [
                'sometimes',
                'string',
                'in:view,details,requirements,company,apply_form,submit',
            ],

            // Performance Tracking
            'load_time' => [
                'sometimes',
                'numeric',
                'min:0',
                'max:300', // 5 minutes max
            ],
            'page_time' => [
                'sometimes',
                'integer',
                'min:0',
                'max:7200', // 2 hours max
            ],

            // Search Context (if coming from search)
            'search_query' => [
                'sometimes',
                'string',
                'max:200',
                'regex:/^[\p{L}\p{N}\s\-_\.@&,\(\)]+$/u',
            ],
            'search_filters' => [
                'sometimes',
                'string',
                'max:500',
            ],
            'search_position' => [
                'sometimes',
                'integer',
                'min:1',
                'max:1000',
            ],

            // Accessibility & UX
            'accessibility' => [
                'sometimes',
                'boolean',
            ],
            'high_contrast' => [
                'sometimes',
                'boolean',
            ],
            'font_size' => [
                'sometimes',
                'string',
                'in:small,medium,large,extra_large',
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

            // Company Profile Context
            'from_company' => [
                'sometimes',
                'boolean',
            ],
            'company_context' => [
                'sometimes',
                'string',
                'in:profile,jobs,about,reviews,gallery',
            ],
        ];
    }

    public function getValidatedWithDefaults(): array
    {
        $validated = $this->validated();

        return array_merge([
            'source' => 'direct',
            'view_context' => 'guest',
            'device_type' => 'desktop',
            'show_related' => true,
            'related_limit' => 6,
            'intent' => 'view',
            'step' => 'view',
            'accessibility' => false,
            'font_size' => 'medium',
            'locale' => app()->getLocale(),
        ], $validated);
    }

    public function messages(): array
    {
        return [
            'uniqueJobId.exists' => __('validation.custom.job.not_found'),
            'uniqueJobId.regex' => __('validation.custom.job.id_format'),
            'ref.regex' => __('validation.custom.job.ref_format'),
            'source.in' => __('validation.custom.job.source_invalid'),
            'utm_source.regex' => __('validation.custom.job.utm_format'),
            'share_platform.in' => __('validation.custom.job.share_platform_invalid'),
            'view_context.in' => __('validation.custom.job.view_context_invalid'),
            'device_type.in' => __('validation.custom.job.device_type_invalid'),
            'related_limit.max' => __('validation.custom.job.related_limit_exceeded'),
            'load_time.max' => __('validation.custom.job.load_time_invalid'),
            'search_query.regex' => __('validation.custom.job.search_query_format'),
            'locale.regex' => __('validation.custom.job.locale_format'),
        ];
    }

    protected function prepareForValidation(): void
    {
        // Get uniqueJobId from route parameter if not in request
        if (! $this->has('uniqueJobId') && $this->route('uniqueJobId')) {
            $this->merge(['uniqueJobId' => $this->route('uniqueJobId')]);
        }

        // Sanitize text inputs
        foreach (['ref', 'search_query'] as $field) {
            if ($this->has($field)) {
                $this->merge([$field => trim($this->input($field))]);
            }
        }

        // Normalize locale
        if ($this->has('locale')) {
            $this->merge(['locale' => strtolower(trim($this->locale))]);
        }

        // Auto-detect device type from user agent if not provided
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
    }

    public function getTrackingContext(): array
    {
        return [
            'job_id' => $this->input('uniqueJobId'),
            'source' => $this->input('source', 'direct'),
            'device' => $this->input('device_type', 'desktop'),
            'intent' => $this->input('intent', 'view'),
            'user_context' => $this->input('view_context', 'guest'),
            'utm_data' => $this->getUtmData(),
            'timestamp' => now()->toISOString(),
        ];
    }

    private function getUtmData(): array
    {
        return [
            'source' => $this->input('utm_source'),
            'medium' => $this->input('utm_medium'),
            'campaign' => $this->input('utm_campaign'),
        ];
    }

    public function getAnalyticsContext(): array
    {
        return [
            'operation' => 'job_details',
            'has_tracking' => $this->hasUtmData(),
            'from_search' => $this->filled('search_query'),
            'has_social_context' => $this->filled('from_social'),
            'accessibility_enabled' => $this->boolean('accessibility'),
            'performance_data' => $this->getPerformanceData(),
        ];
    }

    private function hasUtmData(): bool
    {
        return $this->filled(['utm_source', 'utm_medium', 'utm_campaign']);
    }

    private function getPerformanceData(): array
    {
        return [
            'load_time' => $this->input('load_time'),
            'page_time' => $this->input('page_time'),
            'has_performance_data' => $this->filled(['load_time', 'page_time']),
        ];
    }
}
