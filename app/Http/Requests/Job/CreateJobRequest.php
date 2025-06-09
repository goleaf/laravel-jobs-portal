<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateJobRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check() && (auth()->user()->hasRole('Employer') || auth()->user()->hasRole('Admin'));
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'min:50'],
            'job_category_id' => ['required', 'exists:job_categories,id'],
            'job_type_id' => ['required', 'exists:job_types,id'],
            'career_level_id' => ['required', 'exists:career_levels,id'],
            'functional_area_id' => ['required', 'exists:functional_areas,id'],
            'salary_from' => ['nullable', 'numeric', 'min:0'],
            'salary_to' => ['nullable', 'numeric', 'min:0', 'gte:salary_from'],
            'salary_currency_id' => ['nullable', 'exists:salary_currencies,id'],
            'salary_period_id' => ['nullable', 'exists:salary_periods,id'],
            'country_id' => ['required', 'exists:countries,id'],
            'state_id' => ['nullable', 'exists:states,id'],
            'city_id' => ['nullable', 'exists:cities,id'],
            'location' => ['required', 'string', 'max:255'],
            'job_shift_id' => ['nullable', 'exists:job_shifts,id'],
            'degree_level_id' => ['nullable', 'exists:required_degree_levels,id'],
            'experience' => ['nullable', 'integer', 'min:0', 'max:50'],
            'gender' => ['nullable', 'in:0,1,2'], // 0=Male, 1=Female, 2=Both
            'is_freelance' => ['boolean'],
            'hide_salary' => ['boolean'],
            'position' => ['nullable', 'integer', 'min:1'],
            'expires_at' => ['nullable', 'date', 'after:today'],
            'job_skills' => ['nullable', 'array'],
            'job_skills.*' => ['exists:skills,id'],
            'job_tags' => ['nullable', 'array'],
            'job_tags.*' => ['exists:tags,id'],
            'requirements' => ['nullable', 'string'],
            'benefits' => ['nullable', 'string'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'title.required' => __('validation_custom.job.title_required'),
            'description.required' => __('validation_custom.job.description_required'),
            'job_category_id.required' => __('validation_custom.job.job_category_required'),
            'job_type_id.required' => __('validation_custom.job.job_type_required'),
            'salary_from.numeric' => __('validation_custom.job.salary_from_numeric'),
            'salary_to.numeric' => __('validation_custom.job.salary_to_numeric'),
            'location.required' => __('validation_custom.job.location_required'),
            'description.min' => 'Job description must be at least 50 characters.',
            'salary_to.gte' => 'Salary to must be greater than or equal to salary from.',
            'expires_at.after' => 'Job expiry date must be a future date.',
            'experience.max' => 'Experience cannot exceed 50 years.',
            'position.min' => 'Number of positions must be at least 1.',
            'gender.in' => 'Please select a valid gender preference.',
            'job_skills.array' => 'Skills must be an array.',
            'job_tags.array' => 'Tags must be an array.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'job_category_id' => 'job category',
            'job_type_id' => 'job type',
            'career_level_id' => 'career level',
            'functional_area_id' => 'functional area',
            'salary_currency_id' => 'salary currency',
            'salary_period_id' => 'salary period',
            'country_id' => 'country',
            'state_id' => 'state',
            'city_id' => 'city',
            'job_shift_id' => 'job shift',
            'degree_level_id' => 'degree level',
            'expires_at' => 'expiry date',
            'job_skills' => 'skills',
            'job_tags' => 'tags',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_freelance' => $this->boolean('is_freelance', false),
            'hide_salary' => $this->boolean('hide_salary', false),
        ]);
    }
}
