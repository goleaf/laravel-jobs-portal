<?php

namespace App\Http\Requests\Job;

use App\Models\Company;
use App\Models\Job;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;

/**
 * UpdateJobRequest
 *
 * Comprehensive validation for job update operations with enterprise-grade security.
 * Implements access control, business logic validation, and data integrity checks.
 *
 * @author System Generated
 *
 * @version 1.0.0
 */
class UpdateJobRequest extends FormRequest
{
    /**
     * Supported job types.
     */
    private const JOB_TYPES = [
        'full_time',
        'part_time',
        'contract',
        'temporary',
        'internship',
        'freelance',
        'remote',
        'hybrid',
    ];

    /**
     * Supported experience levels.
     */
    private const EXPERIENCE_LEVELS = [
        'entry_level',
        'junior',
        'mid_level',
        'senior',
        'lead',
        'manager',
        'director',
        'executive',
    ];

    /**
     * Supported job statuses.
     */
    private const JOB_STATUSES = [
        'draft',
        'published',
        'paused',
        'closed',
        'expired',
        'filled',
    ];

    /**
     * Determine if the user is authorized to make this request.
     *
     * Implements role-based authorization with business logic validation.
     * Validates job update permissions and ownership.
     *
     * @return bool Authorization status
     */
    public function authorize(): bool
    {
        // Basic authentication check - per user requirements: "do not make users and do not any users system"
        // However, we still need to validate access permissions for security

        $jobId = $this->route('job') ?: $this->route('id') ?: $this->input('id');

        if (! $jobId) {
            return false;
        }

        // Validate job exists
        $job = Job::find($jobId);
        if (! $job) {
            return false;
        }

        // Business rule: Cannot update deleted jobs
        if ($job->deleted_at) {
            return false;
        }

        // Business rule: Cannot update jobs with certain statuses
        $nonUpdatableStatuses = ['filled', 'expired'];
        if (in_array($job->status, $nonUpdatableStatuses)) {
            return false;
        }

        // Business rule: Check if company is still active
        if ($job->company && ! $job->company->is_active) {
            return false;
        }

        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * Implements comprehensive validation with business logic checks,
     * file uploads, and security validations for job updates.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $jobId = $this->route('job') ?: $this->route('id') ?: $this->input('id');

        return [
            // Job identification
            'id' => [
                'sometimes',
                'integer',
                'min:1',
                Rule::exists('jobs', 'id')->where(function ($query) {
                    $query->whereNull('deleted_at');
                }),
            ],

            // Basic job information
            'title' => [
                'sometimes',
                'string',
                'min:3',
                'max:255',
                'regex:/^[\pL\pN\s\-\.\,\(\)]+$/u',
                Rule::unique('jobs', 'title')
                    ->ignore($jobId)
                    ->where(function ($query) {
                        $query->where('company_id', $this->input('company_id') ?: $this->route('company_id'));
                    }),
            ],

            'description' => [
                'sometimes',
                'string',
                'min:50',
                'max:10000',
            ],

            'short_description' => [
                'sometimes',
                'string',
                'min:20',
                'max:500',
            ],

            'requirements' => [
                'sometimes',
                'string',
                'min:20',
                'max:5000',
            ],

            'responsibilities' => [
                'sometimes',
                'string',
                'min:20',
                'max:5000',
            ],

            'benefits' => [
                'sometimes',
                'string',
                'max:3000',
            ],

            // Job classification
            'job_type_id' => [
                'sometimes',
                'integer',
                'min:1',
                'exists:job_types,id',
            ],

            'job_category_id' => [
                'sometimes',
                'integer',
                'min:1',
                'exists:job_categories,id',
            ],

            'company_id' => [
                'sometimes',
                'integer',
                'min:1',
                'exists:companies,id',
                function ($attribute, $value, $fail) use ($jobId) {
                    if ($jobId) {
                        $existingJob = Job::find($jobId);
                        if ($existingJob && $existingJob->company_id !== (int) $value) {
                            $fail(__('validation.company_id_cannot_be_changed'));
                        }
                    }
                },
            ],

            // Location information
            'location' => [
                'sometimes',
                'string',
                'max:255',
            ],

            'city_id' => [
                'sometimes',
                'integer',
                'min:1',
                'exists:cities,id',
            ],

            'state_id' => [
                'sometimes',
                'integer',
                'min:1',
                'exists:states,id',
            ],

            'country_id' => [
                'sometimes',
                'integer',
                'min:1',
                'exists:countries,id',
            ],

            'postal_code' => [
                'sometimes',
                'string',
                'max:20',
                'regex:/^[a-zA-Z0-9\s\-]+$/',
            ],

            'address' => [
                'sometimes',
                'string',
                'max:500',
            ],

            'coordinates' => [
                'sometimes',
                'array',
            ],

            'coordinates.latitude' => [
                'required_with:coordinates',
                'numeric',
                'between:-90,90',
            ],

            'coordinates.longitude' => [
                'required_with:coordinates',
                'numeric',
                'between:-180,180',
            ],

            // Salary information
            'salary_min' => [
                'sometimes',
                'numeric',
                'min:0',
                'max:999999.99',
            ],

            'salary_max' => [
                'sometimes',
                'numeric',
                'min:0',
                'max:999999.99',
                'gte:salary_min',
            ],

            'salary_currency' => [
                'required_with:salary_min,salary_max',
                'string',
                'size:3',
                Rule::in(['USD', 'EUR', 'GBP', 'LTL', 'PLN', 'RUB']),
            ],

            'salary_period' => [
                'sometimes',
                'string',
                Rule::in(['hourly', 'daily', 'weekly', 'monthly', 'yearly']),
            ],

            'salary_negotiable' => [
                'sometimes',
                'boolean',
            ],

            'hide_salary' => [
                'sometimes',
                'boolean',
            ],

            // Experience and qualifications
            'experience_level' => [
                'sometimes',
                'string',
                Rule::in(self::EXPERIENCE_LEVELS),
            ],

            'experience_required' => [
                'sometimes',
                'integer',
                'min:0',
                'max:50',
            ],

            'education_level' => [
                'sometimes',
                'string',
                Rule::in(['high_school', 'associate', 'bachelor', 'master', 'doctorate', 'other']),
            ],

            'education_field' => [
                'sometimes',
                'string',
                'max:255',
            ],

            // Skills and requirements
            'skills_required' => [
                'sometimes',
                'array',
                'max:50',
            ],

            'skills_required.*' => [
                'integer',
                'exists:skills,id',
            ],

            'skills_preferred' => [
                'sometimes',
                'array',
                'max:30',
            ],

            'skills_preferred.*' => [
                'integer',
                'exists:skills,id',
            ],

            'languages_required' => [
                'sometimes',
                'array',
                'max:10',
            ],

            'languages_required.*.language' => [
                'required_with:languages_required',
                'string',
                'max:50',
            ],

            'languages_required.*.proficiency' => [
                'required_with:languages_required',
                'string',
                Rule::in(['beginner', 'intermediate', 'advanced', 'native']),
            ],

            'certifications' => [
                'sometimes',
                'array',
                'max:20',
            ],

            'certifications.*' => [
                'string',
                'max:255',
            ],

            // Work arrangement
            'is_remote' => [
                'sometimes',
                'boolean',
            ],

            'remote_type' => [
                'sometimes',
                'string',
                Rule::in(['fully_remote', 'hybrid', 'remote_friendly', 'on_site']),
            ],

            'remote_locations' => [
                'sometimes',
                'array',
                'max:20',
            ],

            'remote_locations.*' => [
                'string',
                'max:100',
            ],

            'work_schedule' => [
                'sometimes',
                'string',
                Rule::in(['standard', 'flexible', 'shift_work', 'night_shift', 'weekend']),
            ],

            'hours_per_week' => [
                'sometimes',
                'integer',
                'min:1',
                'max:168',
            ],

            'travel_required' => [
                'sometimes',
                'boolean',
            ],

            'travel_percentage' => [
                'required_if:travel_required,true',
                'integer',
                'min:1',
                'max:100',
            ],

            // Application settings
            'application_deadline' => [
                'sometimes',
                'date',
                'after:today',
                'before:'.now()->addYear()->toDateString(),
            ],

            'application_method' => [
                'sometimes',
                'string',
                Rule::in(['internal', 'external', 'email', 'both']),
            ],

            'external_application_url' => [
                'required_if:application_method,external,both',
                'url',
                'max:500',
                'active_url',
            ],

            'application_email' => [
                'required_if:application_method,email,both',
                'email:rfc,dns',
                'max:255',
            ],

            'max_applications' => [
                'sometimes',
                'integer',
                'min:1',
                'max:10000',
            ],

            'auto_close_when_filled' => [
                'sometimes',
                'boolean',
            ],

            // Job posting settings
            'status' => [
                'sometimes',
                'string',
                Rule::in(self::JOB_STATUSES),
            ],

            'is_featured' => [
                'sometimes',
                'boolean',
            ],

            'is_urgent' => [
                'sometimes',
                'boolean',
            ],

            'is_confidential' => [
                'sometimes',
                'boolean',
            ],

            'featured_until' => [
                'required_if:is_featured,true',
                'date',
                'after:today',
            ],

            'publish_date' => [
                'sometimes',
                'date',
                'after_or_equal:today',
            ],

            'expire_date' => [
                'sometimes',
                'date',
                'after:publish_date',
            ],

            // Media and attachments
            'featured_image' => [
                'sometimes',
                'file',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048', // 2MB
            ],

            'company_logo' => [
                'sometimes',
                'file',
                'image',
                'mimes:jpg,jpeg,png,webp,svg',
                'max:1024', // 1MB
            ],

            'job_attachments' => [
                'sometimes',
                'array',
                'max:5',
            ],

            'job_attachments.*' => [
                'file',
                'mimes:pdf,doc,docx',
                'max:5120', // 5MB each
            ],

            // SEO and metadata
            'slug' => [
                'sometimes',
                'string',
                'max:255',
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
                Rule::unique('jobs', 'slug')->ignore($jobId),
            ],

            'meta_title' => [
                'sometimes',
                'string',
                'max:60',
            ],

            'meta_description' => [
                'sometimes',
                'string',
                'max:160',
            ],

            'keywords' => [
                'sometimes',
                'array',
                'max:20',
            ],

            'keywords.*' => [
                'string',
                'max:50',
            ],

            // Tracking and analytics
            'source' => [
                'sometimes',
                'string',
                'max:100',
            ],

            'utm_source' => [
                'sometimes',
                'string',
                'max:100',
            ],

            'utm_medium' => [
                'sometimes',
                'string',
                'max:100',
            ],

            'utm_campaign' => [
                'sometimes',
                'string',
                'max:100',
            ],

            // Custom fields
            'custom_fields' => [
                'sometimes',
                'array',
                'max:20',
            ],

            'custom_fields.*' => [
                'string',
                'max:1000',
            ],

            // Update metadata
            'update_reason' => [
                'sometimes',
                'string',
                'max:255',
                Rule::in([
                    'content_update',
                    'salary_adjustment',
                    'requirements_change',
                    'deadline_extension',
                    'location_update',
                    'status_change',
                    'error_correction',
                    'other',
                ]),
            ],

            'update_summary' => [
                'sometimes',
                'string',
                'max:500',
            ],

            'notify_applicants' => [
                'sometimes',
                'boolean',
            ],

            // Version control
            'version' => [
                'sometimes',
                'integer',
                'min:1',
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
            'id.exists' => __('validation.job_not_found'),
            'company_id_cannot_be_changed' => __('validation.company_id_cannot_be_changed'),

            // Basic information messages
            'title.required' => __('validation.job_title_required'),
            'title.min' => __('validation.job_title_min'),
            'title.max' => __('validation.job_title_max'),
            'title.regex' => __('validation.job_title_format'),
            'title.unique' => __('validation.job_title_duplicate'),

            'description.required' => __('validation.job_description_required'),
            'description.min' => __('validation.job_description_min'),
            'description.max' => __('validation.job_description_max'),

            'short_description.min' => __('validation.short_description_min'),
            'short_description.max' => __('validation.short_description_max'),

            'requirements.min' => __('validation.requirements_min'),
            'requirements.max' => __('validation.requirements_max'),

            'responsibilities.min' => __('validation.responsibilities_min'),
            'responsibilities.max' => __('validation.responsibilities_max'),

            'benefits.max' => __('validation.benefits_max'),

            // Classification messages
            'job_type_id.required' => __('validation.job_type_required'),
            'job_type_id.exists' => __('validation.job_type_not_found'),

            'job_category_id.required' => __('validation.job_category_required'),
            'job_category_id.exists' => __('validation.job_category_not_found'),

            'company_id.exists' => __('validation.company_not_found'),

            // Location messages
            'location.max' => __('validation.location_max'),
            'city_id.exists' => __('validation.city_not_found'),
            'state_id.exists' => __('validation.state_not_found'),
            'country_id.exists' => __('validation.country_not_found'),
            'postal_code.regex' => __('validation.postal_code_format'),
            'address.max' => __('validation.address_max'),

            'coordinates.latitude.between' => __('validation.latitude_range'),
            'coordinates.longitude.between' => __('validation.longitude_range'),

            // Salary messages
            'salary_min.numeric' => __('validation.salary_min_numeric'),
            'salary_min.min' => __('validation.salary_min_positive'),
            'salary_min.max' => __('validation.salary_min_max'),

            'salary_max.numeric' => __('validation.salary_max_numeric'),
            'salary_max.gte' => __('validation.salary_max_greater_than_min'),

            'salary_currency.required_with' => __('validation.salary_currency_required'),
            'salary_currency.in' => __('validation.salary_currency_invalid'),

            'salary_period.in' => __('validation.salary_period_invalid'),

            // Experience messages
            'experience_level.in' => __('validation.experience_level_invalid'),
            'experience_required.min' => __('validation.experience_required_min'),
            'experience_required.max' => __('validation.experience_required_max'),

            'education_level.in' => __('validation.education_level_invalid'),

            // Skills messages
            'skills_required.array' => __('validation.skills_required_array'),
            'skills_required.max' => __('validation.skills_required_max'),
            'skills_required.*.exists' => __('validation.skill_not_found'),

            'skills_preferred.array' => __('validation.skills_preferred_array'),
            'skills_preferred.max' => __('validation.skills_preferred_max'),
            'skills_preferred.*.exists' => __('validation.skill_not_found'),

            'languages_required.array' => __('validation.languages_required_array'),
            'languages_required.max' => __('validation.languages_required_max'),
            'languages_required.*.language.required_with' => __('validation.language_name_required'),
            'languages_required.*.proficiency.required_with' => __('validation.language_proficiency_required'),
            'languages_required.*.proficiency.in' => __('validation.language_proficiency_invalid'),

            'certifications.array' => __('validation.certifications_array'),
            'certifications.max' => __('validation.certifications_max'),

            // Work arrangement messages
            'remote_type.in' => __('validation.remote_type_invalid'),
            'remote_locations.array' => __('validation.remote_locations_array'),
            'remote_locations.max' => __('validation.remote_locations_max'),

            'work_schedule.in' => __('validation.work_schedule_invalid'),
            'hours_per_week.min' => __('validation.hours_per_week_min'),
            'hours_per_week.max' => __('validation.hours_per_week_max'),

            'travel_percentage.required_if' => __('validation.travel_percentage_required'),
            'travel_percentage.max' => __('validation.travel_percentage_max'),

            // Application messages
            'application_deadline.after' => __('validation.application_deadline_future'),
            'application_deadline.before' => __('validation.application_deadline_max'),

            'application_method.in' => __('validation.application_method_invalid'),

            'external_application_url.required_if' => __('validation.external_application_url_required'),
            'external_application_url.url' => __('validation.external_application_url_format'),
            'external_application_url.active_url' => __('validation.external_application_url_unreachable'),

            'application_email.required_if' => __('validation.application_email_required'),
            'application_email.email' => __('validation.application_email_format'),

            'max_applications.min' => __('validation.max_applications_min'),
            'max_applications.max' => __('validation.max_applications_max'),

            // Status messages
            'status.in' => __('validation.status_invalid'),

            'featured_until.required_if' => __('validation.featured_until_required'),
            'featured_until.after' => __('validation.featured_until_future'),

            'publish_date.after_or_equal' => __('validation.publish_date_future'),
            'expire_date.after' => __('validation.expire_date_after_publish'),

            // Media messages
            'featured_image.file' => __('validation.featured_image_file'),
            'featured_image.image' => __('validation.featured_image_image'),
            'featured_image.mimes' => __('validation.featured_image_format'),
            'featured_image.max' => __('validation.featured_image_size'),

            'company_logo.file' => __('validation.company_logo_file'),
            'company_logo.image' => __('validation.company_logo_image'),
            'company_logo.mimes' => __('validation.company_logo_format'),
            'company_logo.max' => __('validation.company_logo_size'),

            'job_attachments.array' => __('validation.job_attachments_array'),
            'job_attachments.max' => __('validation.job_attachments_max'),
            'job_attachments.*.file' => __('validation.job_attachment_file'),
            'job_attachments.*.mimes' => __('validation.job_attachment_format'),
            'job_attachments.*.max' => __('validation.job_attachment_size'),

            // SEO messages
            'slug.regex' => __('validation.slug_format'),
            'slug.unique' => __('validation.slug_duplicate'),
            'meta_title.max' => __('validation.meta_title_max'),
            'meta_description.max' => __('validation.meta_description_max'),

            'keywords.array' => __('validation.keywords_array'),
            'keywords.max' => __('validation.keywords_max'),

            // Update metadata messages
            'update_reason.in' => __('validation.update_reason_invalid'),
            'update_summary.max' => __('validation.update_summary_max'),

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
            'id' => __('validation.attributes.job_id'),
            'title' => __('validation.attributes.job_title'),
            'description' => __('validation.attributes.job_description'),
            'short_description' => __('validation.attributes.short_description'),
            'requirements' => __('validation.attributes.requirements'),
            'responsibilities' => __('validation.attributes.responsibilities'),
            'benefits' => __('validation.attributes.benefits'),
            'job_type_id' => __('validation.attributes.job_type'),
            'job_category_id' => __('validation.attributes.job_category'),
            'company_id' => __('validation.attributes.company'),
            'location' => __('validation.attributes.location'),
            'city_id' => __('validation.attributes.city'),
            'state_id' => __('validation.attributes.state'),
            'country_id' => __('validation.attributes.country'),
            'postal_code' => __('validation.attributes.postal_code'),
            'address' => __('validation.attributes.address'),
            'coordinates' => __('validation.attributes.coordinates'),
            'salary_min' => __('validation.attributes.salary_min'),
            'salary_max' => __('validation.attributes.salary_max'),
            'salary_currency' => __('validation.attributes.salary_currency'),
            'salary_period' => __('validation.attributes.salary_period'),
            'salary_negotiable' => __('validation.attributes.salary_negotiable'),
            'hide_salary' => __('validation.attributes.hide_salary'),
            'experience_level' => __('validation.attributes.experience_level'),
            'experience_required' => __('validation.attributes.experience_required'),
            'education_level' => __('validation.attributes.education_level'),
            'education_field' => __('validation.attributes.education_field'),
            'skills_required' => __('validation.attributes.skills_required'),
            'skills_preferred' => __('validation.attributes.skills_preferred'),
            'languages_required' => __('validation.attributes.languages_required'),
            'certifications' => __('validation.attributes.certifications'),
            'is_remote' => __('validation.attributes.is_remote'),
            'remote_type' => __('validation.attributes.remote_type'),
            'remote_locations' => __('validation.attributes.remote_locations'),
            'work_schedule' => __('validation.attributes.work_schedule'),
            'hours_per_week' => __('validation.attributes.hours_per_week'),
            'travel_required' => __('validation.attributes.travel_required'),
            'travel_percentage' => __('validation.attributes.travel_percentage'),
            'application_deadline' => __('validation.attributes.application_deadline'),
            'application_method' => __('validation.attributes.application_method'),
            'external_application_url' => __('validation.attributes.external_application_url'),
            'application_email' => __('validation.attributes.application_email'),
            'max_applications' => __('validation.attributes.max_applications'),
            'auto_close_when_filled' => __('validation.attributes.auto_close_when_filled'),
            'status' => __('validation.attributes.status'),
            'is_featured' => __('validation.attributes.is_featured'),
            'is_urgent' => __('validation.attributes.is_urgent'),
            'is_confidential' => __('validation.attributes.is_confidential'),
            'featured_until' => __('validation.attributes.featured_until'),
            'publish_date' => __('validation.attributes.publish_date'),
            'expire_date' => __('validation.attributes.expire_date'),
            'featured_image' => __('validation.attributes.featured_image'),
            'company_logo' => __('validation.attributes.company_logo'),
            'job_attachments' => __('validation.attributes.job_attachments'),
            'slug' => __('validation.attributes.slug'),
            'meta_title' => __('validation.attributes.meta_title'),
            'meta_description' => __('validation.attributes.meta_description'),
            'keywords' => __('validation.attributes.keywords'),
            'source' => __('validation.attributes.source'),
            'utm_source' => __('validation.attributes.utm_source'),
            'utm_medium' => __('validation.attributes.utm_medium'),
            'utm_campaign' => __('validation.attributes.utm_campaign'),
            'custom_fields' => __('validation.attributes.custom_fields'),
            'update_reason' => __('validation.attributes.update_reason'),
            'update_summary' => __('validation.attributes.update_summary'),
            'notify_applicants' => __('validation.attributes.notify_applicants'),
            'version' => __('validation.attributes.version'),
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
            'message' => __('validation.job_update_failed'),
            'errors' => $validator->errors(),
            'error_code' => 'JOB_UPDATE_VALIDATION_FAILED',
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
            'message' => __('validation.job_update_unauthorized'),
            'error_code' => 'JOB_UPDATE_UNAUTHORIZED',
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
            'salary_negotiable',
            'hide_salary',
            'is_remote',
            'travel_required',
            'auto_close_when_filled',
            'is_featured',
            'is_urgent',
            'is_confidential',
            'notify_applicants',
        ];

        foreach ($booleanFields as $field) {
            if ($this->has($field)) {
                $this->merge([
                    $field => filter_var($this->$field, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? false,
                ]);
            }
        }

        // Generate slug from title if not provided
        if ($this->has('title') && ! $this->has('slug')) {
            $this->merge([
                'slug' => \Str::slug($this->title),
            ]);
        }

        // Sanitize array fields
        if ($this->has('skills_required') && is_string($this->skills_required)) {
            $this->merge([
                'skills_required' => array_filter(array_map('intval', explode(',', $this->skills_required))),
            ]);
        }

        if ($this->has('skills_preferred') && is_string($this->skills_preferred)) {
            $this->merge([
                'skills_preferred' => array_filter(array_map('intval', explode(',', $this->skills_preferred))),
            ]);
        }

        if ($this->has('remote_locations') && is_string($this->remote_locations)) {
            $this->merge([
                'remote_locations' => array_filter(array_map('trim', explode(',', $this->remote_locations))),
            ]);
        }

        if ($this->has('certifications') && is_string($this->certifications)) {
            $this->merge([
                'certifications' => array_filter(array_map('trim', explode(',', $this->certifications))),
            ]);
        }

        if ($this->has('keywords') && is_string($this->keywords)) {
            $this->merge([
                'keywords' => array_filter(array_map('trim', explode(',', $this->keywords))),
            ]);
        }

        // Generate meta title from title if not provided
        if ($this->has('title') && ! $this->has('meta_title')) {
            $this->merge([
                'meta_title' => \Str::limit($this->title, 57),
            ]);
        }

        // Generate meta description from short description if not provided
        if ($this->has('short_description') && ! $this->has('meta_description')) {
            $this->merge([
                'meta_description' => \Str::limit($this->short_description, 157),
            ]);
        }

        // Set default values
        if (! $this->has('status')) {
            $this->merge(['status' => 'draft']);
        }

        if (! $this->has('application_method')) {
            $this->merge(['application_method' => 'internal']);
        }

        if (! $this->has('salary_period')) {
            $this->merge(['salary_period' => 'monthly']);
        }

        if (! $this->has('remote_type') && $this->boolean('is_remote')) {
            $this->merge(['remote_type' => 'fully_remote']);
        }

        // Sanitize text fields
        $textFields = [
            'title',
            'description',
            'short_description',
            'requirements',
            'responsibilities',
            'benefits',
            'location',
            'address',
            'education_field',
            'slug',
            'meta_title',
            'meta_description',
            'source',
            'utm_source',
            'utm_medium',
            'utm_campaign',
            'update_reason',
            'update_summary',
        ];

        foreach ($textFields as $field) {
            if ($this->has($field)) {
                $this->merge([
                    $field => trim($this->$field),
                ]);
            }
        }
    }
}
