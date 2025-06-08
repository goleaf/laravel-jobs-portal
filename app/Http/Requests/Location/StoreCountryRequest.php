<?php

namespace App\Http\Requests\Location;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\Validator;

/**
 * Context7 Enhanced Form Request for Store Country
 * Implements Laravel 12 best practices with Context7 MCP patterns
 * Following proven MasterData pattern
 */
class StoreCountryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Context7 Pattern: Role-based authorization
        if (!auth()->check()) {
            return false;
        }
        
        $user = auth()->user();
        return $user && (
            $user->hasRole('Admin') || 
            $user->hasRole('Employer')
        );
    }

    /**
     * Get the validation rules that apply to the request.
     * Context7 Pattern: Comprehensive country validation with security
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'code' => ['nullable', 'string', 'max:255'],
            'phone_code' => ['nullable', 'string', 'max:255'],
            'is_active' => ['boolean'],
            // Security
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
            'name.required' => __('validation.country_name_required'),
            'name.max' => __('validation.country_name_max'),
            'code.max' => __('validation.country_code_max'),
            'phone_code.max' => __('validation.country_phone_code_max'),
        ];
    }

    /**
     * Get custom attributes for validator errors.
     * Context7 Pattern: User-friendly field names
     */
    public function attributes(): array
    {
        return [
            'name' => __('validation.attributes.country_name'),
            'code' => __('validation.attributes.country_code'),
            'phone_code' => __('validation.attributes.country_phone_code'),
            'is_active' => __('validation.attributes.country_is_active'),
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
                $validator->errors()->add('name', __('validation.country_conflict'));
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
        // Add specific Country business logic here
        return false;
    }

    /**
     * Context7 Pattern: Content security validation
     */
    private function hasSuspiciousContent(): bool
    {
        $suspiciousPatterns = ['spam', 'scam', 'virus', 'malware'];
        $content = strtolower($this->name ?? '');
        
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
        logger()->warning('Context7 validation failed for StoreCountryRequest', [
            'errors' => $validator->errors()->toArray(),
            'controller' => 'Country',
            'action' => 'Store',
            'user_id' => $this->user()?->id,
            'ip' => $this->ip(),
            'user_agent' => $this->userAgent(),
        ]);

        parent::failedValidation($validator);
    }
} 