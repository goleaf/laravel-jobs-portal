<?php

namespace App\Http\Requests\Company;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

/**
 * Enhanced Enhanced Form Request for Company index
 * Implements Laravel 12 best practices with Enhanced MCP patterns
 * Auto-generated for Level 4 Complex System Transformation.
 */
class IndexCompanyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Allow authenticated users to view companies list
        return Auth::check();
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
                'max:10000',
            ],
            'per_page' => [
                'sometimes',
                'integer',
                'min:1',
                'max:100',
            ],

            // Search and filtering
            'search' => [
                'sometimes',
                'string',
                'max:255',
                'regex:/^[\p{L}\p{N}\p{P}\p{Z}\s]+$/u', // Unicode-safe search
            ],
            'status' => [
                'sometimes',
                'string',
                Rule::in(['active', 'inactive', 'pending', 'suspended']),
            ],
            'is_featured' => [
                'sometimes',
                'boolean',
            ],
            'is_verified' => [
                'sometimes',
                'boolean',
            ],

            // Location filters
            'country_id' => [
                'sometimes',
                'integer',
                'exists:countries,id',
            ],
            'state_id' => [
                'sometimes',
                'integer',
                'exists:states,id',
            ],
            'city_id' => [
                'sometimes',
                'integer',
                'exists:cities,id',
            ],

            // Industry and size filters
            'industry_id' => [
                'sometimes',
                'integer',
                'exists:industries,id',
            ],
            'company_size_id' => [
                'sometimes',
                'integer',
                'exists:company_sizes,id',
            ],
            'ownership_type_id' => [
                'sometimes',
                'integer',
                'exists:ownership_types,id',
            ],

            // Date range filters
            'created_from' => [
                'sometimes',
                'date',
                'before_or_equal:created_to',
            ],
            'created_to' => [
                'sometimes',
                'date',
                'after_or_equal:created_from',
                'before_or_equal:today',
            ],
            'established_from' => [
                'sometimes',
                'integer',
                'min:1800',
                'max:'.date('Y'),
            ],
            'established_to' => [
                'sometimes',
                'integer',
                'min:1800',
                'max:'.date('Y'),
                'gte:established_from',
            ],

            // Employee count range
            'employees_min' => [
                'sometimes',
                'integer',
                'min:1',
                'max:1000000',
            ],
            'employees_max' => [
                'sometimes',
                'integer',
                'min:1',
                'max:1000000',
                'gte:employees_min',
            ],

            // Sorting
            'sort_by' => [
                'sometimes',
                'string',
                Rule::in([
                    'name', 'created_at', 'updated_at', 'established_in',
                    'no_of_employees', 'jobs_count', 'applications_count',
                ]),
            ],
            'sort_order' => [
                'sometimes',
                'string',
                Rule::in(['asc', 'desc']),
            ],

            // Include relationships
            'include' => [
                'sometimes',
                'array',
            ],
            'include.*' => [
                'string',
                Rule::in([
                    'user', 'country', 'state', 'city', 'industry',
                    'ownershipType', 'companySize', 'jobs', 'activeJobs',
                ]),
            ],

            // Response format
            'format' => [
                'sometimes',
                'string',
                Rule::in(['json', 'csv', 'excel']),
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'page.integer' => __('companies.validation.page_integer'),
            'page.min' => __('companies.validation.page_min'),
            'page.max' => __('companies.validation.page_max'),
            'per_page.integer' => __('companies.validation.per_page_integer'),
            'per_page.min' => __('companies.validation.per_page_min'),
            'per_page.max' => __('companies.validation.per_page_max'),
            'search.string' => __('companies.validation.search_string'),
            'search.max' => __('companies.validation.search_max'),
            'search.regex' => __('companies.validation.search_format'),
            'status.in' => __('companies.validation.status_invalid'),
            'country_id.exists' => __('companies.validation.country_not_found'),
            'state_id.exists' => __('companies.validation.state_not_found'),
            'city_id.exists' => __('companies.validation.city_not_found'),
            'industry_id.exists' => __('companies.validation.industry_not_found'),
            'company_size_id.exists' => __('companies.validation.company_size_not_found'),
            'ownership_type_id.exists' => __('companies.validation.ownership_type_not_found'),
            'created_from.date' => __('companies.validation.created_from_date'),
            'created_from.before_or_equal' => __('companies.validation.created_from_before_to'),
            'created_to.date' => __('companies.validation.created_to_date'),
            'created_to.after_or_equal' => __('companies.validation.created_to_after_from'),
            'created_to.before_or_equal' => __('companies.validation.created_to_before_today'),
            'established_from.integer' => __('companies.validation.established_from_integer'),
            'established_from.min' => __('companies.validation.established_from_min'),
            'established_from.max' => __('companies.validation.established_from_max'),
            'established_to.integer' => __('companies.validation.established_to_integer'),
            'established_to.min' => __('companies.validation.established_to_min'),
            'established_to.max' => __('companies.validation.established_to_max'),
            'established_to.gte' => __('companies.validation.established_to_gte_from'),
            'employees_min.integer' => __('companies.validation.employees_min_integer'),
            'employees_min.min' => __('companies.validation.employees_min_min'),
            'employees_min.max' => __('companies.validation.employees_min_max'),
            'employees_max.integer' => __('companies.validation.employees_max_integer'),
            'employees_max.min' => __('companies.validation.employees_max_min'),
            'employees_max.max' => __('companies.validation.employees_max_max'),
            'employees_max.gte' => __('companies.validation.employees_max_gte_min'),
            'sort_by.in' => __('companies.validation.sort_by_invalid'),
            'sort_order.in' => __('companies.validation.sort_order_invalid'),
            'include.array' => __('companies.validation.include_array'),
            'include.*.in' => __('companies.validation.include_item_invalid'),
            'format.in' => __('companies.validation.format_invalid'),
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
            'page' => __('companies.attributes.page'),
            'per_page' => __('companies.attributes.per_page'),
            'search' => __('companies.attributes.search'),
            'status' => __('companies.attributes.status'),
            'is_featured' => __('companies.attributes.is_featured'),
            'is_verified' => __('companies.attributes.is_verified'),
            'country_id' => __('companies.attributes.country'),
            'state_id' => __('companies.attributes.state'),
            'city_id' => __('companies.attributes.city'),
            'industry_id' => __('companies.attributes.industry'),
            'company_size_id' => __('companies.attributes.company_size'),
            'ownership_type_id' => __('companies.attributes.ownership_type'),
            'created_from' => __('companies.attributes.created_from'),
            'created_to' => __('companies.attributes.created_to'),
            'established_from' => __('companies.attributes.established_from'),
            'established_to' => __('companies.attributes.established_to'),
            'employees_min' => __('companies.attributes.employees_min'),
            'employees_max' => __('companies.attributes.employees_max'),
            'sort_by' => __('companies.attributes.sort_by'),
            'sort_order' => __('companies.attributes.sort_order'),
            'include' => __('companies.attributes.include'),
            'format' => __('companies.attributes.format'),
        ];
    }

    /**
     * Get the processed filter data.
     */
    public function getFilters(): array
    {
        return $this->only([
            'search', 'status', 'is_featured', 'is_verified',
            'country_id', 'state_id', 'city_id',
            'industry_id', 'company_size_id', 'ownership_type_id',
            'created_from', 'created_to',
            'established_from', 'established_to',
            'employees_min', 'employees_max',
        ]);
    }

    /**
     * Get the pagination parameters.
     */
    public function getPagination(): array
    {
        return $this->only(['page', 'per_page']);
    }

    /**
     * Get the sorting parameters.
     */
    public function getSorting(): array
    {
        return $this->only(['sort_by', 'sort_order']);
    }

    /**
     * Get the relationships to include.
     */
    public function getIncludes(): array
    {
        return $this->input('include', []);
    }

    /**
     * Check if user can access advanced filters.
     */
    public function canUseAdvancedFilters(): bool
    {
        return Auth::user()->hasAnyRole(['Admin', 'Super Admin']);
    }

    /**
     * Check if user can export data.
     */
    public function canExport(): bool
    {
        return Auth::user()->hasPermission('companies.export')
               || Auth::user()->hasAnyRole(['Admin', 'Super Admin']);
    }

    /**
     * Configure the validator instance.
     * Enhanced Pattern: Enhanced validation logic.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            if ($this->hasEnhancedValidationConflicts()) {
                $validator->errors()->add('name', __('validation.conflict_detected'));
            }

            if ($this->hasSuspiciousContent()) {
                $validator->errors()->add('name', __('validation.suspicious_content'));
            }
        });
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
            'sort_order' => $this->input('sort_order', 'desc'),
        ]);

        // Clean search input
        if ($this->has('search')) {
            $this->merge([
                'search' => trim($this->input('search')),
            ]);
        }

        // Convert string booleans to actual booleans
        foreach (['is_featured', 'is_verified'] as $field) {
            if ($this->has($field)) {
                $this->merge([
                    $field => filter_var($this->input($field), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE),
                ]);
            }
        }
    }

    /**
     * Handle a failed validation attempt.
     * Enhanced Pattern: Enhanced error handling with security monitoring.
     */
    protected function failedValidation(Validator $validator): void
    {
        logger()->warning('Enhanced validation failed for IndexCompanyRequest', [
            'errors' => $validator->errors()->toArray(),
            'controller' => 'CompanyController',
            'method' => 'index',
            'user_id' => $this->user()?->id,
            'ip' => $this->ip(),
            'user_agent' => $this->userAgent(),
        ]);

        parent::failedValidation($validator);
    }

    /**
     * Enhanced Pattern: Enhanced business logic validation.
     */
    private function hasEnhancedValidationConflicts(): bool
    {
        // Add specific business logic validation here
        return false;
    }

    /**
     * Enhanced Pattern: Content security validation.
     */
    private function hasSuspiciousContent(): bool
    {
        $suspiciousPatterns = ['spam', 'scam', 'virus', 'malware', 'hack', 'exploit'];
        $content = strtolower(($this->search ?? '').' '.($this->search ?? ''));

        foreach ($suspiciousPatterns as $pattern) {
            if (strpos($content, $pattern) !== false) {
                return true;
            }
        }

        return false;
    }
}
