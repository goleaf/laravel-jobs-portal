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
        
        // Admin can update any job
        if (auth()->user()->hasRole('Admin')) {
            return true;
        }
        
        // Employer can only update their own jobs
        if (auth()->user()->hasRole('Employer')) {
            return $job && $job->company && $job->company->user_id === auth()->id();
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
            'job_title' => ['required', 'string', 'max:255'],
            'job_description' => ['required', 'string', 'min:50'],
            'country_id' => ['required', 'exists:countries,id'],
            'state_id' => ['required', 'exists:states,id'],
            'city_id' => ['required', 'exists:cities,id'],
            'salary_from' => ['nullable', 'numeric', 'min:0'],
            'salary_to' => ['nullable', 'numeric', 'min:0', 'gte:salary_from'],
            'salary_currency_id' => ['nullable', 'exists:salary_currencies,id'],
            'salary_period_id' => ['nullable', 'exists:salary_periods,id'],
            'job_category_id' => ['required', 'exists:job_categories,id'],
            'job_type_id' => ['required', 'exists:job_types,id'],
            'career_level_id' => ['nullable', 'exists:career_levels,id'],
            'functional_area_id' => ['nullable', 'exists:functional_areas,id'],
            'job_skill_id' => ['nullable', 'array'],
            'job_skill_id.*' => ['exists:job_skills,id'],
            'job_tag_id' => ['nullable', 'array'],
            'job_tag_id.*' => ['exists:job_tags,id'],
            'degree_level_id' => ['nullable', 'exists:required_degree_levels,id'],
            'position' => ['required', 'integer', 'min:1', 'max:1000'],
            'experience' => ['nullable', 'string', 'max:255'],
            'job_expiry_date' => ['required', 'date', 'after:today'],
            'status' => ['required', 'in:0,1,2'], // 0=Draft, 1=Open, 2=Closed
            'is_freelance' => ['boolean'],
            'hide_salary' => ['boolean'],
            'is_featured' => ['boolean'],
            'is_immediate_available' => ['boolean'],
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
            'job_title' => __('common.job_title'),
            'job_description' => __('common.job_description'),
            'country_id' => __('common.country'),
            'state_id' => __('common.state'),
            'city_id' => __('common.city'),
            'salary_from' => __('common.salary_from'),
            'salary_to' => __('common.salary_to'),
            'job_category_id' => __('common.job_category'),
            'job_type_id' => __('common.job_type'),
            'position' => __('common.positions'),
            'job_expiry_date' => __('common.job_expiry_date'),
            'status' => __('common.status'),
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
            'job_title.required' => __('validation.required', ['attribute' => __('common.job_title')]),
            'job_title.max' => __('validation.max.string', ['attribute' => __('common.job_title'), 'max' => 255]),
            'job_description.required' => __('validation.required', ['attribute' => __('common.job_description')]),
            'job_description.min' => __('validation.min.string', ['attribute' => __('common.job_description'), 'min' => 50]),
            'country_id.required' => __('validation.required', ['attribute' => __('common.country')]),
            'country_id.exists' => __('validation.exists', ['attribute' => __('common.country')]),
            'state_id.required' => __('validation.required', ['attribute' => __('common.state')]),
            'state_id.exists' => __('validation.exists', ['attribute' => __('common.state')]),
            'city_id.required' => __('validation.required', ['attribute' => __('common.city')]),
            'city_id.exists' => __('validation.exists', ['attribute' => __('common.city')]),
            'salary_from.numeric' => __('validation.numeric', ['attribute' => __('common.salary_from')]),
            'salary_from.min' => __('validation.min.numeric', ['attribute' => __('common.salary_from'), 'min' => 0]),
            'salary_to.numeric' => __('validation.numeric', ['attribute' => __('common.salary_to')]),
            'salary_to.min' => __('validation.min.numeric', ['attribute' => __('common.salary_to'), 'min' => 0]),
            'salary_to.gte' => __('validation.gte.numeric', ['attribute' => __('common.salary_to'), 'value' => __('common.salary_from')]),
            'job_category_id.required' => __('validation.required', ['attribute' => __('common.job_category')]),
            'job_category_id.exists' => __('validation.exists', ['attribute' => __('common.job_category')]),
            'job_type_id.required' => __('validation.required', ['attribute' => __('common.job_type')]),
            'job_type_id.exists' => __('validation.exists', ['attribute' => __('common.job_type')]),
            'position.required' => __('validation.required', ['attribute' => __('common.positions')]),
            'position.integer' => __('validation.integer', ['attribute' => __('common.positions')]),
            'position.min' => __('validation.min.numeric', ['attribute' => __('common.positions'), 'min' => 1]),
            'position.max' => __('validation.max.numeric', ['attribute' => __('common.positions'), 'max' => 1000]),
            'job_expiry_date.required' => __('validation.required', ['attribute' => __('common.job_expiry_date')]),
            'job_expiry_date.date' => __('validation.date', ['attribute' => __('common.job_expiry_date')]),
            'job_expiry_date.after' => __('validation.after', ['attribute' => __('common.job_expiry_date'), 'date' => 'today']),
            'status.required' => __('validation.required', ['attribute' => __('common.status')]),
            'status.in' => __('validation.in', ['attribute' => __('common.status')]),
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
            // Check if salary_to is provided when salary_from is provided
            if ($this->filled('salary_from') && !$this->filled('salary_to')) {
                $validator->errors()->add('salary_to', __('validation.required_with', [
                    'attribute' => __('common.salary_to'),
                    'values' => __('common.salary_from')
                ]));
            }

            // Ensure job expiry date is not more than 1 year in the future
            if ($this->filled('job_expiry_date')) {
                $expiryDate = \Carbon\Carbon::parse($this->job_expiry_date);
                $maxDate = \Carbon\Carbon::now()->addYear();
                
                if ($expiryDate->gt($maxDate)) {
                    $validator->errors()->add('job_expiry_date', __('validation.before', [
                        'attribute' => __('common.job_expiry_date'),
                        'date' => $maxDate->format('Y-m-d')
                    ]));
                }
            }

            // Additional validation for published jobs
            $job = $this->route('job');
            if ($this->status == 1 && $job) { // Status Open
                // Ensure required fields are complete for live jobs
                $requiredForLive = ['job_description', 'country_id', 'state_id', 'city_id'];
                foreach ($requiredForLive as $field) {
                    if (!$this->filled($field)) {
                        $validator->errors()->add($field, __('validation.required_for_live_job', [
                            'attribute' => $this->attributes()[$field] ?? $field
                        ]));
                    }
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

        if ($this->has('is_featured')) {
            $this->merge(['is_featured' => $this->boolean('is_featured')]);
        }

        if ($this->has('is_immediate_available')) {
            $this->merge(['is_immediate_available' => $this->boolean('is_immediate_available')]);
        }
    }
} 