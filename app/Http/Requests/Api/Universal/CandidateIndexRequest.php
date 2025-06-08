<?php

namespace App\Http\Requests\Api\Universal;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class CandidateIndexRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Allow public access to candidate listings
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'search' => 'sometimes|string|max:255',
            'page' => 'sometimes|integer|min:1',
            'per_page' => 'sometimes|integer|min:1|max:100',
            'sort_by' => 'sometimes|string|in:id,name,created_at,updated_at',
            'sort_direction' => 'sometimes|string|in:asc,desc',
            'filter_status' => 'sometimes|string|in:active,inactive',
            'filter_location' => 'sometimes|string|max:255',
            'skills' => 'sometimes|array',
            'skills.*' => 'integer|exists:skills,id',
            'experience_years' => 'sometimes|integer|min:0|max:50',
            'salary_min' => 'sometimes|numeric|min:0',
            'salary_max' => 'sometimes|numeric|min:0',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'search.string' => 'Search term must be a valid text string.',
            'search.max' => 'Search term cannot exceed 255 characters.',
            'page.integer' => 'Page number must be a valid integer.',
            'page.min' => 'Page number must be at least 1.',
            'per_page.integer' => 'Items per page must be a valid integer.',
            'per_page.min' => 'Items per page must be at least 1.',
            'per_page.max' => 'Items per page cannot exceed 100.',
            'sort_by.in' => 'Sort field must be one of: id, name, created_at, updated_at.',
            'sort_direction.in' => 'Sort direction must be either asc or desc.',
            'skills.array' => 'Skills must be provided as an array.',
            'skills.*.integer' => 'Each skill ID must be a valid integer.',
            'skills.*.exists' => 'One or more selected skills do not exist.',
            'experience_years.integer' => 'Experience years must be a valid integer.',
            'experience_years.min' => 'Experience years cannot be negative.',
            'experience_years.max' => 'Experience years cannot exceed 50.',
            'salary_min.numeric' => 'Minimum salary must be a valid number.',
            'salary_max.numeric' => 'Maximum salary must be a valid number.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'search' => 'search term',
            'page' => 'page number',
            'per_page' => 'items per page',
            'sort_by' => 'sort field',
            'sort_direction' => 'sort direction',
            'filter_status' => 'status filter',
            'filter_location' => 'location filter',
            'skills' => 'skills',
            'experience_years' => 'years of experience',
            'salary_min' => 'minimum salary',
            'salary_max' => 'maximum salary',
        ];
    }

    /**
     * Handle a failed validation attempt.
     */
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

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Set default values
        $this->merge([
            'per_page' => $this->per_page ?? 20,
            'sort_by' => $this->sort_by ?? 'created_at',
            'sort_direction' => $this->sort_direction ?? 'desc',
        ]);
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            // Validate salary range
            if ($this->has(['salary_min', 'salary_max'])) {
                if ($this->salary_min > $this->salary_max) {
                    $validator->errors()->add('salary_max', 'Maximum salary must be greater than minimum salary.');
                }
            }
        });
    }
} 