<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;

/**
 * Context7 Form Request for deleting FunctionalArea
 * Implements Laravel 12 best practices with Context7 MCP patterns
 */
class DeleteFunctionalAreaRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     * Context7 Pattern: Resource-based authorization
     */
    public function authorize(): bool
    {
        $resource = $this->route(strtolower('FunctionalArea'));
        return $this->user()?->can('delete', $resource) ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     * Context7 Pattern: Delete-specific validation rules
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'force_delete' => ['nullable', 'boolean'],
            'reason' => ['nullable', 'string', 'max:500'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     * Context7 Pattern: Delete operation messages
     */
    public function messages(): array
    {
        return [
            'reason.max' => __('validation.reason_max'),
        ];
    }

    /**
     * Get custom attributes for validator errors.
     * Context7 Pattern: User-friendly field names
     */
    public function attributes(): array
    {
        return [
            'force_delete' => __('validation.attributes.force_delete'),
            'reason' => __('validation.attributes.reason'),
        ];
    }

    /**
     * Prepare the data for validation.
     * Context7 Pattern: Data normalization for delete
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'force_delete' => filter_var($this->force_delete, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? false,
            'reason' => trim($this->reason ?? '') ?: null,
        ]);
    }

    /**
     * Configure the validator instance.
     * Context7 Pattern: Delete validation enhancements
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            // Context7 Pattern: Check for dependencies before delete
            if ($this->hasActiveDependencies()) {
                $validator->errors()->add('dependencies', __('validation.has_active_dependencies'));
            }

            // Context7 Pattern: Check for protected resources
            if ($this->isProtectedResource()) {
                $validator->errors()->add('protected', __('validation.protected_resource'));
            }
        });
    }

    /**
     * Context7 Pattern: Check for active dependencies
     */
    private function hasActiveDependencies(): bool
    {
        $resource = $this->route(strtolower('FunctionalArea'));
        
        // Add specific dependency checks here
        // Example: return $resource->relatedItems()->exists();
        
        return false;
    }

    /**
     * Context7 Pattern: Check if resource is protected from deletion
     */
    private function isProtectedResource(): bool
    {
        $resource = $this->route(strtolower('FunctionalArea'));
        
        // Add protection logic here
        // Example: return $resource->is_system_default;
        
        return false;
    }

    /**
     * Handle a failed validation attempt.
     * Context7 Pattern: Enhanced error handling for delete operations
     */
    protected function failedValidation(Validator $validator): void
    {
        logger()->warning('Delete validation failed for DeleteFunctionalAreaRequest', [
            'errors' => $validator->errors()->toArray(),
            'resource_id' => $this->route('id'),
            'user_id' => $this->user()?->id,
            'ip' => $this->ip(),
            'force_delete' => $this->force_delete,
        ]);

        parent::failedValidation($validator);
    }
}
