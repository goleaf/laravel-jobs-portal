<?php

namespace App\Http\Requests\Job;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\Job;

class UpdateJobRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $job = $this->route('job');
        return $this->user()->can('update', $job);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'job_title' => ['sometimes', 'string', 'max:180'],
            'description' => ['sometimes', 'string'],
            'company_id' => ['sometimes', 'exists:companies,id'],
            'job_category_id' => ['sometimes', 'exists:job_categories,id'],
            'country_id' => ['sometimes', 'exists:countries,id'],
            'state_id' => ['sometimes', 'exists:states,id'],
            'city_id' => ['sometimes', 'exists:cities,id'],
            'salary_from' => ['sometimes', 'numeric', 'min:0', 'max:999999999'],
            'salary_to' => ['sometimes', 'numeric', 'gte:salary_from', 'max:999999999'],
            'currency_id' => ['sometimes', 'exists:salary_currencies,id'],
            'salary_period_id' => ['sometimes', 'exists:salary_periods,id'],
            'job_type_id' => ['sometimes', 'exists:job_types,id'],
            'career_level_id' => ['nullable', 'exists:career_levels,id'],
            'functional_area_id' => ['sometimes', 'exists:functional_areas,id'],
            'job_shift_id' => ['nullable', 'exists:job_shifts,id'],
            'degree_level_id' => ['nullable', 'exists:required_degree_levels,id'],
            'experience' => ['sometimes', 'integer', 'min:0', 'max:255'],
            'position' => ['sometimes', 'integer', 'min:1', 'max:255'],
            'job_expiry_date' => ['sometimes', 'date', 'after:today'],
            'no_preference' => ['sometimes', 'integer', 'in:0,1,2'],
            'hide_salary' => ['sometimes', 'boolean'],
            'is_freelance' => ['sometimes', 'boolean'],
            'is_suspended' => ['sometimes', 'boolean'],
            'status' => ['sometimes', 'integer', Rule::in(array_keys(Job::STATUS_ARRAY))],
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
            'is_suspended' => 'suspension status',
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
            'job_title.max' => 'The job title may not be greater than 180 characters.',
            'salary_to.gte' => 'The maximum salary must be greater than or equal to the minimum salary.',
            'job_expiry_date.after' => 'The job expiry date must be a date after today.',
            'position.min' => 'The number of positions must be at least 1.',
            'experience.min' => 'The experience cannot be negative.',
            'status.in' => 'The selected status is invalid.',
        ];
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
            // Additional validation: Can't set job to live if required fields are missing
            if ($this->input('status') == Job::STATUS_OPEN) {
                $job = $this->route('job');
                
                $requiredFields = [
                    'job_title', 'description', 'company_id', 'job_category_id',
                    'country_id', 'state_id', 'city_id', 'salary_from', 'salary_to',
                    'currency_id', 'salary_period_id', 'job_type_id', 'functional_area_id',
                    'experience', 'position', 'job_expiry_date'
                ];

                foreach ($requiredFields as $field) {
                    $value = $this->input($field) ?? $job->$field;
                    if (empty($value)) {
                        $validator->errors()->add('status', "Cannot publish job: {$field} is required.");
                    }
                }

                // Check if job expiry date is not in the past
                $expiryDate = $this->input('job_expiry_date') ?? $job->job_expiry_date;
                if ($expiryDate && $expiryDate <= now()) {
                    $validator->errors()->add('status', 'Cannot publish job: Job expiry date must be in the future.');
                }
            }
        });
    }

    /**
     * Handle a passed validation attempt.
     *
     * @return void
     */
    protected function passedValidation(): void
    {
        // Track when the job was last changed
        $this->merge([
            'last_change' => now(),
        ]);

        // Convert boolean inputs properly
        if ($this->has('hide_salary')) {
            $this->merge(['hide_salary' => $this->boolean('hide_salary')]);
        }

        if ($this->has('is_freelance')) {
            $this->merge(['is_freelance' => $this->boolean('is_freelance')]);
        }

        if ($this->has('is_suspended')) {
            $this->merge(['is_suspended' => $this->boolean('is_suspended')]);
        }
    }
} 