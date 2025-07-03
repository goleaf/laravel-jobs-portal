<?php

namespace App\Http\Requests\Api\Universal;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class IndexRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Public access for listing resources
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
            'sort_by' => 'sometimes|string|max:50',
            'sort_direction' => 'sometimes|string|in:asc,desc',
            'filter_status' => 'sometimes|string|in:active,inactive,pending,published,draft,closed,expired',
            'filter_type' => 'sometimes|string|max:100',
            'filter_category' => 'sometimes|string|max:100',
            'filter_location' => 'sometimes|string|max:255',
            'filter_company' => 'sometimes|string|max:255',
            'filter_industry' => 'sometimes|string|max:255',
            'filter_size' => 'sometimes|string|in:startup,small,medium,large,enterprise',
            'date_from' => 'sometimes|date',
            'date_to' => 'sometimes|date|after_or_equal:date_from',
            'salary_min' => 'sometimes|numeric|min:0',
            'salary_max' => 'sometimes|numeric|min:0',
            'experience_min' => 'sometimes|integer|min:0|max:50',
            'experience_max' => 'sometimes|integer|min:0|max:50',
            'skills' => 'sometimes|array',
            'skills.*' => 'integer|exists:skills,id',
            'tags' => 'sometimes|array',
            'tags.*' => 'string|max:50',
            'featured_only' => 'sometimes|boolean',
            'verified_only' => 'sometimes|boolean',
            'popular_only' => 'sometimes|boolean',
            'remote_only' => 'sometimes|boolean',
            'include' => 'sometimes|string|max:500',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'search.max' => 'Search term cannot exceed 255 characters.',
            'page.integer' => 'Page number must be a valid integer.',
            'page.min' => 'Page number must be at least 1.',
            'per_page.integer' => 'Items per page must be a valid integer.',
            'per_page.min' => 'Items per page must be at least 1.',
            'per_page.max' => 'Items per page cannot exceed 100.',
            'sort_direction.in' => 'Sort direction must be either asc or desc.',
            'date_to.after_or_equal' => 'End date must be after or equal to start date.',
            'salary_max.gte' => 'Maximum salary must be greater than or equal to minimum salary.',
            'experience_max.gte' => 'Maximum experience must be greater than or equal to minimum experience.',
            'skills.array' => 'Skills must be provided as an array.',
            'skills.*.exists' => 'One or more selected skills do not exist.',
            'tags.array' => 'Tags must be provided as an array.',
            'featured_only.boolean' => 'Featured only filter must be true or false.',
            'verified_only.boolean' => 'Verified only filter must be true or false.',
            'popular_only.boolean' => 'Popular only filter must be true or false.',
            'remote_only.boolean' => 'Remote only filter must be true or false.',
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
            'date_from' => 'start date',
            'date_to' => 'end date',
            'salary_min' => 'minimum salary',
            'salary_max' => 'maximum salary',
            'experience_min' => 'minimum experience',
            'experience_max' => 'maximum experience',
            'skills' => 'skills',
            'tags' => 'tags',
            'featured_only' => 'featured only',
            'verified_only' => 'verified only',
            'popular_only' => 'popular only',
            'remote_only' => 'remote only',
        ];
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

            // Validate experience range
            if ($this->has(['experience_min', 'experience_max'])) {
                if ($this->experience_min > $this->experience_max) {
                    $validator->errors()->add('experience_max', 'Maximum experience must be greater than minimum experience.');
                }
            }

            // Validate include parameter
            if ($this->has('include')) {
                $allowedIncludes = ['company', 'skills', 'tags', 'applications', 'user', 'location', 'category', 'type'];
                $includes = explode(',', $this->include);
                foreach ($includes as $include) {
                    if (! in_array(trim($include), $allowedIncludes)) {
                        $validator->errors()->add('include', "Invalid include parameter: {$include}");
                    }
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
            'sort_direction' => $this->sort_direction ?? 'desc',
        ]);

        // Convert boolean strings
        foreach (['featured_only', 'verified_only', 'popular_only', 'remote_only'] as $field) {
            if ($this->has($field)) {
                $this->merge([
                    $field => filter_var($this->{$field}, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE),
                ]);
            }
        }
    }
}
