<?php

namespace App\Http\Requests\BusinessLogic;

use Illuminate\Support\Facades\DB;

/**
 * 🏢 **ENTERPRISE JOB CREATION REQUEST VALIDATION**
 *
 * **Purpose**: Comprehensive validation for job posting creation with enterprise-grade security
 * **Domain**: Business Logic - Core job management operations
 * **Security Level**: HIGH - Critical business data validation
 * **Languages**: 12+ supported with contextual error messages
 *
 * **Key Features**:
 * - Multi-layered validation (syntax, business rules, security)
 * - Advanced data sanitization and XSS prevention
 * - Real-time performance monitoring (<50ms target)
 * - Comprehensive audit logging for compliance
 * - Context-aware multilingual error messages
 *
 * **Business Impact**:
 * - Ensures data integrity for job postings
 * - Prevents malicious content injection
 * - Supports international job market requirements
 * - Enables comprehensive business analytics
 *
 * @version 2.0.0 - Enterprise Edition
 *
 * @author Laravel Job Portal Team
 *
 * @since 2024-12-28
 */
class CreateJobRequest extends BusinessLogicRequest
{
    /**
     * Security level for job creation operations
     * HIGH level enables comprehensive validation and audit logging
     */
    protected string $securityLevel = 'high';

    /**
     * Business domain context for validation rules
     */
    protected string $businessDomain = 'job_management';

    /**
     * Performance monitoring configuration
     */
    protected array $performanceConfig = [
        'enable_monitoring' => true,
        'max_execution_time_ms' => 50,
        'memory_limit_mb' => 10,
        'enable_query_monitoring' => true,
    ];

    /**
     * Authentication authorization check
     * Since authentication is disabled system-wide, always authorize
     */
    public function authorize(): bool
    {
        // Authentication system removed - universal access
        // Log the job creation attempt for audit purposes
        $this->logSecurityEvent('job_creation_attempted', [
            'ip_address' => $this->ip(),
            'user_agent' => $this->header('User-Agent'),
            'timestamp' => now(),
            'request_data_size' => strlen(json_encode($this->all())),
        ]);

        return true;
    }

    /**
     * Get comprehensive validation rules for job creation
     * Implements multi-layered validation strategy
     */
    public function rules(): array
    {
        return array_merge(
            $this->getBasicJobRules(),
            $this->getLocationRules(),
            $this->getContentRules(),
            $this->getSalaryRules(),
            $this->getApplicationRules(),
            $this->getSkillsRules(),
            $this->getSecurityValidationRules(),
            $this->getBusinessLogicRules()
        );
    }

    /**
     * Get basic job information validation rules
     */
    protected function getBasicJobRules(): array
    {
        return [
            'job_title' => [
                'required',
                'string',
                'min:3',
                'max:255',
                'regex:/^[a-zA-Z0-9\s\-\+\&\(\)\/\.\,\'\"]+$/u',
            ],
            'company_id' => [
                'required',
                'integer',
                'exists:companies,id,deleted_at,NULL',
                'min:1',
            ],
            'category_id' => [
                'required',
                'integer',
                'exists:job_categories,id,is_active,1',
                'min:1',
            ],
            'job_type_id' => [
                'required',
                'integer',
                'exists:job_types,id,is_active,1',
                'min:1',
            ],
            'status' => [
                'required',
                'string',
                'in:draft,pending_review,active,paused,expired,closed',
            ],
            'visibility' => [
                'required',
                'string',
                'in:public,private,company_only',
            ],
        ];
    }

    /**
     * Get location and remote work validation rules
     */
    protected function getLocationRules(): array
    {
        return [
            'location' => [
                'required',
                'string',
                'min:2',
                'max:255',
                'regex:/^[a-zA-Z0-9\s\-\,\.\']+$/u',
            ],
            'country_id' => [
                'nullable',
                'integer',
                'exists:countries,id,is_active,1',
            ],
            'state_id' => [
                'nullable',
                'integer',
                'exists:states,id,is_active,1',
            ],
            'city_id' => [
                'nullable',
                'integer',
                'exists:cities,id,is_active,1',
            ],
            'is_remote' => ['boolean'],
            'remote_type' => [
                'nullable',
                'string',
                'in:fully_remote,hybrid,on_site',
                'required_if:is_remote,true',
            ],
        ];
    }

    /**
     * Get content validation rules
     */
    protected function getContentRules(): array
    {
        return [
            'description' => [
                'required',
                'string',
                'min:100',
                'max:10000',
            ],
            'requirements' => [
                'required',
                'string',
                'min:50',
                'max:5000',
            ],
            'benefits' => [
                'nullable',
                'string',
                'max:3000',
            ],
            'responsibilities' => [
                'nullable',
                'string',
                'max:5000',
            ],
        ];
    }

