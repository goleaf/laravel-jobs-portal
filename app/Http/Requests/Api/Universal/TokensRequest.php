<?php

namespace App\Http\Requests\Api\Universal;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class TokensRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // User must be authenticated to view their tokens
        return $this->user() !== null;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'active_only' => 'sometimes|boolean',
            'sort_by' => 'sometimes|string|in:created_at,last_used_at,name',
            'sort_direction' => 'sometimes|string|in:asc,desc',
            'limit' => 'sometimes|integer|min:1|max:100',
        ];
    }

    /**
     * Get custom validation messages.
     */
    public function messages(): array
    {
        return [
            'active_only.boolean' => __('validation.boolean', ['attribute' => __('validation.attributes.active_only')]),
            'sort_by.in' => __('validation.in', ['attribute' => __('validation.attributes.sort_by')]),
            'sort_direction.in' => __('validation.in', ['attribute' => __('validation.attributes.sort_direction')]),
            'limit.integer' => __('validation.integer', ['attribute' => __('validation.attributes.limit')]),
            'limit.min' => __('validation.min.numeric', ['attribute' => __('validation.attributes.limit'), 'min' => 1]),
            'limit.max' => __('validation.max.numeric', ['attribute' => __('validation.attributes.limit'), 'max' => 100]),
        ];
    }

    /**
     * Get custom attribute names for validation errors.
     */
    public function attributes(): array
    {
        return [
            'active_only' => __('validation.attributes.active_only'),
            'sort_by' => __('validation.attributes.sort_by'),
            'sort_direction' => __('validation.attributes.sort_direction'),
            'limit' => __('validation.attributes.limit'),
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            // Additional validation can be added here if needed
        });
    }

    /**
     * Handle a failed validation attempt for API requests.
     */
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json([
                'success' => false,
                'message' => __('validation.failed'),
                'errors' => $validator->errors(),
            ], 422)
        );
    }

    /**
     * Handle a failed authorization attempt.
     */
    protected function failedAuthorization()
    {
        throw new HttpResponseException(
            response()->json([
                'success' => false,
                'message' => __('auth.unauthenticated'),
                'errors' => ['user' => [__('auth.unauthenticated')]],
            ], 401)
        );
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Set defaults
        $this->merge([
            'active_only' => $this->boolean('active_only', false),
            'sort_by' => $this->input('sort_by', 'created_at'),
            'sort_direction' => $this->input('sort_direction', 'desc'),
            'limit' => $this->integer('limit', 20),
        ]);
    }
}
