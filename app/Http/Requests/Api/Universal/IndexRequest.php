<?php

namespace App\Http\Requests\Api\Universal;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class IndexRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Authorization handled by middleware
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'page' => 'sometimes|integer|min:1',
            'per_page' => 'sometimes|integer|min:1|max:100',
            'search' => 'sometimes|string|max:255',
            'sort' => 'sometimes|string|max:50',
            'order' => 'sometimes|string|in:asc,desc',
            'filter' => 'sometimes|array',
            'filter.*' => 'sometimes|string|max:255',
            'status' => 'sometimes|boolean',
            'is_active' => 'sometimes|boolean',
        ];
    }

    /**
     * Get custom validation messages.
     */
    public function messages(): array
    {
        return [
            'page.integer' => 'The page must be a valid integer.',
            'page.min' => 'The page must be at least 1.',
            'per_page.integer' => 'The per_page must be a valid integer.',
            'per_page.min' => 'The per_page must be at least 1.',
            'per_page.max' => 'The per_page must not exceed 100.',
            'search.string' => 'The search term must be a valid string.',
            'search.max' => 'The search term must not exceed 255 characters.',
            'sort.string' => 'The sort field must be a valid string.',
            'sort.max' => 'The sort field must not exceed 50 characters.',
            'order.string' => 'The order must be a valid string.',
            'order.in' => 'The order must be either asc or desc.',
            'filter.array' => 'The filter must be an array.',
            'filter.*.string' => 'Each filter value must be a valid string.',
            'filter.*.max' => 'Each filter value must not exceed 255 characters.',
            'status.boolean' => 'The status must be true or false.',
            'is_active.boolean' => 'The is_active field must be true or false.',
        ];
    }

    /**
     * Get custom attribute names for validation errors.
     */
    public function attributes(): array
    {
        return [
            'page' => 'page number',
            'per_page' => 'items per page',
            'search' => 'search term',
            'sort' => 'sort field',
            'order' => 'sort order',
            'filter' => 'filters',
            'status' => 'status',
            'is_active' => 'active status',
        ];
    }

    /**
     * Handle a failed validation attempt for API requests.
     */
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json([
                'success' => false,
                'message' => 'The given data was invalid.',
                'errors' => $validator->errors(),
            ], 422)
        );
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Set default pagination values
        $this->merge([
            'page' => $this->input('page', 1),
            'per_page' => $this->input('per_page', 15),
            'order' => $this->input('order', 'desc'),
            'sort' => $this->input('sort', 'created_at'),
        ]);

        // Convert string booleans to actual booleans
        if ($this->has('status')) {
            $this->merge([
                'status' => filter_var($this->status, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE),
            ]);
        }

        if ($this->has('is_active')) {
            $this->merge([
                'is_active' => filter_var($this->is_active, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE),
            ]);
        }

        // Clean search term
        if ($this->has('search')) {
            $this->merge([
                'search' => trim($this->search),
            ]);
        }
    }

    /**
     * Get the pagination parameters.
     */
    public function getPaginationParams(): array
    {
        return [
            'page' => $this->input('page', 1),
            'per_page' => min($this->input('per_page', 15), 100), // Cap at 100
        ];
    }

    /**
     * Get the sorting parameters.
     */
    public function getSortingParams(): array
    {
        return [
            'sort' => $this->input('sort', 'created_at'),
            'order' => $this->input('order', 'desc'),
        ];
    }

    /**
     * Get the filter parameters.
     */
    public function getFilterParams(): array
    {
        $filters = [];

        if ($this->filled('search')) {
            $filters['search'] = $this->input('search');
        }

        if ($this->has('status')) {
            $filters['status'] = $this->input('status');
        }

        if ($this->has('is_active')) {
            $filters['is_active'] = $this->input('is_active');
        }

        if ($this->filled('filter')) {
            $filters = array_merge($filters, $this->input('filter', []));
        }

        return $filters;
    }
} 