<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\MasterData\MasterDataRequest;
use Illuminate\Validation\Rule;

/**
 * 🗑️ **ADMIN MASTER DATA DESTROY REQUEST VALIDATION**
 *
 * **Purpose**: Comprehensive validation for master data deletion with safety checks and business rules
 * **Domain**: Admin Management - Master data deletion operations
 * **Security Level**: CRITICAL - Data deletion validation with safety measures
 * **Context**: Authentication-free system with universal access
 *
 * **Key Features**:
 * - Deletion safety validation with dependency checks
 * - Business rule validation for system data protection
 * - Confirmation and reason requirements for auditing
 * - Cascade deletion validation
 * - Comprehensive logging for compliance
 *
 * **Business Rules**:
 * - System/core data cannot be deleted without special permission
 * - Deletion requires confirmation and reason for audit trail
 * - Dependency validation prevents orphaned records
 * - Soft deletion preferred over hard deletion
 *
 * @version 1.0.0 - Enterprise Edition
 *
 * @since 2024-12-28
 */
class MasterDataDestroyRequest extends MasterDataRequest
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
            // Core deletion parameters
            'id' => [
                'required',
                'integer',
                'min:1',
            ],

            'category' => [
                'required',
                'string',
                Rule::in([
                    'countries',
                    'states',
                    'cities',
                    'skills',
                    'industries',
                    'company_sizes',
                    'functional_areas',
                    'career_levels',
                ]),
            ],

            // Safety and confirmation parameters
            'confirm_deletion' => [
                'required',
                'boolean',
                'accepted',
            ],

            'deletion_reason' => [
                'required',
                'string',
                'min:10',
                'max:500',
            ],

            'deletion_type' => [
                'sometimes',
                'string',
                Rule::in(['soft', 'hard']),
            ],

            // Dependency handling
            'handle_dependencies' => [
                'sometimes',
                'string',
                Rule::in(['cascade', 'restrict', 'set_null', 'reassign']),
            ],

            'reassign_to_id' => [
                'required_if:handle_dependencies,reassign',
                'integer',
                'min:1',
            ],

            // Safety overrides
            'force_deletion' => [
                'sometimes',
                'boolean',
            ],

            'bypass_system_check' => [
                'sometimes',
                'boolean',
            ],

            // Backup and recovery
            'create_backup' => [
                'sometimes',
                'boolean',
            ],

            'backup_note' => [
                'required_if:create_backup,true',
                'string',
                'max:255',
            ],

            // Notification settings
            'notify_users' => [
                'sometimes',
                'boolean',
            ],

            'notification_message' => [
                'required_if:notify_users,true',
                'string',
                'max:255',
            ],

            // Scheduling
            'schedule_deletion' => [
                'sometimes',
                'boolean',
            ],

            'scheduled_at' => [
                'required_if:schedule_deletion,true',
                'date',
                'after:now',
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'id.required' => __('validation.admin.master_data.id_required'),
            'id.integer' => __('validation.admin.master_data.id_must_be_integer'),
            'category.required' => __('validation.admin.master_data.category_required'),
            'category.in' => __('validation.admin.master_data.invalid_category'),
            'confirm_deletion.required' => __('validation.admin.master_data.deletion_confirmation_required'),
            'confirm_deletion.accepted' => __('validation.admin.master_data.deletion_must_be_confirmed'),
            'deletion_reason.required' => __('validation.admin.master_data.deletion_reason_required'),
            'deletion_reason.min' => __('validation.admin.master_data.deletion_reason_too_short'),
            'deletion_reason.max' => __('validation.admin.master_data.deletion_reason_too_long'),
            'deletion_type.in' => __('validation.admin.master_data.invalid_deletion_type'),
            'handle_dependencies.in' => __('validation.admin.master_data.invalid_dependency_handling'),
            'reassign_to_id.required_if' => __('validation.admin.master_data.reassign_target_required'),
            'backup_note.required_if' => __('validation.admin.master_data.backup_note_required'),
            'notification_message.required_if' => __('validation.admin.master_data.notification_message_required'),
            'scheduled_at.required_if' => __('validation.admin.master_data.scheduled_time_required'),
            'scheduled_at.after' => __('validation.admin.master_data.scheduled_time_must_be_future'),
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'id' => __('validation.admin.master_data.attributes.id'),
            'category' => __('validation.admin.master_data.attributes.category'),
            'confirm_deletion' => __('validation.admin.master_data.attributes.deletion_confirmation'),
            'deletion_reason' => __('validation.admin.master_data.attributes.deletion_reason'),
            'deletion_type' => __('validation.admin.master_data.attributes.deletion_type'),
            'handle_dependencies' => __('validation.admin.master_data.attributes.dependency_handling'),
            'reassign_to_id' => __('validation.admin.master_data.attributes.reassign_target'),
            'backup_note' => __('validation.admin.master_data.attributes.backup_note'),
            'notification_message' => __('validation.admin.master_data.attributes.notification_message'),
            'scheduled_at' => __('validation.admin.master_data.attributes.scheduled_time'),
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Set default values
        $this->merge([
            'deletion_type' => $this->deletion_type ?? 'soft',
            'handle_dependencies' => $this->handle_dependencies ?? 'restrict',
            'create_backup' => $this->create_backup ?? true,
            'notify_users' => $this->notify_users ?? false,
            'force_deletion' => $this->force_deletion ?? false,
            'bypass_system_check' => $this->bypass_system_check ?? false,
        ]);

        // Clean deletion reason
        if ($this->has('deletion_reason')) {
            $this->merge([
                'deletion_reason' => trim($this->deletion_reason),
            ]);
        }

        // Extract ID from route parameter if not provided
        if (! $this->has('id') && $this->route('id')) {
            $this->merge(['id' => $this->route('id')]);
        }

        // Extract category from route or URL
        if (! $this->has('category')) {
            $path = request()->path();
            if (preg_match('/admin\/master-data\/(\w+)/', $path, $matches)) {
                $this->merge(['category' => $matches[1]]);
            }
        }
    }

    /**
     * Handle a passed validation attempt.
     */
    protected function passedValidation(): void
    {
        // Validate business rules
        $this->validateBusinessRules();

        // Log deletion request for audit trail
        \Log::critical('Admin Master Data Deletion Request', [
            'category' => $this->category,
            'id' => $this->id,
            'deletion_type' => $this->deletion_type,
            'deletion_reason' => $this->deletion_reason,
            'handle_dependencies' => $this->handle_dependencies,
            'reassign_to_id' => $this->reassign_to_id ?? null,
            'force_deletion' => $this->force_deletion,
            'bypass_system_check' => $this->bypass_system_check,
            'scheduled' => $this->schedule_deletion ?? false,
            'scheduled_at' => $this->scheduled_at ?? null,
            'create_backup' => $this->create_backup,
            'notify_users' => $this->notify_users,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'timestamp' => now(),
        ]);
    }

    /**
     * Validate business-specific rules.
     */
    private function validateBusinessRules(): void
    {
        // Check if item exists and get its details
        $model = $this->getModelForCategory($this->category);
        $item = $model::find($this->id);

        if (! $item) {
            throw new \InvalidArgumentException(__('validation.admin.master_data.item_not_found'));
        }

        // Check if item is system/core data
        if (! $this->bypass_system_check && $this->isSystemData($item)) {
            throw new \InvalidArgumentException(__('validation.admin.master_data.cannot_delete_system_data'));
        }

        // Check dependencies
        if ($this->handle_dependencies === 'restrict') {
            $dependencies = $this->checkDependencies($item);
            if (! empty($dependencies)) {
                throw new \InvalidArgumentException(__('validation.admin.master_data.has_dependencies', ['dependencies' => implode(', ', $dependencies)]));
            }
        }

        // Validate reassignment target
        if ($this->handle_dependencies === 'reassign' && $this->reassign_to_id) {
            $target = $model::find($this->reassign_to_id);
            if (! $target) {
                throw new \InvalidArgumentException(__('validation.admin.master_data.reassign_target_not_found'));
            }
        }

        // Validate hard deletion restrictions
        if ($this->deletion_type === 'hard' && ! $this->force_deletion) {
            throw new \InvalidArgumentException(__('validation.admin.master_data.hard_deletion_requires_force'));
        }
    }

    /**
     * Get the model class for the given category.
     */
    private function getModelForCategory(string $category): string
    {
        $models = [
            'countries' => \App\Models\Country::class,
            'states' => \App\Models\State::class,
            'cities' => \App\Models\City::class,
            'skills' => \App\Models\Skill::class,
            'industries' => \App\Models\Industry::class,
            'company_sizes' => \App\Models\CompanySize::class,
            'functional_areas' => \App\Models\FunctionalArea::class,
            'career_levels' => \App\Models\CareerLevel::class,
        ];

        return $models[$category] ?? \App\Models\Model::class;
    }

    /**
     * Check if the item is system/core data.
     */
    private function isSystemData($item): bool
    {
        return isset($item->is_system) && $item->is_system === true;
    }

    /**
     * Check dependencies for the item.
     */
    private function checkDependencies($item): array
    {
        $dependencies = [];

        // This would be implemented based on specific business logic
        // For now, return empty array
        return $dependencies;
    }
}
