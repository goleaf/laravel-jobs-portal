<?php

namespace App\Http\Requests\JobApplication;

use Carbon\Carbon;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class IndexJobApplicationRequest extends FormRequest
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
                'max:1000',
            ],

            'per_page' => [
                'sometimes',
                'integer',
                'min:5',
                'max:100',
            ],

            // Sorting parameters
            'sort_by' => [
                'sometimes',
                'string',
                Rule::in([
                    'created_at',
                    'updated_at',
                    'status',
                    'job_title',
                    'applicant_name',
                    'application_date',
                    'salary_expectation',
                    'experience_years',
                    'match_score',
                    'priority',
                    'interview_date',
                    'last_activity',
                ]),
            ],

            'sort_direction' => [
                'sometimes',
                'string',
                Rule::in(['asc', 'desc']),
            ],

            // Status filtering
            'status' => [
                'sometimes',
                'array',
                'max:10',
            ],

            'status.*' => [
                'string',
                Rule::in([
                    'pending',
                    'under_review',
                    'shortlisted',
                    'interview_scheduled',
                    'interviewed',
                    'second_interview',
                    'reference_check',
                    'offer_made',
                    'offer_accepted',
                    'offer_declined',
                    'hired',
                    'rejected',
                    'withdrawn',
                    'on_hold',
                ]),
            ],

            // Job filtering
            'job_id' => [
                'sometimes',
                'integer',
                'min:1',
                'exists:jobs,id',
            ],

            'job_title' => [
                'sometimes',
                'string',
                'max:255',
            ],

            'job_category' => [
                'sometimes',
                'integer',
                'exists:job_categories,id',
            ],

            'job_type' => [
                'sometimes',
                'string',
                Rule::in([
                    'full_time',
                    'part_time',
                    'contract',
                    'temporary',
                    'internship',
                    'freelance',
                    'remote',
                    'hybrid',
                ]),
            ],

            // Company filtering
            'company_id' => [
                'sometimes',
                'integer',
                'min:1',
                'exists:companies,id',
            ],

            'company_name' => [
                'sometimes',
                'string',
                'max:255',
            ],

            // Applicant filtering
            'applicant_name' => [
                'sometimes',
                'string',
                'max:255',
            ],

            'applicant_email' => [
                'sometimes',
                'email',
                'max:255',
            ],

            'applicant_phone' => [
                'sometimes',
                'string',
                'max:20',
            ],

            // Date range filtering
            'date_from' => [
                'sometimes',
                'date',
                'before_or_equal:date_to',
                'after:' . now()->subYears(2)->toDateString(),
            ],

            'date_to' => [
                'sometimes',
                'date',
                'after_or_equal:date_from',
                'before_or_equal:today',
            ],

            // Experience filtering
            'min_experience' => [
                'sometimes',
                'integer',
                'min:0',
                'max:50',
            ],

            'max_experience' => [
                'sometimes',
                'integer',
                'min:0',
                'max:50',
                'gte:min_experience',
            ],

            // Salary filtering
            'min_salary' => [
                'sometimes',
                'numeric',
                'min:0',
                'max:10000000',
            ],

            'max_salary' => [
                'sometimes',
                'numeric',
                'min:0',
                'max:10000000',
                'gte:min_salary',
            ],

            'salary_currency' => [
                'sometimes',
                'string',
                'size:3',
                'exists:currencies,code',
            ],

            // Skills filtering
            'skills' => [
                'sometimes',
                'array',
                'max:20',
            ],

            'skills.*' => [
                'integer',
                'exists:skills,id',
            ],

            'skills_match_type' => [
                'sometimes',
                'string',
                Rule::in(['any', 'all', 'exact']),
            ],

            // Location filtering
            'location' => [
                'sometimes',
                'string',
                'max:255',
            ],

            'city_id' => [
                'sometimes',
                'integer',
                'exists:cities,id',
            ],

            'state_id' => [
                'sometimes',
                'integer',
                'exists:states,id',
            ],

            'country_id' => [
                'sometimes',
                'integer',
                'exists:countries,id',
            ],

            'remote_work' => [
                'sometimes',
                'boolean',
            ],

            // Education filtering
            'education_level' => [
                'sometimes',
                'string',
                Rule::in([
                    'high_school',
                    'associate',
                    'bachelor',
                    'master',
                    'doctorate',
                    'professional',
                    'certification',
                ]),
            ],

            // Priority filtering
            'priority' => [
                'sometimes',
                'string',
                Rule::in(['low', 'normal', 'high', 'urgent']),
            ],

            // Match score filtering
            'min_match_score' => [
                'sometimes',
                'integer',
                'min:0',
                'max:100',
            ],

            'max_match_score' => [
                'sometimes',
                'integer',
                'min:0',
                'max:100',
                'gte:min_match_score',
            ],

            // Search parameters
            'search' => [
                'sometimes',
                'string',
                'max:255',
                'min:2',
            ],

            'search_fields' => [
                'sometimes',
                'array',
                'max:10',
            ],

            'search_fields.*' => [
                'string',
                Rule::in([
                    'applicant_name',
                    'applicant_email',
                    'job_title',
                    'company_name',
                    'skills',
                    'education',
                    'experience',
                    'cover_letter',
                    'notes',
                ]),
            ],

            // Advanced filtering
            'has_cover_letter' => [
                'sometimes',
                'boolean',
            ],

            'has_resume' => [
                'sometimes',
                'boolean',
            ],

            'has_portfolio' => [
                'sometimes',
                'boolean',
            ],

            'interview_scheduled' => [
                'sometimes',
                'boolean',
            ],

            'reference_provided' => [
                'sometimes',
                'boolean',
            ],

            // Response format
            'format' => [
                'sometimes',
                'string',
                Rule::in(['json', 'csv', 'excel', 'pdf']),
            ],

            'include_relations' => [
                'sometimes',
                'array',
                'max:10',
            ],

            'include_relations.*' => [
                'string',
                Rule::in([
                    'job',
                    'company',
                    'applicant',
                    'interviews',
                    'documents',
                    'notes',
                    'activities',
                    'references',
                ]),
            ],

            // Performance optimization
            'cache_results' => [
                'sometimes',
                'boolean',
            ],

            'cache_duration' => [
                'sometimes',
                'integer',
                'min:60',
                'max:3600',
            ],

            // Analytics tracking
            'track_view' => [
                'sometimes',
                'boolean',
            ],

            'analytics_context' => [
                'sometimes',
                'string',
                'max:100',
            ],
        ];
    }

    /**
     * Get custom error messages for validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'page.integer' => __('validation.integer', ['attribute' => __('validation.attributes.page')]),
            'page.min' => __('validation.min_value', ['attribute' => __('validation.attributes.page'), 'min' => 1]),
            'page.max' => __('validation.max_value', ['attribute' => __('validation.attributes.page'), 'max' => 1000]),
            
            'per_page.integer' => __('validation.integer', ['attribute' => __('validation.attributes.per_page')]),
            'per_page.min' => __('validation.min_value', ['attribute' => __('validation.attributes.per_page'), 'min' => 5]),
            'per_page.max' => __('validation.max_value', ['attribute' => __('validation.attributes.per_page'), 'max' => 100]),
            
            'sort_by.in' => __('validation.invalid_sort_field'),
            'sort_direction.in' => __('validation.invalid_sort_direction'),
            
            'status.array' => __('validation.array', ['attribute' => __('validation.attributes.status')]),
            'status.max' => __('validation.max_items', ['attribute' => __('validation.attributes.status'), 'max' => 10]),
            'status.*.in' => __('validation.invalid_application_status'),
            
            'job_id.exists' => __('validation.exists', ['attribute' => __('validation.attributes.job')]),
            'job_category.exists' => __('validation.exists', ['attribute' => __('validation.attributes.job_category')]),
            'job_type.in' => __('validation.invalid_job_type'),
            
            'company_id.exists' => __('validation.exists', ['attribute' => __('validation.attributes.company')]),
            
            'applicant_email.email' => __('validation.email', ['attribute' => __('validation.attributes.applicant_email')]),
            
            'date_from.before_or_equal' => __('validation.before_or_equal', ['attribute' => __('validation.attributes.date_from'), 'date' => __('validation.attributes.date_to')]),
            'date_from.after' => __('validation.date_range_limit', ['attribute' => __('validation.attributes.date_from')]),
            
            'date_to.after_or_equal' => __('validation.after_or_equal', ['attribute' => __('validation.attributes.date_to'), 'date' => __('validation.attributes.date_from')]),
            'date_to.before_or_equal' => __('validation.before_or_equal', ['attribute' => __('validation.attributes.date_to'), 'date' => 'today']),
            
            'min_experience.max' => __('validation.max_value', ['attribute' => __('validation.attributes.min_experience'), 'max' => 50]),
            'max_experience.gte' => __('validation.gte_field', ['attribute' => __('validation.attributes.max_experience'), 'value' => __('validation.attributes.min_experience')]),
            
            'min_salary.max' => __('validation.max_value', ['attribute' => __('validation.attributes.min_salary'), 'max' => 10000000]),
            'max_salary.gte' => __('validation.gte_field', ['attribute' => __('validation.attributes.max_salary'), 'value' => __('validation.attributes.min_salary')]),
            
            'salary_currency.size' => __('validation.size', ['attribute' => __('validation.attributes.salary_currency'), 'size' => 3]),
            'salary_currency.exists' => __('validation.exists', ['attribute' => __('validation.attributes.currency')]),
            
            'skills.array' => __('validation.array', ['attribute' => __('validation.attributes.skills')]),
            'skills.max' => __('validation.max_items', ['attribute' => __('validation.attributes.skills'), 'max' => 20]),
            'skills.*.exists' => __('validation.exists', ['attribute' => __('validation.attributes.skill')]),
            'skills_match_type.in' => __('validation.invalid_match_type'),
            
            'city_id.exists' => __('validation.exists', ['attribute' => __('validation.attributes.city')]),
            'state_id.exists' => __('validation.exists', ['attribute' => __('validation.attributes.state')]),
            'country_id.exists' => __('validation.exists', ['attribute' => __('validation.attributes.country')]),
            
            'education_level.in' => __('validation.invalid_education_level'),
            'priority.in' => __('validation.invalid_priority'),
            
            'min_match_score.max' => __('validation.max_value', ['attribute' => __('validation.attributes.min_match_score'), 'max' => 100]),
            'max_match_score.gte' => __('validation.gte_field', ['attribute' => __('validation.attributes.max_match_score'), 'value' => __('validation.attributes.min_match_score')]),
            
            'search.min' => __('validation.min_chars', ['attribute' => __('validation.attributes.search'), 'min' => 2]),
            'search.max' => __('validation.max_chars', ['attribute' => __('validation.attributes.search'), 'max' => 255]),
            
            'search_fields.array' => __('validation.array', ['attribute' => __('validation.attributes.search_fields')]),
            'search_fields.max' => __('validation.max_items', ['attribute' => __('validation.attributes.search_fields'), 'max' => 10]),
            'search_fields.*.in' => __('validation.invalid_search_field'),
            
            'format.in' => __('validation.invalid_format'),
            
            'include_relations.array' => __('validation.array', ['attribute' => __('validation.attributes.include_relations')]),
            'include_relations.max' => __('validation.max_items', ['attribute' => __('validation.attributes.include_relations'), 'max' => 10]),
            'include_relations.*.in' => __('validation.invalid_relation'),
            
            'cache_duration.min' => __('validation.min_value', ['attribute' => __('validation.attributes.cache_duration'), 'min' => 60]),
            'cache_duration.max' => __('validation.max_value', ['attribute' => __('validation.attributes.cache_duration'), 'max' => 3600]),
        ];
    }

    /**
     * Get custom attribute names for validation errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'page' => __('validation.attributes.page'),
            'per_page' => __('validation.attributes.per_page'),
            'sort_by' => __('validation.attributes.sort_by'),
            'sort_direction' => __('validation.attributes.sort_direction'),
            'status' => __('validation.attributes.status'),
            'job_id' => __('validation.attributes.job_id'),
            'job_title' => __('validation.attributes.job_title'),
            'job_category' => __('validation.attributes.job_category'),
            'job_type' => __('validation.attributes.job_type'),
            'company_id' => __('validation.attributes.company_id'),
            'company_name' => __('validation.attributes.company_name'),
            'applicant_name' => __('validation.attributes.applicant_name'),
            'applicant_email' => __('validation.attributes.applicant_email'),
            'applicant_phone' => __('validation.attributes.applicant_phone'),
            'date_from' => __('validation.attributes.date_from'),
            'date_to' => __('validation.attributes.date_to'),
            'min_experience' => __('validation.attributes.min_experience'),
            'max_experience' => __('validation.attributes.max_experience'),
            'min_salary' => __('validation.attributes.min_salary'),
            'max_salary' => __('validation.attributes.max_salary'),
            'salary_currency' => __('validation.attributes.salary_currency'),
            'skills' => __('validation.attributes.skills'),
            'skills_match_type' => __('validation.attributes.skills_match_type'),
            'location' => __('validation.attributes.location'),
            'city_id' => __('validation.attributes.city_id'),
            'state_id' => __('validation.attributes.state_id'),
            'country_id' => __('validation.attributes.country_id'),
            'remote_work' => __('validation.attributes.remote_work'),
            'education_level' => __('validation.attributes.education_level'),
            'priority' => __('validation.attributes.priority'),
            'min_match_score' => __('validation.attributes.min_match_score'),
            'max_match_score' => __('validation.attributes.max_match_score'),
            'search' => __('validation.attributes.search'),
            'search_fields' => __('validation.attributes.search_fields'),
            'has_cover_letter' => __('validation.attributes.has_cover_letter'),
            'has_resume' => __('validation.attributes.has_resume'),
            'has_portfolio' => __('validation.attributes.has_portfolio'),
            'interview_scheduled' => __('validation.attributes.interview_scheduled'),
            'reference_provided' => __('validation.attributes.reference_provided'),
            'format' => __('validation.attributes.format'),
            'include_relations' => __('validation.attributes.include_relations'),
            'cache_results' => __('validation.attributes.cache_results'),
            'cache_duration' => __('validation.attributes.cache_duration'),
            'track_view' => __('validation.attributes.track_view'),
            'analytics_context' => __('validation.attributes.analytics_context'),
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Set default values
        $this->merge([
            'page' => $this->integer('page', 1),
            'per_page' => $this->integer('per_page', 20),
            'sort_by' => $this->sort_by ?? 'created_at',
            'sort_direction' => $this->sort_direction ?? 'desc',
            'skills_match_type' => $this->skills_match_type ?? 'any',
            'format' => $this->format ?? 'json',
            'cache_results' => $this->boolean('cache_results', true),
            'cache_duration' => $this->integer('cache_duration', 300),
            'track_view' => $this->boolean('track_view', true),
        ]);

        // Process arrays
        if ($this->has('status') && is_string($this->status)) {
            $this->merge([
                'status' => array_filter(explode(',', $this->status)),
            ]);
        }

        if ($this->has('skills') && is_string($this->skills)) {
            $this->merge([
                'skills' => array_filter(array_map('intval', explode(',', $this->skills))),
            ]);
        }

        if ($this->has('search_fields') && is_string($this->search_fields)) {
            $this->merge([
                'search_fields' => array_filter(explode(',', $this->search_fields)),
            ]);
        }

        if ($this->has('include_relations') && is_string($this->include_relations)) {
            $this->merge([
                'include_relations' => array_filter(explode(',', $this->include_relations)),
            ]);
        }

        // Normalize search query
        if ($this->has('search')) {
            $this->merge([
                'search' => trim($this->search),
            ]);
        }

        // Log request for analytics
        Log::info('Job application index request', [
            'filters' => $this->only([
                'status', 'job_id', 'company_id', 'date_from', 'date_to',
                'min_experience', 'max_experience', 'min_salary', 'max_salary',
                'skills', 'location', 'education_level', 'priority'
            ]),
            'search' => $this->search ?? null,
            'sort' => [
                'by' => $this->sort_by,
                'direction' => $this->sort_direction,
            ],
            'pagination' => [
                'page' => $this->page,
                'per_page' => $this->per_page,
            ],
            'ip' => $this->ip(),
            'user_agent' => $this->userAgent(),
            'timestamp' => now(),
        ]);
    }

    /**
     * Handle a passed validation attempt.
     */
    protected function passedValidation(): void
    {
        // Set request metadata
        $this->merge([
            'request_id' => 'APP-IDX-' . date('Ymd') . '-' . strtoupper(substr(md5(time() . $this->ip()), 0, 8)),
            'validated_at' => now(),
            'request_source' => $this->header('X-Request-Source', 'web'),
        ]);

        // Performance optimization flags
        $this->merge([
            'optimize_query' => $this->shouldOptimizeQuery(),
            'use_cache' => $this->shouldUseCache(),
            'enable_analytics' => $this->track_view,
        ]);
    }

    /**
     * Determine if query should be optimized.
     */
    private function shouldOptimizeQuery(): bool
    {
        // Optimize for large result sets or complex filters
        return $this->per_page > 50 || 
               count($this->status ?? []) > 5 ||
               count($this->skills ?? []) > 10 ||
               !empty($this->search);
    }

    /**
     * Determine if cache should be used.
     */
    private function shouldUseCache(): bool
    {
        // Use cache for common queries without real-time requirements
        return $this->cache_results && 
               empty($this->search) && 
               $this->sort_by === 'created_at';
    }
}
