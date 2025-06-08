<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\Validator;

/**
 * Context7 Enhanced Form Request for CompanyApi show
 * Implements Laravel 12 best practices with Context7 MCP patterns
 * Auto-generated for Level 4 Complex System Transformation
 */
class ShowCompanyApiRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Context7 Pattern: Enhanced authorization with null checks
        if (!auth()->check()) {
            return false;
        }
        
        $user = auth()->user();
        return $user && (
            $user->hasRole('Admin') || 
            $user->hasRole('Employer') ||
            $user->hasRole('Candidate')
        );
    }

    /**
     * Get the validation rules that apply to the request.
     * Context7 Pattern: Comprehensive validation with security
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
     * Context7 Pattern: Multilingual error messages
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
     * Context7 Pattern: User-friendly field names
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
     * Prepare the data for validation.
     * Context7 Pattern: Data normalization
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
     * Configure the validator instance.
     * Context7 Pattern: Enhanced validation logic
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            if ($this->hasContext7ValidationConflicts()) {
                $validator->errors()->add('name', __('validation.conflict_detected'));
            }
            
            if ($this->hasSuspiciousContent()) {
                $validator->errors()->add('name', __('validation.suspicious_content'));
            }
        });
    }

    /**
     * Context7 Pattern: Enhanced business logic validation
     */
    private function hasContext7ValidationConflicts(): bool
    {
        // Add specific business logic validation here
        return false;
    }

    /**
     * Context7 Pattern: Content security validation
     */
    private function hasSuspiciousContent(): bool
    {
        $suspiciousPatterns = ['spam', 'scam', 'virus', 'malware', 'hack', 'exploit'];
        $content = strtolower(($this->name ?? '') . ' ' . ($this->description ?? ''));
        
        foreach ($suspiciousPatterns as $pattern) {
            if (strpos($content, $pattern) !== false) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Handle a failed validation attempt.
     * Context7 Pattern: Enhanced error handling with security monitoring
     */
    protected function failedValidation(Validator $validator): void
    {
        logger()->warning('Context7 validation failed for ShowCompanyApiRequest', [
            'errors' => $validator->errors()->toArray(),
            'controller' => 'CompanyApiController',
            'method' => 'show',
            'user_id' => $this->user()?->id,
            'ip' => $this->ip(),
            'user_agent' => $this->userAgent(),
        ]);

        parent::failedValidation($validator);
    }
}