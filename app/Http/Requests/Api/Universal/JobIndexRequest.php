<?php

namespace App\Http\Requests\Api\Universal;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class JobIndexRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'search' => 'sometimes|string|max:255',
            'page' => 'sometimes|integer|min:1',
            'per_page' => 'sometimes|integer|min:1|max:100',
            'sort_by' => 'sometimes|string|in:id,title,created_at,updated_at,salary_from,salary_to',
            'sort_direction' => 'sometimes|string|in:asc,desc',
            'job_type_id' => 'sometimes|integer|exists:job_types,id',
            'job_category_id' => 'sometimes|integer|exists:job_categories,id',
            'career_level_id' => 'sometimes|integer|exists:career_levels,id',
            'country_id' => 'sometimes|integer|exists:countries,id',
            'state_id' => 'sometimes|integer|exists:states,id',
            'city_id' => 'sometimes|integer|exists:cities,id',
            'company_id' => 'sometimes|integer|exists:companies,id',
            'salary_min' => 'sometimes|numeric|min:0',
            'salary_max' => 'sometimes|numeric|min:0',
            'experience_years' => 'sometimes|integer|min:0|max:50',
            'remote_only' => 'sometimes|boolean',
            'featured_only' => 'sometimes|boolean',
            'skills' => 'sometimes|array',
            'skills.*' => 'integer|exists:skills,id',
            'posted_within' => 'sometimes|string|in:today,week,month,3months',
        ];
    }

    public function messages(): array
    {
        return [
            'search.max' => 'Search term cannot exceed 255 characters.',
            'per_page.max' => 'Items per page cannot exceed 100.',
            'sort_by.in' => 'Sort field must be one of: id, title, created_at, updated_at, salary_from, salary_to.',
            'job_type_id.exists' => 'The selected job type does not exist.',
            'job_category_id.exists' => 'The selected job category does not exist.',
            'skills.*.exists' => 'One or more selected skills do not exist.',
            'posted_within.in' => 'Posted within must be one of: today, week, month, 3months.',
        ];
    }

    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(
            response()->json([
                'success' => false,
                'message' => 'Invalid search parameters',
                'errors' => $validator->errors()
            ], 422)
        );
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'per_page' => $this->per_page ?? 20,
            'sort_by' => $this->sort_by ?? 'created_at',
            'sort_direction' => $this->sort_direction ?? 'desc',
        ]);
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            if ($this->has(['salary_min', 'salary_max'])) {
                if ($this->salary_min > $this->salary_max) {
                    $validator->errors()->add('salary_max', 'Maximum salary must be greater than minimum salary.');
                }
            }
        });
    }
} 