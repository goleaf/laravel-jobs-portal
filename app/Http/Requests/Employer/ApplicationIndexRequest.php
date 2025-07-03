<?php

declare(strict_types=1);

namespace App\Http\Requests\Employer;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Class ApplicationIndexRequest
 * Enterprise-grade validation for Employer application index operations
 */
class ApplicationIndexRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Authentication-free system
    }

    public function rules(): array
    {
        return [
            // Search & Filtering
            'search' => [
                'sometimes',
                'string',
                'min:1',
                'max:200',
                'regex:/^[\p{L}\p{N}\s\-_\.@&,\(\)]+$/u',
            ],
            'status' => [
                'sometimes',
                'array',
                'max:10',
            ],
            'status.*' => [
                'string',
                'in:pending,shortlisted,rejected,hired,withdrawn',
            ],
            'job_id' => [
                'sometimes',
                'integer',
                'min:1',
                Rule::exists('jobs', 'id'),
            ],
            'experience_level' => [
                'sometimes',
                'array',
                'max:5',
            ],
            'experience_level.*' => [
                'string',
                'in:entry_level,mid_level,senior_level,executive',
            ],
            'education_level' => [
                'sometimes',
                'array',
                'max:10',
            ],
            'education_level.*' => [
                'integer',
                'min:1',
                Rule::exists('required_degree_levels', 'id'),
            ],

            // Date Filtering
            'applied_from' => [
                'sometimes',
                'date',
                'before_or_equal:today',
            ],
            'applied_to' => [
                'sometimes',
                'date',
                'after_or_equal:applied_from',
                'before_or_equal:today',
            ],
            'last_activity' => [
                'sometimes',
                'string',
                'in:today,3_days,week,2_weeks,month,3_months',
            ],

            // Pagination & Sorting
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
            'sort_by' => [
                'sometimes',
                'string',
                'in:created_at,updated_at,candidate_name,job_title,status,experience',
            ],
            'sort_direction' => [
                'sometimes',
                'string',
                'in:asc,desc',
            ],

            // View Options
            'view_type' => [
                'sometimes',
                'string',
                'in:list,grid,detailed',
            ],
            'show_archived' => [
                'sometimes',
                'boolean',
            ],
        ];
    }

    public function getValidatedWithDefaults(): array
    {
        $validated = $this->validated();

        return array_merge([
            'page' => 1,
            'per_page' => 25,
            'sort_by' => 'created_at',
            'sort_direction' => 'desc',
            'view_type' => 'list',
            'show_archived' => false,
        ], $validated);
    }

    public function messages(): array
    {
        return [
            'search.regex' => __('validation.custom.application.search_format'),
            'status.*.in' => __('validation.custom.application.status_invalid'),
            'job_id.exists' => __('validation.custom.application.job_not_found'),
            'applied_to.after_or_equal' => __('validation.custom.application.date_range_invalid'),
            'per_page.max' => __('validation.custom.application.per_page_limit'),
            'sort_by.in' => __('validation.custom.application.sort_field_invalid'),
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('search')) {
            $this->merge(['search' => trim($this->search)]);
        }

        foreach (['status', 'experience_level', 'education_level'] as $field) {
            if ($this->has($field) && is_string($this->input($field))) {
                $this->merge([$field => explode(',', $this->input($field))]);
            }
        }
    }
}
