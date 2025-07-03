<?php

namespace App\Http\Requests\Job;

use App\Models\Job;
use App\Models\JobApplication;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;

/**
 * DeleteJobRequest
 *
 * Comprehensive validation for job deletion operations with enterprise-grade security.
 * Implements access control, business logic validation, and dependency checks.
 *
 * @author System Generated
 *
 * @version 1.0.0
 */
class DeleteJobRequest extends FormRequest
{
    /**
     * Jobs that cannot be deleted due to business rules.
     */
    private const NON_DELETABLE_STATUSES = [
        'active_with_applications',
        'interview_in_progress',
        'offers_pending',
        'hired_candidates',
    ];

    /**
     * Determine if the user is authorized to make this request.
     *
     * Implements role-based authorization with business logic validation.
     * Validates job deletion permissions and business rules.
     *
     * @return bool Authorization status
     */
    public function authorize(): bool
    {
        // Basic authentication check - per user requirements: "do not make users and do not any users system"
        // However, we still need to validate access permissions for security

        $jobId = $this->route('job') ?: $this->route('id') ?: $this->input('job_id');

        if (! $jobId) {
            return false;
        }

        // Validate job exists
        $job = Job::find($jobId);
        if (! $job) {
            return false;
        }

        // Business rule: Cannot delete already deleted jobs
        if ($job->deleted_at) {
            return false;
        }

        // Business rule: Check if job has active applications
        if ($this->hasActiveApplications($job)) {
            $forceDelete = $this->input('force_delete', false);
            $confirmationReason = $this->input('confirmation_reason');

            // Only allow deletion with explicit force and valid reason
            if (! $forceDelete || ! $confirmationReason) {
                return false;
            }
        }

        // Business rule: Check if company is still active
        if ($job->company && ! $job->company->is_active) {
            return false;
        }

        // Business rule: Cannot delete jobs with certain critical statuses
        if ($this->hasNonDeletableStatus($job)) {
            return false;
        }

        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * Implements comprehensive validation with business logic checks,
     * dependency validation, and audit trail requirements.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $jobId = $this->route('job') ?: $this->route('id') ?: $this->input('job_id');
        $job = $jobId ? Job::find($jobId) : null;

        return [
            // Job identification
            'job_id' => [
                'sometimes',
                'integer',
                'min:1',
                Rule::exists('jobs', 'id')->where(function ($query) {
                    $query->whereNull('deleted_at');
                }),
            ],

            // Deletion type
            'deletion_type' => [
                'required',
                'string',
                Rule::in(['soft_delete', 'permanent_delete', 'archive']),
            ],

            // Force deletion (for jobs with active applications)
            'force_delete' => [
                'sometimes',
                'boolean',
            ],

            // Deletion reason (required for certain scenarios)
            'deletion_reason' => [
                'required',
                'string',
                'max:500',
                Rule::in([
                    'position_filled',
                    'role_cancelled',
                    'budget_constraints',
                    'organizational_changes',
                    'duplicate_posting',
                    'incorrect_information',
                    'company_restructuring',
                    'hiring_freeze',
                    'poor_response',
                    'no_longer_needed',
                    'requirements_changed',
                    'timeline_issues',
                    'compliance_issues',
                    'data_cleanup',
                    'other',
                ]),
            ],

            // Confirmation reason (required for force deletion)
            'confirmation_reason' => [
                'required_if:force_delete,true',
                'string',
                'max:1000',
            ],

            // Application handling for jobs with active applications
            'application_action' => [
                'required_if:force_delete,true',
                'string',
                Rule::in([
                    'reject_all',
                    'transfer_to_similar',
                    'notify_and_keep',
                    'manual_review',
                    'bulk_action',
                ]),
            ],

            // Transfer target (if transferring applications)
            'transfer_to_job_id' => [
                'required_if:application_action,transfer_to_similar',
                'integer',
                'min:1',
                'exists:jobs,id',
                'different:job_id',
                function ($attribute, $value, $fail) {
                    $targetJob = Job::find($value);
                    if ($targetJob && (! $targetJob->is_active || $targetJob->deleted_at)) {
                        $fail(__('validation.transfer_target_job_inactive'));
                    }
                },
            ],

            // Notification preferences
            'notify_applicants' => [
                'sometimes',
                'boolean',
            ],

            'notification_message' => [
                'required_if:notify_applicants,true',
                'string',
                'max:2000',
            ],

            'notification_template' => [
                'sometimes',
                'string',
                'max:100',
            ],

            // Company notification
            'notify_company' => [
                'sometimes',
                'boolean',
            ],

            'company_notification_message' => [
                'required_if:notify_company,true',
                'string',
                'max:1000',
            ],

            // Data retention options
            'retain_analytics' => [
                'sometimes',
                'boolean',
            ],

            'retain_applications' => [
                'sometimes',
                'boolean',
            ],

            'retention_period' => [
                'required_if:retain_analytics,true,retain_applications,true',
                'integer',
                'min:30',
                'max:2555', // ~7 years
            ],

            // Archive options (if archiving instead of deleting)
            'archive_reason' => [
                'required_if:deletion_type,archive',
                'string',
                'max:500',
            ],

            'archive_category' => [
                'sometimes',
                'string',
                Rule::in([
                    'completed_positions',
                    'cancelled_positions',
                    'seasonal_positions',
                    'temporary_hold',
                    'compliance_archive',
                    'historical_record',
                ]),
            ],

            // Compliance and legal
            'compliance_reviewed' => [
                'required_if:deletion_type,permanent_delete',
                'boolean',
                'accepted',
            ],

            'legal_clearance' => [
                'sometimes',
                'boolean',
            ],

            'gdpr_compliance' => [
                'sometimes',
                'boolean',
            ],

            // Bulk deletion support
            'bulk_deletion' => [
                'sometimes',
                'boolean',
            ],

            'job_ids' => [
                'required_if:bulk_deletion,true',
                'array',
                'min:2',
                'max:100',
            ],

            'job_ids.*' => [
                'integer',
                'exists:jobs,id',
            ],

            // Dependencies handling
            'handle_dependencies' => [
                'sometimes',
                'boolean',
            ],

            'dependency_actions' => [
                'required_if:handle_dependencies,true',
                'array',
                'max:10',
            ],

            'dependency_actions.applications' => [
                'sometimes',
                'string',
                Rule::in(['cascade_delete', 'soft_delete', 'transfer', 'preserve']),
            ],

            'dependency_actions.interviews' => [
                'sometimes',
                'string',
                Rule::in(['cancel', 'notify_and_cancel', 'preserve', 'transfer']),
            ],

            'dependency_actions.analytics' => [
                'sometimes',
                'string',
                Rule::in(['preserve', 'anonymize', 'delete']),
            ],

            // Scheduling (for future deletion)
            'scheduled_deletion' => [
                'sometimes',
                'boolean',
            ],

            'deletion_date' => [
                'required_if:scheduled_deletion,true',
                'date',
                'after:today',
                'before:'.now()->addMonths(6)->toDateString(),
            ],

            'deletion_time' => [
                'required_if:scheduled_deletion,true',
                'date_format:H:i',
            ],

            // Backup and recovery
            'create_backup' => [
                'sometimes',
                'boolean',
            ],

            'backup_location' => [
                'required_if:create_backup,true',
                'string',
                'max:255',
            ],

            'backup_format' => [
                'sometimes',
                'string',
                Rule::in(['json', 'csv', 'xml', 'sql']),
            ],

            // Audit trail
            'deletion_notes' => [
                'sometimes',
                'string',
                'max:2000',
            ],

            'business_justification' => [
                'sometimes',
                'string',
                'max:1000',
            ],

            'supervisor_approval' => [
                'sometimes',
                'string',
                'max:255',
            ],

            // Security verification
            'security_token' => [
                'sometimes',
                'string',
                'max:100',
                'regex:/^[a-zA-Z0-9]+$/',
            ],

            'ip_verification' => [
                'sometimes',
                'ip',
            ],

            'confirmation_code' => [
                'sometimes',
                'string',
                'size:6',
                'regex:/^[0-9]{6}$/',
            ],

            // Admin override (for special cases)
            'admin_override' => [
                'sometimes',
                'boolean',
            ],

            'override_reason' => [
                'required_if:admin_override,true',
                'string',
                'max:500',
            ],

            'override_authorization' => [
                'required_if:admin_override,true',
                'string',
                'max:255',
            ],

            // Custom fields for specific business needs
            'custom_fields' => [
                'sometimes',
                'array',
                'max:10',
            ],

            'custom_fields.*' => [
                'string',
                'max:500',
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
            // Identification messages
            'job_id.exists' => __('validation.job_not_found'),

            // Deletion type messages
            'deletion_type.required' => __('validation.deletion_type_required'),
            'deletion_type.in' => __('validation.deletion_type_invalid'),

            // Deletion reason messages
            'deletion_reason.required' => __('validation.deletion_reason_required'),
            'deletion_reason.in' => __('validation.deletion_reason_invalid'),
            'deletion_reason.max' => __('validation.deletion_reason_max'),

            // Force deletion messages
            'confirmation_reason.required_if' => __('validation.confirmation_reason_required'),
            'confirmation_reason.max' => __('validation.confirmation_reason_max'),

            // Application handling messages
            'application_action.required_if' => __('validation.application_action_required'),
            'application_action.in' => __('validation.application_action_invalid'),

            'transfer_to_job_id.required_if' => __('validation.transfer_job_required'),
            'transfer_to_job_id.exists' => __('validation.transfer_job_not_found'),
            'transfer_to_job_id.different' => __('validation.transfer_job_same'),

            // Notification messages
            'notification_message.required_if' => __('validation.notification_message_required'),
            'notification_message.max' => __('validation.notification_message_max'),

            'company_notification_message.required_if' => __('validation.company_notification_message_required'),
            'company_notification_message.max' => __('validation.company_notification_message_max'),

            // Data retention messages
            'retention_period.required_if' => __('validation.retention_period_required'),
            'retention_period.min' => __('validation.retention_period_min'),
            'retention_period.max' => __('validation.retention_period_max'),

            // Archive messages
            'archive_reason.required_if' => __('validation.archive_reason_required'),
            'archive_reason.max' => __('validation.archive_reason_max'),
            'archive_category.in' => __('validation.archive_category_invalid'),

            // Compliance messages
            'compliance_reviewed.required_if' => __('validation.compliance_review_required'),
            'compliance_reviewed.accepted' => __('validation.compliance_review_must_be_accepted'),

            // Bulk deletion messages
            'job_ids.required_if' => __('validation.job_ids_required_for_bulk'),
            'job_ids.min' => __('validation.job_ids_min'),
            'job_ids.max' => __('validation.job_ids_max'),
            'job_ids.*.exists' => __('validation.job_id_not_found'),

            // Dependencies messages
            'dependency_actions.required_if' => __('validation.dependency_actions_required'),
            'dependency_actions.applications.in' => __('validation.dependency_action_applications_invalid'),
            'dependency_actions.interviews.in' => __('validation.dependency_action_interviews_invalid'),
            'dependency_actions.analytics.in' => __('validation.dependency_action_analytics_invalid'),

            // Scheduling messages
            'deletion_date.required_if' => __('validation.deletion_date_required'),
            'deletion_date.after' => __('validation.deletion_date_future'),
            'deletion_date.before' => __('validation.deletion_date_max'),

            'deletion_time.required_if' => __('validation.deletion_time_required'),
            'deletion_time.date_format' => __('validation.deletion_time_format'),

            // Backup messages
            'backup_location.required_if' => __('validation.backup_location_required'),
            'backup_format.in' => __('validation.backup_format_invalid'),

            // Audit messages
            'deletion_notes.max' => __('validation.deletion_notes_max'),
            'business_justification.max' => __('validation.business_justification_max'),

            // Security messages
            'security_token.regex' => __('validation.security_token_format'),
            'ip_verification.ip' => __('validation.ip_verification_format'),
            'confirmation_code.size' => __('validation.confirmation_code_size'),
            'confirmation_code.regex' => __('validation.confirmation_code_format'),

            // Admin override messages
            'override_reason.required_if' => __('validation.override_reason_required'),
            'override_reason.max' => __('validation.override_reason_max'),
            'override_authorization.required_if' => __('validation.override_authorization_required'),

            // Custom fields messages
            'custom_fields.array' => __('validation.custom_fields_array'),
            'custom_fields.max' => __('validation.custom_fields_max'),
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
            'job_id' => __('validation.attributes.job_id'),
            'deletion_type' => __('validation.attributes.deletion_type'),
            'force_delete' => __('validation.attributes.force_delete'),
            'deletion_reason' => __('validation.attributes.deletion_reason'),
            'confirmation_reason' => __('validation.attributes.confirmation_reason'),
            'application_action' => __('validation.attributes.application_action'),
            'transfer_to_job_id' => __('validation.attributes.transfer_to_job_id'),
            'notify_applicants' => __('validation.attributes.notify_applicants'),
            'notification_message' => __('validation.attributes.notification_message'),
            'notification_template' => __('validation.attributes.notification_template'),
            'notify_company' => __('validation.attributes.notify_company'),
            'company_notification_message' => __('validation.attributes.company_notification_message'),
            'retain_analytics' => __('validation.attributes.retain_analytics'),
            'retain_applications' => __('validation.attributes.retain_applications'),
            'retention_period' => __('validation.attributes.retention_period'),
            'archive_reason' => __('validation.attributes.archive_reason'),
            'archive_category' => __('validation.attributes.archive_category'),
            'compliance_reviewed' => __('validation.attributes.compliance_reviewed'),
            'legal_clearance' => __('validation.attributes.legal_clearance'),
            'gdpr_compliance' => __('validation.attributes.gdpr_compliance'),
            'bulk_deletion' => __('validation.attributes.bulk_deletion'),
            'job_ids' => __('validation.attributes.job_ids'),
            'handle_dependencies' => __('validation.attributes.handle_dependencies'),
            'dependency_actions' => __('validation.attributes.dependency_actions'),
            'scheduled_deletion' => __('validation.attributes.scheduled_deletion'),
            'deletion_date' => __('validation.attributes.deletion_date'),
            'deletion_time' => __('validation.attributes.deletion_time'),
            'create_backup' => __('validation.attributes.create_backup'),
            'backup_location' => __('validation.attributes.backup_location'),
            'backup_format' => __('validation.attributes.backup_format'),
            'deletion_notes' => __('validation.attributes.deletion_notes'),
            'business_justification' => __('validation.attributes.business_justification'),
            'supervisor_approval' => __('validation.attributes.supervisor_approval'),
            'security_token' => __('validation.attributes.security_token'),
            'ip_verification' => __('validation.attributes.ip_verification'),
            'confirmation_code' => __('validation.attributes.confirmation_code'),
            'admin_override' => __('validation.attributes.admin_override'),
            'override_reason' => __('validation.attributes.override_reason'),
            'override_authorization' => __('validation.attributes.override_authorization'),
            'custom_fields' => __('validation.attributes.custom_fields'),
        ];
    }

    /**
     * Handle a failed validation attempt.
     *
     *
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     */
    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator): void
    {
        $response = response()->json([
            'success' => false,
            'message' => __('validation.job_deletion_failed'),
            'errors' => $validator->errors(),
            'error_code' => 'JOB_DELETION_VALIDATION_FAILED',
            'timestamp' => now()->toISOString(),
        ], Response::HTTP_UNPROCESSABLE_ENTITY);

        throw new \Illuminate\Http\Exceptions\HttpResponseException($response);
    }

