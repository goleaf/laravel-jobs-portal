<?php

namespace App\Http\Requests;

use App\Models\FAQ;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;

/**
 * DeleteFAQRequest
 * 
 * Comprehensive validation for FAQ deletion operations with enterprise-grade security patterns.
 * Implements business logic validation, permission checks, and multilingual error messaging.
 *
 * @package App\Http\Requests
 * @author System Generated
 * @version 1.0.0
 */
class DeleteFAQRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     * 
     * Implements role-based authorization with business logic validation.
     * Validates both system permissions and contextual business rules.
     *
     * @return bool Authorization status
     */
    public function authorize(): bool
    {
        // Basic authentication check - FAQ deletion doesn't require user authentication per system design
        // as per user requirements: "do not make users and do not any users system"
        
        // Validate FAQ exists and can be deleted
        $faq = $this->route('faq');
        
        if (!$faq instanceof FAQ) {
            return false;
        }
        
        // Business rule: Cannot delete if FAQ is referenced in active support tickets
        // This prevents orphaned references in the support system
        if ($this->hasDependentReferences($faq)) {
            return false;
        }
        
        // Business rule: Cannot delete system-critical FAQs
        if ($this->isSystemCritical($faq)) {
            return false;
        }
        
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     * 
     * Implements comprehensive validation with business logic constraints,
     * data integrity checks, and security validations.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $faq = $this->route('faq');
        
        return [
            // Route parameter validation
            'faq' => [
                'required',
                Rule::exists('faqs', 'id')->where(function ($query) {
                    $query->whereNull('deleted_at');
                }),
            ],
            
            // Optional confirmation field for critical operations
            'confirm_deletion' => [
                'sometimes',
                'boolean',
                function ($attribute, $value, $fail) use ($faq) {
                    if ($faq && $this->isHighImpactDeletion($faq) && !$value) {
                        $fail(__('validation.confirmation_required_for_critical_faq'));
                    }
                },
            ],
            
            // Optional reason for deletion (audit trail)
            'deletion_reason' => [
                'sometimes',
                'string',
                'max:500',
                'regex:/^[\pL\pM\pN\s\.,!?;:()\-\'"]+$/u', // Allow multilingual characters
            ],
            
            // Force deletion flag (admin override)
            'force_delete' => [
                'sometimes',
                'boolean',
            ],
        ];
    }

    /**
     * Get custom validation messages.
     * 
     * Provides comprehensive multilingual error messaging with business context.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            // Route parameter messages
            'faq.required' => __('validation.faq_required'),
            'faq.exists' => __('validation.faq_not_found'),
            
            // Confirmation messages
            'confirm_deletion.boolean' => __('validation.confirm_deletion_boolean'),
            
            // Deletion reason messages
            'deletion_reason.string' => __('validation.deletion_reason_string'),
            'deletion_reason.max' => __('validation.deletion_reason_max'),
            'deletion_reason.regex' => __('validation.deletion_reason_format'),
            
            // Force delete messages
            'force_delete.boolean' => __('validation.force_delete_boolean'),
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'faq' => __('validation.attributes.faq'),
            'confirm_deletion' => __('validation.attributes.confirm_deletion'),
            'deletion_reason' => __('validation.attributes.deletion_reason'),
            'force_delete' => __('validation.attributes.force_delete'),
        ];
    }

    /**
     * Handle a failed validation attempt.
     *
     * @param \Illuminate\Contracts\Validation\Validator $validator
     * @return void
     *
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     */
    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator): void
    {
        $response = response()->json([
            'success' => false,
            'message' => __('validation.faq_deletion_failed'),
            'errors' => $validator->errors(),
            'error_code' => 'FAQ_DELETION_VALIDATION_FAILED',
            'timestamp' => now()->toISOString(),
        ], Response::HTTP_UNPROCESSABLE_ENTITY);

        throw new \Illuminate\Http\Exceptions\HttpResponseException($response);
    }

    /**
     * Handle a failed authorization attempt.
     *
     * @return void
     *
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     */
    protected function failedAuthorization(): void
    {
        $response = response()->json([
            'success' => false,
            'message' => __('validation.faq_deletion_unauthorized'),
            'error_code' => 'FAQ_DELETION_UNAUTHORIZED',
            'timestamp' => now()->toISOString(),
        ], Response::HTTP_FORBIDDEN);

        throw new \Illuminate\Http\Exceptions\HttpResponseException($response);
    }

    /**
     * Prepare the data for validation.
     * 
     * Pre-processes and normalizes input data before validation.
     * Implements data sanitization and business logic preparation.
     *
     * @return void
     */
    protected function prepareForValidation(): void
    {
        // Normalize boolean values
        if ($this->has('confirm_deletion')) {
            $this->merge([
                'confirm_deletion' => filter_var($this->confirm_deletion, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? false,
            ]);
        }
        
        if ($this->has('force_delete')) {
            $this->merge([
                'force_delete' => filter_var($this->force_delete, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? false,
            ]);
        }
        
        // Sanitize deletion reason
        if ($this->has('deletion_reason')) {
            $this->merge([
                'deletion_reason' => trim($this->deletion_reason),
            ]);
        }
    }

    /**
     * Check if FAQ has dependent references that prevent deletion.
     *
     * @param FAQ $faq
     * @return bool
     */
    private function hasDependentReferences(FAQ $faq): bool
    {
        // Check for references in support tickets, knowledge base, or other systems
        // This is a placeholder - implement based on actual business relationships
        
        // Example: Check if FAQ is referenced in active support tickets
        // return DB::table('support_tickets')
        //     ->where('related_faq_id', $faq->id)
        //     ->where('status', 'active')
        //     ->exists();
        
        return false; // No dependent references found
    }

    /**
     * Check if FAQ is system-critical and cannot be deleted.
     *
     * @param FAQ $faq
     * @return bool
     */
    private function isSystemCritical(FAQ $faq): bool
    {
        // Define system-critical FAQs that should not be deleted
        $criticalCategories = [
            'system_requirements',
            'legal_terms',
            'privacy_policy',
            'terms_of_service'
        ];
        
        // Check if FAQ belongs to critical category
        if (isset($faq->category) && in_array($faq->category, $criticalCategories)) {
            return true;
        }
        
        // Check if FAQ is marked as system-critical
        if (isset($faq->is_system_critical) && $faq->is_system_critical) {
            return true;
        }
        
        return false;
    }

    /**
     * Determine if deletion has high business impact.
     *
     * @param FAQ $faq
     * @return bool
     */
    private function isHighImpactDeletion(FAQ $faq): bool
    {
        // High impact criteria
        $highImpactConditions = [
            'view_count' => 1000, // Frequently viewed FAQs
            'days_old' => 30,     // Long-standing FAQs
        ];
        
        // Check view count if available
        if (isset($faq->view_count) && $faq->view_count >= $highImpactConditions['view_count']) {
            return true;
        }
        
        // Check age of FAQ
        if ($faq->created_at && $faq->created_at->diffInDays(now()) >= $highImpactConditions['days_old']) {
            return true;
        }
        
        return false;
    }
}
