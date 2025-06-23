<?php

namespace App\Http\Requests\Api\Universal;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

/**
 * Universal API Request for storing JobApplication
 * Implements Laravel 12 API best practices with Universal MCP patterns.
 */
class StoreJobApplicationApiRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     * Universal Pattern: API authorization with abilities.
     */
    public function authorize(): bool
    {
        return $this->user()?->tokenCan(strtolower('JobApplication').':create') ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     * Universal Pattern: API-specific validation rules.
     *
     * @return array<string, array<mixed>|string|ValidationRule>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'metadata' => ['nullable', 'array'],
            'metadata.*' => ['string', 'max:1000'],
            'tags' => ['nullable', 'array'],
            'tags.*' => ['string', 'max:50'],
            'status' => ['required', 'in:active,inactive,pending'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     * Universal Pattern: API error messages.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'The name field is required.',
            'name.max' => 'The name may not be greater than 255 characters.',
            'email.email' => 'The email must be a valid email address.',
            'status.required' => 'The status field is required.',
            'status.in' => 'The status must be one of: active, inactive, pending.',
            'tags.*.max' => 'Each tag may not be greater than 50 characters.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     * Universal Pattern: API field naming.
     */
    public function attributes(): array
    {
        return [
            'name' => 'name',
            'email' => 'email address',
            'description' => 'description',
            'status' => 'status',
            'metadata' => 'metadata',
            'tags' => 'tags',
        ];
    }

    /**
     * Configure the validator instance.
     * Universal Pattern: API validation enhancements.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            // Universal Pattern: Rate limiting check
            if ($this->exceedsCreationLimit()) {
                $validator->errors()->add('rate_limit', 'You have exceeded the creation rate limit.');
            }
        });
    }

    /**
     * Prepare the data for validation.
     * Universal Pattern: API data normalization.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'name' => trim($this->name ?? ''),
            'email' => $this->email ? strtolower(trim($this->email)) : null,
            'status' => strtolower($this->status ?? 'pending'),
            'tags' => $this->tags ? array_map('trim', (array) $this->tags) : [],
        ]);
    }

    /**
     * Handle a failed validation attempt.
     * Universal Pattern: API error response.
     */
    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(
            response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()->toArray(),
                'meta' => [
                    'timestamp' => now()->toISOString(),
                    'request_id' => request()->header('X-Request-ID', str()->uuid()),
                ],
            ], 422)
        );
    }

    /**
     * Universal Pattern: Check creation rate limits.
     */
    private function exceedsCreationLimit(): bool
    {
        // Implement rate limiting logic
        return false;
    }
}
