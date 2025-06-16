<?php

namespace App\Http\Requests\Financial;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

/**
 * Universal Form Request for deleting Plan
 * Implements Laravel 12 best practices with Universal MCP patterns.
 */
class DeletePlanRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     * Universal Pattern: Resource-based authorization.
     */
    public function authorize(): bool
    {
        // Only admin and financial managers can delete plans
        return true; // Based on user requirements: no auth system
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
            // Plan ID - required for deletion
            'id' => [
                'required',
                'integer',
                'min:1',
                'exists:plans,id',
                function ($attribute, $value, $fail) {
                    if (!$this->validatePlanDeletable($value)) {
                        $fail(__('validation.plan_cannot_be_deleted'));
                    }
                },
            ],

            // Confirmation field
            'confirm_deletion' => [
                'required',
                'boolean',
                'accepted',
            ],

            // Reason for deletion
            'deletion_reason' => [
                'required',
                'string',
                'min:10',
                'max:500',
                Rule::in([
                    'discontinued',
                    'replaced_by_new_plan',
                    'pricing_update',
                    'feature_changes',
                    'business_restructure',
                    'compliance_issues',
                    'low_adoption',
                    'strategic_decision',
                    'other'
                ]),
            ],

            // Replacement plan (if applicable)
            'replacement_plan_id' => [
                'sometimes',
                'integer',
                'min:1',
                'exists:plans,id',
                'different:id',
                function ($attribute, $value, $fail) {
                    if ($value && !$this->validateReplacementPlan($value)) {
                        $fail(__('validation.invalid_replacement_plan'));
                    }
                },
            ],

            // Migration strategy for existing subscribers
            'migration_strategy' => [
                'required',
                'string',
                Rule::in([
                    'auto_migrate',
                    'manual_contact',
                    'grace_period',
                    'immediate_cancel',
                    'honor_existing'
                ]),
            ],

            // Grace period (in days) if applicable
            'grace_period_days' => [
                'required_if:migration_strategy,grace_period',
                'integer',
                'min:1',
                'max:365',
            ],

            // Notification settings
            'notify_subscribers' => [
                'sometimes',
                'boolean',
            ],

            'notification_message' => [
                'required_if:notify_subscribers,true',
                'string',
                'min:20',
                'max:1000',
            ],

            // Email template for notifications
            'email_template' => [
                'sometimes',
                'string',
                'max:100',
            ],

            // Effective deletion date
            'effective_date' => [
                'sometimes',
                'date',
                'after:today',
                'before:' . now()->addYear()->toDateString(),
            ],

            // Backup and archival options
            'backup_plan_data' => [
                'sometimes',
                'boolean',
            ],

            'archive_transactions' => [
                'sometimes',
                'boolean',
            ],

            // Admin notes
            'admin_notes' => [
                'sometimes',
                'string',
                'max:1000',
            ],

            // Approval requirements
            'requires_approval' => [
                'sometimes',
                'boolean',
            ],

            'approved_by' => [
                'required_if:requires_approval,false',
                'string',
                'max:100',
            ],

            // Impact assessment
            'subscriber_count' => [
                'sometimes',
                'integer',
                'min:0',
            ],

            'revenue_impact' => [
                'sometimes',
                'numeric',
                'min:0',
            ],

            // Compliance and legal
            'legal_review_completed' => [
                'sometimes',
                'boolean',
            ],

            'compliance_check_passed' => [
                'sometimes',
                'boolean',
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     * Universal Pattern: Delete operation messages.
     */
    public function messages(): array
    {
        return [
            'id.required' => __('validation.required_field', ['field' => __('validation.attributes.plan_id')]),
            'id.exists' => __('validation.exists', ['attribute' => __('validation.attributes.plan')]),
            
            'confirm_deletion.required' => __('validation.required_field', ['field' => __('validation.attributes.confirm_deletion')]),
            'confirm_deletion.accepted' => __('validation.must_confirm_deletion'),
            
            'deletion_reason.required' => __('validation.required_field', ['field' => __('validation.attributes.deletion_reason')]),
            'deletion_reason.min' => __('validation.min_chars', ['attribute' => __('validation.attributes.deletion_reason'), 'min' => 10]),
            'deletion_reason.in' => __('validation.invalid_deletion_reason'),
            
            'replacement_plan_id.exists' => __('validation.exists', ['attribute' => __('validation.attributes.replacement_plan')]),
            'replacement_plan_id.different' => __('validation.replacement_plan_different'),
            
            'migration_strategy.required' => __('validation.required_field', ['field' => __('validation.attributes.migration_strategy')]),
            'migration_strategy.in' => __('validation.invalid_migration_strategy'),
            
            'grace_period_days.required_if' => __('validation.required_when_grace_period'),
            'grace_period_days.min' => __('validation.min_value', ['attribute' => __('validation.attributes.grace_period_days'), 'min' => 1]),
            'grace_period_days.max' => __('validation.max_value', ['attribute' => __('validation.attributes.grace_period_days'), 'max' => 365]),
            
            'notification_message.required_if' => __('validation.required_when_notify'),
            'notification_message.min' => __('validation.min_chars', ['attribute' => __('validation.attributes.notification_message'), 'min' => 20]),
            
            'effective_date.after' => __('validation.future_date', ['attribute' => __('validation.attributes.effective_date')]),
            'effective_date.before' => __('validation.within_year', ['attribute' => __('validation.attributes.effective_date')]),
            
            'approved_by.required_if' => __('validation.approval_required'),
        ];
    }

    /**
     * Get custom attributes for validator errors.
     * Universal Pattern: User-friendly field names.
     */
    public function attributes(): array
    {
        return [
            'id' => __('validation.attributes.plan_id'),
            'confirm_deletion' => __('validation.attributes.confirm_deletion'),
            'deletion_reason' => __('validation.attributes.deletion_reason'),
            'replacement_plan_id' => __('validation.attributes.replacement_plan'),
            'migration_strategy' => __('validation.attributes.migration_strategy'),
            'grace_period_days' => __('validation.attributes.grace_period_days'),
            'notify_subscribers' => __('validation.attributes.notify_subscribers'),
            'notification_message' => __('validation.attributes.notification_message'),
            'email_template' => __('validation.attributes.email_template'),
            'effective_date' => __('validation.attributes.effective_date'),
            'backup_plan_data' => __('validation.attributes.backup_plan_data'),
            'archive_transactions' => __('validation.attributes.archive_transactions'),
            'admin_notes' => __('validation.attributes.admin_notes'),
            'requires_approval' => __('validation.attributes.requires_approval'),
            'approved_by' => __('validation.attributes.approved_by'),
            'subscriber_count' => __('validation.attributes.subscriber_count'),
            'revenue_impact' => __('validation.attributes.revenue_impact'),
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
        // Set default values
        $this->merge([
            'notify_subscribers' => $this->boolean('notify_subscribers', true),
            'backup_plan_data' => $this->boolean('backup_plan_data', true),
            'archive_transactions' => $this->boolean('archive_transactions', true),
            'requires_approval' => $this->boolean('requires_approval', true),
            'legal_review_completed' => $this->boolean('legal_review_completed', false),
            'compliance_check_passed' => $this->boolean('compliance_check_passed', false),
            'effective_date' => $this->effective_date ?? now()->addDays(30)->toDateString(),
        ]);

        // Get plan statistics
        if ($this->has('id')) {
            $planStats = $this->getPlanStatistics($this->id);
            $this->merge([
                'subscriber_count' => $planStats['subscriber_count'] ?? 0,
                'revenue_impact' => $planStats['revenue_impact'] ?? 0,
            ]);
        }

        // Log deletion attempt
        Log::warning('Plan deletion attempt', [
            'plan_id' => $this->id,
            'deletion_reason' => $this->deletion_reason ?? null,
            'ip' => $this->ip(),
            'timestamp' => now(),
        ]);
    }

    /**
     * Handle a failed validation attempt.
     * Universal Pattern: Enhanced error handling for delete operations.
     */
    protected function failedValidation(Validator $validator): void
    {
        logger()->warning('Delete validation failed for DeletePlanRequest', [
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
        $resource = $this->route(strtolower('Plan'));

        // Add specific dependency checks here
        // Example: return $resource->relatedItems()->exists();

        return false;
    }

    /**
     * Universal Pattern: Check if resource is protected from deletion.
     */
    private function isProtectedResource(): bool
    {
        $resource = $this->route(strtolower('Plan'));

        // Add protection logic here
        // Example: return $resource->is_system_default;

        return false;
    }

    /**
     * Validate if plan can be deleted.
     */
    private function validatePlanDeletable($planId): bool
    {
        // Check if plan has active subscriptions
        $activeSubscriptions = \DB::table('subscriptions')
            ->where('plan_id', $planId)
            ->whereIn('status', ['active', 'trialing'])
            ->count();

        // Check if plan is marked as non-deletable
        $plan = \DB::table('plans')
            ->where('id', $planId)
            ->first();

        if (!$plan) {
            return false;
        }

        // Allow deletion if:
        // 1. No active subscriptions OR migration strategy is provided
        // 2. Plan is not marked as system/core plan
        // 3. Plan is not the default plan
        return ($activeSubscriptions === 0 || $this->has('migration_strategy')) &&
               !($plan->is_system ?? false) &&
               !($plan->is_default ?? false);
    }

    /**
     * Validate replacement plan.
     */
    private function validateReplacementPlan($replacementPlanId): bool
    {
        $replacementPlan = \DB::table('plans')
            ->where('id', $replacementPlanId)
            ->where('status', 'active')
            ->first();

        return $replacementPlan !== null;
    }

    /**
     * Get plan statistics for impact assessment.
     */
    private function getPlanStatistics($planId): array
    {
        $subscriberCount = \DB::table('subscriptions')
            ->where('plan_id', $planId)
            ->whereIn('status', ['active', 'trialing'])
            ->count();

        $revenueImpact = \DB::table('subscriptions')
            ->join('plans', 'subscriptions.plan_id', '=', 'plans.id')
            ->where('plan_id', $planId)
            ->whereIn('subscriptions.status', ['active', 'trialing'])
            ->sum('plans.price');

        return [
            'subscriber_count' => $subscriberCount,
            'revenue_impact' => $revenueImpact,
        ];
    }
}
