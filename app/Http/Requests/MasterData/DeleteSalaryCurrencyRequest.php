<?php

namespace App\Http\Requests\MasterData;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Universal Form Request for deleting SalaryCurrency
 * Implements Laravel 12 best practices with Universal MCP patterns.
 */
class DeleteSalaryCurrencyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     * Universal Pattern: Resource-based authorization.
     */
    public function authorize(): bool
    {
        $resource = $this->route(strtolower('SalaryCurrency'));

        return $this->user()?->can('delete', $resource) ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     * Universal Pattern: Delete-specific validation rules.
     *
     * @return array<string, array<mixed>|string|ValidationRule>
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
     * Universal Pattern: Delete operation messages.
     */
    public function messages(): array
    {
        return [
            'reason.max' => __('validation.reason_max'),
        ];
    }

    /**
     * Get custom attributes for validator errors.
     * Universal Pattern: User-friendly field names.
     */
    public function attributes(): array
    {
        return [
            'force_delete' => __('validation.attributes.force_delete'),
            'reason' => __('validation.attributes.reason'),
        ];
    }

    /**
     * Configure the validator instance.
     * Universal Pattern: Delete validation enhancements.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            // Universal Pattern: Check for dependencies before delete
            if ($this->hasActiveDependencies()) {
                $validator->errors()->add('dependencies', __('validation.has_active_dependencies'));
            }

            // Universal Pattern: Check for protected resources
            if ($this->isProtectedResource()) {
                $validator->errors()->add('protected', __('validation.protected_resource'));
            }
        });
    }

    /**
     * Prepare the data for validation.
     * Universal Pattern: Data normalization for delete.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'force_delete' => filter_var($this->force_delete, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? false,
            'reason' => trim($this->reason ?? '') ?: null,
        ]);
    }

    /**
     * Handle a failed validation attempt.
     * Universal Pattern: Enhanced error handling for delete operations.
     */
    protected function failedValidation(Validator $validator): void
    {
        logger()->warning('Delete validation failed for DeleteSalaryCurrencyRequest', [
            'errors' => $validator->errors()->toArray(),
            'resource_id' => $this->route('id'),
            'user_id' => $this->user()?->id,
            'ip' => $this->ip(),
            'force_delete' => $this->force_delete,
        ]);

        parent::failedValidation($validator);
    }

    /**
     * Universal Pattern: Check for active dependencies.
     */
    private function hasActiveDependencies(): bool
    {
        $resource = $this->route(strtolower('SalaryCurrency'));

        // Add specific dependency checks here
        // Example: return $resource->relatedItems()->exists();

        return false;
    }

    /**
     * Universal Pattern: Check if resource is protected from deletion.
     */
    private function isProtectedResource(): bool
    {
        $resource = $this->route(strtolower('SalaryCurrency'));

        // Add protection logic here
        // Example: return $resource->is_system_default;

        return false;
    }
}
