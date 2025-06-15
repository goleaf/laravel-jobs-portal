<?php

namespace App\Http\Requests\Api\Universal;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class JobUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        $job = $this->route('job');

        return auth()->check() && (
            auth()->user()->id === $job->company->user_id
            || auth()->user()->hasRole('admin')
        );
    }

    public function rules(): array
    {
        $jobId = $this->route('job')->id;

        return [
            'title' => 'sometimes|required|string|max:255',
            'slug' => ['sometimes', 'string', 'max:255', Rule::unique('jobs', 'slug')->ignore($jobId)],
            'description' => 'sometimes|required|string|min:100',
            'responsibilities' => 'sometimes|string',
            'requirements' => 'sometimes|string',
            'benefits' => 'sometimes|string',
            'job_type_id' => 'sometimes|integer|exists:job_types,id',
            'job_category_id' => 'sometimes|integer|exists:job_categories,id',
            'job_shift_id' => 'sometimes|integer|exists:job_shifts,id',
            'career_level_id' => 'sometimes|integer|exists:career_levels,id',
            'functional_area_id' => 'sometimes|integer|exists:functional_areas,id',
            'job_experience_id' => 'sometimes|integer|exists:job_experiences,id',
            'salary_from' => 'sometimes|numeric|min:0',
            'salary_to' => 'sometimes|numeric|min:0',
            'salary_currency_id' => 'sometimes|integer|exists:salary_currencies,id',
            'salary_period_id' => 'sometimes|integer|exists:salary_periods,id',
            'hide_salary' => 'sometimes|boolean',
            'country_id' => 'sometimes|integer|exists:countries,id',
            'state_id' => 'sometimes|integer|exists:states,id',
            'city_id' => 'sometimes|integer|exists:cities,id',
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
            'title.required' => 'Job title is required when provided.',
            'description.required' => 'Job description is required when provided.',
            'description.min' => 'Job description must be at least 100 characters.',
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
                'message' => 'Job update validation failed',
                'errors' => $validator->errors(),
            ], 422)
        );
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('title') && !$this->has('slug')) {
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
