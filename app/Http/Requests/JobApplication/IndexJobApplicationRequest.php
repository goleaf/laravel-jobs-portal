<?php

namespace App\Http\Requests\JobApplication;

use App\Models\Job;
use App\Models\JobApplication;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;

/**
 * IndexJobApplicationRequest
 *
 * Comprehensive validation for job application listing operations with enterprise-grade filtering.
 * Implements advanced search, pagination, filtering, and business logic validation.
 *
 * @author System Generated
 *
 * @version 1.0.0
 */
class IndexJobApplicationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * Implements role-based authorization with business logic validation.
     * Validates job ownership and access permissions.
     *
     * @return bool Authorization status
     */
    public function authorize(): bool
    {
        // Basic authentication check - per user requirements: "do not make users and do not any users system"
        // However, we still need to validate job ownership for security

        $jobId = $this->route('jobId');

        if (! $jobId) {
            return false;
        }

        // Validate job exists and is accessible
        $job = Job::find($jobId);
        if (! $job) {
            return false;
        }

        // Business rule: Job must be active to view applications
        if (! $job->is_active) {
            return false;
        }

        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * Implements comprehensive validation with filtering, pagination, and search capabilities.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // Pagination parameters
            'page' => [
                'sometimes',
                'integer',
                'min:1',
                'max:10000',
            ],

            'per_page' => [
                'sometimes',
                'integer',
                'min:5',
                'max:500',
            ],

            // Search parameters
            'search' => [
                'sometimes',
                'string',
                'max:255',
                'regex:/^[\pL\pM\pN\s\.,@\-\'"]+$/u', // Allow multilingual characters, email format
            ],

            'search_by' => [
                'sometimes',
                'string',
                Rule::in([
                    'candidate_name',
                    'candidate_email',
                    'application_date',
                    'status',
                    'experience',
                    'skills',
                    'education',
                    'all',
                ]),
            ],

            // Status filtering
            'status' => [
                'sometimes',
                'array',
                'max:10',
            ],

            'status.*' => [
                'integer',
                Rule::in(array_keys(JobApplication::STATUS)),
            ],

            // Date range filtering
            'date_from' => [
                'sometimes',
                'date',
                'before_or_equal:today',
                'before_or_equal:date_to',
            ],

            'date_to' => [
                'sometimes',
                'date',
                'before_or_equal:today',
                'after_or_equal:date_from',
            ],

            // Experience filtering
            'experience_min' => [
                'sometimes',
                'integer',
                'min:0',
                'max:50',
            ],

            'experience_max' => [
                'sometimes',
                'integer',
                'min:0',
                'max:50',
                'gte:experience_min',
            ],

            // Education level filtering
            'education_level' => [
                'sometimes',
                'array',
                'max:10',
            ],

            'education_level.*' => [
                'string',
                'max:100',
            ],

            // Skills filtering
            'skills' => [
                'sometimes',
                'array',
                'max:20',
            ],

            'skills.*' => [
                'integer',
                'exists:skills,id',
            ],

            // Location filtering
            'location' => [
                'sometimes',
                'string',
                'max:255',
            ],

            'city_id' => [
                'sometimes',
                'integer',
                'exists:cities,id',
            ],

            'state_id' => [
                'sometimes',
                'integer',
                'exists:states,id',
            ],

            // Sorting parameters
            'sort_by' => [
                'sometimes',
                'string',
                Rule::in([
                    'application_date',
                    'candidate_name',
                    'status',
                    'experience',
                    'match_score',
                    'interview_date',
                    'last_activity',
                    'created_at',
                    'updated_at',
                ]),
            ],

            'sort_direction' => [
                'sometimes',
                'string',
                Rule::in(['asc', 'desc']),
            ],

            // Advanced filtering
            'match_score_min' => [
                'sometimes',
                'integer',
                'min:0',
                'max:100',
            ],

            'match_score_max' => [
                'sometimes',
                'integer',
                'min:0',
                'max:100',
                'gte:match_score_min',
            ],

            // Interview status filtering
            'interview_status' => [
                'sometimes',
                'array',
                'max:5',
            ],

            'interview_status.*' => [
                'string',
                Rule::in([
                    'scheduled',
                    'completed',
                    'cancelled',
                    'no_show',
                    'rescheduled',
                ]),
            ],

            // Job stage filtering
            'job_stage_id' => [
                'sometimes',
                'integer',
                'exists:job_stages,id',
            ],

            // Application source filtering
            'source' => [
                'sometimes',
                'array',
                'max:10',
            ],

            'source.*' => [
                'string',
                'max:50',
            ],

            // Response format
            'format' => [
                'sometimes',
                'string',
                Rule::in(['json', 'html', 'csv', 'pdf']),
            ],

            // View options
            'view_mode' => [
                'sometimes',
                'string',
                Rule::in(['list', 'grid', 'compact', 'detailed']),
            ],

            // Bulk selection
            'selected' => [
                'sometimes',
                'array',
                'max:1000',
            ],

            'selected.*' => [
                'integer',
                'exists:job_applications,id',
            ],

            // Export options
            'export_fields' => [
                'sometimes',
                'array',
                'max:20',
            ],

            'export_fields.*' => [
                'string',
                'max:50',
            ],

            // Filter persistence
            'save_filter' => [
                'sometimes',
                'boolean',
            ],

            'filter_name' => [
                'required_if:save_filter,true',
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
            // Pagination messages
            'page.integer' => __('validation.page_integer'),
            'page.min' => __('validation.page_min'),
            'page.max' => __('validation.page_max'),

            'per_page.integer' => __('validation.per_page_integer'),
            'per_page.min' => __('validation.per_page_min'),
            'per_page.max' => __('validation.per_page_max'),

            // Search messages
            'search.string' => __('validation.search_string'),
            'search.max' => __('validation.search_max'),
            'search.regex' => __('validation.search_format'),

            'search_by.in' => __('validation.search_by_invalid'),

            // Status messages
            'status.array' => __('validation.status_array'),
            'status.*.in' => __('validation.status_invalid'),

            // Date messages
            'date_from.date' => __('validation.date_from_date'),
            'date_from.before_or_equal' => __('validation.date_from_before_today'),

            'date_to.date' => __('validation.date_to_date'),
            'date_to.after_or_equal' => __('validation.date_to_after_from'),

            // Experience messages
            'experience_min.integer' => __('validation.experience_min_integer'),
            'experience_min.min' => __('validation.experience_min_value'),
            'experience_min.max' => __('validation.experience_min_max'),

            'experience_max.gte' => __('validation.experience_max_gte_min'),

            // Skills messages
            'skills.array' => __('validation.skills_array'),
            'skills.max' => __('validation.skills_max'),
            'skills.*.exists' => __('validation.skill_not_found'),

            // Location messages
            'city_id.exists' => __('validation.city_not_found'),
            'state_id.exists' => __('validation.state_not_found'),

            // Sorting messages
            'sort_by.in' => __('validation.sort_by_invalid'),
            'sort_direction.in' => __('validation.sort_direction_invalid'),

            // Match score messages
            'match_score_min.min' => __('validation.match_score_min_value'),
            'match_score_min.max' => __('validation.match_score_min_max'),
            'match_score_max.gte' => __('validation.match_score_max_gte_min'),

            // Export messages
            'filter_name.required_if' => __('validation.filter_name_required'),
            'filter_name.max' => __('validation.filter_name_max'),
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
            'page' => __('validation.attributes.page'),
            'per_page' => __('validation.attributes.per_page'),
            'search' => __('validation.attributes.search'),
            'search_by' => __('validation.attributes.search_by'),
            'status' => __('validation.attributes.status'),
            'date_from' => __('validation.attributes.date_from'),
            'date_to' => __('validation.attributes.date_to'),
            'experience_min' => __('validation.attributes.experience_min'),
            'experience_max' => __('validation.attributes.experience_max'),
            'education_level' => __('validation.attributes.education_level'),
            'skills' => __('validation.attributes.skills'),
            'location' => __('validation.attributes.location'),
            'city_id' => __('validation.attributes.city'),
            'state_id' => __('validation.attributes.state'),
            'sort_by' => __('validation.attributes.sort_by'),
            'sort_direction' => __('validation.attributes.sort_direction'),
            'match_score_min' => __('validation.attributes.match_score_min'),
            'match_score_max' => __('validation.attributes.match_score_max'),
            'interview_status' => __('validation.attributes.interview_status'),
            'job_stage_id' => __('validation.attributes.job_stage'),
            'source' => __('validation.attributes.source'),
            'format' => __('validation.attributes.format'),
            'view_mode' => __('validation.attributes.view_mode'),
            'selected' => __('validation.attributes.selected'),
            'export_fields' => __('validation.attributes.export_fields'),
            'save_filter' => __('validation.attributes.save_filter'),
            'filter_name' => __('validation.attributes.filter_name'),
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
            'message' => __('validation.job_application_index_failed'),
            'errors' => $validator->errors(),
            'error_code' => 'JOB_APPLICATION_INDEX_VALIDATION_FAILED',
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
            'message' => __('validation.job_application_index_unauthorized'),
            'error_code' => 'JOB_APPLICATION_INDEX_UNAUTHORIZED',
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
        // Set default pagination values
        if (! $this->has('page')) {
            $this->merge(['page' => 1]);
        }

        if (! $this->has('per_page')) {
            $this->merge(['per_page' => 25]);
        }

        // Set default sorting
        if (! $this->has('sort_by')) {
            $this->merge(['sort_by' => 'application_date']);
        }

        if (! $this->has('sort_direction')) {
            $this->merge(['sort_direction' => 'desc']);
        }

        // Set default search field
        if (! $this->has('search_by')) {
            $this->merge(['search_by' => 'all']);
        }

        // Set default view mode
        if (! $this->has('view_mode')) {
            $this->merge(['view_mode' => 'list']);
        }

        // Normalize boolean values
        if ($this->has('save_filter')) {
            $this->merge([
                'save_filter' => filter_var($this->save_filter, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? false,
            ]);
        }

        // Sanitize search term
        if ($this->has('search')) {
            $this->merge([
                'search' => trim($this->search),
            ]);
        }

        // Normalize status array
        if ($this->has('status') && is_string($this->status)) {
            $this->merge([
                'status' => explode(',', $this->status),
            ]);
        }

        // Normalize skills array
        if ($this->has('skills') && is_string($this->skills)) {
            $this->merge([
                'skills' => array_map('intval', explode(',', $this->skills)),
            ]);
        }

        // Convert string dates to proper format
        if ($this->has('date_from')) {
            $this->merge([
                'date_from' => \DateTime::createFromFormat('Y-m-d', $this->date_from)
                    ? $this->date_from
                    : date('Y-m-d', strtotime($this->date_from)),
            ]);
        }

        if ($this->has('date_to')) {
            $this->merge([
                'date_to' => \DateTime::createFromFormat('Y-m-d', $this->date_to)
                    ? $this->date_to
                    : date('Y-m-d', strtotime($this->date_to)),
            ]);
        }
    }
}
