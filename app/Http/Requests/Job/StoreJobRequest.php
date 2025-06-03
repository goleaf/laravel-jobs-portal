<?php

namespace App\Http\Requests\Job;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\Job;

class StoreJobRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('create', Job::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'job_title' => ['required', 'string', 'max:180'],
            'description' => ['required', 'string'],
            'company_id' => ['required', 'exists:companies,id'],
            'job_category_id' => ['required', 'exists:job_categories,id'],
            'country_id' => ['required', 'exists:countries,id'],
            'state_id' => ['required', 'exists:states,id'],
            'city_id' => ['required', 'exists:cities,id'],
            'salary_from' => ['required', 'numeric', 'min:0', 'max:999999999'],
            'salary_to' => ['required', 'numeric', 'gte:salary_from', 'max:999999999'],
            'currency_id' => ['required', 'exists:salary_currencies,id'],
            'salary_period_id' => ['required', 'exists:salary_periods,id'],
            'job_type_id' => ['required', 'exists:job_types,id'],
            'career_level_id' => ['nullable', 'exists:career_levels,id'],
            'functional_area_id' => ['required', 'exists:functional_areas,id'],
            'job_shift_id' => ['nullable', 'exists:job_shifts,id'],
            'degree_level_id' => ['nullable', 'exists:required_degree_levels,id'],
            'experience' => ['required', 'integer', 'min:0', 'max:255'],
            'position' => ['required', 'integer', 'min:1', 'max:255'],
            'job_expiry_date' => ['required', 'date', 'after:today'],
            'no_preference' => ['sometimes', 'integer', 'in:0,1,2'],
            'hide_salary' => ['sometimes', 'boolean'],
            'is_freelance' => ['sometimes', 'boolean'],
            'key_responsibilities' => ['nullable', 'string'],
            'skills' => ['sometimes', 'array'],
            'skills.*' => ['exists:skills,id'],
            'tags' => ['sometimes', 'array'],
            'tags.*' => ['exists:tags,id'],
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
            'job_title' => 'job title',
            'job_category_id' => 'job category',
            'country_id' => 'country',
            'state_id' => 'state',
            'city_id' => 'city',
            'salary_from' => 'minimum salary',
            'salary_to' => 'maximum salary',
            'currency_id' => 'currency',
            'salary_period_id' => 'salary period',
            'job_type_id' => 'job type',
            'career_level_id' => 'career level',
            'functional_area_id' => 'functional area',
            'job_shift_id' => 'job shift',
            'degree_level_id' => 'degree level',
            'job_expiry_date' => 'job expiry date',
            'key_responsibilities' => 'key responsibilities',
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
            'job_title.required' => 'The job title is required.',
            'job_title.max' => 'The job title may not be greater than 180 characters.',
            'salary_to.gte' => 'The maximum salary must be greater than or equal to the minimum salary.',
            'job_expiry_date.after' => 'The job expiry date must be a date after today.',
            'position.min' => 'The number of positions must be at least 1.',
            'experience.min' => 'The experience cannot be negative.',
        ];
    }

    /**
     * Handle a passed validation attempt.
     *
     * @return void
     */
    protected function passedValidation(): void
    {
        // Set default values if not provided
        $this->merge([
            'hide_salary' => $this->boolean('hide_salary', false),
            'is_freelance' => $this->boolean('is_freelance', false),
            'no_preference' => $this->input('no_preference', 2), // Both
            'status' => Job::STATUS_DRAFT, // Always start as draft
            'is_created_by_admin' => $this->user()->hasRole('admin'),
        ]);
    }
} 