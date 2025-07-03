<?php

namespace App\Http\Requests\Job;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Create Job Request
 *
 * Handles validation for creating new jobs with Context7 patterns
 * Includes multilingual support and comprehensive validation
 */
class CreateJobRequest extends FormRequest
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
     */
    public function rules(): array
    {
        return [
            'job_title' => ['required', 'string', 'max:255', 'min:3'],
            'company_id' => ['required', 'integer', 'exists:companies,id'],
            'job_category_id' => ['required', 'integer', 'exists:job_categories,id'],
            'job_type_id' => ['required', 'integer', 'exists:job_types,id'],
            'career_level_id' => ['nullable', 'integer', 'exists:career_levels,id'],
            'functional_area_id' => ['nullable', 'integer', 'exists:functional_areas,id'],
            'job_shift_id' => ['nullable', 'integer', 'exists:job_shifts,id'],
            'required_degree_level_id' => ['nullable', 'integer', 'exists:required_degree_levels,id'],
            'description' => ['required', 'string', 'min:50'],
            'requirements' => ['nullable', 'string'],
            'benefits' => ['nullable', 'string'],
            'salary_from' => ['nullable', 'numeric', 'min:0', 'max:9999999999'],
            'salary_to' => ['nullable', 'numeric', 'min:0', 'max:9999999999', 'gte:salary_from'],
            'salary_currency_id' => ['nullable', 'integer', 'exists:salary_currencies,id'],
            'salary_period_id' => ['nullable', 'integer', 'exists:salary_periods,id'],
            'hide_salary' => ['nullable', 'boolean'],
            'no_preference' => ['nullable', 'boolean'],
            'country_id' => ['required', 'integer', 'exists:countries,id'],
            'state_id' => ['nullable', 'integer', 'exists:states,id'],
            'city_id' => ['nullable', 'integer', 'exists:cities,id'],
            'position' => ['nullable', 'integer', 'min:1', 'max:9999'],
            'experience' => ['nullable', 'string', 'max:255'],
            'is_featured' => ['nullable', 'boolean'],
            'is_suspended' => ['nullable', 'boolean'],
            'job_expiry_date' => ['nullable', 'date', 'after:today'],
            'status' => ['nullable', 'in:active,inactive,draft'],
            'skills' => ['nullable', 'array'],
            'skills.*' => ['integer', 'exists:skills,id'],
            'tags' => ['nullable', 'array'],
            'tags.*' => ['string', 'max:50'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'job_title.required' => __('validation.required', ['attribute' => __('jobs.job_title')]),
            'job_title.string' => __('validation.string', ['attribute' => __('jobs.job_title')]),
            'job_title.max' => __('validation.max.string', ['attribute' => __('jobs.job_title'), 'max' => 255]),
            'job_title.min' => __('validation.min.string', ['attribute' => __('jobs.job_title'), 'min' => 3]),
            'company_id.required' => __('validation.required', ['attribute' => __('jobs.company')]),
            'company_id.integer' => __('validation.integer', ['attribute' => __('jobs.company')]),
            'company_id.exists' => __('validation.exists', ['attribute' => __('jobs.company')]),
            'job_category_id.required' => __('validation.required', ['attribute' => __('jobs.category')]),
            'job_category_id.integer' => __('validation.integer', ['attribute' => __('jobs.category')]),
            'job_category_id.exists' => __('validation.exists', ['attribute' => __('jobs.category')]),
            'job_type_id.required' => __('validation.required', ['attribute' => __('jobs.job_type')]),
            'job_type_id.integer' => __('validation.integer', ['attribute' => __('jobs.job_type')]),
            'job_type_id.exists' => __('validation.exists', ['attribute' => __('jobs.job_type')]),
            'description.required' => __('validation.required', ['attribute' => __('jobs.description')]),
            'description.string' => __('validation.string', ['attribute' => __('jobs.description')]),
            'description.min' => __('validation.min.string', ['attribute' => __('jobs.description'), 'min' => 50]),
            'salary_from.numeric' => __('validation.numeric', ['attribute' => __('jobs.salary_from')]),
            'salary_from.min' => __('validation.min.numeric', ['attribute' => __('jobs.salary_from'), 'min' => 0]),
            'salary_from.max' => __('validation.max.numeric', ['attribute' => __('jobs.salary_from'), 'max' => 9999999999]),
            'salary_to.numeric' => __('validation.numeric', ['attribute' => __('jobs.salary_to')]),
            'salary_to.min' => __('validation.min.numeric', ['attribute' => __('jobs.salary_to'), 'min' => 0]),
            'salary_to.max' => __('validation.max.numeric', ['attribute' => __('jobs.salary_to'), 'max' => 9999999999]),
            'salary_to.gte' => __('validation.gte.numeric', ['attribute' => __('jobs.salary_to'), 'value' => __('jobs.salary_from')]),
            'country_id.required' => __('validation.required', ['attribute' => __('jobs.country')]),
            'country_id.integer' => __('validation.integer', ['attribute' => __('jobs.country')]),
            'country_id.exists' => __('validation.exists', ['attribute' => __('jobs.country')]),
            'position.integer' => __('validation.integer', ['attribute' => __('jobs.position')]),
            'position.min' => __('validation.min.numeric', ['attribute' => __('jobs.position'), 'min' => 1]),
            'position.max' => __('validation.max.numeric', ['attribute' => __('jobs.position'), 'max' => 9999]),
            'job_expiry_date.date' => __('validation.date', ['attribute' => __('jobs.expiry_date')]),
            'job_expiry_date.after' => __('validation.after', ['attribute' => __('jobs.expiry_date'), 'date' => 'today']),
            'status.in' => __('validation.in', ['attribute' => __('jobs.status')]),
            'skills.array' => __('validation.array', ['attribute' => __('jobs.skills')]),
            'skills.*.integer' => __('validation.integer', ['attribute' => __('jobs.skills')]),
            'skills.*.exists' => __('validation.exists', ['attribute' => __('jobs.skills')]),
            'tags.array' => __('validation.array', ['attribute' => __('jobs.tags')]),
            'tags.*.string' => __('validation.string', ['attribute' => __('jobs.tags')]),
            'tags.*.max' => __('validation.max.string', ['attribute' => __('jobs.tags'), 'max' => 50]),
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'job_title' => __('jobs.job_title'),
            'company_id' => __('jobs.company'),
            'job_category_id' => __('jobs.category'),
            'job_type_id' => __('jobs.job_type'),
            'career_level_id' => __('jobs.career_level'),
            'functional_area_id' => __('jobs.functional_area'),
            'job_shift_id' => __('jobs.job_shift'),
            'required_degree_level_id' => __('jobs.degree_level'),
            'description' => __('jobs.description'),
            'requirements' => __('jobs.requirements'),
            'benefits' => __('jobs.benefits'),
            'salary_from' => __('jobs.salary_from'),
            'salary_to' => __('jobs.salary_to'),
            'salary_currency_id' => __('jobs.salary_currency'),
            'salary_period_id' => __('jobs.salary_period'),
            'country_id' => __('jobs.country'),
            'state_id' => __('jobs.state'),
            'city_id' => __('jobs.city'),
            'position' => __('jobs.position'),
            'experience' => __('jobs.experience'),
            'is_featured' => __('jobs.featured'),
            'job_expiry_date' => __('jobs.expiry_date'),
            'status' => __('jobs.status'),
            'skills' => __('jobs.skills'),
            'tags' => __('jobs.tags'),
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Convert string values to proper types
        $this->merge([
            'hide_salary' => $this->boolean('hide_salary'),
            'no_preference' => $this->boolean('no_preference'),
            'is_featured' => $this->boolean('is_featured'),
            'is_suspended' => $this->boolean('is_suspended'),
            'position' => $this->input('position') ? (int) $this->input('position') : 1,
            'status' => $this->input('status', 'active'),
        ]);

        // Clean salary values by removing commas and formatting
        if ($this->has('salary_from')) {
            $this->merge(['salary_from' => str_replace(',', '', $this->input('salary_from'))]);
        }

        if ($this->has('salary_to')) {
            $this->merge(['salary_to' => str_replace(',', '', $this->input('salary_to'))]);
        }
    }
}