    /**
     * Get salary validation rules
     */
    protected function getSalaryRules(): array
    {
        return [
            'salary_min' => [
                'nullable',
                'numeric',
                'min:0',
                'max:10000000',
            ],
            'salary_max' => [
                'nullable',
                'numeric',
                'min:0',
                'max:10000000',
                'gte:salary_min',
            ],
            'salary_currency' => [
                'nullable',
                'string',
                'size:3',
                'exists:currencies,code,is_active,1',
                'required_with:salary_min,salary_max',
            ],
            'salary_period_id' => [
                'nullable',
                'integer',
                'exists:salary_periods,id,is_active,1',
                'required_with:salary_min,salary_max',
            ],
            'salary_negotiable' => [
                'boolean',
            ],
            'hide_salary' => [
                'boolean',
            ],
        ];
    }

    /**
     * Get application validation rules
     */
    protected function getApplicationRules(): array
    {
        return [
            'application_deadline' => [
                'required',
                'date',
                'after:today',
                'before:'.now()->addYear()->format('Y-m-d'),
            ],
            'application_email' => [
                'nullable',
                'email:rfc,dns',
                'max:255',
            ],
            'application_url' => [
                'nullable',
                'url',
                'max:500',
            ],
            'how_to_apply' => [
                'nullable',
                'string',
                'max:1000',
            ],
        ];
    }

    /**
     * Get skills validation rules
     */
    protected function getSkillsRules(): array
    {
        return [
            'required_skills' => [
                'nullable',
                'array',
                'max:20',
            ],
            'required_skills.*' => [
                'integer',
                'exists:skills,id,is_active,1',
            ],
            'preferred_skills' => [
                'nullable',
                'array',
                'max:15',
            ],
            'preferred_skills.*' => [
                'integer',
                'exists:skills,id,is_active,1',
            ],
            'tags' => [
                'nullable',
                'array',
                'max:10',
            ],
            'tags.*' => [
                'string',
                'max:50',
                'regex:/^[a-zA-Z0-9\s\-\_]+$/',
            ],
        ];
    }

    /**
     * Get business logic specific validation rules
     */
    protected function getBusinessLogicRules(): array
    {
        return [
            'company_id' => [
                'required',
                function ($attribute, $value, $fail) {
                    $company = DB::table('companies')
                        ->where('id', $value)
                        ->where('is_verified', true)
                        ->first();

                    if (! $company) {
                        $fail(__('validation.job.company_must_be_verified'));
                    }
                },
            ],
        ];
    }

    /**
     * Apply job-specific data sanitization
     */
    protected function applySanitization(array $data): array
    {
        $sanitized = parent::applySanitization($data);

        // Job title sanitization
        if (isset($sanitized['job_title'])) {
            $sanitized['job_title'] = $this->sanitizeJobTitle($sanitized['job_title']);
        }

        // Description and requirements HTML sanitization
        if (isset($sanitized['description'])) {
            $sanitized['description'] = $this->sanitizeRichText($sanitized['description']);
        }

        if (isset($sanitized['requirements'])) {
            $sanitized['requirements'] = $this->sanitizeRichText($sanitized['requirements']);
        }

        // Location standardization
        if (isset($sanitized['location'])) {
            $sanitized['location'] = $this->standardizeLocation($sanitized['location']);
        }

        // Skills array sanitization
        if (isset($sanitized['required_skills']) && is_array($sanitized['required_skills'])) {
            $sanitized['required_skills'] = array_unique(array_map('intval', $sanitized['required_skills']));
        }

        if (isset($sanitized['preferred_skills']) && is_array($sanitized['preferred_skills'])) {
            $sanitized['preferred_skills'] = array_unique(array_map('intval', $sanitized['preferred_skills']));
        }

        // Tags sanitization
        if (isset($sanitized['tags']) && is_array($sanitized['tags'])) {
            $sanitized['tags'] = array_unique(array_map(function ($tag) {
                return strtolower(trim($tag));
            }, $sanitized['tags']));
        }

        return $sanitized;
    }

    /**
     * Sanitize job title for professional formatting
     */
    private function sanitizeJobTitle(string $title): string
    {
        // Remove excessive whitespace
        $title = preg_replace('/\s+/', ' ', trim($title));

        // Capitalize appropriately (preserve existing case for acronyms)
        $words = explode(' ', $title);
        $result = [];

        foreach ($words as $word) {
            if (ctype_upper($word) && strlen($word) <= 5) {
                // Preserve acronyms (CEO, CTO, etc.)
                $result[] = $word;
            } else {
                $result[] = ucwords(strtolower($word));
            }
        }

        return implode(' ', $result);
    }

    /**
     * Sanitize rich text content
     */
    private function sanitizeRichText(string $content): string
    {
        // Remove potentially dangerous HTML tags
        $allowedTags = '<p><br><strong><em><ul><ol><li><h3><h4>';
        $content = strip_tags($content, $allowedTags);

        // Remove excessive line breaks
        $content = preg_replace('/(\n\s*){3,}/', "\n\n", $content);

        return trim($content);
    }

    /**
     * Standardize location format
     */
    private function standardizeLocation(string $location): string
    {
        // Remove excessive whitespace and standardize format
        $location = preg_replace('/\s+/', ' ', trim($location));

        // Capitalize each word
        return ucwords(strtolower($location));
    }

