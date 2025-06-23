<?php

namespace App\Http\Requests\Job;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

/**
 * Index Job Request
 * 
 * Handles validation for listing jobs with Context7 patterns
 * Includes multilingual support and comprehensive validation
 */
class IndexJobRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Authorization handled by Gate in controller
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<mixed>|string|ValidationRule>
     */
    public function rules(): array
    {
        return [
            'search' => ['nullable', 'string', 'max:255'],
            'category_id' => ['nullable', 'integer', 'exists:job_categories,id'],
            'company_id' => ['nullable', 'integer', 'exists:companies,id'],
            'location' => ['nullable', 'string', 'max:255'],
            'job_type_id' => ['nullable', 'integer', 'exists:job_types,id'],
            'salary_from' => ['nullable', 'numeric', 'min:0'],
            'salary_to' => ['nullable', 'numeric', 'min:0', 'gte:salary_from'],
            'status' => ['nullable', 'in:active,inactive,draft,expired'],
            'featured' => ['nullable', 'boolean'],
            'sort_by' => ['nullable', 'in:created_at,title,salary,company,deadline'],
            'sort_direction' => ['nullable', 'in:asc,desc'],
            'per_page' => ['nullable', 'integer', 'min:5', 'max:100'],
            'page' => ['nullable', 'integer', 'min:1'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'search.string' => __('validation.string', ['attribute' => __('common.search')]),
            'search.max' => __('validation.max.string', ['attribute' => __('common.search'), 'max' => 255]),
            'category_id.integer' => __('validation.integer', ['attribute' => __('jobs.category')]),
            'category_id.exists' => __('validation.exists', ['attribute' => __('jobs.category')]),
            'company_id.integer' => __('validation.integer', ['attribute' => __('jobs.company')]),
            'company_id.exists' => __('validation.exists', ['attribute' => __('jobs.company')]),
            'location.string' => __('validation.string', ['attribute' => __('jobs.location')]),
            'location.max' => __('validation.max.string', ['attribute' => __('jobs.location'), 'max' => 255]),
            'job_type_id.integer' => __('validation.integer', ['attribute' => __('jobs.job_type')]),
            'job_type_id.exists' => __('validation.exists', ['attribute' => __('jobs.job_type')]),
            'salary_from.numeric' => __('validation.numeric', ['attribute' => __('jobs.salary_from')]),
            'salary_from.min' => __('validation.min.numeric', ['attribute' => __('jobs.salary_from'), 'min' => 0]),
            'salary_to.numeric' => __('validation.numeric', ['attribute' => __('jobs.salary_to')]),
            'salary_to.min' => __('validation.min.numeric', ['attribute' => __('jobs.salary_to'), 'min' => 0]),
            'salary_to.gte' => __('validation.gte.numeric', ['attribute' => __('jobs.salary_to'), 'value' => __('jobs.salary_from')]),
            'status.in' => __('validation.in', ['attribute' => __('jobs.status')]),
            'featured.boolean' => __('validation.boolean', ['attribute' => __('jobs.featured')]),
            'sort_by.in' => __('validation.in', ['attribute' => __('common.sort_by')]),
            'sort_direction.in' => __('validation.in', ['attribute' => __('common.sort_direction')]),
            'per_page.integer' => __('validation.integer', ['attribute' => __('common.per_page')]),
            'per_page.min' => __('validation.min.numeric', ['attribute' => __('common.per_page'), 'min' => 5]),
            'per_page.max' => __('validation.max.numeric', ['attribute' => __('common.per_page'), 'max' => 100]),
            'page.integer' => __('validation.integer', ['attribute' => __('common.page')]),
            'page.min' => __('validation.min.numeric', ['attribute' => __('common.page'), 'min' => 1]),
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'search' => __('common.search'),
            'category_id' => __('jobs.category'),
            'company_id' => __('jobs.company'),
            'location' => __('jobs.location'),
            'job_type_id' => __('jobs.job_type'),
            'salary_from' => __('jobs.salary_from'),
            'salary_to' => __('jobs.salary_to'),
            'status' => __('jobs.status'),
            'featured' => __('jobs.featured'),
            'sort_by' => __('common.sort_by'),
            'sort_direction' => __('common.sort_direction'),
            'per_page' => __('common.per_page'),
            'page' => __('common.page'),
        ];
    }

    /**
     * Get the validated data with additional computed fields.
     */
    public function getValidatedWithDefaults(): array
    {
        return array_merge($this->validated(), [
            'user_id' => Auth::id(),
            'company_id' => Auth::user()->company?->id,
        ]);
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Set default values for pagination
        $this->merge([
            'per_page' => $this->input('per_page', 15),
            'page' => $this->input('page', 1),
            'sort_by' => $this->input('sort_by', 'created_at'),
            'sort_direction' => $this->input('sort_direction', 'desc'),
        ]);
    }
}
