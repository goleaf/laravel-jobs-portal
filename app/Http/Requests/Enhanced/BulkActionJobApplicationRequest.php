<?php

namespace App\Http\Requests\Enhanced;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class BulkActionJobApplicationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Based on user requirements: no auth system, but employer access required
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<mixed>|string|ValidationRule>
     */
    public function rules(): array
    {
        return [
            // Bulk action type
            'action' => [
                'required',
                'string',
                Rule::in([
                    'approve',
                    'reject',
                    'shortlist',
                    'interview',
                    'hire',
                    'archive',
                    'delete',
                    'move_to_stage',
                    'send_notification',
                    'schedule_interview',
                    'send_email',
                    'export',
                    'mark_reviewed',
                    'update_rating',
                    'add_notes'
                ]),
            ],

            // Selection criteria
            'application_ids' => [
                'required_without:selection_criteria',
                'array',
                'max:1000', // Maximum bulk operations limit
                'min:1',
            ],

            'application_ids.*' => [
                'integer',
                'exists:job_applications,id',
                function ($attribute, $value, $fail) {
                    if (!$this->validateApplicationAccess($value)) {
                        $fail(__('validation.unauthorized_application_access'));
                    }
                },
            ],

            // Advanced selection criteria
            'selection_criteria' => [
                'required_without:application_ids',
                'array',
            ],

            'selection_criteria.job_id' => [
                'sometimes',
                'integer',
                'exists:jobs,id',
                function ($attribute, $value, $fail) {
                    if (!$this->validateJobOwnership($value)) {
                        $fail(__('validation.unauthorized_job_access'));
                    }
                },
            ],

            'selection_criteria.current_status' => [
                'sometimes',
                'array',
                'max:10',
            ],

            'selection_criteria.current_status.*' => [
                'integer',
                Rule::in([0, 1, 2, 3, 4, 5, 6, 7, 8]), // JobApplication status constants
            ],

            'selection_criteria.current_stage_id' => [
                'sometimes',
                'integer',
                'exists:job_stages,id',
            ],

            'selection_criteria.date_range' => [
                'sometimes',
                'array',
            ],

            'selection_criteria.date_range.from' => [
                'sometimes',
                'date',
                'before_or_equal:today',
            ],

            'selection_criteria.date_range.to' => [
                'sometimes',
                'date',
                'before_or_equal:today',
                'after_or_equal:selection_criteria.date_range.from',
            ],

            'selection_criteria.rating_min' => [
                'sometimes',
                'integer',
                'min:1',
                'max:5',
            ],

            'selection_criteria.rating_max' => [
                'sometimes',
                'integer',
                'min:1',
                'max:5',
                'gte:selection_criteria.rating_min',
            ],

            'selection_criteria.has_resume' => [
                'sometimes',
                'boolean',
            ],

            'selection_criteria.has_cover_letter' => [
                'sometimes',
                'boolean',
            ],

            'selection_criteria.experience_min' => [
                'sometimes',
                'integer',
                'min:0',
                'max:50',
            ],

            'selection_criteria.experience_max' => [
                'sometimes',
                'integer',
                'min:0',
                'max:50',
                'gte:selection_criteria.experience_min',
            ],

            // Action-specific parameters
            'target_stage_id' => [
                'required_if:action,move_to_stage',
                'integer',
                'exists:job_stages,id',
                function ($attribute, $value, $fail) {
                    if ($this->input('action') === 'move_to_stage' && !$this->validateStageAccess($value)) {
                        $fail(__('validation.unauthorized_stage_access'));
                    }
                },
            ],

            'target_status' => [
                'required_if:action,approve,required_if:action,reject',
                'integer',
                Rule::in([0, 1, 2, 3, 4, 5, 6, 7, 8]),
            ],

            'rating' => [
                'required_if:action,update_rating',
                'integer',
                'min:1',
                'max:5',
            ],

            // Notification and communication
            'notification_message' => [
                'required_if:action,send_notification',
                'string',
                'max:1000',
                'min:10',
                function ($attribute, $value, $fail) {
                    if ($this->containsInappropriateContent($value)) {
                        $fail(__('validation.inappropriate_content'));
                    }
                },
            ],

            'email_template_id' => [
                'required_if:action,send_email',
                'integer',
                'exists:email_templates,id',
            ],

            'email_subject' => [
                'required_if:action,send_email',
                'string',
                'max:255',
                'min:5',
            ],

            'email_body' => [
                'required_if:action,send_email',
                'string',
                'max:5000',
                'min:20',
            ],

            'custom_variables' => [
                'sometimes',
                'array',
                'max:20',
            ],

            'custom_variables.*' => [
                'string',
                'max:255',
            ],

            // Interview scheduling (for bulk interview scheduling)
            'interview_date' => [
                'required_if:action,schedule_interview',
                'date',
                'after:now',
                'before:' . now()->addMonths(6)->format('Y-m-d'),
            ],

            'interview_time' => [
                'required_if:action,schedule_interview',
                'date_format:H:i',
            ],

            'interview_duration' => [
                'sometimes',
                'integer',
                'min:15',
                'max:480', // 8 hours maximum
            ],

            'interview_type' => [
                'required_if:action,schedule_interview',
                'string',
                Rule::in(['phone', 'video', 'in_person', 'assessment']),
            ],

            'interview_location' => [
                'required_if:interview_type,in_person',
                'string',
                'max:500',
            ],

            'interview_link' => [
                'required_if:interview_type,video',
                'url',
                'max:500',
            ],

            'interview_notes' => [
                'sometimes',
                'string',
                'max:1000',
            ],

            'interviewer_ids' => [
                'sometimes',
                'array',
                'max:10',
            ],

            'interviewer_ids.*' => [
                'integer',
                'exists:users,id',
            ],

            // Notes and comments
            'notes' => [
                'required_if:action,add_notes',
                'string',
                'max:2000',
                'min:10',
                function ($attribute, $value, $fail) {
                    if ($this->containsInappropriateContent($value)) {
                        $fail(__('validation.inappropriate_content'));
                    }
                },
            ],

            'internal_notes' => [
                'sometimes',
                'string',
                'max:1000',
            ],

            'note_visibility' => [
                'sometimes',
                'string',
                Rule::in(['public', 'internal', 'private']),
            ],

            // Export parameters
            'export_format' => [
                'required_if:action,export',
                'string',
                Rule::in(['csv', 'excel', 'pdf', 'json']),
            ],

            'export_fields' => [
                'sometimes',
                'array',
                'max:50',
            ],

            'export_fields.*' => [
                'string',
                Rule::in([
                    'candidate_name',
                    'email',
                    'phone',
                    'status',
                    'stage',
                    'rating',
                    'applied_date',
                    'last_updated',
                    'resume_url',
                    'cover_letter',
                    'experience',
                    'education',
                    'skills',
                    'salary_expectation',
                    'availability',
                    'notes',
                    'interview_scheduled',
                    'interview_feedback'
                ]),
            ],

            'export_file_name' => [
                'sometimes',
                'string',
                'max:100',
                'regex:/^[a-zA-Z0-9\-_\s]+$/',
            ],

            'include_attachments' => [
                'sometimes',
                'boolean',
            ],

            // Processing options
            'process_async' => [
                'sometimes',
                'boolean',
            ],

            'batch_size' => [
                'sometimes',
                'integer',
                'min:1',
                'max:100',
            ],

            'send_confirmation' => [
                'sometimes',
                'boolean',
            ],

            'notify_candidates' => [
                'sometimes',
                'boolean',
            ],

            'notify_team' => [
                'sometimes',
                'boolean',
            ],

            // Workflow and approval
            'requires_approval' => [
                'sometimes',
                'boolean',
            ],

            'approver_ids' => [
                'sometimes',
                'array',
                'required_if:requires_approval,true',
                'max:5',
            ],

            'approver_ids.*' => [
                'integer',
                'exists:users,id',
            ],

            'approval_deadline' => [
                'sometimes',
                'date',
                'after:now',
                'required_if:requires_approval,true',
            ],

            'priority' => [
                'sometimes',
                'string',
                Rule::in(['low', 'normal', 'high', 'urgent']),
            ],

            // Conditional logic
            'conditions' => [
                'sometimes',
                'array',
                'max:10',
            ],

            'conditions.*.field' => [
                'string',
                Rule::in(['status', 'stage', 'rating', 'experience', 'education_level', 'last_activity']),
            ],

            'conditions.*.operator' => [
                'string',
                Rule::in(['equals', 'not_equals', 'greater_than', 'less_than', 'contains', 'not_contains']),
            ],

            'conditions.*.value' => [
                'required',
                'string',
                'max:255',
            ],

            // Audit and compliance
            'reason' => [
                'sometimes',
                'string',
                'max:500',
                'required_if:action,delete,required_if:action,reject',
            ],

            'compliance_checked' => [
                'sometimes',
                'boolean',
            ],

            'gdpr_compliant' => [
                'sometimes',
                'boolean',
                'required_if:action,delete,required_if:action,export',
            ],

            'data_retention_policy' => [
                'sometimes',
                'string',
                Rule::in(['standard', 'extended', 'minimal', 'custom']),
            ],

            // Performance and limits
            'max_processing_time' => [
                'sometimes',
                'integer',
                'min:10',
                'max:3600', // 1 hour maximum
            ],

            'chunk_processing' => [
                'sometimes',
                'boolean',
            ],

            'memory_limit' => [
                'sometimes',
                'string',
                'regex:/^\d+[MG]$/', // e.g., "256M" or "1G"
            ],

            // Rollback and recovery
            'create_backup' => [
                'sometimes',
                'boolean',
            ],

            'allow_rollback' => [
                'sometimes',
                'boolean',
            ],

            'rollback_deadline' => [
                'sometimes',
                'date',
                'after:now',
                'required_if:allow_rollback,true',
            ],

            // Integration and webhooks
            'trigger_webhooks' => [
                'sometimes',
                'boolean',
            ],

            'webhook_urls' => [
                'sometimes',
                'array',
                'max:10',
            ],

            'webhook_urls.*' => [
                'url',
                'max:500',
            ],

            'external_system_sync' => [
                'sometimes',
                'boolean',
            ],

            'sync_systems' => [
                'sometimes',
                'array',
                'required_if:external_system_sync,true',
                'max:5',
            ],

            'sync_systems.*' => [
                'string',
                Rule::in(['ats', 'hrms', 'payroll', 'background_check', 'assessment']),
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'action.required' => __('validation.bulk_action_required'),
            'action.in' => __('validation.invalid_bulk_action'),
            'application_ids.required_without' => __('validation.applications_or_criteria_required'),
            'application_ids.max' => __('validation.bulk_operation_limit_exceeded'),
            'application_ids.min' => __('validation.at_least_one_application_required'),
            'target_stage_id.required_if' => __('validation.target_stage_required_for_move'),
            'target_status.required_if' => __('validation.target_status_required'),
            'rating.required_if' => __('validation.rating_required_for_update'),
            'notification_message.required_if' => __('validation.notification_message_required'),
            'email_template_id.required_if' => __('validation.email_template_required'),
            'email_subject.required_if' => __('validation.email_subject_required'),
            'email_body.required_if' => __('validation.email_body_required'),
            'interview_date.required_if' => __('validation.interview_date_required'),
            'interview_time.required_if' => __('validation.interview_time_required'),
            'interview_type.required_if' => __('validation.interview_type_required'),
            'interview_location.required_if' => __('validation.interview_location_required'),
            'interview_link.required_if' => __('validation.interview_link_required'),
            'notes.required_if' => __('validation.notes_required'),
            'export_format.required_if' => __('validation.export_format_required'),
            'approver_ids.required_if' => __('validation.approvers_required'),
            'approval_deadline.required_if' => __('validation.approval_deadline_required'),
            'reason.required_if' => __('validation.reason_required'),
            'gdpr_compliant.required_if' => __('validation.gdpr_compliance_required'),
            'rollback_deadline.required_if' => __('validation.rollback_deadline_required'),
            'sync_systems.required_if' => __('validation.sync_systems_required'),
            'interview_date.after' => __('validation.interview_date_must_be_future'),
            'interview_date.before' => __('validation.interview_date_too_far'),
            'selection_criteria.date_range.to.after_or_equal' => __('validation.date_range_invalid'),
            'selection_criteria.rating_max.gte' => __('validation.rating_range_invalid'),
            'selection_criteria.experience_max.gte' => __('validation.experience_range_invalid'),
            'export_file_name.regex' => __('validation.invalid_file_name_format'),
            'memory_limit.regex' => __('validation.invalid_memory_limit_format'),
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'application_ids' => __('validation.attributes.job_applications'),
            'target_stage_id' => __('validation.attributes.target_stage'),
            'target_status' => __('validation.attributes.target_status'),
            'notification_message' => __('validation.attributes.notification_message'),
            'email_template_id' => __('validation.attributes.email_template'),
            'email_subject' => __('validation.attributes.email_subject'),
            'email_body' => __('validation.attributes.email_body'),
            'interview_date' => __('validation.attributes.interview_date'),
            'interview_time' => __('validation.attributes.interview_time'),
            'interview_type' => __('validation.attributes.interview_type'),
            'interview_location' => __('validation.attributes.interview_location'),
            'interview_link' => __('validation.attributes.interview_link'),
            'interviewer_ids' => __('validation.attributes.interviewers'),
            'export_format' => __('validation.attributes.export_format'),
            'export_fields' => __('validation.attributes.export_fields'),
            'export_file_name' => __('validation.attributes.export_file_name'),
            'approver_ids' => __('validation.attributes.approvers'),
            'approval_deadline' => __('validation.attributes.approval_deadline'),
            'rollback_deadline' => __('validation.attributes.rollback_deadline'),
            'webhook_urls' => __('validation.attributes.webhook_urls'),
            'sync_systems' => __('validation.attributes.sync_systems'),
            'selection_criteria.current_status' => __('validation.attributes.current_status'),
            'selection_criteria.date_range' => __('validation.attributes.date_range'),
            'selection_criteria.rating_min' => __('validation.attributes.minimum_rating'),
            'selection_criteria.rating_max' => __('validation.attributes.maximum_rating'),
            'selection_criteria.experience_min' => __('validation.attributes.minimum_experience'),
            'selection_criteria.experience_max' => __('validation.attributes.maximum_experience'),
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Set default values
        if (!$this->has('batch_size')) {
            $this->merge(['batch_size' => 50]);
        }

        if (!$this->has('priority')) {
            $this->merge(['priority' => 'normal']);
        }

        if (!$this->has('note_visibility')) {
            $this->merge(['note_visibility' => 'internal']);
        }

        if (!$this->has('data_retention_policy')) {
            $this->merge(['data_retention_policy' => 'standard']);
        }

        if (!$this->has('interview_duration')) {
            $this->merge(['interview_duration' => 60]); // 1 hour default
        }

        // Convert string booleans to actual booleans
        $booleanFields = [
            'selection_criteria.has_resume', 'selection_criteria.has_cover_letter',
            'process_async', 'send_confirmation', 'notify_candidates', 'notify_team',
            'requires_approval', 'compliance_checked', 'gdpr_compliant',
            'chunk_processing', 'create_backup', 'allow_rollback',
            'trigger_webhooks', 'external_system_sync', 'include_attachments'
        ];

        foreach ($booleanFields as $field) {
            if ($this->has($field)) {
                if (strpos($field, '.') !== false) {
                    // Handle nested fields
                    $parts = explode('.', $field);
                    $value = $this->input($parts[0] . '.' . $parts[1]);
                    if ($value !== null) {
                        $this->merge([
                            $parts[0] => array_merge(
                                $this->input($parts[0], []),
                                [$parts[1] => filter_var($value, FILTER_VALIDATE_BOOLEAN)]
                            )
                        ]);
                    }
                } else {
                    $this->merge([
                        $field => filter_var($this->input($field), FILTER_VALIDATE_BOOLEAN)
                    ]);
                }
            }
        }

        // Ensure arrays are properly formatted
        $arrayFields = [
            'application_ids', 'selection_criteria.current_status', 'custom_variables',
            'interviewer_ids', 'export_fields', 'approver_ids', 'webhook_urls', 'sync_systems'
        ];

        foreach ($arrayFields as $field) {
            if (strpos($field, '.') !== false) {
                // Handle nested fields
                $parts = explode('.', $field);
                $value = $this->input($parts[0] . '.' . $parts[1]);
                if ($value !== null && !is_array($value)) {
                    $this->merge([
                        $parts[0] => array_merge(
                            $this->input($parts[0], []),
                            [$parts[1] => array_filter(explode(',', $value))]
                        )
                    ]);
                }
            } else {
                if ($this->has($field) && !is_array($this->input($field))) {
                    $this->merge([
                        $field => array_filter(explode(',', $this->input($field)))
                    ]);
                }
            }
        }

        // Set default export fields if export action is selected
        if ($this->input('action') === 'export' && !$this->has('export_fields')) {
            $this->merge([
                'export_fields' => [
                    'candidate_name', 'email', 'status', 'applied_date', 'rating'
                ]
            ]);
        }

        // Auto-enable GDPR compliance for delete and export actions
        if (in_array($this->input('action'), ['delete', 'export']) && !$this->has('gdpr_compliant')) {
            $this->merge(['gdpr_compliant' => true]);
        }

        // Log bulk action request for audit
        Log::info('Bulk job application action requested', [
            'action' => $this->input('action'),
            'application_count' => $this->has('application_ids') ? count($this->input('application_ids')) : 0,
            'uses_criteria' => $this->has('selection_criteria'),
            'requires_approval' => $this->input('requires_approval', false),
            'ip_address' => $this->ip(),
            'user_agent' => $this->userAgent(),
            'timestamp' => now(),
        ]);
    }

    /**
     * Handle a passed validation attempt.
     */
    protected function passedValidation(): void
    {
        // Log successful bulk action validation
        Log::info('Bulk job application action validated', [
            'action' => $this->input('action'),
            'target_applications' => $this->input('application_ids', []),
            'selection_criteria' => $this->input('selection_criteria', []),
            'notification_enabled' => $this->input('notify_candidates', false),
            'requires_approval' => $this->input('requires_approval', false),
            'async_processing' => $this->input('process_async', false),
            'compliance_features' => [
                'gdpr_compliant' => $this->input('gdpr_compliant', false),
                'create_backup' => $this->input('create_backup', false),
                'allow_rollback' => $this->input('allow_rollback', false),
            ],
            'integration_features' => [
                'trigger_webhooks' => $this->input('trigger_webhooks', false),
                'external_sync' => $this->input('external_system_sync', false),
            ],
            'ip_address' => $this->ip(),
            'timestamp' => now(),
        ]);
    }

    /**
     * Check if content contains inappropriate material.
     */
    private function containsInappropriateContent(string $content): bool
    {
        $inappropriateWords = [
            'spam', 'scam', 'fraud', 'fake', 'illegal', 'hack', 'virus',
            'malware', 'phishing', 'adult', 'xxx', 'porn', 'sex', 'drug',
            'weapon', 'violence', 'hate', 'racist', 'terrorist', 'discrimin'
        ];

        $lowercaseContent = strtolower($content);
        
        foreach ($inappropriateWords as $word) {
            if (strpos($lowercaseContent, $word) !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * Validate application access for current user.
     */
    private function validateApplicationAccess(int $applicationId): bool
    {
        // This would typically check if the current user has access to this application
        // For now, returning true as per user requirements (no auth system)
        return \DB::table('job_applications')->where('id', $applicationId)->exists();
    }

    /**
     * Validate job ownership.
     */
    private function validateJobOwnership(int $jobId): bool
    {
        // This would typically check if the current user owns this job
        // For now, returning true as per user requirements (no auth system)
        return \DB::table('jobs')->where('id', $jobId)->exists();
    }

    /**
     * Validate stage access.
     */
    private function validateStageAccess(int $stageId): bool
    {
        // This would typically check if the current user has access to this stage
        // For now, returning true as per user requirements (no auth system)
        return \DB::table('job_stages')->where('id', $stageId)->exists();
    }
} 