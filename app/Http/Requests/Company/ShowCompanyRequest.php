<?php

namespace App\Http\Requests\Company;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\Company;

/**
 * Class ShowCompanyRequest
 * 
 * Handles company detail view requests with authorization checks, 
 * data loading options, analytics tracking, and multilingual support.
 */
class ShowCompanyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $company = $this->route('company');
        
        // Basic access control
        if (!$company) {
            return false;
        }

        // Public companies are viewable by everyone
        if ($company->is_active && !$company->is_private) {
            return true;
        }

        $user = auth()->user();
        
        // Unauthenticated users can't view private/inactive companies
        if (!$user) {
            return false;
        }

        // Company owners can always view their own company
        if ($company->user_id === $user->id) {
            return true;
        }

        // Admins can view any company
        if ($user->hasRole(['admin', 'super-admin'])) {
            return true;
        }

        // Employees of the company can view it
        if ($company->employees()->where('user_id', $user->id)->exists()) {
            return true;
        }

        // Users with specific permissions
        if ($user->can('view', $company)) {
            return true;
        }

        // Private companies require special access
        if ($company->is_private) {
            // Check if user has been granted access
            return $company->authorizedViewers()->where('user_id', $user->id)->exists();
        }

        // Inactive companies require admin access
        if (!$company->is_active) {
            return $user->hasRole(['admin', 'super-admin']);
        }

        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            // Data loading options
            'include_relations' => [
                'nullable',
                'array',
                'max:20',
            ],

            'include_relations.*' => [
                'string',
                Rule::in([
                    'user', 'industry', 'companySize', 'ownershipType', 'country',
                    'state', 'city', 'jobs', 'activeJobs', 'featuredJobs', 'recentJobs',
                    'employees', 'departments', 'reviews', 'ratings', 'followers',
                    'media', 'gallery', 'socialLinks', 'certifications', 'awards',
                    'benefits', 'culture', 'offices', 'technologies', 'clients',
                    'partnerships', 'financials', 'timeline', 'news', 'events'
                ]),
            ],

            'load_statistics' => [
                'nullable',
                'boolean',
            ],

            'load_analytics' => [
                'nullable',
                'boolean',
            ],

            'load_performance_metrics' => [
                'nullable',
                'boolean',
            ],

            'load_social_proof' => [
                'nullable',
                'boolean',
            ],

            // Job-related data
            'include_jobs' => [
                'nullable',
                'boolean',
            ],

            'jobs_limit' => [
                'nullable',
                'integer',
                'min:1',
                'max:50',
            ],

            'jobs_status' => [
                'nullable',
                'string',
                Rule::in(['active', 'all', 'featured', 'recent', 'expired']),
            ],

            'jobs_category' => [
                'nullable',
                'integer',
                Rule::exists('job_categories', 'id'),
            ],

            // Review and rating data
            'include_reviews' => [
                'nullable',
                'boolean',
            ],

            'reviews_limit' => [
                'nullable',
                'integer',
                'min:1',
                'max:20',
            ],

            'reviews_sort' => [
                'nullable',
                'string',
                Rule::in(['newest', 'oldest', 'highest_rated', 'lowest_rated', 'most_helpful']),
            ],

            'min_review_rating' => [
                'nullable',
                'integer',
                'min:1',
                'max:5',
            ],

            // Employee data
            'include_employees' => [
                'nullable',
                'boolean',
            ],

            'employees_limit' => [
                'nullable',
                'integer',
                'min:1',
                'max:100',
            ],

            'employees_role' => [
                'nullable',
                'string',
                'max:100',
            ],

            'employees_department' => [
                'nullable',
                'string',
                'max:100',
            ],

            // Media and gallery
            'include_media' => [
                'nullable',
                'boolean',
            ],

            'media_types' => [
                'nullable',
                'array',
                'max:10',
            ],

            'media_types.*' => [
                'string',
                Rule::in(['logo', 'cover', 'gallery', 'video', 'document', 'brochure']),
            ],

            'media_limit' => [
                'nullable',
                'integer',
                'min:1',
                'max:50',
            ],

            // Timeline and history
            'include_timeline' => [
                'nullable',
                'boolean',
            ],

            'timeline_limit' => [
                'nullable',
                'integer',
                'min:1',
                'max:20',
            ],

            'timeline_types' => [
                'nullable',
                'array',
                'max:10',
            ],

            'timeline_types.*' => [
                'string',
                Rule::in(['milestone', 'achievement', 'expansion', 'acquisition', 'award', 'product_launch']),
            ],

            // Financial and business data (admin only)
            'include_financials' => [
                'nullable',
                'boolean',
            ],

            'financial_period' => [
                'nullable',
                'string',
                Rule::in(['current_year', 'last_year', 'last_3_years', 'last_5_years', 'all']),
            ],

            // Analytics and tracking
            'track_view' => [
                'nullable',
                'boolean',
            ],

            'track_source' => [
                'nullable',
                'string',
                'max:100',
            ],

            'track_medium' => [
                'nullable',
                'string',
                'max:100',
            ],

            'track_campaign' => [
                'nullable',
                'string',
                'max:100',
            ],

            'referrer' => [
                'nullable',
                'url',
                'max:500',
            ],

            'user_intent' => [
                'nullable',
                'string',
                Rule::in(['job_search', 'company_research', 'competitor_analysis', 'partnership', 'investment']),
            ],

            // View preferences
            'view_type' => [
                'nullable',
                'string',
                Rule::in(['full', 'summary', 'preview', 'embed']),
            ],

            'format' => [
                'nullable',
                'string',
                Rule::in(['html', 'json', 'api']),
            ],

            'language' => [
                'nullable',
                'string',
                'size:2',
                Rule::in(['en', 'ar', 'es', 'fr', 'de', 'pt', 'ru', 'tr', 'zh']),
            ],

            // Cache control
            'no_cache' => [
                'nullable',
                'boolean',
            ],

            'cache_duration' => [
                'nullable',
                'integer',
                'min:60',
                'max:3600',
            ],

            // Admin-specific options
            'admin_view' => [
                'nullable',
                'boolean',
            ],

            'include_sensitive_data' => [
                'nullable',
                'boolean',
            ],

            'include_audit_trail' => [
                'nullable',
                'boolean',
            ],

            'include_compliance_data' => [
                'nullable',
                'boolean',
            ],

            // Privacy options
            'respect_privacy_settings' => [
                'nullable',
                'boolean',
            ],

            'anonymize_sensitive_data' => [
                'nullable',
                'boolean',
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            // Include relations messages
            'include_relations.array' => __('validation.company_show.include_relations.array'),
            'include_relations.max' => __('validation.company_show.include_relations.max'),
            'include_relations.*.in' => __('validation.company_show.include_relations.item_in'),

            // Jobs data messages
            'jobs_limit.min' => __('validation.company_show.jobs_limit.min'),
            'jobs_limit.max' => __('validation.company_show.jobs_limit.max'),
            'jobs_status.in' => __('validation.company_show.jobs_status.in'),
            'jobs_category.exists' => __('validation.company_show.jobs_category.exists'),

            // Reviews data messages
            'reviews_limit.min' => __('validation.company_show.reviews_limit.min'),
            'reviews_limit.max' => __('validation.company_show.reviews_limit.max'),
            'reviews_sort.in' => __('validation.company_show.reviews_sort.in'),
            'min_review_rating.min' => __('validation.company_show.min_review_rating.min'),
            'min_review_rating.max' => __('validation.company_show.min_review_rating.max'),

            // Employees data messages
            'employees_limit.min' => __('validation.company_show.employees_limit.min'),
            'employees_limit.max' => __('validation.company_show.employees_limit.max'),
            'employees_role.max' => __('validation.company_show.employees_role.max'),

            // Media messages
            'media_types.max' => __('validation.company_show.media_types.max'),
            'media_types.*.in' => __('validation.company_show.media_types.item_in'),
            'media_limit.min' => __('validation.company_show.media_limit.min'),
            'media_limit.max' => __('validation.company_show.media_limit.max'),

            // Timeline messages
            'timeline_limit.min' => __('validation.company_show.timeline_limit.min'),
            'timeline_limit.max' => __('validation.company_show.timeline_limit.max'),
            'timeline_types.*.in' => __('validation.company_show.timeline_types.item_in'),

            // Financial messages
            'financial_period.in' => __('validation.company_show.financial_period.in'),

            // Tracking messages
            'track_source.max' => __('validation.company_show.track_source.max'),
            'track_medium.max' => __('validation.company_show.track_medium.max'),
            'track_campaign.max' => __('validation.company_show.track_campaign.max'),
            'referrer.url' => __('validation.company_show.referrer.url'),
            'referrer.max' => __('validation.company_show.referrer.max'),
            'user_intent.in' => __('validation.company_show.user_intent.in'),

            // View preferences messages
            'view_type.in' => __('validation.company_show.view_type.in'),
            'format.in' => __('validation.company_show.format.in'),
            'language.size' => __('validation.company_show.language.size'),
            'language.in' => __('validation.company_show.language.in'),

            // Cache messages
            'cache_duration.min' => __('validation.company_show.cache_duration.min'),
            'cache_duration.max' => __('validation.company_show.cache_duration.max'),
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'include_relations' => __('validation.attributes.include_relations'),
            'jobs_limit' => __('validation.attributes.jobs_limit'),
            'jobs_status' => __('validation.attributes.jobs_status'),
            'jobs_category' => __('validation.attributes.jobs_category'),
            'reviews_limit' => __('validation.attributes.reviews_limit'),
            'reviews_sort' => __('validation.attributes.reviews_sort'),
            'min_review_rating' => __('validation.attributes.min_review_rating'),
            'employees_limit' => __('validation.attributes.employees_limit'),
            'employees_role' => __('validation.attributes.employees_role'),
            'employees_department' => __('validation.attributes.employees_department'),
            'media_types' => __('validation.attributes.media_types'),
            'media_limit' => __('validation.attributes.media_limit'),
            'timeline_limit' => __('validation.attributes.timeline_limit'),
            'timeline_types' => __('validation.attributes.timeline_types'),
            'financial_period' => __('validation.attributes.financial_period'),
            'track_source' => __('validation.attributes.track_source'),
            'track_medium' => __('validation.attributes.track_medium'),
            'track_campaign' => __('validation.attributes.track_campaign'),
            'referrer' => __('validation.attributes.referrer'),
            'user_intent' => __('validation.attributes.user_intent'),
            'view_type' => __('validation.attributes.view_type'),
            'format' => __('validation.attributes.format'),
            'language' => __('validation.attributes.language'),
            'cache_duration' => __('validation.attributes.cache_duration'),
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Set default values
        $this->merge([
            'view_type' => $this->input('view_type', 'full'),
            'format' => $this->input('format', 'html'),
            'language' => $this->input('language', app()->getLocale()),
            'track_view' => $this->input('track_view', true),
            'respect_privacy_settings' => $this->input('respect_privacy_settings', true),
            'jobs_limit' => $this->input('jobs_limit', 10),
            'reviews_limit' => $this->input('reviews_limit', 5),
            'employees_limit' => $this->input('employees_limit', 20),
            'media_limit' => $this->input('media_limit', 20),
            'timeline_limit' => $this->input('timeline_limit', 10),
            'jobs_status' => $this->input('jobs_status', 'active'),
            'reviews_sort' => $this->input('reviews_sort', 'newest'),
            'financial_period' => $this->input('financial_period', 'current_year'),
        ]);

        // Ensure boolean fields are properly typed
        $booleanFields = [
            'load_statistics', 'load_analytics', 'load_performance_metrics',
            'load_social_proof', 'include_jobs', 'include_reviews', 'include_employees',
            'include_media', 'include_timeline', 'include_financials', 'track_view',
            'no_cache', 'admin_view', 'include_sensitive_data', 'include_audit_trail',
            'include_compliance_data', 'respect_privacy_settings', 'anonymize_sensitive_data'
        ];

        foreach ($booleanFields as $field) {
            if ($this->has($field)) {
                $this->merge([$field => $this->boolean($field)]);
            }
        }

        // Ensure numeric fields are properly typed
        $numericFields = [
            'jobs_limit', 'jobs_category', 'reviews_limit', 'min_review_rating',
            'employees_limit', 'media_limit', 'timeline_limit', 'cache_duration'
        ];

        foreach ($numericFields as $field) {
            if ($this->has($field) && !empty($this->$field)) {
                $this->merge([$field => (int) $this->$field]);
            }
        }

        // Clean string fields
        $stringFields = [
            'track_source', 'track_medium', 'track_campaign', 'employees_role', 'employees_department'
        ];

        foreach ($stringFields as $field) {
            if ($this->has($field) && !empty($this->$field)) {
                $this->merge([$field => trim($this->$field)]);
            }
        }
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $this->validateAdminAccess($validator);
            $this->validateDataAccess($validator);
            $this->validateBusinessLogic($validator);
        });
    }

    /**
     * Validate admin access requirements.
     */
    protected function validateAdminAccess($validator): void
    {
        $user = auth()->user();

        // Admin-specific options require admin role
        $adminOptions = [
            'include_financials', 'admin_view', 'include_sensitive_data',
            'include_audit_trail', 'include_compliance_data'
        ];

        foreach ($adminOptions as $option) {
            if ($this->has($option) && $this->$option && (!$user || !$user->hasRole(['admin', 'super-admin']))) {
                $validator->errors()->add($option, __('validation.company_show.admin_access_required'));
            }
        }
    }

    /**
     * Validate data access permissions.
     */
    protected function validateDataAccess($validator): void
    {
        $company = $this->route('company');
        $user = auth()->user();

        // Financial data requires ownership or admin access
        if ($this->include_financials && $company) {
            if (!$user || (!$user->hasRole(['admin', 'super-admin']) && $company->user_id !== $user->id)) {
                $validator->errors()->add('include_financials', __('validation.company_show.financial_access_denied'));
            }
        }

        // Sensitive data requires special permissions
        if ($this->include_sensitive_data && $company) {
            if (!$user || !$user->can('viewSensitiveData', $company)) {
                $validator->errors()->add('include_sensitive_data', __('validation.company_show.sensitive_data_access_denied'));
            }
        }
    }

    /**
     * Validate business logic constraints.
     */
    protected function validateBusinessLogic($validator): void
    {
        // Validate reasonable limits for performance
        if ($this->jobs_limit && $this->jobs_limit > 50) {
            $validator->errors()->add('jobs_limit', __('validation.company_show.jobs_limit_exceeded'));
        }

        if ($this->reviews_limit && $this->reviews_limit > 20) {
            $validator->errors()->add('reviews_limit', __('validation.company_show.reviews_limit_exceeded'));
        }

        if ($this->employees_limit && $this->employees_limit > 100) {
            $validator->errors()->add('employees_limit', __('validation.company_show.employees_limit_exceeded'));
        }

        // Validate include_relations array size for performance
        if ($this->include_relations && count($this->include_relations) > 20) {
            $validator->errors()->add('include_relations', __('validation.company_show.too_many_relations'));
        }
    }

    /**
     * Get data loading options.
     */
    public function getDataLoadingOptions(): array
    {
        return [
            'include_relations' => $this->include_relations ?? [],
            'load_statistics' => $this->load_statistics ?? false,
            'load_analytics' => $this->load_analytics ?? false,
            'load_performance_metrics' => $this->load_performance_metrics ?? false,
            'load_social_proof' => $this->load_social_proof ?? false,
        ];
    }

    /**
     * Get jobs loading options.
     */
    public function getJobsOptions(): array
    {
        return [
            'include_jobs' => $this->include_jobs ?? false,
            'jobs_limit' => $this->jobs_limit ?? 10,
            'jobs_status' => $this->jobs_status ?? 'active',
            'jobs_category' => $this->jobs_category,
        ];
    }

    /**
     * Get reviews loading options.
     */
    public function getReviewsOptions(): array
    {
        return [
            'include_reviews' => $this->include_reviews ?? false,
            'reviews_limit' => $this->reviews_limit ?? 5,
            'reviews_sort' => $this->reviews_sort ?? 'newest',
            'min_review_rating' => $this->min_review_rating,
        ];
    }

    /**
     * Get tracking options.
     */
    public function getTrackingOptions(): array
    {
        return [
            'track_view' => $this->track_view ?? true,
            'track_source' => $this->track_source,
            'track_medium' => $this->track_medium,
            'track_campaign' => $this->track_campaign,
            'referrer' => $this->referrer,
            'user_intent' => $this->user_intent,
        ];
    }

    /**
     * Get view preferences.
     */
    public function getViewPreferences(): array
    {
        return [
            'view_type' => $this->view_type ?? 'full',
            'format' => $this->format ?? 'html',
            'language' => $this->language ?? app()->getLocale(),
            'respect_privacy_settings' => $this->respect_privacy_settings ?? true,
            'anonymize_sensitive_data' => $this->anonymize_sensitive_data ?? false,
        ];
    }
}
