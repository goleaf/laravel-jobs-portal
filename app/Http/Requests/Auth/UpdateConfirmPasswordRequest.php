<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\Validator;

/**
 * Context7 Form Request for updating ConfirmPassword
 * Implements Laravel 12 best practices with Context7 MCP patterns
 */
class UpdateConfirmPasswordRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     * Context7 Pattern: Resource-based authorization
     */
    public function authorize(): bool
    {
        // Context7 Pattern: Check if user can update this specific resource
        return $this->user()?->can('update', $this->route(strtolower('ConfirmPassword'))) ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     * Context7 Pattern: Update-specific validation rules
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $id = $this->route(strtolower('ConfirmPassword'))?->id ?? $this->route('id');

        return [
            'name' => ['required', 'string', 'max:255', Rule::unique(strtolower('ConfirmPasswords'))->ignore($id)],
            'email' => ['nullable', 'email', 'max:255', Rule::unique('users')->ignore($id)],
            'description' => ['nullable', 'string', 'max:1000'],
            'status' => ['required', 'boolean'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     * Context7 Pattern: Multilingual error messages
     */
    public function messages(): array
    {
        return [
            'name.required' => __('validation.name_required'),
            'name.unique' => __('validation.name_unique'),
            'email.email' => __('validation.email_format'),
            'email.unique' => __('validation.email_unique'),
            'status.required' => __('validation.status_required'),
        ];
    }

    /**
     * Get custom attributes for validator errors.
     * Context7 Pattern: User-friendly field names
     */
    public function attributes(): array
    {
        return [
            'name' => __('validation.attributes.name'),
            'email' => __('validation.attributes.email'),
            'description' => __('validation.attributes.description'),
            'status' => __('validation.attributes.status'),
        ];
    }

    /**
     * Prepare the data for validation.
     * Context7 Pattern: Data normalization
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'name' => trim($this->name ?? ''),
            'email' => strtolower(trim($this->email ?? '')),
            'status' => filter_var($this->status, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? false,
        ]);
    }

    /**
     * Configure the validator instance.
     * Context7 Pattern: Enhanced validation logic
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            if ($this->hasUnauthorizedChanges()) {
                $validator->errors()->add('status', __('validation.unauthorized_status_change'));
            }
        });
    }

    /**
     * Context7 Pattern: Check for unauthorized changes
     */
    private function hasUnauthorizedChanges(): bool
    {
        // Add specific business logic for unauthorized changes
        return false;
    }

    /**
     * Handle a failed validation attempt.
     * Context7 Pattern: Enhanced error handling with audit logging
     */
    protected function failedValidation(Validator $validator): void
    {
        logger()->warning('Update validation failed for UpdateConfirmPasswordRequest', [
            'errors' => $validator->errors()->toArray(),
            'resource_id' => $this->route('id'),
            'user_id' => $this->user()?->id,
            'ip' => $this->ip(),
        ]);

        parent::failedValidation($validator);
    }
}
