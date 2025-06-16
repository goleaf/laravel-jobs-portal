<?php

namespace App\Http\Requests\JobApplication;

use App\Models\JobApplication;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class ShowJobApplicationRequest extends FormRequest
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
            // Application ID - required for viewing
            'id' => [
                'required',
                'integer',
                'min:1',
                'exists:job_applications,id',
                function ($attribute, $value, $fail) {
                    if (!$this->validateApplicationAccessible($value)) {
                        $fail(__('validation.application_not_accessible'));
                    }
                },
            ],

            // Include relationships
            'include' => [
                'sometimes',
                'array',
                'max:15',
            ],

            'include.*' => [
                'string',
                Rule::in([
                    'job',
                    'job.company',
                    'job.category',
                    'job.type',
                    'job.skills',
                    'applicant',
                    'applicant.profile',
                    'applicant.skills',
                    'applicant.education',
                    'applicant.experience',
                    'resume',
                    'cover_letter',
                    'portfolio',
                    'interviews',
                    'interviews.feedback',
                    'notes',
                    'activities',
                    'references',
                    'documents',
                    'assessments',
                    'communications',
                ]),
            ],

            // Response format options
            'format' => [
                'sometimes',
                'string',
                Rule::in(['json', 'detailed', 'summary', 'export']),
            ],

            // View tracking
            'track_view' => [
                'sometimes',
                'boolean',
            ],

            'view_context' => [
                'sometimes',
                'string',
                'max:100',
                Rule::in([
                    'dashboard',
                    'job_listing',
                    'applicant_profile',
                    'interview_preparation',
                    'hiring_review',
                    'analytics',
                    'export',
                    'api',
                ]),
            ],

            // Security and access control
            'access_token' => [
                'sometimes',
                'string',
                'max:255',
            ],

            'access_level' => [
                'sometimes',
                'string',
                Rule::in(['basic', 'detailed', 'full', 'admin']),
            ],

            // Data filtering
            'fields' => [
                'sometimes',
                'array',
                'max:50',
            ],

            'fields.*' => [
                'string',
                Rule::in([
                    'id',
                    'status',
                    'applied_at',
                    'updated_at',
                    'expected_salary',
                    'availability_date',
                    'cover_letter_text',
                    'notes',
                    'priority',
                    'match_score',
                    'interview_status',
                    'feedback',
                    'rating',
                    'skills_match',
                    'experience_match',
                    'education_match',
                    'location_preference',
                    'remote_work_preference',
                    'visa_status',
                    'notice_period',
                    'references_available',
                    'portfolio_url',
                    'linkedin_profile',
                    'github_profile',
                    'personal_website',
                ]),
            ],

            // Sensitive data access
            'include_sensitive' => [
                'sometimes',
                'boolean',
            ],

            'sensitive_fields' => [
                'sometimes',
                'array',
                'max:20',
            ],

            'sensitive_fields.*' => [
                'string',
                Rule::in([
                    'personal_phone',
                    'personal_email',
                    'home_address',
                    'salary_history',
                    'references_contact',
                    'background_check',
                    'medical_information',
                    'emergency_contact',
                    'bank_details',
                    'tax_information',
                    'visa_details',
                    'criminal_record',
                ]),
            ],

            // Analytics and reporting
            'analytics_context' => [
                'sometimes',
                'string',
                'max:255',
            ],

            'report_type' => [
                'sometimes',
                'string',
                Rule::in([
                    'candidate_summary',
                    'hiring_progress',
                    'skills_analysis',
                    'interview_report',
                    'reference_check',
                    'background_verification',
                    'offer_preparation',
                ]),
            ],

            // Performance optimization
            'cache_response' => [
                'sometimes',
                'boolean',
            ],

            'cache_duration' => [
                'sometimes',
                'integer',
                'min:60',
                'max:3600',
            ],

            // Export options
            'export_format' => [
                'sometimes',
                'string',
                Rule::in(['pdf', 'docx', 'csv', 'json']),
            ],

            'export_template' => [
                'sometimes',
                'string',
                'max:100',
            ],

            'export_options' => [
                'sometimes',
                'array',
                'max:10',
            ],

            'export_options.*' => [
                'string',
                Rule::in([
                    'include_resume',
                    'include_cover_letter',
                    'include_portfolio',
                    'include_references',
                    'include_interview_notes',
                    'include_assessments',
                    'include_communications',
                    'watermark',
                    'confidential_header',
                    'company_branding',
                ]),
            ],

            // Audit and compliance
            'audit_reason' => [
                'sometimes',
                'string',
                'max:500',
            ],

            'compliance_check' => [
                'sometimes',
                'boolean',
            ],

            'gdpr_consent' => [
                'sometimes',
                'boolean',
            ],

            // Real-time features
            'subscribe_updates' => [
                'sometimes',
                'boolean',
            ],

            'notification_preferences' => [
                'sometimes',
                'array',
                'max:10',
            ],

            'notification_preferences.*' => [
                'string',
                Rule::in([
                    'status_change',
                    'interview_scheduled',
                    'feedback_added',
                    'document_uploaded',
                    'reference_contacted',
                    'offer_made',
                    'deadline_approaching',
                    'priority_changed',
                ]),
            ],

            // Version control
            'version' => [
                'sometimes',
                'string',
                'max:20',
            ],

            'snapshot_date' => [
                'sometimes',
                'date',
                'before_or_equal:today',
                'after:' . now()->subYears(2)->toDateString(),
            ],

            // Integration parameters
            'integration_source' => [
                'sometimes',
                'string',
                'max:100',
            ],

            'external_reference' => [
                'sometimes',
                'string',
                'max:255',
            ],

            // Quality assurance
            'qa_mode' => [
                'sometimes',
                'boolean',
            ],

            'validation_level' => [
                'sometimes',
                'string',
                Rule::in(['basic', 'standard', 'strict', 'enterprise']),
            ],
        ];
    }

    /**
     * Get custom error messages for validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'id.required' => __('validation.required_field', ['field' => __('validation.attributes.application_id')]),
            'id.integer' => __('validation.integer', ['attribute' => __('validation.attributes.application_id')]),
            'id.exists' => __('validation.exists', ['attribute' => __('validation.attributes.job_application')]),
            
            'include.array' => __('validation.array', ['attribute' => __('validation.attributes.include')]),
            'include.max' => __('validation.max_items', ['attribute' => __('validation.attributes.include'), 'max' => 15]),
            'include.*.in' => __('validation.invalid_include_relation'),
            
            'format.in' => __('validation.invalid_format'),
            
            'view_context.max' => __('validation.max_chars', ['attribute' => __('validation.attributes.view_context'), 'max' => 100]),
            'view_context.in' => __('validation.invalid_view_context'),
            
            'access_token.max' => __('validation.max_chars', ['attribute' => __('validation.attributes.access_token'), 'max' => 255]),
            'access_level.in' => __('validation.invalid_access_level'),
            
            'fields.array' => __('validation.array', ['attribute' => __('validation.attributes.fields')]),
            'fields.max' => __('validation.max_items', ['attribute' => __('validation.attributes.fields'), 'max' => 50]),
            'fields.*.in' => __('validation.invalid_field_name'),
            
            'sensitive_fields.array' => __('validation.array', ['attribute' => __('validation.attributes.sensitive_fields')]),
            'sensitive_fields.max' => __('validation.max_items', ['attribute' => __('validation.attributes.sensitive_fields'), 'max' => 20]),
            'sensitive_fields.*.in' => __('validation.invalid_sensitive_field'),
            
            'analytics_context.max' => __('validation.max_chars', ['attribute' => __('validation.attributes.analytics_context'), 'max' => 255]),
            'report_type.in' => __('validation.invalid_report_type'),
            
            'cache_duration.min' => __('validation.min_value', ['attribute' => __('validation.attributes.cache_duration'), 'min' => 60]),
            'cache_duration.max' => __('validation.max_value', ['attribute' => __('validation.attributes.cache_duration'), 'max' => 3600]),
            
            'export_format.in' => __('validation.invalid_export_format'),
            'export_template.max' => __('validation.max_chars', ['attribute' => __('validation.attributes.export_template'), 'max' => 100]),
            
            'export_options.array' => __('validation.array', ['attribute' => __('validation.attributes.export_options')]),
            'export_options.max' => __('validation.max_items', ['attribute' => __('validation.attributes.export_options'), 'max' => 10]),
            'export_options.*.in' => __('validation.invalid_export_option'),
            
            'audit_reason.max' => __('validation.max_chars', ['attribute' => __('validation.attributes.audit_reason'), 'max' => 500]),
            
            'notification_preferences.array' => __('validation.array', ['attribute' => __('validation.attributes.notification_preferences')]),
            'notification_preferences.max' => __('validation.max_items', ['attribute' => __('validation.attributes.notification_preferences'), 'max' => 10]),
            'notification_preferences.*.in' => __('validation.invalid_notification_preference'),
            
            'version.max' => __('validation.max_chars', ['attribute' => __('validation.attributes.version'), 'max' => 20]),
            
            'snapshot_date.before_or_equal' => __('validation.before_or_equal', ['attribute' => __('validation.attributes.snapshot_date'), 'date' => 'today']),
            'snapshot_date.after' => __('validation.date_range_limit', ['attribute' => __('validation.attributes.snapshot_date')]),
            
            'integration_source.max' => __('validation.max_chars', ['attribute' => __('validation.attributes.integration_source'), 'max' => 100]),
            'external_reference.max' => __('validation.max_chars', ['attribute' => __('validation.attributes.external_reference'), 'max' => 255]),
            
            'validation_level.in' => __('validation.invalid_validation_level'),
        ];
    }

    /**
     * Get custom attribute names for validation errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'id' => __('validation.attributes.application_id'),
            'include' => __('validation.attributes.include'),
            'format' => __('validation.attributes.format'),
            'track_view' => __('validation.attributes.track_view'),
            'view_context' => __('validation.attributes.view_context'),
            'access_token' => __('validation.attributes.access_token'),
            'access_level' => __('validation.attributes.access_level'),
            'fields' => __('validation.attributes.fields'),
            'include_sensitive' => __('validation.attributes.include_sensitive'),
            'sensitive_fields' => __('validation.attributes.sensitive_fields'),
            'analytics_context' => __('validation.attributes.analytics_context'),
            'report_type' => __('validation.attributes.report_type'),
            'cache_response' => __('validation.attributes.cache_response'),
            'cache_duration' => __('validation.attributes.cache_duration'),
            'export_format' => __('validation.attributes.export_format'),
            'export_template' => __('validation.attributes.export_template'),
            'export_options' => __('validation.attributes.export_options'),
            'audit_reason' => __('validation.attributes.audit_reason'),
            'compliance_check' => __('validation.attributes.compliance_check'),
            'gdpr_consent' => __('validation.attributes.gdpr_consent'),
            'subscribe_updates' => __('validation.attributes.subscribe_updates'),
            'notification_preferences' => __('validation.attributes.notification_preferences'),
            'version' => __('validation.attributes.version'),
            'snapshot_date' => __('validation.attributes.snapshot_date'),
            'integration_source' => __('validation.attributes.integration_source'),
            'external_reference' => __('validation.attributes.external_reference'),
            'qa_mode' => __('validation.attributes.qa_mode'),
            'validation_level' => __('validation.attributes.validation_level'),
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Set default values
        $this->merge([
            'format' => $this->format ?? 'json',
            'access_level' => $this->access_level ?? 'basic',
            'track_view' => $this->boolean('track_view', true),
            'include_sensitive' => $this->boolean('include_sensitive', false),
            'cache_response' => $this->boolean('cache_response', true),
            'cache_duration' => $this->integer('cache_duration', 300),
            'compliance_check' => $this->boolean('compliance_check', true),
            'gdpr_consent' => $this->boolean('gdpr_consent', false),
            'subscribe_updates' => $this->boolean('subscribe_updates', false),
            'qa_mode' => $this->boolean('qa_mode', false),
            'validation_level' => $this->validation_level ?? 'standard',
        ]);

        // Process arrays from comma-separated strings
        if ($this->has('include') && is_string($this->include)) {
            $this->merge([
                'include' => array_filter(explode(',', $this->include)),
            ]);
        }

        if ($this->has('fields') && is_string($this->fields)) {
            $this->merge([
                'fields' => array_filter(explode(',', $this->fields)),
            ]);
        }

        if ($this->has('sensitive_fields') && is_string($this->sensitive_fields)) {
            $this->merge([
                'sensitive_fields' => array_filter(explode(',', $this->sensitive_fields)),
            ]);
        }

        if ($this->has('export_options') && is_string($this->export_options)) {
            $this->merge([
                'export_options' => array_filter(explode(',', $this->export_options)),
            ]);
        }

        if ($this->has('notification_preferences') && is_string($this->notification_preferences)) {
            $this->merge([
                'notification_preferences' => array_filter(explode(',', $this->notification_preferences)),
            ]);
        }

        // Security validation for sensitive data access
        if ($this->include_sensitive && !$this->validateSensitiveDataAccess()) {
            $this->merge([
                'include_sensitive' => false,
                'sensitive_fields' => [],
            ]);
        }

        // Log access attempt for security monitoring
        Log::info('Job application view request', [
            'application_id' => $this->id,
            'access_level' => $this->access_level,
            'include_sensitive' => $this->include_sensitive,
            'view_context' => $this->view_context ?? null,
            'format' => $this->format,
            'ip' => $this->ip(),
            'user_agent' => $this->userAgent(),
            'timestamp' => now(),
        ]);
    }

    /**
     * Handle a passed validation attempt.
     */
    protected function passedValidation(): void
    {
        // Set request metadata
        $this->merge([
            'request_id' => 'APP-VIEW-' . date('Ymd') . '-' . strtoupper(substr(md5($this->id . time()), 0, 8)),
            'validated_at' => now(),
            'request_source' => $this->header('X-Request-Source', 'web'),
        ]);

        // Security and performance flags
        $this->merge([
            'requires_audit' => $this->shouldAuditAccess(),
            'use_cache' => $this->shouldUseCache(),
            'enable_tracking' => $this->track_view,
            'security_level' => $this->determineSecurityLevel(),
        ]);

        // GDPR compliance check
        if ($this->include_sensitive && !$this->gdpr_consent) {
            Log::warning('Sensitive data access without GDPR consent', [
                'application_id' => $this->id,
                'ip' => $this->ip(),
                'timestamp' => now(),
            ]);
        }
    }

    /**
     * Validate if application is accessible.
     */
    private function validateApplicationAccessible($applicationId): bool
    {
        // Check if application exists and is not deleted
        $application = \DB::table('job_applications')
            ->where('id', $applicationId)
            ->whereNull('deleted_at')
            ->first();

        if (!$application) {
            return false;
        }

        // Check if application is in accessible status
        $restrictedStatuses = ['deleted', 'archived', 'confidential'];
        if (in_array($application->status ?? '', $restrictedStatuses)) {
            return false;
        }

        return true;
    }

    /**
     * Validate sensitive data access permissions.
     */
    private function validateSensitiveDataAccess(): bool
    {
        // Based on user requirements: no auth system
        // In production, this would check user permissions
        return true;
    }

    /**
     * Determine if access should be audited.
     */
    private function shouldAuditAccess(): bool
    {
        return $this->include_sensitive || 
               $this->access_level === 'admin' ||
               !empty($this->sensitive_fields) ||
               $this->format === 'export';
    }

    /**
     * Determine if response should be cached.
     */
    private function shouldUseCache(): bool
    {
        return $this->cache_response && 
               !$this->include_sensitive &&
               $this->access_level === 'basic' &&
               empty($this->sensitive_fields);
    }

    /**
     * Determine security level for the request.
     */
    private function determineSecurityLevel(): string
    {
        if ($this->include_sensitive || !empty($this->sensitive_fields)) {
            return 'high';
        }

        if ($this->access_level === 'admin' || $this->format === 'export') {
            return 'medium';
        }

        return 'low';
    }
}