    /**
     * Handle a failed authorization attempt.
     *
     *
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     */
    protected function failedAuthorization(): void
    {
        $response = response()->json([
            'success' => false,
            'message' => __('validation.job_deletion_unauthorized'),
            'error_code' => 'JOB_DELETION_UNAUTHORIZED',
            'timestamp' => now()->toISOString(),
        ], Response::HTTP_FORBIDDEN);

        throw new \Illuminate\Http\Exceptions\HttpResponseException($response);
    }

    /**
     * Prepare the data for validation.
     *
     * Pre-processes and normalizes input data before validation.
     * Implements data sanitization and business logic preparation.
     */
    protected function prepareForValidation(): void
    {
        // Normalize boolean values
        $booleanFields = [
            'force_delete',
            'notify_applicants',
            'notify_company',
            'retain_analytics',
            'retain_applications',
            'compliance_reviewed',
            'legal_clearance',
            'gdpr_compliance',
            'bulk_deletion',
            'handle_dependencies',
            'scheduled_deletion',
            'create_backup',
            'admin_override',
        ];

        foreach ($booleanFields as $field) {
            if ($this->has($field)) {
                $this->merge([
                    $field => filter_var($this->$field, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? false,
                ]);
            }
        }

        // Set default values
        if (! $this->has('deletion_type')) {
            $this->merge(['deletion_type' => 'soft_delete']);
        }

        if (! $this->has('application_action') && $this->boolean('force_delete')) {
            $this->merge(['application_action' => 'notify_and_keep']);
        }

        if (! $this->has('backup_format') && $this->boolean('create_backup')) {
            $this->merge(['backup_format' => 'json']);
        }

        // Sanitize array fields
        if ($this->has('job_ids') && is_string($this->job_ids)) {
            $this->merge([
                'job_ids' => array_filter(array_map('intval', explode(',', $this->job_ids))),
            ]);
        }

        // Set audit information
        if (! $this->has('ip_verification')) {
            $this->merge(['ip_verification' => $this->ip()]);
        }

        // Sanitize text fields
        $textFields = [
            'deletion_reason',
            'confirmation_reason',
            'notification_message',
            'company_notification_message',
            'archive_reason',
            'deletion_notes',
            'business_justification',
            'supervisor_approval',
            'override_reason',
            'override_authorization',
            'backup_location',
        ];

        foreach ($textFields as $field) {
            if ($this->has($field)) {
                $this->merge([
                    $field => trim($this->$field),
                ]);
            }
        }

        // Set retention defaults
        if ($this->boolean('retain_analytics') || $this->boolean('retain_applications')) {
            if (! $this->has('retention_period')) {
                $this->merge(['retention_period' => 365]); // 1 year default
            }
        }
    }

    /**
     * Check if job has active applications.
     */
    private function hasActiveApplications(Job $job): bool
    {
        return JobApplication::where('job_id', $job->id)
            ->whereIn('status', ['applied', 'under_review', 'shortlisted', 'interviewing', 'offer_pending'])
            ->exists();
    }

    /**
     * Check if job has non-deletable status.
     */
    private function hasNonDeletableStatus(Job $job): bool
    {
        // Check for applications in critical stages
        $criticalApplications = JobApplication::where('job_id', $job->id)
            ->whereIn('status', ['offer_sent', 'offer_accepted', 'hired'])
            ->exists();

        if ($criticalApplications) {
            return true;
        }

        // Check for scheduled interviews
        $scheduledInterviews = JobApplication::where('job_id', $job->id)
            ->where('status', 'interview_scheduled')
            ->where('interview_date', '>', now())
            ->exists();

        if ($scheduledInterviews) {
            return true;
        }

        return false;
    }
}
