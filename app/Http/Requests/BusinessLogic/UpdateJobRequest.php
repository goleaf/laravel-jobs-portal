<?php

namespace App\Http\Requests\BusinessLogic;

/**
 * 🔄 **ENTERPRISE JOB UPDATE REQUEST VALIDATION**
 *
 * **Purpose**: Comprehensive validation for job posting updates with enterprise-grade security
 * **Domain**: Business Logic - Job management operations
 * **Security Level**: HIGH - Critical business data validation
 * **Extends**: CreateJobRequest for consistency
 *
 * **Key Features**:
 * - Inherits all creation validation rules
 * - Adds update-specific business logic
 * - Status transition validation
 * - Audit trail for changes
 *
 * @version 2.0.0 - Enterprise Edition
 *
 * @since 2024-12-28
 */
class UpdateJobRequest extends CreateJobRequest
{
    /**
     * Authorization for job updates
     */
    public function authorize(): bool
    {
        // Log the job update attempt
        $this->logSecurityEvent('job_update_attempted', [
            'job_id' => $this->route('job')?->id ?? $this->route('id'),
            'ip_address' => $this->ip(),
            'user_agent' => $this->header('User-Agent'),
            'timestamp' => now(),
        ]);

        return true;
    }

    /**
     * Get validation rules specific to job updates
     */
    public function rules(): array
    {
        $rules = parent::rules();

        // Make fields optional for updates (partial updates allowed)
        $optionalFields = [
            'job_title', 'company_id', 'category_id', 'job_type_id',
            'location', 'description', 'requirements', 'application_deadline',
        ];

        foreach ($optionalFields as $field) {
            if (isset($rules[$field])) {
                // Remove 'required' and add 'sometimes'
                $rules[$field] = array_filter($rules[$field], function ($rule) {
                    return $rule !== 'required';
                });
                array_unshift($rules[$field], 'sometimes');
            }
        }

        // Add update-specific rules
        $rules = array_merge($rules, $this->getUpdateSpecificRules());

        return $rules;
    }

    /**
     * Get update-specific validation rules
     */
    protected function getUpdateSpecificRules(): array
    {
        return [
            'status' => [
                'sometimes',
                'string',
                'in:draft,pending_review,active,paused,expired,closed',
                function ($attribute, $value, $fail) {
                    $this->validateStatusTransition($value, $fail);
                },
            ],
            'updated_by' => [
                'nullable',
                'string',
                'max:255',
            ],
            'update_reason' => [
                'nullable',
                'string',
                'max:500',
            ],
        ];
    }

    /**
     * Validate status transitions are allowed
     */
    protected function validateStatusTransition($newStatus, $fail): void
    {
        $jobId = $this->route('job')?->id ?? $this->route('id');

        if (! $jobId) {
            return;
        }

        $currentJob = DB::table('jobs')->where('id', $jobId)->first();

        if (! $currentJob) {
            return;
        }

        $allowedTransitions = [
            'draft' => ['pending_review', 'active', 'closed'],
            'pending_review' => ['draft', 'active', 'closed'],
            'active' => ['paused', 'expired', 'closed'],
            'paused' => ['active', 'closed'],
            'expired' => ['active', 'closed'],
            'closed' => ['draft'], // Can reopen as draft
        ];

        $currentStatus = $currentJob->status;

        if (! isset($allowedTransitions[$currentStatus]) ||
            ! in_array($newStatus, $allowedTransitions[$currentStatus])) {
            $fail(__('validation.job.invalid_status_transition', [
                'from' => $currentStatus,
                'to' => $newStatus,
            ]));
        }
    }

    /**
     * Get update-specific error messages
     */
    public function messages(): array
    {
        return array_merge(parent::messages(), [
            'update_reason.max' => __('validation.job.update_reason_too_long'),
            'updated_by.max' => __('validation.job.updated_by_too_long'),
        ]);
    }

    /**
     * Handle successful validation for updates
     */
    protected function passedValidation(): void
    {
        parent::passedValidation();

        // Log successful update validation
        $this->logSecurityEvent('job_update_validated', [
            'job_id' => $this->route('job')?->id ?? $this->route('id'),
            'fields_updated' => array_keys($this->validated()),
            'ip_address' => $this->ip(),
        ]);

        // Add update metadata
        $this->merge([
            'updated_at' => now(),
            'updated_by' => $this->updated_by ?? 'system',
        ]);
    }
}
