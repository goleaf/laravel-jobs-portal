<?php

namespace App\Http\Requests\JobApplication;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class IndexJobApplicationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = $this->user();
        
        // Admin can view all applications
        if ($user && $user->hasRole('admin')) {
            return true;
        }
        
        // Employer can view applications for their jobs
        if ($user && $user->hasRole('employer')) {
            return true;
        }
        
        // Candidate can view their own applications
        if ($user && $user->hasRole('candidate')) {
            return true;
        }
        
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // Pagination
            'page' => ['sometimes', 'integer', 'min:1'],
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:100'],
            
            // Search
            'search' => ['sometimes', 'string', 'max:255'],
            'search_fields' => ['sometimes', 'array'],
            'search_fields.*' => ['string', Rule::in(['candidate_name', 'job_title', 'company_name', 'email'])],
            
            // Filtering
            'job_id' => ['sometimes', 'integer', 'exists:jobs,id'],
            'candidate_id' => ['sometimes', 'integer', 'exists:users,id'],
            'company_id' => ['sometimes', 'integer', 'exists:companies,id'],
            'status' => ['sometimes', 'string', Rule::in(['pending', 'shortlisted', 'interview', 'hired', 'rejected'])],
            'application_date_from' => ['sometimes', 'date', 'before_or_equal:application_date_to'],
            'application_date_to' => ['sometimes', 'date', 'after_or_equal:application_date_from'],
            'expected_salary_min' => ['sometimes', 'numeric', 'min:0'],
            'expected_salary_max' => ['sometimes', 'numeric', 'min:0', 'gte:expected_salary_min'],
            
            // Location filters
            'city_id' => ['sometimes', 'integer', 'exists:cities,id'],
            'state_id' => ['sometimes', 'integer', 'exists:states,id'],
            'country_id' => ['sometimes', 'integer', 'exists:countries,id'],
            
            // Job category filters
            'job_category_id' => ['sometimes', 'integer', 'exists:job_categories,id'],
            'job_type_id' => ['sometimes', 'integer', 'exists:job_types,id'],
            'career_level_id' => ['sometimes', 'integer', 'exists:career_levels,id'],
            
            // Experience filters
            'experience_min' => ['sometimes', 'integer', 'min:0'],
            'experience_max' => ['sometimes', 'integer', 'min:0', 'gte:experience_min'],
            
            // Boolean filters
            'has_resume' => ['sometimes', 'boolean'],
            'has_cover_letter' => ['sometimes', 'boolean'],
            'is_remote_candidate' => ['sometimes', 'boolean'],
            'is_featured_candidate' => ['sometimes', 'boolean'],
            
            // Date filters
            'created_from' => ['sometimes', 'date', 'before_or_equal:created_to'],
            'created_to' => ['sometimes', 'date', 'after_or_equal:created_from'],
            'updated_from' => ['sometimes', 'date', 'before_or_equal:updated_to'],
            'updated_to' => ['sometimes', 'date', 'after_or_equal:updated_from'],
            
            // Sorting
            'sort_by' => ['sometimes', 'string', Rule::in([
                'id', 'created_at', 'updated_at', 'application_date', 'expected_salary',
                'candidate_name', 'job_title', 'company_name', 'status', 'experience'
            ])],
            'sort_direction' => ['sometimes', 'string', Rule::in(['asc', 'desc'])],
            
            // Advanced filters
            'skills' => ['sometimes', 'array'],
            'skills.*' => ['integer', 'exists:skills,id'],
            'education_level_id' => ['sometimes', 'integer', 'exists:required_degree_levels,id'],
            'marital_status_id' => ['sometimes', 'integer', 'exists:marital_statuses,id'],
            
            // Export options
            'export_format' => ['sometimes', 'string', Rule::in(['csv', 'excel', 'pdf'])],
            'export_fields' => ['sometimes', 'array'],
            'export_fields.*' => ['string'],
            
            // Include relationships
            'include' => ['sometimes', 'array'],
            'include.*' => ['string', Rule::in([
                'candidate', 'job', 'company', 'resume', 'skills', 'education', 'experience'
            ])],
            
            // Aggregation options
            'group_by' => ['sometimes', 'string', Rule::in([
                'status', 'job_id', 'company_id', 'city_id', 'job_category_id', 'created_date'
            ])],
            'aggregate' => ['sometimes', 'array'],
            'aggregate.*' => ['string', Rule::in(['count', 'avg_salary', 'min_salary', 'max_salary'])],
        ];
    }

    /**
     * Get custom error messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'page.integer' => __('validation.page_must_be_integer'),
            'page.min' => __('validation.page_must_be_positive'),
            'per_page.integer' => __('validation.per_page_must_be_integer'),
            'per_page.min' => __('validation.per_page_must_be_positive'),
            'per_page.max' => __('validation.per_page_too_large'),
            
            'search.string' => __('validation.search_must_be_string'),
            'search.max' => __('validation.search_too_long'),
            
            'job_id.integer' => __('validation.job_id_must_be_integer'),
            'job_id.exists' => __('validation.job_not_found'),
            'candidate_id.integer' => __('validation.candidate_id_must_be_integer'),
            'candidate_id.exists' => __('validation.candidate_not_found'),
            'company_id.integer' => __('validation.company_id_must_be_integer'),
            'company_id.exists' => __('validation.company_not_found'),
            
            'status.in' => __('validation.invalid_application_status'),
            
            'application_date_from.date' => __('validation.application_date_from_invalid'),
            'application_date_from.before_or_equal' => __('validation.application_date_from_after_to'),
            'application_date_to.date' => __('validation.application_date_to_invalid'),
            'application_date_to.after_or_equal' => __('validation.application_date_to_before_from'),
            
            'expected_salary_min.numeric' => __('validation.expected_salary_min_must_be_numeric'),
            'expected_salary_min.min' => __('validation.expected_salary_min_negative'),
            'expected_salary_max.numeric' => __('validation.expected_salary_max_must_be_numeric'),
            'expected_salary_max.min' => __('validation.expected_salary_max_negative'),
            'expected_salary_max.gte' => __('validation.expected_salary_max_less_than_min'),
            
            'city_id.exists' => __('validation.city_not_found'),
            'state_id.exists' => __('validation.state_not_found'),
            'country_id.exists' => __('validation.country_not_found'),
            
            'job_category_id.exists' => __('validation.job_category_not_found'),
            'job_type_id.exists' => __('validation.job_type_not_found'),
            'career_level_id.exists' => __('validation.career_level_not_found'),
            
            'experience_min.integer' => __('validation.experience_min_must_be_integer'),
            'experience_min.min' => __('validation.experience_min_negative'),
            'experience_max.integer' => __('validation.experience_max_must_be_integer'),
            'experience_max.min' => __('validation.experience_max_negative'),
            'experience_max.gte' => __('validation.experience_max_less_than_min'),
            
            'sort_by.in' => __('validation.invalid_sort_field'),
            'sort_direction.in' => __('validation.invalid_sort_direction'),
            
            'export_format.in' => __('validation.invalid_export_format'),
            'include.*.in' => __('validation.invalid_include_relationship'),
            'group_by.in' => __('validation.invalid_group_by_field'),
            'aggregate.*.in' => __('validation.invalid_aggregate_function'),
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'page' => __('attributes.page'),
            'per_page' => __('attributes.per_page'),
            'search' => __('attributes.search'),
            'job_id' => __('attributes.job'),
            'candidate_id' => __('attributes.candidate'),
            'company_id' => __('attributes.company'),
            'status' => __('attributes.status'),
            'application_date_from' => __('attributes.application_date_from'),
            'application_date_to' => __('attributes.application_date_to'),
            'expected_salary_min' => __('attributes.expected_salary_min'),
            'expected_salary_max' => __('attributes.expected_salary_max'),
            'city_id' => __('attributes.city'),
            'state_id' => __('attributes.state'),
            'country_id' => __('attributes.country'),
            'job_category_id' => __('attributes.job_category'),
            'job_type_id' => __('attributes.job_type'),
            'career_level_id' => __('attributes.career_level'),
            'experience_min' => __('attributes.experience_min'),
            'experience_max' => __('attributes.experience_max'),
            'sort_by' => __('attributes.sort_by'),
            'sort_direction' => __('attributes.sort_direction'),
            'export_format' => __('attributes.export_format'),
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
            'sort_by' => $this->input('sort_by', 'created_at'),
            'sort_direction' => $this->input('sort_direction', 'desc'),
        ]);

        // Clean search input
        if ($this->filled('search')) {
            $this->merge([
                'search' => trim($this->input('search')),
            ]);
        }

        // Convert string booleans to actual booleans
        $booleanFields = ['has_resume', 'has_cover_letter', 'is_remote_candidate', 'is_featured_candidate'];
        foreach ($booleanFields as $field) {
            if ($this->has($field)) {
                $this->merge([
                    $field => filter_var($this->input($field), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE),
                ]);
            }
        }

        // Ensure arrays are properly formatted
        if ($this->has('search_fields') && is_string($this->input('search_fields'))) {
            $this->merge([
                'search_fields' => explode(',', $this->input('search_fields')),
            ]);
        }

        if ($this->has('skills') && is_string($this->input('skills'))) {
            $this->merge([
                'skills' => array_map('intval', explode(',', $this->input('skills'))),
            ]);
        }

        if ($this->has('include') && is_string($this->input('include'))) {
            $this->merge([
                'include' => explode(',', $this->input('include')),
            ]);
        }
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            // Additional business logic validation
            $user = $this->user();
            
            // Restrict candidate access to their own applications
            if ($user && $user->hasRole('candidate')) {
                if ($this->filled('candidate_id') && $this->input('candidate_id') != $user->id) {
                    $validator->errors()->add('candidate_id', __('validation.unauthorized_candidate_access'));
                }
            }
            
            // Restrict employer access to their company's job applications
            if ($user && $user->hasRole('employer')) {
                if ($this->filled('company_id')) {
                    $userCompanyIds = $user->companies()->pluck('id')->toArray();
                    if (!in_array($this->input('company_id'), $userCompanyIds)) {
                        $validator->errors()->add('company_id', __('validation.unauthorized_company_access'));
                    }
                }
            }
            
            // Validate date ranges
            if ($this->filled(['application_date_from', 'application_date_to'])) {
                $from = \Carbon\Carbon::parse($this->input('application_date_from'));
                $to = \Carbon\Carbon::parse($this->input('application_date_to'));
                
                if ($from->gt($to)) {
                    $validator->errors()->add('application_date_from', __('validation.date_range_invalid'));
                }
                
                // Limit date range to prevent performance issues
                if ($from->diffInDays($to) > 365) {
                    $validator->errors()->add('application_date_to', __('validation.date_range_too_large'));
                }
            }
            
            // Validate salary ranges
            if ($this->filled(['expected_salary_min', 'expected_salary_max'])) {
                $min = $this->input('expected_salary_min');
                $max = $this->input('expected_salary_max');
                
                if ($min > $max) {
                    $validator->errors()->add('expected_salary_max', __('validation.salary_range_invalid'));
                }
                
                // Reasonable salary limits
                if ($max > 10000000) { // 10 million
                    $validator->errors()->add('expected_salary_max', __('validation.salary_too_high'));
                }
            }
        });
    }
} 