<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\MasterData\MasterDataRequest;
use Illuminate\Validation\Rule;

/**
 * ⚡ **ADMIN TAXONOMY BULK ACTION REQUEST VALIDATION**
 *
 * **Purpose**: Comprehensive validation for taxonomy bulk operations with security and business rules
 * **Domain**: Admin Management - Taxonomy bulk operations
 * **Security Level**: CRITICAL - Mass data operations validation
 * **Context**: Authentication-free system with universal access
 *
 * **Key Features**:
 * - Bulk operation validation with safety limits
 * - Action-specific validation rules
 * - Security validation for mass operations
 * - Performance optimization for large datasets
 * - Comprehensive audit logging
 *
 * **Business Rules**:
 * - Maximum 100 items per bulk operation for performance
 * - Validation of selected taxonomy existence
 * - Action-specific business logic validation
 * - Prevention of destructive operations on system taxonomies
 *
 * @version 1.0.0 - Enterprise Edition
 *
 * @since 2024-12-28
 */
class TaxonomyBulkActionRequest extends MasterDataRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Authentication-free system - universal access
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'action' => ['required', 'string', Rule::in(['activate', 'deactivate', 'delete', 'export'])],
            'selected_ids' => ['required', 'array', 'min:1', 'max:100'],
            'selected_ids.*' => ['integer', 'exists:taxonomies,id'],
            'confirm_deletion' => ['required_if:action,delete', 'boolean', 'accepted'],
            'export_format' => ['required_if:action,export', 'string', Rule::in(['csv', 'excel', 'json'])],
            'reason' => ['required_if:action,delete', 'string', 'max:500'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'action.required' => __('validation.admin.taxonomy.bulk_action_required'),
            'selected_ids.required' => __('validation.admin.taxonomy.no_items_selected'),
            'selected_ids.max' => __('validation.admin.taxonomy.too_many_items_selected'),
            'confirm_deletion.required_if' => __('validation.admin.taxonomy.deletion_confirmation_required'),
            'export_format.required_if' => __('validation.admin.taxonomy.export_format_required'),
            'reason.required_if' => __('validation.admin.taxonomy.reason_required_for_deletion'),
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'action' => __('validation.admin.taxonomy.attributes.action'),
            'selected_ids' => __('validation.admin.taxonomy.attributes.selected_items'),
            'confirm_deletion' => __('validation.admin.taxonomy.attributes.deletion_confirmation'),
            'export_format' => __('validation.admin.taxonomy.attributes.export_format'),
            'reason' => __('validation.admin.taxonomy.attributes.reason'),
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Ensure selected_ids is an array
        if ($this->has('selected_ids') && ! is_array($this->selected_ids)) {
            $this->merge([
                'selected_ids' => explode(',', $this->selected_ids),
            ]);
        }

        // Set default values based on action
        if ($this->has('action')) {
            switch ($this->action) {
                case 'activate':
                    $this->merge(['new_status' => 'active']);
                    break;
                case 'deactivate':
                    $this->merge(['new_status' => 'inactive']);
                    break;
                case 'duplicate':
                    $this->merge(['duplicate_suffix' => $this->duplicate_suffix ?? 'Copy']);
                    break;
                case 'export':
                    $this->merge(['export_format' => $this->export_format ?? 'csv']);
                    break;
            }
        }

        // Clean reason text
        if ($this->has('reason')) {
            $this->merge([
                'reason' => trim($this->reason),
            ]);
        }
    }

    /**
     * Handle a passed validation attempt.
     */
    protected function passedValidation(): void
    {
        // Log bulk action for audit trail
        \Log::info('Admin Taxonomy Bulk Action Validated', [
            'action' => $this->action,
            'selected_count' => count($this->selected_ids),
            'selected_ids' => $this->selected_ids,
            'parameters' => $this->only(['new_status', 'export_format', 'duplicate_suffix', 'reason']),
            'scheduled' => $this->schedule_action ?? false,
            'scheduled_at' => $this->scheduled_at ?? null,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'timestamp' => now(),
        ]);

        // Validate business rules
        $this->validateBusinessRules();
    }

    /**
     * Validate business-specific rules.
     */
    private function validateBusinessRules(): void
    {
        // Prevent deletion of system taxonomies
        if ($this->action === 'delete' && ! $this->bypass_restrictions) {
            $systemTaxonomies = \App\Models\Taxonomy::whereIn('id', $this->selected_ids)
                ->where('is_system', true)
                ->exists();

            if ($systemTaxonomies) {
                throw new \InvalidArgumentException(__('validation.admin.taxonomy.cannot_delete_system_taxonomies'));
            }
        }

        // Validate maximum items for performance-sensitive operations
        if (in_array($this->action, ['duplicate', 'export']) && count($this->selected_ids) > 50) {
            throw new \InvalidArgumentException(__('validation.admin.taxonomy.too_many_items_for_action'));
        }

        // Validate scheduled actions
        if ($this->schedule_action && ! in_array($this->action, ['activate', 'deactivate', 'delete', 'archive'])) {
            throw new \InvalidArgumentException(__('validation.admin.taxonomy.action_cannot_be_scheduled'));
        }
    }
}
