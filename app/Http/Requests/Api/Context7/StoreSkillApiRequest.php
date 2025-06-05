<?php

namespace App\Http\Requests\Api\Context7;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

/**
 * Context7 API Request for storing Skill
 * Implements Laravel 12 API best practices with Context7 MCP patterns
 */
class StoreSkillApiRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     * Context7 Pattern: API authorization with abilities
     */
    public function authorize(): bool
    {
        return $this->user()?->tokenCan(strtolower('Skill') . ':create') ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     * Context7 Pattern: API-specific validation rules
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
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
     * Context7 Pattern: API error messages
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
     * Context7 Pattern: API field naming
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
     * Prepare the data for validation.
     * Context7 Pattern: API data normalization
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
     * Configure the validator instance.
     * Context7 Pattern: API validation enhancements
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            // Context7 Pattern: Rate limiting check
            if ($this->exceedsCreationLimit()) {
                $validator->errors()->add('rate_limit', 'You have exceeded the creation rate limit.');
            }
        });
    }

    /**
     * Context7 Pattern: Check creation rate limits
     */
    private function exceedsCreationLimit(): bool
    {
        // Implement rate limiting logic
        return false;
    }

    /**
     * Handle a failed validation attempt.
     * Context7 Pattern: API error response
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
}