    /**
     * Get comprehensive multilingual error messages
     */
    public function messages(): array
    {
        return [
            // Job Basic Information
            'job_title.required' => __('validation.job.title_required'),
            'job_title.min' => __('validation.job.title_too_short'),
            'job_title.max' => __('validation.job.title_too_long'),
            'job_title.regex' => __('validation.job.title_invalid_characters'),

            'company_id.required' => __('validation.job.company_required'),
            'company_id.exists' => __('validation.job.company_not_found'),

            'category_id.required' => __('validation.job.category_required'),
            'category_id.exists' => __('validation.job.category_not_found'),

            'job_type_id.required' => __('validation.job.job_type_required'),
            'job_type_id.exists' => __('validation.job.job_type_not_found'),

            // Location
            'location.required' => __('validation.job.location_required'),
            'location.min' => __('validation.job.location_too_short'),
            'location.max' => __('validation.job.location_too_long'),
            'location.regex' => __('validation.job.location_invalid_format'),

            // Remote work
            'remote_type.required_if' => __('validation.job.remote_type_required'),
            'remote_type.in' => __('validation.job.remote_type_invalid'),

            // Content
            'description.required' => __('validation.job.description_required'),
            'description.min' => __('validation.job.description_too_short'),
            'description.max' => __('validation.job.description_too_long'),

            'requirements.required' => __('validation.job.requirements_required'),
            'requirements.min' => __('validation.job.requirements_too_short'),
            'requirements.max' => __('validation.job.requirements_too_long'),

            // Salary
            'salary_min.numeric' => __('validation.job.salary_min_numeric'),
            'salary_min.min' => __('validation.job.salary_min_positive'),
            'salary_max.numeric' => __('validation.job.salary_max_numeric'),
            'salary_max.gte' => __('validation.job.salary_max_greater_than_min'),
            'salary_currency.required_with' => __('validation.job.salary_currency_required'),
            'salary_currency.exists' => __('validation.job.salary_currency_invalid'),
            'salary_period_id.required_with' => __('validation.job.salary_period_required'),

            // Application
            'application_deadline.required' => __('validation.job.deadline_required'),
            'application_deadline.after' => __('validation.job.deadline_future'),
            'application_deadline.before' => __('validation.job.deadline_too_far'),
            'application_email.email' => __('validation.job.application_email_invalid'),
            'application_url.url' => __('validation.job.application_url_invalid'),

            // Status
            'status.required' => __('validation.job.status_required'),
            'status.in' => __('validation.job.status_invalid'),
            'visibility.required' => __('validation.job.visibility_required'),
            'visibility.in' => __('validation.job.visibility_invalid'),

            // Skills and tags
            'required_skills.max' => __('validation.job.too_many_required_skills'),
            'preferred_skills.max' => __('validation.job.too_many_preferred_skills'),
            'tags.max' => __('validation.job.too_many_tags'),
            'tags.*.regex' => __('validation.job.tag_invalid_format'),
        ];
    }

    /**
     * Get custom attribute names for multilingual support
     */
    public function attributes(): array
    {
        return [
            'job_title' => __('fields.job_title'),
            'company_id' => __('fields.company'),
            'category_id' => __('fields.category'),
            'job_type_id' => __('fields.job_type'),
            'location' => __('fields.location'),
            'description' => __('fields.description'),
            'requirements' => __('fields.requirements'),
            'benefits' => __('fields.benefits'),
            'responsibilities' => __('fields.responsibilities'),
            'salary_min' => __('fields.salary_min'),
            'salary_max' => __('fields.salary_max'),
            'salary_currency' => __('fields.salary_currency'),
            'experience_level_id' => __('fields.experience_level'),
            'education_level_id' => __('fields.education_level'),
            'application_deadline' => __('fields.application_deadline'),
            'application_email' => __('fields.application_email'),
            'application_url' => __('fields.application_url'),
            'status' => __('fields.status'),
            'visibility' => __('fields.visibility'),
            'contact_person' => __('fields.contact_person'),
            'contact_phone' => __('fields.contact_phone'),
            'required_skills' => __('fields.required_skills'),
            'preferred_skills' => __('fields.preferred_skills'),
            'tags' => __('fields.tags'),
        ];
    }

    /**
     * Handle validation completion
     */
    protected function passedValidation(): void
    {
        parent::passedValidation();

        // Log successful validation for audit trail
        $this->logSecurityEvent('job_creation_validated', [
            'job_title' => $this->job_title,
            'company_id' => $this->company_id,
            'validation_time_ms' => $this->getValidationPerformanceMetrics()['duration_ms'],
            'ip_address' => $this->ip(),
        ]);

        // Set defaults for optional fields
        $this->merge([
            'is_remote' => $this->boolean('is_remote', false),
            'is_featured' => $this->boolean('is_featured', false),
            'is_urgent' => $this->boolean('is_urgent', false),
            'salary_negotiable' => $this->boolean('salary_negotiable', false),
            'hide_salary' => $this->boolean('hide_salary', false),
            'auto_renew' => $this->boolean('auto_renew', false),
            'boost_views' => $this->boolean('boost_views', false),
            'source' => $this->source ?? 'manual',
            'visibility' => $this->visibility ?? 'public',
        ]);
    }
}
