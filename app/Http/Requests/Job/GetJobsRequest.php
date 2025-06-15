<?php

namespace App\Http\Requests\Job;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Class GetJobsRequest.
 *
 * Handles job listing and filtering requests with comprehensive search parameters,
 * pagination, sorting, and advanced filtering options.
 */
class GetJobsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Public endpoint, no specific authorization needed
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            // Search and basic filters
            'search' => [
                'nullable',
                'string',
                'max:255',
                'regex:/^[\p{L}\p{N}\p{P}\p{Z}\s]+$/u', // Unicode-safe search
            ],

            'keywords' => [
                'nullable',
                'array',
                'max:10', // Maximum 10 keywords
            ],

            'keywords.*' => [
                'string',
                'max:50',
                'regex:/^[\p{L}\p{N}\-_\s]+$/u',
            ],

            // Location filters
            'country_id' => [
                'nullable',
                'integer',
                Rule::exists('countries', 'id'),
            ],

            'state_id' => [
                'nullable',
                'integer',
                Rule::exists('states', 'id'),
            ],

            'city_id' => [
                'nullable',
                'integer',
                Rule::exists('cities', 'id'),
            ],

            'location_radius' => [
                'nullable',
                'integer',
                'min:1',
                'max:500', // Maximum 500km radius
            ],

            'is_remote' => [
                'nullable',
                'boolean',
            ],

            'remote_percentage' => [
                'nullable',
                'integer',
                'min:0',
                'max:100',
            ],

            // Job categorization
            'job_category_id' => [
                'nullable',
                'integer',
                Rule::exists('job_categories', 'id')->where('is_active', true),
            ],

            'job_categories' => [
                'nullable',
                'array',
                'max:5', // Maximum 5 categories
            ],

            'job_categories.*' => [
                'integer',
                Rule::exists('job_categories', 'id')->where('is_active', true),
            ],

            'job_type_id' => [
                'nullable',
                'integer',
                Rule::exists('job_types', 'id')->where('is_active', true),
            ],

            'job_types' => [
                'nullable',
                'array',
                'max:5',
            ],

            'job_types.*' => [
                'integer',
                Rule::exists('job_types', 'id')->where('is_active', true),
            ],

            'job_shift_id' => [
                'nullable',
                'integer',
                Rule::exists('job_shifts', 'id')->where('is_active', true),
            ],

            'career_level_id' => [
                'nullable',
                'integer',
                Rule::exists('career_levels', 'id')->where('is_active', true),
            ],

            'degree_level_id' => [
                'nullable',
                'integer',
                Rule::exists('required_degree_levels', 'id')->where('is_active', true),
            ],

            // Company filters
            'company_id' => [
                'nullable',
                'integer',
                Rule::exists('companies', 'id')->where('is_active', true),
            ],

            'companies' => [
                'nullable',
                'array',
                'max:10',
            ],

            'companies.*' => [
                'integer',
                Rule::exists('companies', 'id')->where('is_active', true),
            ],

            'company_size_id' => [
                'nullable',
                'integer',
                Rule::exists('company_sizes', 'id'),
            ],

            'industry_id' => [
                'nullable',
                'integer',
                Rule::exists('industries', 'id')->where('is_active', true),
            ],

            // Salary filters
            'salary_min' => [
                'nullable',
                'numeric',
                'min:0',
                'max:9999999',
            ],

            'salary_max' => [
                'nullable',
                'numeric',
                'min:0',
                'max:9999999',
                'gte:salary_min',
            ],

            'currency_id' => [
                'nullable',
                'integer',
                Rule::exists('salary_currencies', 'id'),
            ],

            'salary_period_id' => [
                'nullable',
                'integer',
                Rule::exists('salary_periods', 'id'),
            ],

            'hide_salary_jobs' => [
                'nullable',
                'boolean',
            ],

            // Experience filters
            'experience_min' => [
                'nullable',
                'integer',
                'min:0',
                'max:50',
            ],

            'experience_max' => [
                'nullable',
                'integer',
                'min:0',
                'max:50',
                'gte:experience_min',
            ],

            // Skills filters
            'skills' => [
                'nullable',
                'array',
                'max:20', // Maximum 20 skills
            ],

            'skills.*' => [
                'integer',
                Rule::exists('skills', 'id')->where('is_active', true),
            ],

            'skills_match' => [
                'nullable',
                'string',
                Rule::in(['any', 'all']), // Match any skill or all skills
            ],

            // Date filters
            'posted_after' => [
                'nullable',
                'date',
                'before_or_equal:today',
            ],

            'posted_before' => [
                'nullable',
                'date',
                'before_or_equal:today',
                'after:posted_after',
            ],

            'deadline_after' => [
                'nullable',
                'date',
                'after_or_equal:today',
            ],

            'deadline_before' => [
                'nullable',
                'date',
                'after_or_equal:today',
            ],

            // Job status filters
            'is_active' => [
                'nullable',
                'boolean',
            ],

            'is_featured' => [
                'nullable',
                'boolean',
            ],

            'is_urgent' => [
                'nullable',
                'boolean',
            ],

            'exclude_expired' => [
                'nullable',
                'boolean',
            ],

            'only_new_jobs' => [
                'nullable',
                'boolean',
            ],

            // Pagination
            'page' => [
                'nullable',
                'integer',
                'min:1',
                'max:1000',
            ],

            'per_page' => [
                'nullable',
                'integer',
                'min:1',
                'max:100',
            ],

            // Sorting
            'sort' => [
                'nullable',
                'string',
                Rule::in([
                    'created_at', 'updated_at', 'title', 'salary_from', 'salary_to',
                    'experience', 'application_deadline', 'views_count', 'applications_count',
                    'relevance', 'company_name', 'location', 'featured', 'random',
                ]),
            ],

            'direction' => [
                'nullable',
                'string',
                Rule::in(['asc', 'desc']),
            ],

            // Advanced options
            'include_relations' => [
                'nullable',
                'array',
                'max:10',
            ],

            'include_relations.*' => [
                'string',
                Rule::in([
                    'company', 'jobCategory', 'jobType', 'careerLevel', 'degreeLevel',
                    'jobShift', 'skills', 'country', 'state', 'city', 'salaryCurrency',
                    'salaryPeriod', 'jobApplications', 'views',
                ]),
            ],

            'exclude_applied' => [
                'nullable',
                'boolean',
            ],

            'exclude_saved' => [
                'nullable',
                'boolean',
            ],

            'only_applied' => [
                'nullable',
                'boolean',
            ],

            'only_saved' => [
                'nullable',
                'boolean',
            ],

            // Analytics and tracking
            'track_view' => [
                'nullable',
                'boolean',
            ],

            'source' => [
                'nullable',
                'string',
                'max:50',
                Rule::in(['web', 'mobile', 'api', 'widget', 'embed']),
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
                'max:3600', // 1 hour max
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            // Search validation messages
            'search.max' => __('validation.job_search.search.max'),
            'search.regex' => __('validation.job_search.search.format'),

            // Keywords validation messages
            'keywords.array' => __('validation.job_search.keywords.array'),
            'keywords.max' => __('validation.job_search.keywords.max'),
            'keywords.*.max' => __('validation.job_search.keywords.item_max'),
            'keywords.*.regex' => __('validation.job_search.keywords.format'),

            // Location validation messages
            'country_id.exists' => __('validation.job_search.country_id.exists'),
            'state_id.exists' => __('validation.job_search.state_id.exists'),
            'city_id.exists' => __('validation.job_search.city_id.exists'),
            'location_radius.min' => __('validation.job_search.location_radius.min'),
            'location_radius.max' => __('validation.job_search.location_radius.max'),

            // Job categorization messages
            'job_category_id.exists' => __('validation.job_search.job_category_id.exists'),
            'job_categories.max' => __('validation.job_search.job_categories.max'),
            'job_categories.*.exists' => __('validation.job_search.job_categories.item_exists'),
            'job_type_id.exists' => __('validation.job_search.job_type_id.exists'),
            'career_level_id.exists' => __('validation.job_search.career_level_id.exists'),

            // Company validation messages
            'company_id.exists' => __('validation.job_search.company_id.exists'),
            'companies.max' => __('validation.job_search.companies.max'),
            'companies.*.exists' => __('validation.job_search.companies.item_exists'),

            // Salary validation messages
            'salary_min.numeric' => __('validation.job_search.salary_min.numeric'),
            'salary_min.min' => __('validation.job_search.salary_min.min'),
            'salary_max.gte' => __('validation.job_search.salary_max.gte'),

            // Experience validation messages
            'experience_min.min' => __('validation.job_search.experience_min.min'),
            'experience_max.gte' => __('validation.job_search.experience_max.gte'),

            // Skills validation messages
            'skills.max' => __('validation.job_search.skills.max'),
            'skills.*.exists' => __('validation.job_search.skills.item_exists'),
            'skills_match.in' => __('validation.job_search.skills_match.in'),

            // Date validation messages
            'posted_after.date' => __('validation.job_search.posted_after.date'),
            'posted_before.after' => __('validation.job_search.posted_before.after'),
            'deadline_after.after_or_equal' => __('validation.job_search.deadline_after.after_or_equal'),

            // Pagination validation messages
            'page.min' => __('validation.job_search.page.min'),
            'page.max' => __('validation.job_search.page.max'),
            'per_page.min' => __('validation.job_search.per_page.min'),
            'per_page.max' => __('validation.job_search.per_page.max'),

            // Sorting validation messages
            'sort.in' => __('validation.job_search.sort.in'),
            'direction.in' => __('validation.job_search.direction.in'),

            // Advanced options messages
            'include_relations.max' => __('validation.job_search.include_relations.max'),
            'include_relations.*.in' => __('validation.job_search.include_relations.item_in'),
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'search' => __('validation.attributes.search_query'),
            'keywords' => __('validation.attributes.keywords'),
            'country_id' => __('validation.attributes.country'),
            'state_id' => __('validation.attributes.state'),
            'city_id' => __('validation.attributes.city'),
            'location_radius' => __('validation.attributes.search_radius'),
            'job_category_id' => __('validation.attributes.job_category'),
            'job_categories' => __('validation.attributes.job_categories'),
            'job_type_id' => __('validation.attributes.job_type'),
            'company_id' => __('validation.attributes.company'),
            'companies' => __('validation.attributes.companies'),
            'salary_min' => __('validation.attributes.minimum_salary'),
            'salary_max' => __('validation.attributes.maximum_salary'),
            'experience_min' => __('validation.attributes.minimum_experience'),
            'experience_max' => __('validation.attributes.maximum_experience'),
            'skills' => __('validation.attributes.required_skills'),
            'posted_after' => __('validation.attributes.posted_after'),
            'posted_before' => __('validation.attributes.posted_before'),
            'deadline_after' => __('validation.attributes.deadline_after'),
            'deadline_before' => __('validation.attributes.deadline_before'),
            'page' => __('validation.attributes.page_number'),
            'per_page' => __('validation.attributes.items_per_page'),
            'sort' => __('validation.attributes.sort_field'),
            'direction' => __('validation.attributes.sort_direction'),
        ];
    }

    /**
     * Configure the validator instance.
     *
     * @param mixed $validator
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $this->validateAdvancedFilters($validator);
            $this->validateUserSpecificFilters($validator);
        });
    }

    /**
     * Get search filters as an array for processing.
     */
    public function getSearchFilters(): array
    {
        return array_filter([
            'search' => $this->search,
            'keywords' => $this->keywords,
            'country_id' => $this->country_id,
            'state_id' => $this->state_id,
            'city_id' => $this->city_id,
            'location_radius' => $this->location_radius,
            'is_remote' => $this->is_remote,
            'remote_percentage' => $this->remote_percentage,
            'job_category_id' => $this->job_category_id,
            'job_categories' => $this->job_categories,
            'job_type_id' => $this->job_type_id,
            'job_types' => $this->job_types,
            'job_shift_id' => $this->job_shift_id,
            'career_level_id' => $this->career_level_id,
            'degree_level_id' => $this->degree_level_id,
            'company_id' => $this->company_id,
            'companies' => $this->companies,
            'company_size_id' => $this->company_size_id,
            'industry_id' => $this->industry_id,
            'salary_min' => $this->salary_min,
            'salary_max' => $this->salary_max,
            'currency_id' => $this->currency_id,
            'salary_period_id' => $this->salary_period_id,
            'hide_salary_jobs' => $this->hide_salary_jobs,
            'experience_min' => $this->experience_min,
            'experience_max' => $this->experience_max,
            'skills' => $this->skills,
            'skills_match' => $this->skills_match,
            'posted_after' => $this->posted_after,
            'posted_before' => $this->posted_before,
            'deadline_after' => $this->deadline_after,
            'deadline_before' => $this->deadline_before,
            'is_active' => $this->is_active,
            'is_featured' => $this->is_featured,
            'is_urgent' => $this->is_urgent,
            'exclude_expired' => $this->exclude_expired,
            'only_new_jobs' => $this->only_new_jobs,
        ], function ($value) {
            return null !== $value;
        });
    }

    /**
     * Get pagination parameters.
     */
    public function getPaginationParams(): array
    {
        return [
            'page' => $this->page,
            'per_page' => $this->per_page,
        ];
    }

    /**
     * Get sorting parameters.
     */
    public function getSortingParams(): array
    {
        return [
            'sort' => $this->sort,
            'direction' => $this->direction,
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Set default values
        $this->merge([
            'page' => $this->input('page', 1),
            'per_page' => $this->input('per_page', 15),
            'sort' => $this->input('sort', 'created_at'),
            'direction' => $this->input('direction', 'desc'),
            'is_active' => $this->input('is_active', true),
            'exclude_expired' => $this->input('exclude_expired', true),
            'skills_match' => $this->input('skills_match', 'any'),
            'track_view' => $this->input('track_view', true),
            'source' => $this->input('source', 'web'),
        ]);

        // Clean and validate search query
        if ($this->has('search') && !empty($this->search)) {
            $cleanSearch = trim($this->search);
            $cleanSearch = preg_replace('/\s+/', ' ', $cleanSearch); // Remove extra spaces
            $this->merge(['search' => $cleanSearch]);
        }

        // Clean keywords array
        if ($this->has('keywords') && is_array($this->keywords)) {
            $cleanKeywords = array_filter(array_map('trim', $this->keywords), function ($keyword) {
                return !empty($keyword);
            });
            $this->merge(['keywords' => array_values($cleanKeywords)]);
        }

        // Ensure numeric fields are properly typed
        $numericFields = [
            'country_id', 'state_id', 'city_id', 'location_radius',
            'job_category_id', 'job_type_id', 'company_id', 'career_level_id',
            'salary_min', 'salary_max', 'experience_min', 'experience_max',
            'page', 'per_page',
        ];

        foreach ($numericFields as $field) {
            if ($this->has($field) && !empty($this->{$field})) {
                $this->merge([$field => (int) $this->{$field}]);
            }
        }

        // Ensure boolean fields are properly typed
        $booleanFields = [
            'is_remote', 'is_active', 'is_featured', 'is_urgent',
            'exclude_expired', 'exclude_applied', 'exclude_saved',
            'only_applied', 'only_saved', 'hide_salary_jobs',
            'only_new_jobs', 'track_view', 'no_cache',
        ];

        foreach ($booleanFields as $field) {
            if ($this->has($field)) {
                $this->merge([$field => $this->boolean($field)]);
            }
        }
    }

    /**
     * Validate advanced filter combinations.
     *
     * @param mixed $validator
     */
    protected function validateAdvancedFilters($validator): void
    {
        // Validate location hierarchy
        if ($this->city_id && !$this->state_id) {
            $validator->errors()->add('state_id', __('validation.job_search.state_required_for_city'));
        }

        if ($this->state_id && !$this->country_id) {
            $validator->errors()->add('country_id', __('validation.job_search.country_required_for_state'));
        }

        // Validate contradictory filters
        if ($this->exclude_applied && $this->only_applied) {
            $validator->errors()->add('only_applied', __('validation.job_search.contradictory_applied_filters'));
        }

        if ($this->exclude_saved && $this->only_saved) {
            $validator->errors()->add('only_saved', __('validation.job_search.contradictory_saved_filters'));
        }

        // Validate reasonable search parameters
        if ($this->per_page > 50 && !auth()->check()) {
            $validator->errors()->add('per_page', __('validation.job_search.per_page_limit_guest'));
        }
    }

    /**
     * Validate user-specific filters.
     *
     * @param mixed $validator
     */
    protected function validateUserSpecificFilters($validator): void
    {
        $user = auth()->user();

        // User-specific filters require authentication
        $userSpecificFilters = [
            'exclude_applied', 'exclude_saved', 'only_applied', 'only_saved',
        ];

        foreach ($userSpecificFilters as $filter) {
            if ($this->has($filter) && $this->{$filter} && !$user) {
                $validator->errors()->add($filter, __('validation.job_search.authentication_required'));
            }
        }

        // Check if user has candidate profile for certain filters
        if ($user && !$user->candidate) {
            $candidateFilters = ['exclude_applied', 'only_applied'];
            foreach ($candidateFilters as $filter) {
                if ($this->has($filter) && $this->{$filter}) {
                    $validator->errors()->add($filter, __('validation.job_search.candidate_profile_required'));
                }
            }
        }
    }
}
