<?php

namespace App\Http\Requests\Job;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\NoMaliciousContent;

/**
 * Request validation for JobController::store
 * 
 * @enhanced by RequestValidationImprover
 */
class UpdateJobRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // TODO: Implement proper authorization logic based on user permissions
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return {
    "job_title": "required|string|max:255",
    "job_description": "required|string|min:50",
    "job_requirement": "nullable|string",
    "job_benefit": "nullable|string",
    "country_id": "required|exists:countries,id",
    "state_id": "nullable|exists:states,id",
    "city_id": "nullable|exists:cities,id",
    "salary_from": "nullable|numeric|min:0",
    "salary_to": "nullable|numeric|min:0|gte:salary_from",
    "salary_currency_id": "nullable|exists:salary_currencies,id",
    "salary_period_id": "nullable|exists:salary_periods,id",
    "job_category_id": "required|exists:job_categories,id",
    "job_type_id": "required|exists:job_types,id",
    "career_level_id": "nullable|exists:career_levels,id",
    "functional_area_id": "nullable|exists:functional_areas,id",
    "job_shift_id": "nullable|exists:job_shifts,id",
    "degree_level_id": "nullable|exists:required_degree_levels,id",
    "position": "nullable|integer|min:1",
    "experience": "nullable|string|max:100",
    "job_expiry_date": "required|date|after:today",
    "hide_salary": "boolean",
    "is_freelance": "boolean",
    "is_suspended": "boolean"
};
    }

    /**
     * Get custom validation messages.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return {
    "job_title.required": "Job title is required",
    "job_description.required": "Job description is required",
    "job_description.min": "Job description must be at least 50 characters",
    "country_id.required": "Country is required",
    "job_category_id.required": "Job category is required",
    "job_type_id.required": "Job type is required",
    "salary_to.gte": "Maximum salary must be greater than or equal to minimum salary",
    "job_expiry_date.required": "Job expiry date is required",
    "job_expiry_date.after": "Job expiry date must be in the future"
};
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'user.first_name' => 'first name',
            'user.last_name' => 'last name',
            'user.email' => 'email address',
            'user.phone' => 'phone number',
            'job_title' => 'job title',
            'job_description' => 'job description',
            'job_expiry_date' => 'job expiry date',
            'salary_from' => 'minimum salary',
            'salary_to' => 'maximum salary'
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Sanitize input data
        if ($this->has('job_title')) {
            $this->merge([
                'job_title' => strip_tags($this->job_title)
            ]);
        }
        
        if ($this->has('job_description')) {
            $this->merge([
                'job_description' => strip_tags($this->job_description, '<p><br><ul><ol><li><strong><em>')
            ]);
        }
    }

    /**
     * Configure the validator instance.
     *
     * @param  \Illuminate\Validation\Validator  $validator
     * @return void
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            // Add custom validation logic here
            if ($this->has('salary_from') && $this->has('salary_to')) {
                if ($this->salary_from > $this->salary_to) {
                    $validator->errors()->add('salary_to', 'Maximum salary must be greater than minimum salary');
                }
            }
            
            // Check for malicious content in text fields
            foreach (['job_description', 'job_requirement', 'job_benefit'] as $field) {
                if ($this->has($field) && $this->{$field}) {
                    $rule = new NoMaliciousContent();
                    if (!$rule->passes($field, $this->{$field})) {
                        $validator->errors()->add($field, $rule->message());
                    }
                }
            }
        });
    }
}
