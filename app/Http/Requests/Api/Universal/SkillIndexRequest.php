<?php

namespace App\Http\Requests\Api\Universal;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class SkillIndexRequest extends FormRequest
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
            'sort_by' => 'sometimes|string|in:id,name,created_at,updated_at,usage_count',
            'sort_direction' => 'sometimes|string|in:asc,desc',
            'category' => 'sometimes|string|max:100',
            'level' => 'sometimes|string|in:beginner,intermediate,advanced,expert',
            'popular_only' => 'sometimes|boolean',
            'verified_only' => 'sometimes|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'search.max' => 'Search term cannot exceed 255 characters.',
            'per_page.max' => 'Items per page cannot exceed 100.',
            'sort_by.in' => 'Sort field must be one of: id, name, created_at, updated_at, usage_count.',
            'level.in' => 'Skill level must be one of: beginner, intermediate, advanced, expert.',
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
            'per_page' => $this->per_page ?? 50,
            'sort_by' => $this->sort_by ?? 'name',
            'sort_direction' => $this->sort_direction ?? 'asc',
        ]);
    }
} 