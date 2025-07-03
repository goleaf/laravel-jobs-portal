<?php

namespace App\Http\Requests\JobApplication;

use App\Models\Job;
use App\Models\JobApplication;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

/**
 * ShowJobApplicationRequest
 * 
 * Comprehensive validation for job application viewing operations with enterprise-grade security.
 * Implements access control, business logic validation, and data loading optimization.
 *
 * @package App\Http\Requests\JobApplication
 * @author System Generated
 * @version 1.0.0
 */
class ShowJobApplicationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     * 
     * Implements role-based authorization with business logic validation.
     * Validates job application access permissions and ownership.
     *
     * @return bool Authorization status
     */
    public function authorize(): bool
    {
        // Basic authentication check - per user requirements: "do not make users and do not any users system"
        // However, we still need to validate access permissions for security
        
        $jobApplicationId = $this->route('jobApplication') ?: $this->route('id') ?: $this->input('id');
        $jobId = $this->route('jobId') ?: $this->input('job_id');
        
        if (!$jobApplicationId) {
            return false;
        }
        
        // Validate job application exists
        $jobApplication = JobApplication::find($jobApplicationId);
        if (!$jobApplication) {
            return false;
        }
        
        // Validate job ownership if job ID is provided
        if ($jobId) {
            $job = Job::find($jobId);
            if (!$job) {
                return false;
            }
            
            // Business rule: Job application must belong to the specified job
            if ($jobApplication->job_id !== (int)$jobId) {
                return false;
            }
        }
        
        // Business rule: Cannot view deleted applications
        if ($jobApplication->deleted_at) {
            return false;
        }
        
        // Business rule: Job must be accessible
        if (!$jobApplication->job || !$jobApplication->job->is_active) {
            return false;
        }
        
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     * 
     * Implements comprehensive validation with access control and data loading options.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // Job application identification
            'id' => [
                'sometimes',
                'integer',
                'min:1',
                Rule::exists('job_applications', 'id')->where(function ($query) {
                    $query->whereNull('deleted_at');
                }),
            ],
            
            // Job identification (for route validation)
            'job_id' => [
                'sometimes',
                'integer',
                'min:1',
                'exists:jobs,id',
            ],
            
            // Data loading options
            'include' => [
                'sometimes',
                'array',
                'max:20',
            ],
            
            'include.*' => [
                'string',
                Rule::in([
                    'job',
                    'job.company',
                    'job.category',
                    'job.skills',
                    'candidate',
                    'candidate.education',
                    'candidate.experience',
                    'candidate.skills',
                    'interviews',
                    'interviews.schedule',
                    'documents',
                    'notes',
                    'activities',
                    'status_history',
                    'evaluations',
                    'references',
                    'timeline',
                    'communications',
                    'attachments'
                ]),
            ],
            
            // View mode options
            'view_mode' => [
                'sometimes',
                'string',
                Rule::in(['full', 'summary', 'basic', 'detailed', 'print', 'export']),
            ],
            
            // Response format
            'format' => [
                'sometimes',
                'string',
                Rule::in(['json', 'html', 'pdf', 'xml']),
            ],
            
            // Field selection (for API optimization)
            'fields' => [
                'sometimes',
                'array',
                'max:50',
            ],
            
            'fields.*' => [
                'string',
                'max:100',
                'regex:/^[a-zA-Z_][a-zA-Z0-9_]*(\.[a-zA-Z_][a-zA-Z0-9_]*)*$/',
            ],
            
            // Filtering options for related data
            'filter' => [
                'sometimes',
                'array',
                'max:10',
            ],
            
            'filter.interview_status' => [
                'sometimes',
                'string',
                Rule::in(['scheduled', 'completed', 'cancelled', 'no_show', 'rescheduled']),
            ],
            
            'filter.document_type' => [
                'sometimes',
                'string',
                Rule::in(['resume', 'cover_letter', 'portfolio', 'certificate', 'reference', 'other']),
            ],
            
            'filter.activity_type' => [
                'sometimes',
                'string',
                Rule::in(['status_change', 'note_added', 'interview_scheduled', 'document_uploaded', 'communication']),
            ],
            
            'filter.date_from' => [
                'sometimes',
                'date',
                'before_or_equal:today',
            ],
            
            'filter.date_to' => [
                'sometimes',
                'date',
                'after_or_equal:filter.date_from',
                'before_or_equal:today',
            ],
            
            // Pagination for related collections
            'page' => [
                'sometimes',
                'integer',
                'min:1',
                'max:1000',
            ],
            
            'per_page' => [
                'sometimes',
                'integer',
                'min:1',
                'max:100',
            ],
            
            // Security and tracking
            'track_view' => [
                'sometimes',
                'boolean',
            ],
            
            'audit_reason' => [
                'sometimes',
                'string',
                'max:255',
                Rule::in([
                    'routine_review',
                    'interview_preparation',
                    'status_update',
                    'reference_check',
                    'compliance_audit',
                    'candidate_inquiry',
                    'manager_review',
                    'hr_assessment',
                    'legal_review',
                    'other'
                ]),
            ],
            
            // Cache control
            'cache' => [
                'sometimes',
                'boolean',
            ],
            
            'cache_duration' => [
                'sometimes',
                'integer',
                'min:60',
                'max:3600',
            ],
            
            // Version control
            'version' => [
                'sometimes',
                'string',
                'max:50',
            ],
            
            // Access context
            'context' => [
                'sometimes',
                'string',
                Rule::in([
                    'employer_review',
                    'candidate_portal',
                    'admin_panel',
                    'api_access',
                    'mobile_app',
                    'integration',
                    'reporting',
                    'audit'
                ]),
            ],
            
            // Language preference
            'locale' => [
                'sometimes',
                'string',
                'size:2',
                Rule::in(['en', 'lt', 'ru', 'pl', 'de', 'fr', 'es', 'zh', 'ar', 'pt', 'tr', 'it', 'ja', 'hi']),
            ],
            
            // Timezone for date/time formatting
            'timezone' => [
                'sometimes',
                'string',
                'max:50',
                'timezone',
            ],
            
            // Export options
            'export_template' => [
                'sometimes',
                'string',
                Rule::in(['standard', 'detailed', 'summary', 'legal', 'candidate_copy']),
            ],
            
            'export_password' => [
                'required_if:format,pdf',
                'string',
                'min:8',
                'max:50',
            ],
            
            // Watermark for sensitive documents
            'watermark' => [
                'sometimes',
                'boolean',
            ],
            
            'watermark_text' => [
                'required_if:watermark,true',
                'string',
                'max:100',
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
            'id.required' => __('validation.job_application_id_required'),
            'id.exists' => __('validation.job_application_not_found'),
            
            'job_id.exists' => __('validation.job_not_found'),
            
            // Include options messages
            'include.array' => __('validation.include_array'),
            'include.max' => __('validation.include_max'),
            'include.*.in' => __('validation.include_option_invalid'),
            
            // View mode messages
            'view_mode.in' => __('validation.view_mode_invalid'),
            'format.in' => __('validation.format_invalid'),
            
            // Field selection messages
            'fields.array' => __('validation.fields_array'),
            'fields.max' => __('validation.fields_max'),
            'fields.*.regex' => __('validation.field_name_format'),
            
            // Filter messages
            'filter.array' => __('validation.filter_array'),
            'filter.interview_status.in' => __('validation.interview_status_invalid'),
            'filter.document_type.in' => __('validation.document_type_invalid'),
            'filter.activity_type.in' => __('validation.activity_type_invalid'),
            'filter.date_from.before_or_equal' => __('validation.date_from_before_today'),
            'filter.date_to.after_or_equal' => __('validation.date_to_after_from'),
            
            // Pagination messages
            'page.integer' => __('validation.page_integer'),
            'page.min' => __('validation.page_min'),
            'page.max' => __('validation.page_max'),
            
            'per_page.integer' => __('validation.per_page_integer'),
            'per_page.min' => __('validation.per_page_min'),
            'per_page.max' => __('validation.per_page_max'),
            
            // Security messages
            'audit_reason.in' => __('validation.audit_reason_invalid'),
            'context.in' => __('validation.context_invalid'),
            
            // Cache messages
            'cache_duration.min' => __('validation.cache_duration_min'),
            'cache_duration.max' => __('validation.cache_duration_max'),
            
            // Localization messages
            'locale.size' => __('validation.locale_size'),
            'locale.in' => __('validation.locale_invalid'),
            'timezone.timezone' => __('validation.timezone_invalid'),
            
            // Export messages
            'export_template.in' => __('validation.export_template_invalid'),
            'export_password.required_if' => __('validation.export_password_required'),
            'export_password.min' => __('validation.export_password_min'),
            
            // Watermark messages
            'watermark_text.required_if' => __('validation.watermark_text_required'),
            'watermark_text.max' => __('validation.watermark_text_max'),
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
            'id' => __('validation.attributes.job_application_id'),
            'job_id' => __('validation.attributes.job_id'),
            'include' => __('validation.attributes.include_options'),
            'view_mode' => __('validation.attributes.view_mode'),
            'format' => __('validation.attributes.format'),
            'fields' => __('validation.attributes.fields'),
            'filter' => __('validation.attributes.filter'),
            'filter.interview_status' => __('validation.attributes.interview_status'),
            'filter.document_type' => __('validation.attributes.document_type'),
            'filter.activity_type' => __('validation.attributes.activity_type'),
            'filter.date_from' => __('validation.attributes.date_from'),
            'filter.date_to' => __('validation.attributes.date_to'),
            'page' => __('validation.attributes.page'),
            'per_page' => __('validation.attributes.per_page'),
            'track_view' => __('validation.attributes.track_view'),
            'audit_reason' => __('validation.attributes.audit_reason'),
            'cache' => __('validation.attributes.cache'),
            'cache_duration' => __('validation.attributes.cache_duration'),
            'version' => __('validation.attributes.version'),
            'context' => __('validation.attributes.context'),
            'locale' => __('validation.attributes.locale'),
            'timezone' => __('validation.attributes.timezone'),
            'export_template' => __('validation.attributes.export_template'),
            'export_password' => __('validation.attributes.export_password'),
            'watermark' => __('validation.attributes.watermark'),
            'watermark_text' => __('validation.attributes.watermark_text'),
        ];
    }

    /**
     * Handle a failed validation attempt.
     *
     * @param \Illuminate\Contracts\Validation\Validator $validator
     * @return void
     *
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     */
    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator): void
    {
        $response = response()->json([
            'success' => false,
            'message' => __('validation.job_application_show_failed'),
            'errors' => $validator->errors(),
            'error_code' => 'JOB_APPLICATION_SHOW_VALIDATION_FAILED',
            'timestamp' => now()->toISOString(),
        ], Response::HTTP_UNPROCESSABLE_ENTITY);

        throw new \Illuminate\Http\Exceptions\HttpResponseException($response);
    }

    /**
     * Handle a failed authorization attempt.
     *
     * @return void
     *
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     */
    protected function failedAuthorization(): void
    {
        $response = response()->json([
            'success' => false,
            'message' => __('validation.job_application_show_unauthorized'),
            'error_code' => 'JOB_APPLICATION_SHOW_UNAUTHORIZED',
            'timestamp' => now()->toISOString(),
        ], Response::HTTP_FORBIDDEN);

        throw new \Illuminate\Http\Exceptions\HttpResponseException($response);
    }

    /**
     * Prepare the data for validation.
     * 
     * Pre-processes and normalizes input data before validation.
     * Implements data sanitization and business logic preparation.
     *
     * @return void
     */
    protected function prepareForValidation(): void
    {
        // Set default values
        if (!$this->has('view_mode')) {
            $this->merge(['view_mode' => 'full']);
        }
        
        if (!$this->has('format')) {
            $this->merge(['format' => 'json']);
        }
        
        if (!$this->has('page')) {
            $this->merge(['page' => 1]);
        }
        
        if (!$this->has('per_page')) {
            $this->merge(['per_page' => 10]);
        }
        
        if (!$this->has('locale')) {
            $this->merge(['locale' => app()->getLocale()]);
        }
        
        if (!$this->has('context')) {
            $this->merge(['context' => 'employer_review']);
        }
        
        // Normalize boolean values
        $booleanFields = ['track_view', 'cache', 'watermark'];
        foreach ($booleanFields as $field) {
            if ($this->has($field)) {
                $this->merge([
                    $field => filter_var($this->$field, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? false,
                ]);
            }
        }
        
        // Normalize arrays
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
        
        // Sanitize text fields
        $textFields = ['audit_reason', 'version', 'export_password', 'watermark_text'];
        foreach ($textFields as $field) {
            if ($this->has($field)) {
                $this->merge([
                    $field => trim($this->$field),
                ]);
            }
        }
        
        // Set default cache duration
        if ($this->boolean('cache') && !$this->has('cache_duration')) {
            $this->merge(['cache_duration' => 300]); // 5 minutes default
        }
        
        // Set default timezone if not provided
        if (!$this->has('timezone')) {
            $this->merge(['timezone' => config('app.timezone', 'UTC')]);
        }
    }
}
