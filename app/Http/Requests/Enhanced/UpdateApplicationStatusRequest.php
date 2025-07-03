<?php

namespace App\Http\Requests\Enhanced;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class UpdateApplicationStatusRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Based on user requirements: no auth system
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
            // Core status update fields
            'status' => [
                'required',
                'string',
                Rule::in([
                    'pending',
                    'under_review',
                    'reviewed',
                    'shortlisted',
                    'interview_scheduled',
                    'interview_completed',
                    'assessment_pending',
                    'assessment_completed',
                    'reference_check',
                    'background_check',
                    'offer_pending',
                    'offer_extended',
                    'offer_accepted',
                    'offer_declined',
                    'hired',
                    'rejected',
                    'withdrawn',
                    'on_hold',
                    'archived',
                ]),
            ],

            'previous_status' => [
                'sometimes',
                'string',
                Rule::in([
                    'pending',
                    'under_review',
                    'reviewed',
                    'shortlisted',
                    'interview_scheduled',
                    'interview_completed',
                    'assessment_pending',
                    'assessment_completed',
                    'reference_check',
                    'background_check',
                    'offer_pending',
                    'offer_extended',
                    'offer_accepted',
                    'offer_declined',
                    'hired',
                    'rejected',
                    'withdrawn',
                    'on_hold',
                    'archived',
                ]),
            ],

            'stage_id' => [
                'sometimes',
                'integer',
                'exists:job_stages,id',
                function ($attribute, $value, $fail) {
                    if (! $this->validateStageAccess($value)) {
                        $fail(__('validation.unauthorized_stage_access'));
                    }
                },
            ],

            // Status transition management
            'transition_reason' => [
                'sometimes',
                'string',
                'max:500',
                'required_if:status,rejected,required_if:status,withdrawn,required_if:status,on_hold',
            ],

            'transition_type' => [
                'sometimes',
                'string',
                Rule::in(['automatic', 'manual', 'bulk', 'scheduled', 'trigger_based']),
            ],

            'transition_metadata' => [
                'sometimes',
                'array',
                'max:20',
            ],

            'transition_metadata.*.key' => [
                'string',
                'max:100',
                'regex:/^[a-zA-Z0-9_]+$/',
            ],

            'transition_metadata.*.value' => [
                'string',
                'max:500',
            ],

            // Notes and feedback
            'notes' => [
                'sometimes',
                'string',
                'max:2000',
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

            'feedback' => [
                'sometimes',
                'string',
                'max:1500',
                'required_if:status,rejected',
            ],

            'feedback_type' => [
                'sometimes',
                'string',
                Rule::in(['positive', 'neutral', 'constructive', 'rejection', 'improvement']),
            ],

            'feedback_categories' => [
                'sometimes',
                'array',
                'max:10',
            ],

            'feedback_categories.*' => [
                'string',
                Rule::in([
                    'qualifications',
                    'experience',
                    'skills',
                    'communication',
                    'culture_fit',
                    'availability',
                    'salary_expectations',
                    'technical_skills',
                    'soft_skills',
                    'portfolio',
                    'references',
                    'background_check',
                ]),
            ],

            // Rating and scoring
            'rating' => [
                'sometimes',
                'integer',
                'min:1',
                'max:5',
            ],

            'score' => [
                'sometimes',
                'numeric',
                'min:0',
                'max:100',
            ],

            'score_breakdown' => [
                'sometimes',
                'array',
                'max:15',
            ],

            'score_breakdown.*.category' => [
                'string',
                'max:100',
            ],

            'score_breakdown.*.score' => [
                'numeric',
                'min:0',
                'max:100',
            ],

            'score_breakdown.*.weight' => [
                'numeric',
                'min:0',
                'max:1',
            ],

            'score_breakdown.*.notes' => [
                'sometimes',
                'string',
                'max:500',
            ],

            // Interview management
            'schedule_interview' => [
                'sometimes',
                'boolean',
            ],

            'interview_type' => [
                'required_if:schedule_interview,true',
                'string',
                Rule::in(['phone', 'video', 'in_person', 'panel', 'technical', 'behavioral', 'case_study', 'assessment']),
            ],

            'interview_date' => [
                'required_if:schedule_interview,true',
                'date',
                'after:now',
                'before:'.now()->addMonths(6)->format('Y-m-d H:i:s'),
            ],

            'interview_time' => [
                'required_if:schedule_interview,true',
                'date_format:H:i',
            ],

            'interview_duration' => [
                'sometimes',
                'integer',
                'min:15',
                'max:480', // 8 hours maximum
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

            'interview_agenda' => [
                'sometimes',
                'array',
                'max:20',
            ],

            'interview_agenda.*.topic' => [
                'string',
                'max:200',
            ],

            'interview_agenda.*.duration' => [
                'integer',
                'min:5',
                'max:120',
            ],

            'interview_agenda.*.interviewer_id' => [
                'sometimes',
                'integer',
                'exists:users,id',
            ],

            // Notification and communication
            'notify_candidate' => [
                'sometimes',
                'boolean',
            ],

            'notify_team' => [
                'sometimes',
                'boolean',
            ],

            'notify_stakeholders' => [
                'sometimes',
                'boolean',
            ],

            'notification_method' => [
                'sometimes',
                'string',
                Rule::in(['email', 'sms', 'push', 'all']),
            ],

            'notification_template_id' => [
                'sometimes',
                'integer',
                'exists:notification_templates,id',
            ],

            'custom_message' => [
                'sometimes',
                'string',
                'max:1000',
                function ($attribute, $value, $fail) {
                    if ($this->containsInappropriateContent($value)) {
                        $fail(__('validation.inappropriate_content'));
                    }
                },
            ],

            'notification_delay' => [
                'sometimes',
                'integer',
                'min:0',
                'max:86400', // 24 hours maximum
            ],

            'notification_scheduled_at' => [
                'sometimes',
                'date',
                'after:now',
            ],

            // Real-time broadcasting
            'broadcast_update' => [
                'sometimes',
                'boolean',
            ],

            'broadcast_channels' => [
                'sometimes',
                'array',
                'max:10',
            ],

            'broadcast_channels.*' => [
                'string',
                'max:100',
                'regex:/^[a-zA-Z0-9\-_\.]+$/',
            ],

            'real_time_sync' => [
                'sometimes',
                'boolean',
            ],

            'websocket_event' => [
                'sometimes',
                'string',
                'max:100',
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

            'approval_priority' => [
                'sometimes',
                'string',
                Rule::in(['low', 'normal', 'high', 'urgent']),
            ],

            'auto_approve_conditions' => [
                'sometimes',
                'array',
                'max:10',
            ],

            'auto_approve_conditions.*.field' => [
                'string',
                'max:100',
            ],

            'auto_approve_conditions.*.operator' => [
                'string',
                Rule::in(['equals', 'greater_than', 'less_than', 'contains']),
            ],

            'auto_approve_conditions.*.value' => [
                'string',
                'max:255',
            ],

            // Assessment and testing
            'schedule_assessment' => [
                'sometimes',
                'boolean',
            ],

            'assessment_type' => [
                'required_if:schedule_assessment,true',
                'string',
                Rule::in(['technical', 'cognitive', 'personality', 'skills', 'portfolio_review', 'case_study']),
            ],

            'assessment_deadline' => [
                'required_if:schedule_assessment,true',
                'date',
                'after:now',
                'before:'.now()->addMonth()->format('Y-m-d'),
            ],

            'assessment_instructions' => [
                'sometimes',
                'string',
                'max:2000',
            ],

            'assessment_duration' => [
                'sometimes',
                'integer',
                'min:10',
                'max:480', // 8 hours maximum
            ],

            'assessment_platform' => [
                'sometimes',
                'string',
                'max:100',
            ],

            'assessment_links' => [
                'sometimes',
                'array',
                'max:5',
            ],

            'assessment_links.*' => [
                'url',
                'max:500',
            ],

            // Background and reference checks
            'background_check_required' => [
                'sometimes',
                'boolean',
            ],

            'background_check_type' => [
                'required_if:background_check_required,true',
                'string',
                Rule::in(['basic', 'enhanced', 'criminal', 'financial', 'education', 'employment', 'comprehensive']),
            ],

            'reference_check_required' => [
                'sometimes',
                'boolean',
            ],

            'reference_contacts' => [
                'sometimes',
                'array',
                'max:5',
            ],

            'reference_contacts.*.name' => [
                'string',
                'max:200',
            ],

            'reference_contacts.*.email' => [
                'email',
                'max:255',
            ],

            'reference_contacts.*.phone' => [
                'sometimes',
                'string',
                'max:20',
                'regex:/^[+]?[0-9\s\-\(\)]+$/',
            ],

            'reference_contacts.*.relationship' => [
                'string',
                'max:100',
            ],

            'reference_contacts.*.company' => [
                'sometimes',
                'string',
                'max:200',
            ],

            // Salary and offer management
            'salary_negotiation' => [
                'sometimes',
                'boolean',
            ],

            'offered_salary' => [
                'sometimes',
                'numeric',
                'min:0',
                'max:10000000',
            ],

            'salary_currency' => [
                'sometimes',
                'string',
                'size:3',
                'regex:/^[A-Z]{3}$/',
            ],

            'salary_type' => [
                'sometimes',
                'string',
                Rule::in(['hourly', 'daily', 'weekly', 'monthly', 'annually']),
            ],

            'benefits_offered' => [
                'sometimes',
                'array',
                'max:20',
            ],

            'benefits_offered.*' => [
                'string',
                'max:200',
            ],

            'start_date_offered' => [
                'sometimes',
                'date',
                'after:today',
            ],

            'offer_expiry_date' => [
                'sometimes',
                'date',
                'after:start_date_offered',
            ],

            'offer_conditions' => [
                'sometimes',
                'string',
                'max:2000',
            ],

            // Timeline and deadline management
            'deadline_date' => [
                'sometimes',
                'date',
                'after:now',
            ],

            'deadline_type' => [
                'sometimes',
                'string',
                Rule::in(['soft', 'hard', 'flexible', 'critical']),
            ],

            'timeline_milestones' => [
                'sometimes',
                'array',
                'max:10',
            ],

            'timeline_milestones.*.name' => [
                'string',
                'max:200',
            ],

            'timeline_milestones.*.date' => [
                'date',
                'after:now',
            ],

            'timeline_milestones.*.type' => [
                'string',
                Rule::in(['interview', 'assessment', 'decision', 'offer', 'start']),
            ],

            'timeline_milestones.*.completed' => [
                'boolean',
            ],

            // Performance tracking
            'track_metrics' => [
                'sometimes',
                'boolean',
            ],

            'performance_indicators' => [
                'sometimes',
                'array',
                'max:15',
            ],

            'performance_indicators.*' => [
                'string',
                Rule::in([
                    'response_time',
                    'decision_speed',
                    'candidate_experience',
                    'process_efficiency',
                    'cost_per_hire',
                    'time_to_fill',
                    'quality_of_hire',
                    'hiring_manager_satisfaction',
                    'candidate_satisfaction',
                    'offer_acceptance_rate',
                ]),
            ],

            'benchmark_against' => [
                'sometimes',
                'string',
                Rule::in(['industry_average', 'company_average', 'role_average', 'historical_data']),
            ],

            // Integration and synchronization
            'sync_external_systems' => [
                'sometimes',
                'boolean',
            ],

            'external_system_ids' => [
                'sometimes',
                'array',
                'max:10',
            ],

            'external_system_ids.*' => [
                'string',
                'max:100',
            ],

            'webhook_triggers' => [
                'sometimes',
                'array',
                'max:5',
            ],

            'webhook_triggers.*' => [
                'url',
                'max:500',
            ],

            'api_callbacks' => [
                'sometimes',
                'array',
                'max:5',
            ],

            'api_callbacks.*.url' => [
                'url',
                'max:500',
            ],

            'api_callbacks.*.method' => [
                'string',
                Rule::in(['GET', 'POST', 'PUT', 'PATCH']),
            ],

            'api_callbacks.*.headers' => [
                'sometimes',
                'array',
                'max:10',
            ],

            // Audit and compliance
            'audit_trail' => [
                'sometimes',
                'boolean',
            ],

            'compliance_check' => [
                'sometimes',
                'boolean',
            ],

            'gdpr_compliant' => [
                'sometimes',
                'boolean',
            ],

            'data_retention_period' => [
                'sometimes',
                'integer',
                'min:30',
                'max:3650', // 10 years maximum
            ],

            'legal_review_required' => [
                'sometimes',
                'boolean',
            ],

            'confidentiality_level' => [
                'sometimes',
                'string',
                Rule::in(['public', 'internal', 'confidential', 'restricted']),
            ],

            // Custom fields and metadata
            'custom_fields' => [
                'sometimes',
                'array',
                'max:20',
            ],

            'custom_fields.*.name' => [
                'string',
                'max:100',
                'regex:/^[a-zA-Z0-9_\s]+$/',
            ],

            'custom_fields.*.value' => [
                'string',
                'max:500',
            ],

            'custom_fields.*.type' => [
                'string',
                Rule::in(['text', 'number', 'date', 'boolean', 'select', 'multi_select']),
            ],

            'tags' => [
                'sometimes',
                'array',
                'max:20',
            ],

            'tags.*' => [
                'string',
                'max:50',
                'regex:/^[a-zA-Z0-9\-_\s]+$/',
            ],

            'priority' => [
                'sometimes',
                'string',
                Rule::in(['low', 'normal', 'high', 'urgent', 'critical']),
            ],

            'source' => [
                'sometimes',
                'string',
                'max:100',
            ],

            'automation_rules' => [
                'sometimes',
                'array',
                'max:10',
            ],

            'automation_rules.*.trigger' => [
                'string',
                'max:100',
            ],

            'automation_rules.*.action' => [
                'string',
                'max:100',
            ],

            'automation_rules.*.conditions' => [
                'array',
                'max:5',
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'status.required' => __('validation.status_required'),
            'status.in' => __('validation.invalid_status'),
            'transition_reason.required_if' => __('validation.transition_reason_required'),
            'feedback.required_if' => __('validation.feedback_required_for_rejection'),
            'interview_type.required_if' => __('validation.interview_type_required'),
            'interview_date.required_if' => __('validation.interview_date_required'),
            'interview_time.required_if' => __('validation.interview_time_required'),
            'interview_location.required_if' => __('validation.interview_location_required'),
            'interview_link.required_if' => __('validation.interview_link_required'),
            'assessment_type.required_if' => __('validation.assessment_type_required'),
            'assessment_deadline.required_if' => __('validation.assessment_deadline_required'),
            'background_check_type.required_if' => __('validation.background_check_type_required'),
            'approver_ids.required_if' => __('validation.approvers_required'),
            'approval_deadline.required_if' => __('validation.approval_deadline_required'),
            'interview_date.after' => __('validation.interview_date_must_be_future'),
            'interview_date.before' => __('validation.interview_date_too_far'),
            'assessment_deadline.after' => __('validation.assessment_deadline_must_be_future'),
            'assessment_deadline.before' => __('validation.assessment_deadline_too_far'),
            'offered_salary.min' => __('validation.salary_must_be_positive'),
            'offered_salary.max' => __('validation.salary_exceeds_maximum'),
            'salary_currency.regex' => __('validation.invalid_currency_code'),
            'rating.min' => __('validation.rating_too_low'),
            'rating.max' => __('validation.rating_too_high'),
            'score.min' => __('validation.score_too_low'),
            'score.max' => __('validation.score_too_high'),
            'reference_contacts.*.email.email' => __('validation.invalid_reference_email'),
            'reference_contacts.*.phone.regex' => __('validation.invalid_phone_format'),
            'notes.max' => __('validation.notes_too_long'),
            'feedback.max' => __('validation.feedback_too_long'),
            'custom_message.max' => __('validation.custom_message_too_long'),
            'tags.*.regex' => __('validation.invalid_tag_format'),
            'custom_fields.*.name.regex' => __('validation.invalid_custom_field_name'),
            'broadcast_channels.*.regex' => __('validation.invalid_broadcast_channel_format'),
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'status' => __('validation.attributes.status'),
            'previous_status' => __('validation.attributes.previous_status'),
            'stage_id' => __('validation.attributes.stage'),
            'transition_reason' => __('validation.attributes.transition_reason'),
            'notes' => __('validation.attributes.notes'),
            'internal_notes' => __('validation.attributes.internal_notes'),
            'feedback' => __('validation.attributes.feedback'),
            'rating' => __('validation.attributes.rating'),
            'score' => __('validation.attributes.score'),
            'interview_type' => __('validation.attributes.interview_type'),
            'interview_date' => __('validation.attributes.interview_date'),
            'interview_time' => __('validation.attributes.interview_time'),
            'interview_location' => __('validation.attributes.interview_location'),
            'interview_link' => __('validation.attributes.interview_link'),
            'interviewer_ids' => __('validation.attributes.interviewers'),
            'assessment_type' => __('validation.attributes.assessment_type'),
            'assessment_deadline' => __('validation.attributes.assessment_deadline'),
            'background_check_type' => __('validation.attributes.background_check_type'),
            'reference_contacts' => __('validation.attributes.reference_contacts'),
            'offered_salary' => __('validation.attributes.offered_salary'),
            'salary_currency' => __('validation.attributes.salary_currency'),
            'benefits_offered' => __('validation.attributes.benefits_offered'),
            'start_date_offered' => __('validation.attributes.start_date_offered'),
            'deadline_date' => __('validation.attributes.deadline_date'),
            'approver_ids' => __('validation.attributes.approvers'),
            'approval_deadline' => __('validation.attributes.approval_deadline'),
            'custom_message' => __('validation.attributes.custom_message'),
            'notification_method' => __('validation.attributes.notification_method'),
            'broadcast_channels' => __('validation.attributes.broadcast_channels'),
            'external_system_ids' => __('validation.attributes.external_system_ids'),
            'webhook_triggers' => __('validation.attributes.webhook_triggers'),
            'custom_fields' => __('validation.attributes.custom_fields'),
            'tags' => __('validation.attributes.tags'),
            'priority' => __('validation.attributes.priority'),
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Set intelligent defaults
        if (! $this->has('transition_type')) {
            $this->merge(['transition_type' => 'manual']);
        }

        if (! $this->has('notify_candidate')) {
            $this->merge(['notify_candidate' => true]);
        }

        if (! $this->has('notify_team')) {
            $this->merge(['notify_team' => false]);
        }

        if (! $this->has('broadcast_update')) {
            $this->merge(['broadcast_update' => true]);
        }

        if (! $this->has('real_time_sync')) {
            $this->merge(['real_time_sync' => true]);
        }

        if (! $this->has('audit_trail')) {
            $this->merge(['audit_trail' => true]);
        }

        if (! $this->has('gdpr_compliant')) {
            $this->merge(['gdpr_compliant' => true]);
        }

        if (! $this->has('priority')) {
            $this->merge(['priority' => 'normal']);
        }

        if (! $this->has('feedback_type')) {
            $this->merge(['feedback_type' => 'neutral']);
        }

        if (! $this->has('approval_priority')) {
            $this->merge(['approval_priority' => 'normal']);
        }

        if (! $this->has('notification_method')) {
            $this->merge(['notification_method' => 'email']);
        }

        if (! $this->has('confidentiality_level')) {
            $this->merge(['confidentiality_level' => 'internal']);
        }

        if (! $this->has('salary_currency')) {
            $this->merge(['salary_currency' => config('app.currency', 'USD')]);
        }

        if (! $this->has('salary_type')) {
            $this->merge(['salary_type' => 'annually']);
        }

        if (! $this->has('interview_duration')) {
            $this->merge(['interview_duration' => 60]); // 1 hour default
        }

        if (! $this->has('assessment_duration')) {
            $this->merge(['assessment_duration' => 120]); // 2 hours default
        }

        if (! $this->has('data_retention_period')) {
            $this->merge(['data_retention_period' => 2555]); // 7 years default
        }

        if (! $this->has('deadline_type')) {
            $this->merge(['deadline_type' => 'soft']);
        }

        // Convert string booleans to actual booleans
        $booleanFields = [
            'schedule_interview', 'notify_candidate', 'notify_team', 'notify_stakeholders',
            'broadcast_update', 'real_time_sync', 'requires_approval', 'schedule_assessment',
            'background_check_required', 'reference_check_required', 'salary_negotiation',
            'track_metrics', 'sync_external_systems', 'audit_trail', 'compliance_check',
            'gdpr_compliant', 'legal_review_required',
        ];

        foreach ($booleanFields as $field) {
            if ($this->has($field)) {
                $this->merge([
                    $field => filter_var($this->input($field), FILTER_VALIDATE_BOOLEAN),
                ]);
            }
        }

        // Ensure arrays are properly formatted
        $arrayFields = [
            'feedback_categories', 'interviewer_ids', 'broadcast_channels', 'approver_ids',
            'assessment_links', 'benefits_offered', 'performance_indicators', 'external_system_ids',
            'webhook_triggers', 'tags',
        ];

        foreach ($arrayFields as $field) {
            if ($this->has($field) && ! is_array($this->input($field))) {
                $this->merge([
                    $field => array_filter(explode(',', $this->input($field))),
                ]);
            }
        }

        // Auto-enable features for certain status changes
        if (in_array($this->input('status'), ['rejected', 'hired', 'offer_extended'])) {
            if (! $this->has('audit_trail')) {
                $this->merge(['audit_trail' => true]);
            }
            if (! $this->has('notify_candidate')) {
                $this->merge(['notify_candidate' => true]);
            }
        }

        // Auto-set interview requirements for interview_scheduled status
        if ($this->input('status') === 'interview_scheduled' && ! $this->has('schedule_interview')) {
            $this->merge(['schedule_interview' => true]);
        }

        // Auto-set assessment requirements for assessment_pending status
        if ($this->input('status') === 'assessment_pending' && ! $this->has('schedule_assessment')) {
            $this->merge(['schedule_assessment' => true]);
        }

        // Set default broadcast channels based on status
        if ($this->input('broadcast_update') && ! $this->has('broadcast_channels')) {
            $defaultChannels = ['application.status.updated'];
            if ($this->input('status') === 'hired') {
                $defaultChannels[] = 'hiring.completed';
            }
            if ($this->input('status') === 'interview_scheduled') {
                $defaultChannels[] = 'interview.scheduled';
            }
            $this->merge(['broadcast_channels' => $defaultChannels]);
        }

        // Set default WebSocket event
        if ($this->input('real_time_sync') && ! $this->has('websocket_event')) {
            $this->merge(['websocket_event' => 'application.status.changed']);
        }

        // Log status update request for audit
        Log::info('Application status update request prepared', [
            'status' => $this->input('status'),
            'previous_status' => $this->input('previous_status'),
            'transition_type' => $this->input('transition_type'),
            'schedule_interview' => $this->input('schedule_interview', false),
            'notify_candidate' => $this->input('notify_candidate', false),
            'requires_approval' => $this->input('requires_approval', false),
            'real_time_enabled' => $this->input('real_time_sync', false),
            'audit_enabled' => $this->input('audit_trail', false),
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
        // Log successful validation with comprehensive metrics
        Log::info('Application status update request validated successfully', [
            'status_transition' => [
                'from' => $this->input('previous_status'),
                'to' => $this->input('status'),
                'reason' => $this->input('transition_reason'),
                'type' => $this->input('transition_type'),
            ],
            'interview_management' => [
                'schedule_interview' => $this->input('schedule_interview', false),
                'interview_type' => $this->input('interview_type'),
                'interview_date' => $this->input('interview_date'),
                'interviewers_count' => count($this->input('interviewer_ids', [])),
            ],
            'assessment_management' => [
                'schedule_assessment' => $this->input('schedule_assessment', false),
                'assessment_type' => $this->input('assessment_type'),
                'assessment_deadline' => $this->input('assessment_deadline'),
            ],
            'notification_settings' => [
                'notify_candidate' => $this->input('notify_candidate', false),
                'notify_team' => $this->input('notify_team', false),
                'notification_method' => $this->input('notification_method'),
                'custom_message_provided' => ! empty($this->input('custom_message')),
            ],
            'real_time_features' => [
                'broadcast_update' => $this->input('broadcast_update', false),
                'real_time_sync' => $this->input('real_time_sync', false),
                'websocket_event' => $this->input('websocket_event'),
                'broadcast_channels' => $this->input('broadcast_channels', []),
            ],
            'workflow_management' => [
                'requires_approval' => $this->input('requires_approval', false),
                'approvers_count' => count($this->input('approver_ids', [])),
                'approval_deadline' => $this->input('approval_deadline'),
                'approval_priority' => $this->input('approval_priority'),
            ],
            'compliance_features' => [
                'audit_trail' => $this->input('audit_trail', false),
                'gdpr_compliant' => $this->input('gdpr_compliant', false),
                'legal_review_required' => $this->input('legal_review_required', false),
                'confidentiality_level' => $this->input('confidentiality_level'),
            ],
            'integration_features' => [
                'sync_external_systems' => $this->input('sync_external_systems', false),
                'webhook_triggers_count' => count($this->input('webhook_triggers', [])),
                'api_callbacks_count' => count($this->input('api_callbacks', [])),
            ],
            'performance_tracking' => [
                'track_metrics' => $this->input('track_metrics', false),
                'performance_indicators' => $this->input('performance_indicators', []),
                'benchmark_against' => $this->input('benchmark_against'),
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
            'weapon', 'violence', 'hate', 'racist', 'terrorist', 'discrimin',
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
     * Validate stage access for current context.
     */
    private function validateStageAccess(int $stageId): bool
    {
        // This would typically check if the current user has access to this stage
        // For now, returning true as per user requirements (no auth system)
        return \DB::table('job_stages')->where('id', $stageId)->exists();
    }
}
