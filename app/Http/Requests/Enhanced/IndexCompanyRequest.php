<?php

namespace App\Http\Requests\Enhanced;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class IndexCompanyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Based on user requirements: no auth system
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<mixed>|string|ValidationRule>
     */
    public function rules(): array
    {
        return [
            // Pagination parameters
            'page' => [
                'sometimes',
                'integer',
                'min:1',
                'max:9999',
            ],

            'per_page' => [
                'sometimes',
                'integer',
                'min:5',
                'max:100',
                Rule::in([5, 10, 15, 20, 25, 50, 100]),
            ],

            // Search parameters
            'search' => [
                'sometimes',
                'string',
                'max:255',
                'min:2',
                function ($attribute, $value, $fail) {
                    if ($this->containsInappropriateContent($value)) {
                        $fail(__('validation.inappropriate_search_content'));
                    }
                },
            ],

            'search_type' => [
                'sometimes',
                'string',
                Rule::in(['company_name', 'email', 'description', 'website', 'industry', 'location', 'all']),
            ],

            'search_fields' => [
                'sometimes',
                'array',
                'max:10',
            ],

            'search_fields.*' => [
                'string',
                Rule::in(['company_name', 'email', 'description', 'website', 'industry', 'location', 'phone']),
            ],

            // Company status and verification filters
            'status' => [
                'sometimes',
                'string',
                Rule::in(['active', 'inactive', 'pending', 'suspended', 'verified', 'unverified', 'all']),
            ],

            'verification_status' => [
                'sometimes',
                'string',
                Rule::in(['verified', 'pending', 'rejected', 'unverified', 'all']),
            ],

            'is_active' => [
                'sometimes',
                'boolean',
            ],

            'is_verified' => [
                'sometimes',
                'boolean',
            ],

            'is_featured' => [
                'sometimes',
                'boolean',
            ],

            'has_logo' => [
                'sometimes',
                'boolean',
            ],

            'has_website' => [
                'sometimes',
                'boolean',
            ],

            'has_description' => [
                'sometimes',
                'boolean',
            ],

            // Industry and categorization filters
            'industry_id' => [
                'sometimes',
                'integer',
                'exists:industries,id',
            ],

            'industries' => [
                'sometimes',
                'array',
                'max:20',
            ],

            'industries.*' => [
                'integer',
                'exists:industries,id',
            ],

            'functional_area_id' => [
                'sometimes',
                'integer',
                'exists:functional_areas,id',
            ],

            'functional_areas' => [
                'sometimes',
                'array',
                'max:10',
            ],

            'functional_areas.*' => [
                'integer',
                'exists:functional_areas,id',
            ],

            // Company size filters
            'company_size_id' => [
                'sometimes',
                'integer',
                'exists:company_sizes,id',
            ],

            'company_sizes' => [
                'sometimes',
                'array',
                'max:10',
            ],

            'company_sizes.*' => [
                'integer',
                'exists:company_sizes,id',
            ],

            'employee_count_min' => [
                'sometimes',
                'integer',
                'min:1',
                'max:100000',
            ],

            'employee_count_max' => [
                'sometimes',
                'integer',
                'min:1',
                'max:100000',
                'gte:employee_count_min',
            ],

            // Geographic filters
            'country_id' => [
                'sometimes',
                'integer',
                'exists:countries,id',
            ],

            'countries' => [
                'sometimes',
                'array',
                'max:50',
            ],

            'countries.*' => [
                'integer',
                'exists:countries,id',
            ],

            'state_id' => [
                'sometimes',
                'integer',
                'exists:states,id',
                function ($attribute, $value, $fail) {
                    if ($this->input('country_id') && !$this->validateStateCountryRelation($value, $this->input('country_id'))) {
                        $fail(__('validation.state_country_mismatch'));
                    }
                },
            ],

            'states' => [
                'sometimes',
                'array',
                'max:100',
            ],

            'states.*' => [
                'integer',
                'exists:states,id',
            ],

            'city_id' => [
                'sometimes',
                'integer',
                'exists:cities,id',
                function ($attribute, $value, $fail) {
                    if ($this->input('state_id') && !$this->validateCityStateRelation($value, $this->input('state_id'))) {
                        $fail(__('validation.city_state_mismatch'));
                    }
                },
            ],

            'cities' => [
                'sometimes',
                'array',
                'max:200',
            ],

            'cities.*' => [
                'integer',
                'exists:cities,id',
            ],

            'location' => [
                'sometimes',
                'string',
                'max:255',
            ],

            // Ownership and company type filters
            'ownership_type_id' => [
                'sometimes',
                'integer',
                'exists:ownership_types,id',
            ],

            'ownership_types' => [
                'sometimes',
                'array',
                'max:10',
            ],

            'ownership_types.*' => [
                'integer',
                'exists:ownership_types,id',
            ],

            // Job-related filters
            'has_active_jobs' => [
                'sometimes',
                'boolean',
            ],

            'active_jobs_min' => [
                'sometimes',
                'integer',
                'min:0',
                'max:1000',
            ],

            'active_jobs_max' => [
                'sometimes',
                'integer',
                'min:0',
                'max:1000',
                'gte:active_jobs_min',
            ],

            'total_jobs_min' => [
                'sometimes',
                'integer',
                'min:0',
                'max:10000',
            ],

            'total_jobs_max' => [
                'sometimes',
                'integer',
                'min:0',
                'max:10000',
                'gte:total_jobs_min',
            ],

            'hiring_actively' => [
                'sometimes',
                'boolean',
            ],

            'recent_hiring_days' => [
                'sometimes',
                'integer',
                'min:1',
                'max:365',
            ],

            // Performance and engagement filters
            'application_count_min' => [
                'sometimes',
                'integer',
                'min:0',
                'max:100000',
            ],

            'application_count_max' => [
                'sometimes',
                'integer',
                'min:0',
                'max:100000',
                'gte:application_count_min',
            ],

            'hired_count_min' => [
                'sometimes',
                'integer',
                'min:0',
                'max:10000',
            ],

            'hired_count_max' => [
                'sometimes',
                'integer',
                'min:0',
                'max:10000',
                'gte:hired_count_min',
            ],

            'rating_min' => [
                'sometimes',
                'numeric',
                'min:0',
                'max:5',
            ],

            'rating_max' => [
                'sometimes',
                'numeric',
                'min:0',
                'max:5',
                'gte:rating_min',
            ],

            'response_rate_min' => [
                'sometimes',
                'integer',
                'min:0',
                'max:100',
            ],

            'response_rate_max' => [
                'sometimes',
                'integer',
                'min:0',
                'max:100',
                'gte:response_rate_min',
            ],

            // Date filters
            'created_from' => [
                'sometimes',
                'date',
                'before_or_equal:today',
                'before_or_equal:created_to',
            ],

            'created_to' => [
                'sometimes',
                'date',
                'before_or_equal:today',
                'after_or_equal:created_from',
            ],

            'updated_from' => [
                'sometimes',
                'date',
                'before_or_equal:today',
                'before_or_equal:updated_to',
            ],

            'updated_to' => [
                'sometimes',
                'date',
                'before_or_equal:today',
                'after_or_equal:updated_from',
            ],

            'last_activity_from' => [
                'sometimes',
                'date',
                'before_or_equal:today',
                'before_or_equal:last_activity_to',
            ],

            'last_activity_to' => [
                'sometimes',
                'date',
                'before_or_equal:today',
                'after_or_equal:last_activity_from',
            ],

            'inactive_days' => [
                'sometimes',
                'integer',
                'min:1',
                'max:1095', // 3 years
            ],

            // Sorting parameters
            'sort_by' => [
                'sometimes',
                'string',
                Rule::in([
                    'company_name',
                    'email',
                    'created_at',
                    'updated_at',
                    'last_activity',
                    'jobs_count',
                    'applications_count',
                    'hired_count',
                    'rating',
                    'response_rate',
                    'employee_count',
                    'featured_status',
                    'verification_status',
                    'alphabetical',
                ]),
            ],

            'sort_direction' => [
                'sometimes',
                'string',
                Rule::in(['asc', 'desc']),
            ],

            'sort_secondary' => [
                'sometimes',
                'string',
                Rule::in(['company_name', 'created_at', 'jobs_count', 'rating']),
            ],

            'sort_secondary_direction' => [
                'sometimes',
                'string',
                Rule::in(['asc', 'desc']),
            ],

            // Display and data loading options
            'with_relationships' => [
                'sometimes',
                'array',
                'max:10',
            ],

            'with_relationships.*' => [
                'string',
                Rule::in(['user', 'industry', 'companySize', 'ownershipType', 'jobs', 'applications', 'country', 'state', 'city']),
            ],

            'with_counts' => [
                'sometimes',
                'array',
                'max:10',
            ],

            'with_counts.*' => [
                'string',
                Rule::in(['jobs', 'activeJobs', 'applications', 'hired', 'reviews', 'followers']),
            ],

            'with_analytics' => [
                'sometimes',
                'boolean',
            ],

            'analytics_period' => [
                'sometimes',
                'string',
                'required_if:with_analytics,true',
                Rule::in(['week', 'month', 'quarter', 'year']),
            ],

            'minimal_data' => [
                'sometimes',
                'boolean',
            ],

            'include_statistics' => [
                'sometimes',
                'boolean',
            ],

            // Performance optimization
            'use_cache' => [
                'sometimes',
                'boolean',
            ],

            'cache_duration' => [
                'sometimes',
                'integer',
                'min:0',
                'max:3600', // 1 hour max
            ],

            'force_refresh' => [
                'sometimes',
                'boolean',
            ],

            // Export parameters
            'export_format' => [
                'sometimes',
                'string',
                Rule::in(['csv', 'excel', 'json', 'pdf']),
            ],

            'export_fields' => [
                'sometimes',
                'array',
                'required_with:export_format',
                'max:30',
            ],

            'export_fields.*' => [
                'string',
                Rule::in([
                    'company_name',
                    'email',
                    'phone',
                    'website',
                    'industry',
                    'company_size',
                    'location',
                    'description',
                    'status',
                    'verification_status',
                    'jobs_count',
                    'applications_count',
                    'hired_count',
                    'rating',
                    'created_at',
                    'last_activity',
                ]),
            ],

            'export_file_name' => [
                'sometimes',
                'string',
                'max:100',
                'regex:/^[a-zA-Z0-9\-_\s]+$/',
            ],

            'export_include_relationships' => [
                'sometimes',
                'boolean',
            ],

            // Bulk operations
            'bulk_action' => [
                'sometimes',
                'string',
                Rule::in(['activate', 'deactivate', 'verify', 'unverify', 'feature', 'unfeature', 'delete', 'export', 'send_notification']),
            ],

            'selected_companies' => [
                'sometimes',
                'array',
                'required_with:bulk_action',
                'max:100',
            ],

            'selected_companies.*' => [
                'integer',
                'exists:companies,id',
            ],

            'bulk_message' => [
                'sometimes',
                'string',
                'max:1000',
                'required_if:bulk_action,send_notification',
            ],

            // API response format
            'response_format' => [
                'sometimes',
                'string',
                Rule::in(['full', 'minimal', 'cards', 'list', 'analytics']),
            ],

            'include_meta' => [
                'sometimes',
                'boolean',
            ],

            'include_debug' => [
                'sometimes',
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
            'search.min' => __('validation.search_too_short'),
            'search.max' => __('validation.search_too_long'),
            'per_page.in' => __('validation.invalid_page_size'),
            'page.max' => __('validation.page_number_too_high'),
            'employee_count_max.gte' => __('validation.employee_count_range_invalid'),
            'active_jobs_max.gte' => __('validation.active_jobs_range_invalid'),
            'total_jobs_max.gte' => __('validation.total_jobs_range_invalid'),
            'application_count_max.gte' => __('validation.application_count_range_invalid'),
            'hired_count_max.gte' => __('validation.hired_count_range_invalid'),
            'rating_max.gte' => __('validation.rating_range_invalid'),
            'response_rate_max.gte' => __('validation.response_rate_range_invalid'),
            'created_to.after_or_equal' => __('validation.date_range_invalid'),
            'updated_to.after_or_equal' => __('validation.date_range_invalid'),
            'last_activity_to.after_or_equal' => __('validation.date_range_invalid'),
            'industries.max' => __('validation.too_many_industries_selected'),
            'countries.max' => __('validation.too_many_countries_selected'),
            'export_fields.required_with' => __('validation.export_fields_required'),
            'export_fields.max' => __('validation.too_many_export_fields'),
            'selected_companies.max' => __('validation.bulk_action_limit_exceeded'),
            'bulk_message.required_if' => __('validation.bulk_message_required'),
            'analytics_period.required_if' => __('validation.analytics_period_required'),
            'cache_duration.max' => __('validation.cache_duration_too_long'),
            'export_file_name.regex' => __('validation.invalid_file_name_format'),
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'per_page' => __('validation.attributes.items_per_page'),
            'search_type' => __('validation.attributes.search_type'),
            'search_fields' => __('validation.attributes.search_fields'),
            'verification_status' => __('validation.attributes.verification_status'),
            'industry_id' => __('validation.attributes.industry'),
            'company_size_id' => __('validation.attributes.company_size'),
            'country_id' => __('validation.attributes.country'),
            'state_id' => __('validation.attributes.state'),
            'city_id' => __('validation.attributes.city'),
            'ownership_type_id' => __('validation.attributes.ownership_type'),
            'employee_count_min' => __('validation.attributes.minimum_employee_count'),
            'employee_count_max' => __('validation.attributes.maximum_employee_count'),
            'active_jobs_min' => __('validation.attributes.minimum_active_jobs'),
            'active_jobs_max' => __('validation.attributes.maximum_active_jobs'),
            'total_jobs_min' => __('validation.attributes.minimum_total_jobs'),
            'total_jobs_max' => __('validation.attributes.maximum_total_jobs'),
            'application_count_min' => __('validation.attributes.minimum_application_count'),
            'application_count_max' => __('validation.attributes.maximum_application_count'),
            'hired_count_min' => __('validation.attributes.minimum_hired_count'),
            'hired_count_max' => __('validation.attributes.maximum_hired_count'),
            'rating_min' => __('validation.attributes.minimum_rating'),
            'rating_max' => __('validation.attributes.maximum_rating'),
            'response_rate_min' => __('validation.attributes.minimum_response_rate'),
            'response_rate_max' => __('validation.attributes.maximum_response_rate'),
            'recent_hiring_days' => __('validation.attributes.recent_hiring_days'),
            'inactive_days' => __('validation.attributes.inactive_days'),
            'created_from' => __('validation.attributes.created_from_date'),
            'created_to' => __('validation.attributes.created_to_date'),
            'updated_from' => __('validation.attributes.updated_from_date'),
            'updated_to' => __('validation.attributes.updated_to_date'),
            'last_activity_from' => __('validation.attributes.last_activity_from_date'),
            'last_activity_to' => __('validation.attributes.last_activity_to_date'),
            'sort_by' => __('validation.attributes.sort_field'),
            'sort_direction' => __('validation.attributes.sort_direction'),
            'with_relationships' => __('validation.attributes.relationships'),
            'with_counts' => __('validation.attributes.count_fields'),
            'analytics_period' => __('validation.attributes.analytics_period'),
            'cache_duration' => __('validation.attributes.cache_duration'),
            'export_format' => __('validation.attributes.export_format'),
            'export_fields' => __('validation.attributes.export_fields'),
            'export_file_name' => __('validation.attributes.export_file_name'),
            'bulk_action' => __('validation.attributes.bulk_action'),
            'selected_companies' => __('validation.attributes.selected_companies'),
            'bulk_message' => __('validation.attributes.bulk_message'),
            'response_format' => __('validation.attributes.response_format'),
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Set default values
        if (!$this->has('per_page')) {
            $this->merge(['per_page' => 20]);
        }

        if (!$this->has('sort_by')) {
            $this->merge(['sort_by' => 'company_name']);
        }

        if (!$this->has('sort_direction')) {
            $this->merge(['sort_direction' => 'asc']);
        }

        if (!$this->has('search_type')) {
            $this->merge(['search_type' => 'all']);
        }

        if (!$this->has('response_format')) {
            $this->merge(['response_format' => 'full']);
        }

        if (!$this->has('recent_hiring_days')) {
            $this->merge(['recent_hiring_days' => 30]);
        }

        if (!$this->has('cache_duration')) {
            $this->merge(['cache_duration' => 1800]); // 30 minutes
        }

        if (!$this->has('use_cache')) {
            $this->merge(['use_cache' => true]);
        }

        // Clean search input
        if ($this->has('search')) {
            $this->merge([
                'search' => trim($this->input('search'))
            ]);
        }

        // Convert string booleans to actual booleans
        $booleanFields = [
            'is_active', 'is_verified', 'is_featured', 'has_logo', 'has_website',
            'has_description', 'has_active_jobs', 'hiring_actively', 'with_analytics',
            'minimal_data', 'include_statistics', 'use_cache', 'force_refresh',
            'export_include_relationships', 'include_meta', 'include_debug'
        ];

        foreach ($booleanFields as $field) {
            if ($this->has($field)) {
                $this->merge([
                    $field => filter_var($this->input($field), FILTER_VALIDATE_BOOLEAN)
                ]);
            }
        }

        // Ensure arrays are properly formatted
        $arrayFields = [
            'search_fields', 'industries', 'functional_areas', 'company_sizes',
            'countries', 'states', 'cities', 'ownership_types', 'with_relationships',
            'with_counts', 'export_fields', 'selected_companies'
        ];

        foreach ($arrayFields as $field) {
            if ($this->has($field) && !is_array($this->input($field))) {
                $this->merge([
                    $field => array_filter(explode(',', $this->input($field)))
                ]);
            }
        }

        // Log company search for analytics
        if ($this->has('search') && $this->input('search')) {
            Log::info('Enhanced company search performed', [
                'search_term' => $this->input('search'),
                'search_type' => $this->input('search_type'),
                'filters_applied' => count(array_filter($this->only([
                    'status', 'industry_id', 'company_size_id', 'country_id'
                ]))),
                'ip_address' => $this->ip(),
                'user_agent' => $this->userAgent(),
                'timestamp' => now(),
            ]);
        }
    }

    /**
     * Handle a passed validation attempt.
     */
    protected function passedValidation(): void
    {
        // Log successful enhanced company index request
        Log::info('Enhanced company index request validated', [
            'filters_applied' => count(array_filter($this->only([
                'search', 'status', 'industry_id', 'company_size_id', 'country_id',
                'state_id', 'city_id', 'is_featured', 'has_active_jobs'
            ]))),
            'search_performed' => $this->has('search'),
            'export_requested' => $this->has('export_format'),
            'bulk_action_requested' => $this->has('bulk_action'),
            'analytics_requested' => $this->has('with_analytics'),
            'sort_by' => $this->input('sort_by'),
            'response_format' => $this->input('response_format'),
            'use_cache' => $this->input('use_cache'),
            'ip_address' => $this->ip(),
            'timestamp' => now(),
        ]);
    }

    /**
     * Check if content contains inappropriate material.
     */
    private function containsInappropriateContent(string $content): bool
    {
        $inappropriateWords = [
            'spam', 'scam', 'fraud', 'fake', 'illegal', 'hack', 'virus',
            'malware', 'phishing', 'adult', 'xxx', 'porn', 'sex', 'drug',
            'weapon', 'violence', 'hate', 'racist', 'terrorist'
        ];

        $lowercaseContent = strtolower($content);
        
        foreach ($inappropriateWords as $word) {
            if (strpos($lowercaseContent, $word) !== false) {
                return true;
            }
        }

        // Check for SQL injection patterns
        $sqlPatterns = [
            '/(\bselect\b|\binsert\b|\bupdate\b|\bdelete\b|\bdrop\b|\bunion\b)/i',
            '/(\bor\s+1\s*=\s*1\b|\band\s+1\s*=\s*1\b)/i',
            '/(--|\/\*|\*\/|;)/i'
        ];

        foreach ($sqlPatterns as $pattern) {
            if (preg_match($pattern, $content)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Validate state belongs to country.
     */
    private function validateStateCountryRelation(int $stateId, int $countryId): bool
    {
        return \DB::table('states')
            ->where('id', $stateId)
            ->where('country_id', $countryId)
            ->exists();
    }

    /**
     * Validate city belongs to state.
     */
    private function validateCityStateRelation(int $cityId, int $stateId): bool
    {
        return \DB::table('cities')
            ->where('id', $cityId)
            ->where('state_id', $stateId)
            ->exists();
    }
} 