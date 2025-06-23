<?php

namespace App\Http\Requests\Api\Universal;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class CompanyIndexRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Allow public access to company listings
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
            'sort_by' => 'sometimes|string|in:id,name,created_at,updated_at,employee_count',
            'sort_direction' => 'sometimes|string|in:asc,desc',
            'filter_status' => 'sometimes|string|in:active,inactive',
            'filter_industry' => 'sometimes|string|max:255',
            'filter_location' => 'sometimes|string|max:255',
            'filter_size' => 'sometimes|string|in:startup,small,medium,large,enterprise',
            'employee_count_min' => 'sometimes|integer|min:0',
            'employee_count_max' => 'sometimes|integer|min:0',
            'founded_year_min' => 'sometimes|integer|min:1800|max:'.date('Y'),
            'founded_year_max' => 'sometimes|integer|min:1800|max:'.date('Y'),
            'verified_only' => 'sometimes|boolean',
            'featured_only' => 'sometimes|boolean',
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
            'sort_by.in' => 'Sort field must be one of: id, name, created_at, updated_at, employee_count.',
            'sort_direction.in' => 'Sort direction must be either asc or desc.',
            'filter_size.in' => 'Company size must be one of: startup, small, medium, large, enterprise.',
            'employee_count_min.integer' => 'Minimum employee count must be a valid integer.',
            'employee_count_max.integer' => 'Maximum employee count must be a valid integer.',
            'founded_year_min.integer' => 'Minimum founded year must be a valid integer.',
            'founded_year_max.integer' => 'Maximum founded year must be a valid integer.',
            'founded_year_min.min' => 'Founded year cannot be before 1800.',
            'founded_year_max.max' => 'Founded year cannot be in the future.',
            'verified_only.boolean' => 'Verified only filter must be true or false.',
            'featured_only.boolean' => 'Featured only filter must be true or false.',
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
            'filter_industry' => 'industry filter',
            'filter_location' => 'location filter',
            'filter_size' => 'company size filter',
            'employee_count_min' => 'minimum employee count',
            'employee_count_max' => 'maximum employee count',
            'founded_year_min' => 'minimum founded year',
            'founded_year_max' => 'maximum founded year',
            'verified_only' => 'verified companies only',
            'featured_only' => 'featured companies only',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            // Validate employee count range
            if ($this->has(['employee_count_min', 'employee_count_max'])) {
                if ($this->employee_count_min > $this->employee_count_max) {
                    $validator->errors()->add('employee_count_max', 'Maximum employee count must be greater than minimum.');
                }
            }

            // Validate founded year range
            if ($this->has(['founded_year_min', 'founded_year_max'])) {
                if ($this->founded_year_min > $this->founded_year_max) {
                    $validator->errors()->add('founded_year_max', 'Maximum founded year must be greater than minimum.');
                }
            }
        });
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
                'errors' => $validator->errors(),
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

        // Convert boolean strings
        foreach (['verified_only', 'featured_only'] as $field) {
            if ($this->has($field)) {
                $this->merge([
                    $field => filter_var($this->{$field}, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE),
                ]);
            }
        }
    }
}
