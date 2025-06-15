<?php

namespace App\Http\Requests\Api\Universal;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class JobStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'company_id' => 'required|integer|exists:companies,id',
            'title' => 'required|string|max:255',
            'slug' => 'sometimes|string|max:255|unique:jobs,slug',
            'description' => 'required|string|min:100',
            'responsibilities' => 'sometimes|string',
            'requirements' => 'sometimes|string',
            'benefits' => 'sometimes|string',
            'job_type_id' => 'required|integer|exists:job_types,id',
            'job_category_id' => 'required|integer|exists:job_categories,id',
            'job_shift_id' => 'sometimes|integer|exists:job_shifts,id',
            'career_level_id' => 'required|integer|exists:career_levels,id',
            'functional_area_id' => 'sometimes|integer|exists:functional_areas,id',
            'job_experience_id' => 'required|integer|exists:job_experiences,id',
            'salary_from' => 'sometimes|numeric|min:0',
            'salary_to' => 'sometimes|numeric|min:0',
            'salary_currency_id' => 'required_with:salary_from|integer|exists:salary_currencies,id',
            'salary_period_id' => 'required_with:salary_from|integer|exists:salary_periods,id',
            'hide_salary' => 'sometimes|boolean',
            'country_id' => 'required|integer|exists:countries,id',
            'state_id' => 'required|integer|exists:states,id',
            'city_id' => 'required|integer|exists:cities,id',
            'is_remote' => 'sometimes|boolean',
            'is_freelance' => 'sometimes|boolean',
            'required_degree_level_id' => 'sometimes|integer|exists:required_degree_levels,id',
            'experience_years_min' => 'sometimes|integer|min:0|max:50',
            'experience_years_max' => 'sometimes|integer|min:0|max:50',
            'deadline' => 'sometimes|date|after:today',
            'skills' => 'sometimes|array',
            'skills.*' => 'integer|exists:skills,id',
            'tags' => 'sometimes|array',
            'tags.*' => 'string|max:50',
            'is_featured' => 'sometimes|boolean',
            'status' => 'sometimes|string|in:draft,published,closed,expired',
        ];
    }

    public function messages(): array
    {
        return [
            'company_id.required' => 'Company is required.',
            'company_id.exists' => 'The selected company does not exist.',
            'title.required' => 'Job title is required.',
            'description.required' => 'Job description is required.',
            'description.min' => 'Job description must be at least 100 characters.',
            'job_type_id.required' => 'Job type is required.',
            'job_category_id.required' => 'Job category is required.',
            'career_level_id.required' => 'Career level is required.',
            'salary_currency_id.required_with' => 'Currency is required when salary is specified.',
            'salary_period_id.required_with' => 'Salary period is required when salary is specified.',
            'deadline.after' => 'Application deadline must be in the future.',
            'skills.*.exists' => 'One or more selected skills do not exist.',
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            if ($this->has(['salary_from', 'salary_to']) && $this->salary_from > $this->salary_to) {
                $validator->errors()->add('salary_to', 'Maximum salary must be greater than minimum salary.');
            }

            if ($this->has(['experience_years_min', 'experience_years_max']) && $this->experience_years_min > $this->experience_years_max) {
                $validator->errors()->add('experience_years_max', 'Maximum experience must be greater than minimum.');
            }
        });
    }

    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(
            response()->json([
                'success' => false,
                'message' => 'Job creation validation failed',
                'errors' => $validator->errors(),
            ], 422)
        );
    }

    protected function prepareForValidation(): void
    {
        if (!$this->slug && $this->title) {
            $this->merge(['slug' => \Str::slug($this->title)]);
        }

        foreach (['is_remote', 'is_freelance', 'hide_salary', 'is_featured'] as $field) {
            if ($this->has($field)) {
                $this->merge([
                    $field => filter_var($this->{$field}, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE),
                ]);
            }
        }
    }
}
