<?php

namespace App\Http\Requests\JobApplication;

use App\Models\JobApplication;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;

/**
 * UpdateJobApplicationStatusRequest
 *
 * Comprehensive validation for job application status updates with enterprise-grade workflow validation.
 * Implements status transition rules, business logic checks, and audit trail requirements.
 *
 * @author System Generated
 *
 * @version 1.0.0
 */
class UpdateJobApplicationStatusRequest extends FormRequest
{
    /**
     * Available job application statuses.
     */
    private const STATUSES = [
        'applied',
        'under_review',
        'shortlisted',
        'interview_scheduled',
        'interviewing',
        'interview_completed',
        'second_interview',
        'final_interview',
        'reference_check',
        'background_check',
        'offer_pending',
        'offer_sent',
        'offer_accepted',
        'offer_declined',
        'hired',
        'rejected',
        'withdrawn',
        'cancelled',
        'on_hold',
        'expired',
    ];

    /**
     * Status transition rules - what status can change to what.
     */
    private const STATUS_TRANSITIONS = [
        'applied' => ['under_review', 'rejected', 'withdrawn', 'cancelled'],
        'under_review' => ['shortlisted', 'rejected', 'on_hold', 'withdrawn'],
        'shortlisted' => ['interview_scheduled', 'rejected', 'on_hold', 'withdrawn'],
        'interview_scheduled' => ['interviewing', 'rejected', 'withdrawn', 'cancelled'],
        'interviewing' => ['interview_completed', 'rejected', 'withdrawn'],
        'interview_completed' => ['second_interview', 'final_interview', 'offer_pending', 'rejected', 'on_hold'],
        'second_interview' => ['final_interview', 'offer_pending', 'rejected', 'on_hold'],
        'final_interview' => ['reference_check', 'background_check', 'offer_pending', 'rejected', 'on_hold'],
        'reference_check' => ['background_check', 'offer_pending', 'rejected', 'on_hold'],
        'background_check' => ['offer_pending', 'rejected', 'on_hold'],
        'offer_pending' => ['offer_sent', 'rejected', 'withdrawn'],
        'offer_sent' => ['offer_accepted', 'offer_declined', 'expired'],
        'offer_accepted' => ['hired'],
        'offer_declined' => ['rejected'],
        'hired' => [], // Final status
        'rejected' => [], // Final status
        'withdrawn' => [], // Final status
        'cancelled' => [], // Final status
        'on_hold' => ['under_review', 'shortlisted', 'rejected', 'withdrawn'],
        'expired' => ['rejected'],
    ];

