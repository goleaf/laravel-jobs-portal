<?php

namespace App\Http\Requests;

use App\Models\Job;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreJobRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Check if user can create jobs
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
            'job_title' => [
                'required',
                'string',
                'max:180',
                'min:3'
            ],
            'description' => [
                'required',
                'string',
                'min:50',
                'max:10000'
            ],
            'company_id' => [
                'required',
                'integer',
                'exists:companies,id'
            ],
            'job_category_id' => [
                'required',
                'integer',
                'exists:job_categories,id'
            ],
            'job_type_id' => [
                'required',
                'integer',
                'exists:job_types,id'
            ],
            'career_level_id' => [
                'required',
                'integer',
                'exists:career_levels,id'
            ],
            'functional_area_id' => [
                'required',
                'integer',
                'exists:functional_areas,id'
            ],
            'job_shift_id' => [
                'required',
                'integer',
                'exists:job_shifts,id'
            ],
            'degree_level_id' => [
                'required',
                'integer',
                'exists:required_degree_levels,id'
            ],
            'country_id' => [
                'required',
                'integer',
                'exists:countries,id'
            ],
            'state_id' => [
                'required',
                'integer',
                'exists:states,id'
            ],
            'city_id' => [
                'required',
                'integer',
                'exists:cities,id'
            ],
            'salary_from' => [
                'required',
                'numeric',
                'min:0',
                'max:999999999'
            ],
            'salary_to' => [
                'required',
                'numeric',
                'min:0',
                'max:999999999',
                'gte:salary_from'
            ],
            'currency_id' => [
                'required',
                'integer',
                'exists:salary_currencies,id'
            ],
            'salary_period_id' => [
                'required',
                'integer',
                'exists:salary_periods,id'
            ],
            'position' => [
                'required',
                'string',
                'max:255'
            ],
            'experience' => [
                'required',
                'integer',
                'min:0',
                'max:50'
            ],
            'job_expiry_date' => [
                'required',
                'date',
                'after:today',
                'before:' . now()->addYear()->format('Y-m-d')
            ],
            'no_preference' => [
                'sometimes',
                'integer',
                Rule::in([0, 1, 2])
            ],
            'hide_salary' => [
                'sometimes',
                'boolean'
            ],
            'is_freelance' => [
                'sometimes',
                'boolean'
            ],
            'status' => [
                'sometimes',
                'integer',
                Rule::in([
                    Job::STATUS_DRAFT,
                    Job::STATUS_OPEN,
                    Job::STATUS_CLOSED,
                    Job::STATUS_PAUSED
                ])
            ],
            'skills' => [
                'sometimes',
                'array',
                'max:20'
            ],
            'skills.*' => [
                'integer',
                'exists:skills,id'
            ],
            'tags' => [
                'sometimes',
                'array',
                'max:10'
            ],
            'tags.*' => [
                'integer',
                'exists:tags,id'
            ]
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'job_title.required' => 'The job title is required.',
            'job_title.min' => 'The job title must be at least 3 characters.',
            'job_title.max' => 'The job title may not be greater than 180 characters.',
            'description.required' => 'The job description is required.',
            'description.min' => 'The job description must be at least 50 characters.',
            'description.max' => 'The job description may not be greater than 10,000 characters.',
            'salary_to.gte' => 'The maximum salary must be greater than or equal to the minimum salary.',
            'job_expiry_date.after' => 'The job expiry date must be a date after today.',
            'job_expiry_date.before' => 'The job expiry date must be within one year from today.',
            'skills.max' => 'You may select a maximum of 20 skills.',
            'tags.max' => 'You may select a maximum of 10 tags.',
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
            'job_type_id' => 'job type',
            'career_level_id' => 'career level',
            'functional_area_id' => 'functional area',
            'job_shift_id' => 'job shift',
            'degree_level_id' => 'degree level',
            'country_id' => 'country',
            'state_id' => 'state',
            'city_id' => 'city',
            'salary_from' => 'minimum salary',
            'salary_to' => 'maximum salary',
            'currency_id' => 'currency',
            'salary_period_id' => 'salary period',
            'job_expiry_date' => 'job expiry date',
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
            // Custom validation: Check if company belongs to the authenticated user
            if ($this->user()->hasRole('employer')) {
                $company = $this->user()->company;
                if (!$company || $company->id !== (int) $this->company_id) {
                    $validator->errors()->add('company_id', 'You can only create jobs for your own company.');
                }
            }

            // Custom validation: Check if state belongs to the selected country
            if ($this->filled(['country_id', 'state_id'])) {
                $stateExists = \App\Models\State::where('id', $this->state_id)
                    ->where('country_id', $this->country_id)
                    ->exists();

                if (!$stateExists) {
                    $validator->errors()->add('state_id', 'The selected state does not belong to the selected country.');
                }
            }

            // Custom validation: Check if city belongs to the selected state
            if ($this->filled(['state_id', 'city_id'])) {
                $cityExists = \App\Models\City::where('id', $this->city_id)
                    ->where('state_id', $this->state_id)
                    ->exists();

                if (!$cityExists) {
                    $validator->errors()->add('city_id', 'The selected city does not belong to the selected state.');
                }
            }
        });
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Set default status if not provided
        if (!$this->has('status')) {
            $this->merge([
                'status' => Job::STATUS_DRAFT,
            ]);
        }

        // Set default boolean values
        $this->merge([
            'hide_salary' => $this->boolean('hide_salary'),
            'is_freelance' => $this->boolean('is_freelance'),
        ]);

        // Auto-assign company for employers
        if ($this->user()->hasRole('employer') && !$this->has('company_id')) {
            $company = $this->user()->company;
            if ($company) {
                $this->merge([
                    'company_id' => $company->id,
                ]);
            }
        }
    }
} 