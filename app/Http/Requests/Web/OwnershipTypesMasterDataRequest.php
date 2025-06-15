<?php

namespace App\Http\Requests\Web;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Enhanced Enhanced Form Request for MasterData ownershipTypes
 * Implements Laravel 12 best practices with Enhanced MCP patterns
 * Auto-generated for Level 4 Complex System Transformation.
 */
class OwnershipTypesMasterDataRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Enhanced Pattern: Enhanced authorization with null checks
        if (!auth()->check()) {
            return false;
        }

        $user = auth()->user();

        return $user && (
            $user->hasRole('Admin')
            || $user->hasRole('Employer')
            || $user->hasRole('Candidate')
        );
    }

    /**
     * Get the validation rules that apply to the request.
     * Enhanced Pattern: Comprehensive validation with security.
     */
    public function rules(): array
    {
        return [
            // Add specific validation rules based on method
            'name' => ['sometimes', 'string', 'max:255'],
            'email' => ['sometimes', 'email', 'max:255'],
            'description' => ['sometimes', 'string', 'max:1000'],
            'is_active' => ['sometimes', 'boolean'],

            // Security validation
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
     * Enhanced Pattern: Multilingual error messages.
     */
    public function messages(): array
    {
        return [
            'name.required' => __('validation.name_required'),
            'name.max' => __('validation.name_max'),
            'email.email' => __('validation.email_invalid'),
            'email.max' => __('validation.email_max'),
            'description.max' => __('validation.description_max'),
        ];
    }

    /**
     * Get custom attributes for validator errors.
     * Enhanced Pattern: User-friendly field names.
     */
    public function attributes(): array
    {
        return [
            'name' => __('validation.attributes.name'),
            'email' => __('validation.attributes.email'),
            'description' => __('validation.attributes.description'),
            'is_active' => __('validation.attributes.is_active'),
        ];
    }

    /**
     * Configure the validator instance.
     * Enhanced Pattern: Enhanced validation logic.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            if ($this->hasEnhancedValidationConflicts()) {
                $validator->errors()->add('name', __('validation.conflict_detected'));
            }

            if ($this->hasSuspiciousContent()) {
                $validator->errors()->add('name', __('validation.suspicious_content'));
            }
        });
    }

    /**
     * Prepare the data for validation.
     * Enhanced Pattern: Data normalization.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'name' => trim($this->name ?? ''),
            'email' => strtolower(trim($this->email ?? '')),
            'is_active' => filter_var($this->is_active, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? true,
        ]);
    }

    /**
     * Handle a failed validation attempt.
     * Enhanced Pattern: Enhanced error handling with security monitoring.
     */
    protected function failedValidation(Validator $validator): void
    {
        logger()->warning('Enhanced validation failed for OwnershipTypesMasterDataRequest', [
            'errors' => $validator->errors()->toArray(),
            'controller' => 'MasterDataController',
            'method' => 'ownershipTypes',
            'user_id' => $this->user()?->id,
            'ip' => $this->ip(),
            'user_agent' => $this->userAgent(),
        ]);

        parent::failedValidation($validator);
    }

    /**
     * Enhanced Pattern: Enhanced business logic validation.
     */
    private function hasEnhancedValidationConflicts(): bool
    {
        // Add specific business logic validation here
        return false;
    }

    /**
     * Enhanced Pattern: Content security validation.
     */
    private function hasSuspiciousContent(): bool
    {
        $suspiciousPatterns = ['spam', 'scam', 'virus', 'malware', 'hack', 'exploit'];
        $content = strtolower(($this->name ?? '').' '.($this->description ?? ''));

        foreach ($suspiciousPatterns as $pattern) {
            if (false !== strpos($content, $pattern)) {
                return true;
            }
        }

        return false;
    }
}