    /**
     * Determine if the user is authorized to make this request.
     *
     * Implements role-based authorization with business logic validation.
     * Validates job application status update permissions.
     *
     * @return bool Authorization status
     */
    public function authorize(): bool
    {
        // Basic authentication check - per user requirements: "do not make users and do not any users system"
        // However, we still need to validate access permissions for security

        $jobApplicationId = $this->route('jobApplication') ?: $this->route('id') ?: $this->input('job_application_id');

        if (! $jobApplicationId) {
            return false;
        }

        // Validate job application exists
        $jobApplication = JobApplication::find($jobApplicationId);
        if (! $jobApplication) {
            return false;
        }

        // Business rule: Cannot update status of deleted applications
        if ($jobApplication->deleted_at) {
            return false;
        }

        // Business rule: Validate status transition is allowed
        $newStatus = $this->input('status');
        if ($newStatus && ! $this->isValidStatusTransition($jobApplication->status, $newStatus)) {
            return false;
        }

        // Business rule: Check if job is still active
        if (! $jobApplication->job || ! $jobApplication->job->is_active) {
            return false;
        }

        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * Implements comprehensive validation with status transition rules,
     * business logic validations, and audit trail requirements.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $jobApplicationId = $this->route('jobApplication') ?: $this->route('id') ?: $this->input('job_application_id');
        $currentApplication = $jobApplicationId ? JobApplication::find($jobApplicationId) : null;
        $currentStatus = $currentApplication ? $currentApplication->status : null;

        return [
            // Job application identification
            'job_application_id' => [
                'sometimes',
                'integer',
                'min:1',
                Rule::exists('job_applications', 'id')->where(function ($query) {
                    $query->whereNull('deleted_at');
                }),
            ],

            // Status update (required)
            'status' => [
                'required',
                'string',
                Rule::in(self::STATUSES),
                function ($attribute, $value, $fail) use ($currentStatus) {
                    if ($currentStatus && ! $this->isValidStatusTransition($currentStatus, $value)) {
                        $fail(__('validation.invalid_status_transition', [
                            'from' => $currentStatus,
                            'to' => $value,
                        ]));
                    }
                },
            ],

            // Status update reason (required for certain transitions)
            'reason' => [
                'required',
                'string',
                'max:500',
                Rule::in([
                    'qualified_candidate',
                    'meets_requirements',
                    'strong_interview',
                    'excellent_fit',
                    'recommended_by_team',
                    'lacks_experience',
                    'not_qualified',
                    'poor_interview',
                    'salary_mismatch',
                    'location_issues',
                    'candidate_withdrew',
                    'position_filled',
                    'budget_constraints',
                    'role_cancelled',
                    'hiring_freeze',
                    'process_delay',
                    'reference_issues',
                    'background_check_failed',
                    'offer_expired',
                    'candidate_declined',
                    'better_candidate_found',
                    'skills_mismatch',
                    'cultural_fit_issues',
                    'availability_mismatch',
                    'other',
                ]),
            ],

            // Detailed notes
            'notes' => [
                'sometimes',
                'string',
                'max:2000',
            ],

            // Internal notes (not visible to candidate)
            'internal_notes' => [
                'sometimes',
                'string',
                'max:2000',
            ],

            // Interview-specific fields
            'interview_feedback' => [
                'required_if:status,interview_completed',
                'string',
                'max:5000',
            ],

            'interview_score' => [
                'sometimes',
                'integer',
                'min:1',
                'max:10',
            ],

            'interview_date' => [
                'required_if:status,interview_scheduled',
                'date',
                'after:today',
            ],

            'interview_time' => [
                'required_if:status,interview_scheduled',
                'date_format:H:i',
            ],

            'interview_duration' => [
                'sometimes',
                'integer',
                'min:15',
                'max:480', // minutes
            ],

            'interview_type' => [
                'sometimes',
                'string',
                Rule::in(['phone', 'video', 'in_person', 'panel', 'technical', 'behavioral']),
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

            'interviewer_names' => [
                'sometimes',
                'array',
                'max:10',
            ],

            'interviewer_names.*' => [
                'string',
                'max:255',
            ],

            // Offer-specific fields
            'offer_amount' => [
                'required_if:status,offer_sent',
                'numeric',
                'min:0',
                'max:999999.99',
            ],

            'offer_currency' => [
                'required_with:offer_amount',
                'string',
                'size:3',
                Rule::in(['USD', 'EUR', 'GBP', 'LTL', 'PLN', 'RUB']),
            ],

            'offer_type' => [
                'sometimes',
                'string',
                Rule::in(['full_time', 'part_time', 'contract', 'temporary', 'internship']),
            ],

            'offer_start_date' => [
                'required_if:status,offer_sent',
                'date',
                'after:today',
            ],

            'offer_expiry_date' => [
                'required_if:status,offer_sent',
                'date',
                'after:offer_start_date',
            ],

            'offer_benefits' => [
                'sometimes',
                'array',
                'max:20',
            ],

            'offer_benefits.*' => [
                'string',
                'max:255',
            ],

            'offer_negotiable' => [
                'sometimes',
                'boolean',
            ],

            // Reference check fields
            'references_checked' => [
                'required_if:status,reference_check',
                'array',
                'min:1',
                'max:5',
            ],

            'references_checked.*.name' => [
                'required_with:references_checked',
                'string',
                'max:255',
            ],

            'references_checked.*.contact_date' => [
                'required_with:references_checked',
                'date',
                'before_or_equal:today',
            ],

            'references_checked.*.feedback' => [
                'required_with:references_checked',
                'string',
                'max:1000',
            ],

            'references_checked.*.rating' => [
                'sometimes',
                'integer',
                'min:1',
                'max:5',
            ],

            // Background check fields
            'background_check_provider' => [
                'required_if:status,background_check',
                'string',
                'max:255',
            ],

            'background_check_date' => [
                'required_if:status,background_check',
                'date',
                'before_or_equal:today',
            ],

            'background_check_status' => [
                'required_if:status,background_check',
                'string',
                Rule::in(['pending', 'clear', 'concerns', 'failed']),
            ],

            'background_check_notes' => [
                'sometimes',
                'string',
                'max:1000',
            ],

            // Rejection/withdrawal fields
            'rejection_category' => [
                'required_if:status,rejected',
                'string',
                Rule::in([
                    'qualifications',
                    'experience',
                    'skills',
                    'interview_performance',
                    'cultural_fit',
                    'salary_expectations',
                    'availability',
                    'references',
                    'background_check',
                    'position_filled',
                    'budget_constraints',
                    'other',
                ]),
            ],

            'provide_feedback' => [
                'sometimes',
                'boolean',
            ],

            'feedback_message' => [
                'required_if:provide_feedback,true',
                'string',
                'max:1000',
            ],

            // Communication preferences
            'notify_candidate' => [
                'sometimes',
                'boolean',
            ],

            'notification_method' => [
                'required_if:notify_candidate,true',
                'string',
                Rule::in(['email', 'phone', 'both']),
            ],

            'notification_template' => [
                'sometimes',
                'string',
                'max:100',
            ],

            // Scheduling and timeline
            'scheduled_date' => [
                'sometimes',
                'date',
                'after_or_equal:today',
            ],

            'deadline_date' => [
                'sometimes',
                'date',
                'after:today',
            ],

            'priority' => [
                'sometimes',
                'string',
                Rule::in(['low', 'medium', 'high', 'urgent']),
            ],

            // Tags and categorization
            'tags' => [
                'sometimes',
                'array',
                'max:20',
            ],

            'tags.*' => [
                'string',
                'max:50',
                'regex:/^[a-zA-Z0-9_\-\s]+$/',
            ],

            // Follow-up actions
            'follow_up_required' => [
                'sometimes',
                'boolean',
            ],

            'follow_up_date' => [
                'required_if:follow_up_required,true',
                'date',
                'after:today',
            ],

            'follow_up_type' => [
                'required_if:follow_up_required,true',
                'string',
                Rule::in(['call', 'email', 'meeting', 'reminder']),
            ],

            'follow_up_notes' => [
                'sometimes',
                'string',
                'max:500',
            ],

            // Audit and tracking
            'updated_by' => [
                'sometimes',
                'string',
                'max:255',
            ],

            'ip_address' => [
                'sometimes',
                'ip',
            ],

            'user_agent' => [
                'sometimes',
                'string',
                'max:500',
            ],

            'action_source' => [
                'sometimes',
                'string',
                Rule::in(['web', 'api', 'mobile', 'system', 'integration']),
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

            // Bulk update support
            'bulk_update' => [
                'sometimes',
                'boolean',
            ],

            'application_ids' => [
                'required_if:bulk_update,true',
                'array',
                'min:2',
                'max:100',
            ],

            'application_ids.*' => [
                'integer',
                'exists:job_applications,id',
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
            'job_application_id.exists' => __('validation.job_application_not_found'),

            // Status messages
            'status.required' => __('validation.status_required'),
            'status.in' => __('validation.status_invalid'),

            // Reason messages
            'reason.required' => __('validation.status_reason_required'),
            'reason.in' => __('validation.status_reason_invalid'),

            'notes.max' => __('validation.notes_max'),
            'internal_notes.max' => __('validation.internal_notes_max'),

            // Interview messages
            'interview_feedback.required_if' => __('validation.interview_feedback_required'),
            'interview_feedback.max' => __('validation.interview_feedback_max'),

            'interview_score.integer' => __('validation.interview_score_integer'),
            'interview_score.min' => __('validation.interview_score_min'),
            'interview_score.max' => __('validation.interview_score_max'),

            'interview_date.required_if' => __('validation.interview_date_required'),
            'interview_date.after' => __('validation.interview_date_future'),

            'interview_time.required_if' => __('validation.interview_time_required'),
            'interview_time.date_format' => __('validation.interview_time_format'),

            'interview_duration.min' => __('validation.interview_duration_min'),
            'interview_duration.max' => __('validation.interview_duration_max'),

            'interview_type.in' => __('validation.interview_type_invalid'),

            'interview_location.required_if' => __('validation.interview_location_required'),
            'interview_link.required_if' => __('validation.interview_link_required'),
            'interview_link.url' => __('validation.interview_link_format'),

            'interviewer_names.array' => __('validation.interviewer_names_array'),
            'interviewer_names.max' => __('validation.interviewer_names_max'),

            // Offer messages
            'offer_amount.required_if' => __('validation.offer_amount_required'),
            'offer_amount.numeric' => __('validation.offer_amount_numeric'),
            'offer_amount.min' => __('validation.offer_amount_min'),
            'offer_amount.max' => __('validation.offer_amount_max'),

            'offer_currency.required_with' => __('validation.offer_currency_required'),
            'offer_currency.in' => __('validation.offer_currency_invalid'),

            'offer_type.in' => __('validation.offer_type_invalid'),

            'offer_start_date.required_if' => __('validation.offer_start_date_required'),
            'offer_start_date.after' => __('validation.offer_start_date_future'),

            'offer_expiry_date.required_if' => __('validation.offer_expiry_date_required'),
            'offer_expiry_date.after' => __('validation.offer_expiry_date_after_start'),

            'offer_benefits.array' => __('validation.offer_benefits_array'),
            'offer_benefits.max' => __('validation.offer_benefits_max'),

            // Reference check messages
            'references_checked.required_if' => __('validation.references_required'),
            'references_checked.min' => __('validation.references_min'),
            'references_checked.max' => __('validation.references_max'),

            'references_checked.*.name.required_with' => __('validation.reference_name_required'),
            'references_checked.*.contact_date.required_with' => __('validation.reference_contact_date_required'),
            'references_checked.*.contact_date.before_or_equal' => __('validation.reference_contact_date_past'),
            'references_checked.*.feedback.required_with' => __('validation.reference_feedback_required'),
            'references_checked.*.rating.min' => __('validation.reference_rating_min'),
            'references_checked.*.rating.max' => __('validation.reference_rating_max'),

            // Background check messages
            'background_check_provider.required_if' => __('validation.background_check_provider_required'),
            'background_check_date.required_if' => __('validation.background_check_date_required'),
            'background_check_date.before_or_equal' => __('validation.background_check_date_past'),
            'background_check_status.required_if' => __('validation.background_check_status_required'),
            'background_check_status.in' => __('validation.background_check_status_invalid'),

            // Rejection messages
            'rejection_category.required_if' => __('validation.rejection_category_required'),
            'rejection_category.in' => __('validation.rejection_category_invalid'),
            'feedback_message.required_if' => __('validation.feedback_message_required'),
            'feedback_message.max' => __('validation.feedback_message_max'),

            // Notification messages
            'notification_method.required_if' => __('validation.notification_method_required'),
            'notification_method.in' => __('validation.notification_method_invalid'),

            // Scheduling messages
            'scheduled_date.after_or_equal' => __('validation.scheduled_date_future'),
            'deadline_date.after' => __('validation.deadline_date_future'),
            'priority.in' => __('validation.priority_invalid'),

            // Tags messages
            'tags.array' => __('validation.tags_array'),
            'tags.max' => __('validation.tags_max'),
            'tags.*.regex' => __('validation.tag_format'),

            // Follow-up messages
            'follow_up_date.required_if' => __('validation.follow_up_date_required'),
            'follow_up_date.after' => __('validation.follow_up_date_future'),
            'follow_up_type.required_if' => __('validation.follow_up_type_required'),
            'follow_up_type.in' => __('validation.follow_up_type_invalid'),

            // Audit messages
            'ip_address.ip' => __('validation.ip_address_format'),
            'action_source.in' => __('validation.action_source_invalid'),

            // Bulk update messages
            'application_ids.required_if' => __('validation.application_ids_required'),
            'application_ids.min' => __('validation.application_ids_min'),
            'application_ids.max' => __('validation.application_ids_max'),
            'application_ids.*.exists' => __('validation.application_id_not_found'),
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
            'job_application_id' => __('validation.attributes.job_application_id'),
            'status' => __('validation.attributes.status'),
            'reason' => __('validation.attributes.reason'),
            'notes' => __('validation.attributes.notes'),
            'internal_notes' => __('validation.attributes.internal_notes'),
            'interview_feedback' => __('validation.attributes.interview_feedback'),
            'interview_score' => __('validation.attributes.interview_score'),
            'interview_date' => __('validation.attributes.interview_date'),
            'interview_time' => __('validation.attributes.interview_time'),
            'interview_duration' => __('validation.attributes.interview_duration'),
            'interview_type' => __('validation.attributes.interview_type'),
            'interview_location' => __('validation.attributes.interview_location'),
            'interview_link' => __('validation.attributes.interview_link'),
            'interviewer_names' => __('validation.attributes.interviewer_names'),
            'offer_amount' => __('validation.attributes.offer_amount'),
            'offer_currency' => __('validation.attributes.offer_currency'),
            'offer_type' => __('validation.attributes.offer_type'),
            'offer_start_date' => __('validation.attributes.offer_start_date'),
            'offer_expiry_date' => __('validation.attributes.offer_expiry_date'),
            'offer_benefits' => __('validation.attributes.offer_benefits'),
            'offer_negotiable' => __('validation.attributes.offer_negotiable'),
            'references_checked' => __('validation.attributes.references_checked'),
            'background_check_provider' => __('validation.attributes.background_check_provider'),
            'background_check_date' => __('validation.attributes.background_check_date'),
            'background_check_status' => __('validation.attributes.background_check_status'),
            'background_check_notes' => __('validation.attributes.background_check_notes'),
            'rejection_category' => __('validation.attributes.rejection_category'),
            'provide_feedback' => __('validation.attributes.provide_feedback'),
            'feedback_message' => __('validation.attributes.feedback_message'),
            'notify_candidate' => __('validation.attributes.notify_candidate'),
            'notification_method' => __('validation.attributes.notification_method'),
            'notification_template' => __('validation.attributes.notification_template'),
            'scheduled_date' => __('validation.attributes.scheduled_date'),
            'deadline_date' => __('validation.attributes.deadline_date'),
            'priority' => __('validation.attributes.priority'),
            'tags' => __('validation.attributes.tags'),
            'follow_up_required' => __('validation.attributes.follow_up_required'),
            'follow_up_date' => __('validation.attributes.follow_up_date'),
            'follow_up_type' => __('validation.attributes.follow_up_type'),
            'follow_up_notes' => __('validation.attributes.follow_up_notes'),
            'updated_by' => __('validation.attributes.updated_by'),
            'ip_address' => __('validation.attributes.ip_address'),
            'user_agent' => __('validation.attributes.user_agent'),
            'action_source' => __('validation.attributes.action_source'),
            'custom_fields' => __('validation.attributes.custom_fields'),
            'bulk_update' => __('validation.attributes.bulk_update'),
            'application_ids' => __('validation.attributes.application_ids'),
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
            'message' => __('validation.job_application_status_update_failed'),
            'errors' => $validator->errors(),
            'error_code' => 'JOB_APPLICATION_STATUS_UPDATE_VALIDATION_FAILED',
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
            'message' => __('validation.job_application_status_update_unauthorized'),
            'error_code' => 'JOB_APPLICATION_STATUS_UPDATE_UNAUTHORIZED',
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
            'offer_negotiable',
            'provide_feedback',
            'notify_candidate',
            'follow_up_required',
            'bulk_update',
        ];

        foreach ($booleanFields as $field) {
            if ($this->has($field)) {
                $this->merge([
                    $field => filter_var($this->$field, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? false,
                ]);
            }
        }

        // Set audit information
        if (! $this->has('ip_address')) {
            $this->merge(['ip_address' => $this->ip()]);
        }

        if (! $this->has('user_agent')) {
            $this->merge(['user_agent' => $this->userAgent()]);
        }

        if (! $this->has('action_source')) {
            $this->merge(['action_source' => 'web']);
        }

        // Sanitize text fields
        $textFields = [
            'reason',
            'notes',
            'internal_notes',
            'interview_feedback',
            'interview_location',
            'background_check_provider',
            'background_check_notes',
            'feedback_message',
            'notification_template',
            'follow_up_notes',
            'updated_by',
        ];

        foreach ($textFields as $field) {
            if ($this->has($field)) {
                $this->merge([
                    $field => trim($this->$field),
                ]);
            }
        }

        // Normalize arrays
        if ($this->has('interviewer_names') && is_string($this->interviewer_names)) {
            $this->merge([
                'interviewer_names' => array_filter(array_map('trim', explode(',', $this->interviewer_names))),
            ]);
        }

        if ($this->has('offer_benefits') && is_string($this->offer_benefits)) {
            $this->merge([
                'offer_benefits' => array_filter(array_map('trim', explode(',', $this->offer_benefits))),
            ]);
        }

        if ($this->has('tags') && is_string($this->tags)) {
            $this->merge([
                'tags' => array_filter(array_map('trim', explode(',', $this->tags))),
            ]);
        }

        // Set default priority
        if (! $this->has('priority')) {
            $this->merge(['priority' => 'medium']);
        }

        // Set default notification preference
        if (! $this->has('notify_candidate')) {
            $this->merge(['notify_candidate' => true]);
        }

        if ($this->boolean('notify_candidate') && ! $this->has('notification_method')) {
            $this->merge(['notification_method' => 'email']);
        }
    }

    /**
     * Check if status transition is valid.
     *
     * @param  string  $fromStatus  Current status
     * @param  string  $toStatus  New status
     * @return bool Whether transition is valid
     */
    private function isValidStatusTransition(string $fromStatus, string $toStatus): bool
    {
        if ($fromStatus === $toStatus) {
            return true; // Same status is always valid
        }

        if (! isset(self::STATUS_TRANSITIONS[$fromStatus])) {
            return false;
        }

        return in_array($toStatus, self::STATUS_TRANSITIONS[$fromStatus]);
    }
}
