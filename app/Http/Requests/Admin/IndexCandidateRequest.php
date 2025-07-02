<?php

namespace App\Http\Requests\Admin;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class IndexCandidateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Based on user requirements: no auth system, but admin access required
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
                Rule::in([5, 10, 15, 25, 50, 100]),
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
                Rule::in(['name', 'email', 'phone', 'skills', 'experience', 'location', 'all']),
            ],

            // Filtering parameters
            'status' => [
                'sometimes',
                'string',
                Rule::in(['active', 'inactive', 'suspended', 'pending', 'all']),
            ],

            'verification_status' => [
                'sometimes',
                'string',
                Rule::in(['verified', 'unverified', 'pending', 'all']),
            ],

            'experience_level' => [
                'sometimes',
                'string',
                Rule::in([
                    'entry_level',
                    'junior',
                    'mid_level',
                    'senior',
                    'executive',
                    'expert',
                ]),
            ],

            'experience_min' => [
                'sometimes',
                'integer',
                'min:0',
                'max:50',
            ],

            'experience_max' => [
                'sometimes',
                'integer',
                'min:0',
                'max:50',
                'gte:experience_min',
            ],

            'current_salary_min' => [
                'sometimes',
                'integer',
                'min:0',
                'max:10000000',
            ],

            'current_salary_max' => [
                'sometimes',
                'integer',
                'min:0',
                'max:10000000',
                'gte:current_salary_min',
            ],

            'expected_salary_min' => [
                'sometimes',
                'integer',
                'min:0',
                'max:10000000',
            ],

            'expected_salary_max' => [
                'sometimes',
                'integer',
                'min:0',
                'max:10000000',
                'gte:expected_salary_min',
            ],

            'availability' => [
                'sometimes',
                'string',
                Rule::in(['immediate', 'notice_period', 'specific_date', 'negotiable']),
            ],

            'notice_period' => [
                'sometimes',
                'integer',
                'min:0',
                'max:365',
            ],

            // Geographic filters
            'country_id' => [
                'sometimes',
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

            'remote_preference' => [
                'sometimes',
                'string',
                Rule::in(['remote_only', 'hybrid', 'onsite', 'flexible', 'any']),
            ],

            // Skills and categories
            'skills' => [
                'sometimes',
                'array',
                'max:20',
            ],

            'skills.*' => [
                'integer',
                'exists:skills,id',
            ],

            'job_categories' => [
                'sometimes',
                'array',
                'max:10',
            ],

            'job_categories.*' => [
                'integer',
                'exists:job_categories,id',
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

            // Education filters
            'education_level' => [
                'sometimes',
                'string',
                Rule::in([
                    'high_school',
                    'associate',
                    'bachelor',
                    'master',
                    'phd',
                    'professional',
                    'certification',
                ]),
            ],

            'degree_fields' => [
                'sometimes',
                'array',
                'max:10',
            ],

            'degree_fields.*' => [
                'string',
                'max:100',
            ],

            // Personal information filters
            'gender' => [
                'sometimes',
                'string',
                Rule::in(['male', 'female', 'other', 'prefer_not_to_say']),
            ],

            'age_min' => [
                'sometimes',
                'integer',
                'min:16',
                'max:100',
            ],

            'age_max' => [
                'sometimes',
                'integer',
                'min:16',
                'max:100',
                'gte:age_min',
            ],

            'marital_status' => [
                'sometimes',
                'string',
                Rule::in(['single', 'married', 'divorced', 'widowed', 'prefer_not_to_say']),
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

            'last_active_from' => [
                'sometimes',
                'date',
                'before_or_equal:today',
                'before_or_equal:last_active_to',
            ],

            'last_active_to' => [
                'sometimes',
                'date',
                'before_or_equal:today',
                'after_or_equal:last_active_from',
            ],

            // Sorting parameters
            'sort_by' => [
                'sometimes',
                'string',
                Rule::in([
                    'name',
                    'email',
                    'created_at',
                    'updated_at',
                    'last_active_at',
                    'experience',
                    'current_salary',
                    'expected_salary',
                    'verification_status',
                    'status',
                    'profile_completion',
                ]),
            ],

            'sort_direction' => [
                'sometimes',
                'string',
                Rule::in(['asc', 'desc']),
            ],

            // Advanced filters
            'has_resume' => [
                'sometimes',
                'boolean',
            ],

            'has_cover_letter' => [
                'sometimes',
                'boolean',
            ],

            'has_portfolio' => [
                'sometimes',
                'boolean',
            ],

            'profile_completion_min' => [
                'sometimes',
                'integer',
                'min:0',
                'max:100',
            ],

            'profile_completion_max' => [
                'sometimes',
                'integer',
                'min:0',
                'max:100',
                'gte:profile_completion_min',
            ],

            'application_count_min' => [
                'sometimes',
                'integer',
                'min:0',
                'max:1000',
            ],

            'application_count_max' => [
                'sometimes',
                'integer',
                'min:0',
                'max:1000',
                'gte:application_count_min',
            ],

            'interview_count_min' => [
                'sometimes',
                'integer',
                'min:0',
                'max:100',
            ],

            'interview_count_max' => [
                'sometimes',
                'integer',
                'min:0',
                'max:100',
                'gte:interview_count_min',
            ],

            // Export parameters
            'export_format' => [
                'sometimes',
                'string',
                Rule::in(['csv', 'excel', 'pdf']),
            ],

            'export_fields' => [
                'sometimes',
                'array',
                'required_with:export_format',
            ],

            'export_fields.*' => [
                'string',
                Rule::in([
                    'name',
                    'email',
                    'phone',
                    'experience',
                    'current_salary',
                    'expected_salary',
                    'location',
                    'skills',
                    'education',
                    'status',
                    'created_at',
                    'last_active_at',
                ]),
            ],

            // Admin action parameters
            'bulk_action' => [
                'sometimes',
                'string',
                Rule::in(['activate', 'deactivate', 'verify', 'unverify', 'delete', 'export']),
            ],

            'selected_candidates' => [
                'sometimes',
                'array',
                'required_with:bulk_action',
                'max:100',
            ],

            'selected_candidates.*' => [
                'integer',
                'exists:candidates,id',
            ],

            // Analytics parameters
            'include_analytics' => [
                'sometimes',
                'boolean',
            ],

            'analytics_period' => [
                'sometimes',
                'string',
                'required_if:include_analytics,true',
                Rule::in(['week', 'month', 'quarter', 'year']),
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
            'experience_max.gte' => __('validation.experience_range_invalid'),
            'current_salary_max.gte' => __('validation.salary_range_invalid'),
            'expected_salary_max.gte' => __('validation.expected_salary_range_invalid'),
            'age_max.gte' => __('validation.age_range_invalid'),
            'created_to.after_or_equal' => __('validation.date_range_invalid'),
            'last_active_to.after_or_equal' => __('validation.date_range_invalid'),
            'state_id.exists' => __('validation.invalid_state'),
            'city_id.exists' => __('validation.invalid_city'),
            'skills.max' => __('validation.too_many_skills_selected'),
            'job_categories.max' => __('validation.too_many_categories_selected'),
            'selected_candidates.max' => __('validation.bulk_action_limit_exceeded'),
            'export_fields.required_with' => __('validation.export_fields_required'),
            'analytics_period.required_if' => __('validation.analytics_period_required'),
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
            'verification_status' => __('validation.attributes.verification_status'),
            'experience_level' => __('validation.attributes.experience_level'),
            'experience_min' => __('validation.attributes.minimum_experience'),
            'experience_max' => __('validation.attributes.maximum_experience'),
            'current_salary_min' => __('validation.attributes.minimum_current_salary'),
            'current_salary_max' => __('validation.attributes.maximum_current_salary'),
            'expected_salary_min' => __('validation.attributes.minimum_expected_salary'),
            'expected_salary_max' => __('validation.attributes.maximum_expected_salary'),
            'country_id' => __('validation.attributes.country'),
            'state_id' => __('validation.attributes.state'),
            'city_id' => __('validation.attributes.city'),
            'remote_preference' => __('validation.attributes.remote_preference'),
            'job_categories' => __('validation.attributes.job_categories'),
            'functional_areas' => __('validation.attributes.functional_areas'),
            'education_level' => __('validation.attributes.education_level'),
            'degree_fields' => __('validation.attributes.degree_fields'),
            'age_min' => __('validation.attributes.minimum_age'),
            'age_max' => __('validation.attributes.maximum_age'),
            'marital_status' => __('validation.attributes.marital_status'),
            'created_from' => __('validation.attributes.created_from_date'),
            'created_to' => __('validation.attributes.created_to_date'),
            'last_active_from' => __('validation.attributes.last_active_from_date'),
            'last_active_to' => __('validation.attributes.last_active_to_date'),
            'sort_by' => __('validation.attributes.sort_field'),
            'sort_direction' => __('validation.attributes.sort_direction'),
            'export_format' => __('validation.attributes.export_format'),
            'export_fields' => __('validation.attributes.export_fields'),
            'bulk_action' => __('validation.attributes.bulk_action'),
            'selected_candidates' => __('validation.attributes.selected_candidates'),
            'analytics_period' => __('validation.attributes.analytics_period'),
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Set default values
        if (!$this->has('per_page')) {
            $this->merge(['per_page' => 10]);
        }

        if (!$this->has('sort_by')) {
            $this->merge(['sort_by' => 'created_at']);
        }

        if (!$this->has('sort_direction')) {
            $this->merge(['sort_direction' => 'desc']);
        }

        if (!$this->has('search_type')) {
            $this->merge(['search_type' => 'all']);
        }

        // Clean search input
        if ($this->has('search')) {
            $this->merge([
                'search' => trim($this->input('search'))
            ]);
        }

        // Convert string booleans to actual booleans
        $booleanFields = [
            'has_resume',
            'has_cover_letter', 
            'has_portfolio',
            'include_analytics'
        ];

        foreach ($booleanFields as $field) {
            if ($this->has($field)) {
                $this->merge([
                    $field => filter_var($this->input($field), FILTER_VALIDATE_BOOLEAN)
                ]);
            }
        }

        // Ensure arrays are properly formatted
        $arrayFields = ['skills', 'job_categories', 'functional_areas', 'degree_fields', 'export_fields', 'selected_candidates'];
        foreach ($arrayFields as $field) {
            if ($this->has($field) && !is_array($this->input($field))) {
                $this->merge([
                    $field => array_filter(explode(',', $this->input($field)))
                ]);
            }
        }

        // Log admin candidate search for audit purposes
        if ($this->has('search') && $this->input('search')) {
            Log::info('Admin candidate search performed', [
                'search_term' => $this->input('search'),
                'search_type' => $this->input('search_type'),
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
        // Log successful admin candidate index request for audit
        Log::info('Admin candidate index request validated', [
            'filters_applied' => count(array_filter($this->only([
                'status', 'verification_status', 'experience_level', 'country_id',
                'state_id', 'city_id', 'skills', 'job_categories'
            ]))),
            'search_performed' => $this->has('search'),
            'export_requested' => $this->has('export_format'),
            'bulk_action_requested' => $this->has('bulk_action'),
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
            'malware', 'phishing', 'adult', 'xxx', 'porn', 'sex'
        ];

        $lowercaseContent = strtolower($content);
        
        foreach ($inappropriateWords as $word) {
            if (strpos($lowercaseContent, $word) !== false) {
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