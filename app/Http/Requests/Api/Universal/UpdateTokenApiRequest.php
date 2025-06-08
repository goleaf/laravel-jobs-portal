<?php

namespace App\Http\Requests\Api\Universal;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\Rule;

/**
 * Universal API Request for updating Token
 * Implements Laravel 12 API best practices with Universal MCP patterns
 */
class UpdateTokenApiRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     * Universal Pattern: API authorization with abilities and resource ownership
     */
    public function authorize(): bool
    {
        $canUpdate = $this->user()?->tokenCan(strtolower('Token') . ':update') ?? false;
        $resource = $this->route(strtolower('Token'));
        
        return $canUpdate && ($resource && $this->user()?->can('update', $resource));
    }

    /**
     * Get the validation rules that apply to the request.
     * Universal Pattern: API update validation with uniqueness checks
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $id = $this->route(strtolower('Token'))?->id ?? $this->route('id');

        return [
            'name' => ['sometimes', 'string', 'max:255', Rule::unique(strtolower('Tokens'))->ignore($id)],
            'email' => ['sometimes', 'email', 'max:255', Rule::unique('users')->ignore($id)],
            'description' => ['sometimes', 'string', 'max:2000'],
            'metadata' => ['sometimes', 'array'],
            'metadata.*' => ['string', 'max:1000'],
            'tags' => ['sometimes', 'array'],
            'tags.*' => ['string', 'max:50'],
            'status' => ['sometimes', 'in:active,inactive,pending'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     * Universal Pattern: API error messages
     */
    public function messages(): array
    {
        return [
            'name.unique' => 'The name has already been taken.',
            'name.max' => 'The name may not be greater than 255 characters.',
            'email.email' => 'The email must be a valid email address.',
            'email.unique' => 'The email has already been taken.',
            'status.in' => 'The status must be one of: active, inactive, pending.',
            'tags.*.max' => 'Each tag may not be greater than 50 characters.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     * Universal Pattern: API field naming
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
     * Universal Pattern: API data normalization for updates
     */
    protected function prepareForValidation(): void
    {
        $data = [];
        
        if ($this->has('name')) {
            $data['name'] = trim($this->name);
        }
        
        if ($this->has('email')) {
            $data['email'] = $this->email ? strtolower(trim($this->email)) : null;
        }
        
        if ($this->has('status')) {
            $data['status'] = strtolower($this->status);
        }
        
        if ($this->has('tags')) {
            $data['tags'] = $this->tags ? array_map('trim', (array) $this->tags) : [];
        }
        
        $this->merge($data);
    }

    /**
     * Configure the validator instance.
     * Universal Pattern: API update validation enhancements
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            // Universal Pattern: Check for protected field updates
            if ($this->hasProtectedFieldChanges()) {
                $validator->errors()->add('protected_fields', 'You cannot modify protected fields.');
            }
        });
    }

    /**
     * Universal Pattern: Check for protected field modifications
     */
    private function hasProtectedFieldChanges(): bool
    {
        // Add logic to check for protected fields
        return false;
    }

    /**
     * Handle a failed validation attempt.
     * Universal Pattern: API error response with enhanced details
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
                    'resource_id' => $this->route('id'),
                ],
            ], 422)
        );
    }
}
