<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;
use Illuminate\Contracts\Validation\Validator;

/**
 * Context7 Form Request for storing Health
 * Implements Laravel 12 best practices with Context7 MCP patterns
 */
class StoreHealthRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     * Context7 Pattern: Simple authorization check
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     * Context7 Pattern: Comprehensive validation with security
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'status' => ['required', 'boolean'],
            'g-recaptcha-response' => [
                'nullable',
                function ($attribute, $value, $fail) {
                    if (config('app.recaptcha_enabled', false) && empty($value)) {
                        $fail(__('validation.recaptcha_required'));
                    }
                },
            ],
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
            'name.max' => __('validation.name_max'),
            'email.email' => __('validation.email_format'),
            'status.required' => __('validation.status_required'),
            'status.boolean' => __('validation.status_boolean'),
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
     * Context7 Pattern: Performance optimization
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            // Context7 Pattern: Additional business logic validation
            if ($this->hasConflictingData()) {
                $validator->errors()->add('name', __('validation.conflicting_data'));
            }
        });
    }

    /**
     * Context7 Pattern: Custom business logic check
     */
    private function hasConflictingData(): bool
    {
        // Add specific business logic here
        return false;
    }

    /**
     * Handle a failed validation attempt.
     * Context7 Pattern: Enhanced error handling
     */
    protected function failedValidation(Validator $validator): void
    {
        // Context7 Pattern: Log validation failures for security monitoring
        logger()->warning('Validation failed for StoreHealthRequest', [
            'errors' => $validator->errors()->toArray(),
            'input' => $this->safe()->toArray(),
            'ip' => $this->ip(),
            'user_agent' => $this->userAgent(),
        ]);

        parent::failedValidation($validator);
    }
}
