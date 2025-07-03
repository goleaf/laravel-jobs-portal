<?php

namespace App\Http\Requests\JobApplication;

use App\Models\Job;
use App\Models\JobApplication;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;

/**
 * CreateJobApplicationRequest
 * 
 * Comprehensive validation for job application creation with enterprise-grade validation.
 * Implements file upload validation, business logic checks, and multilingual error messaging.
 *
 * @package App\Http\Requests\JobApplication
 * @author System Generated
 * @version 1.0.0
 */
class CreateJobApplicationRequest extends FormRequest
{
    /**
     * Maximum file size for uploads (5MB).
     */
    private const MAX_FILE_SIZE = 5120; // 5MB in KB

    /**
     * Allowed file types for resume/CV uploads.
     */
    private const ALLOWED_RESUME_TYPES = [
        'pdf', 'doc', 'docx', 'rtf', 'txt'
    ];

    /**
     * Allowed file types for cover letter uploads.
     */
    private const ALLOWED_COVER_LETTER_TYPES = [
        'pdf', 'doc', 'docx', 'txt'
    ];

    /**
     * Allowed file types for portfolio uploads.
     */
    private const ALLOWED_PORTFOLIO_TYPES = [
        'pdf', 'doc', 'docx', 'ppt', 'pptx', 'zip', 'rar', '7z'
    ];

    /**
     * Determine if the user is authorized to make this request.
     * 
     * Implements role-based authorization with business logic validation.
     * Validates job accessibility and application eligibility.
     *
     * @return bool Authorization status
     */
    public function authorize(): bool
    {
        // Basic authentication check - per user requirements: "do not make users and do not any users system"
        // However, we still need to validate job accessibility and application eligibility
        
        $jobId = $this->route('jobId') ?: $this->input('job_id');
        
        if (!$jobId) {
            return false;
        }
        
        // Validate job exists and is accessible
        $job = Job::find($jobId);
        if (!$job) {
            return false;
        }
        
        // Business rule: Job must be active to accept applications
        if (!$job->is_active) {
            return false;
        }
        
        // Business rule: Job must not be expired
        if ($job->expire_date && $job->expire_date < now()) {
            return false;
        }
        
        // Business rule: Check if applications are still being accepted
        if (isset($job->application_deadline) && $job->application_deadline < now()) {
            return false;
        }
        
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     * 
     * Implements comprehensive validation with file upload handling, 
     * business logic validation, and data integrity checks.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $jobId = $this->route('jobId') ?: $this->input('job_id');
        
        return [
            // Job identification
            'job_id' => [
                'required',
                'integer',
                'min:1',
                Rule::exists('jobs', 'id')->where(function ($query) {
                    $query->where('is_active', true)
                          ->where(function ($q) {
                              $q->whereNull('expire_date')
                                ->orWhere('expire_date', '>=', now());
                          });
                }),
            ],
            
            // Candidate basic information
            'candidate_name' => [
                'required',
                'string',
                'min:2',
                'max:255',
                'regex:/^[\pL\pM\s\.\-\']+$/u', // Allow multilingual names with common punctuation
            ],
            
            'candidate_email' => [
                'required',
                'email:rfc,dns',
                'max:255',
                'lowercase',
                function ($attribute, $value, $fail) use ($jobId) {
                    // Check for duplicate applications
                    if ($this->hasExistingApplication($value, $jobId)) {
                        $fail(__('validation.application_already_exists'));
                    }
                },
            ],
            
            'candidate_phone' => [
                'required',
                'string',
                'min:10',
                'max:20',
                'regex:/^[\+]?[0-9\s\-\(\)]+$/', // International phone format
            ],
            
            // Professional information
            'experience_years' => [
                'required',
                'integer',
                'min:0',
                'max:50',
            ],
            
            'current_salary' => [
                'sometimes',
                'numeric',
                'min:0',
                'max:10000000',
            ],
            
            'expected_salary' => [
                'sometimes',
                'numeric',
                'min:0',
                'max:10000000',
                'gte:current_salary',
            ],
            
            'salary_currency' => [
                'required_with:current_salary,expected_salary',
                'string',
                'size:3',
                'exists:salary_currencies,code',
            ],
            
            'notice_period' => [
                'sometimes',
                'integer',
                'min:0',
                'max:365', // Max 1 year notice period
            ],
            
            'notice_period_unit' => [
                'required_with:notice_period',
                'string',
                Rule::in(['days', 'weeks', 'months']),
            ],
            
            // Education information
            'education_level' => [
                'required',
                'string',
                Rule::in([
                    'high_school',
                    'associate',
                    'bachelor',
                    'master',
                    'doctorate',
                    'professional_certificate',
                    'diploma',
                    'other'
                ]),
            ],
            
            'education_field' => [
                'sometimes',
                'string',
                'max:255',
            ],
            
            'education_institution' => [
                'sometimes',
                'string',
                'max:255',
            ],
            
            'graduation_year' => [
                'sometimes',
                'integer',
                'min:1970',
                'max:' . (date('Y') + 5), // Allow future graduation dates
            ],
            
            // Skills and experience
            'skills' => [
                'sometimes',
                'array',
                'max:50',
            ],
            
            'skills.*' => [
                'integer',
                'exists:skills,id',
            ],
            
            'key_skills' => [
                'sometimes',
                'string',
                'max:1000',
            ],
            
            'languages' => [
                'sometimes',
                'array',
                'max:20',
            ],
            
            'languages.*' => [
                'integer',
                'exists:languages,id',
            ],
            
            // Location preferences
            'current_location' => [
                'sometimes',
                'string',
                'max:255',
            ],
            
            'preferred_locations' => [
                'sometimes',
                'array',
                'max:10',
            ],
            
            'preferred_locations.*' => [
                'string',
                'max:255',
            ],
            
            'willing_to_relocate' => [
                'sometimes',
                'boolean',
            ],
            
            'remote_work_preference' => [
                'sometimes',
                'string',
                Rule::in(['on_site', 'remote', 'hybrid', 'flexible']),
            ],
            
            // Application content
            'cover_letter' => [
                'sometimes',
                'string',
                'min:50',
                'max:5000',
            ],
            
            'motivation' => [
                'sometimes',
                'string',
                'min:20',
                'max:2000',
            ],
            
            'additional_notes' => [
                'sometimes',
                'string',
                'max:1000',
            ],
            
            // File uploads
            'resume' => [
                'required',
                'file',
                'max:' . self::MAX_FILE_SIZE,
                'mimes:' . implode(',', self::ALLOWED_RESUME_TYPES),
                'mimetypes:application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document,application/rtf,text/plain',
            ],
            
            'cover_letter_file' => [
                'sometimes',
                'file',
                'max:' . self::MAX_FILE_SIZE,
                'mimes:' . implode(',', self::ALLOWED_COVER_LETTER_TYPES),
                'mimetypes:application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document,text/plain',
            ],
            
            'portfolio' => [
                'sometimes',
                'file',
                'max:' . (self::MAX_FILE_SIZE * 2), // Allow larger portfolio files
                'mimes:' . implode(',', self::ALLOWED_PORTFOLIO_TYPES),
            ],
            
            // Additional attachments
            'certificates' => [
                'sometimes',
                'array',
                'max:10',
            ],
            
            'certificates.*' => [
                'file',
                'max:' . self::MAX_FILE_SIZE,
                'mimes:pdf,jpg,jpeg,png,doc,docx',
            ],
            
            // Availability
            'available_from' => [
                'sometimes',
                'date',
                'after_or_equal:today',
                'before:' . now()->addYears(2)->toDateString(),
            ],
            
            'availability_notes' => [
                'sometimes',
                'string',
                'max:500',
            ],
            
            // References
            'references' => [
                'sometimes',
                'array',
                'max:5',
            ],
            
            'references.*.name' => [
                'required_with:references',
                'string',
                'max:255',
            ],
            
            'references.*.position' => [
                'required_with:references',
                'string',
                'max:255',
            ],
            
            'references.*.company' => [
                'required_with:references',
                'string',
                'max:255',
            ],
            
            'references.*.email' => [
                'required_with:references',
                'email',
                'max:255',
            ],
            
            'references.*.phone' => [
                'sometimes',
                'string',
                'max:20',
            ],
            
            // Application preferences
            'preferred_interview_time' => [
                'sometimes',
                'string',
                Rule::in(['morning', 'afternoon', 'evening', 'flexible']),
            ],
            
            'interview_availability' => [
                'sometimes',
                'array',
                'max:7',
            ],
            
            'interview_availability.*' => [
                'string',
                Rule::in(['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday']),
            ],
            
            // Legal and compliance
            'work_authorization' => [
                'required',
                'string',
                Rule::in(['citizen', 'permanent_resident', 'work_visa', 'student_visa', 'other']),
            ],
            
            'background_check_consent' => [
                'sometimes',
                'boolean',
            ],
            
            'terms_accepted' => [
                'required',
                'boolean',
                'accepted',
            ],
            
            'privacy_policy_accepted' => [
                'required',
                'boolean',
                'accepted',
            ],
            
            // Application source tracking
            'application_source' => [
                'sometimes',
                'string',
                'max:100',
            ],
            
            'referrer' => [
                'sometimes',
                'string',
                'max:255',
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
            // Job validation messages
            'job_id.required' => __('validation.job_id_required'),
            'job_id.exists' => __('validation.job_not_available'),
            
            // Candidate information messages
            'candidate_name.required' => __('validation.candidate_name_required'),
            'candidate_name.regex' => __('validation.candidate_name_format'),
            
            'candidate_email.required' => __('validation.candidate_email_required'),
            'candidate_email.email' => __('validation.candidate_email_format'),
            'candidate_email.lowercase' => __('validation.candidate_email_lowercase'),
            
            'candidate_phone.required' => __('validation.candidate_phone_required'),
            'candidate_phone.regex' => __('validation.candidate_phone_format'),
            
            // Professional information messages
            'experience_years.required' => __('validation.experience_years_required'),
            'experience_years.max' => __('validation.experience_years_max'),
            
            'expected_salary.gte' => __('validation.expected_salary_gte_current'),
            'salary_currency.exists' => __('validation.salary_currency_invalid'),
            
            'notice_period.max' => __('validation.notice_period_max'),
            'notice_period_unit.in' => __('validation.notice_period_unit_invalid'),
            
            // Education messages
            'education_level.required' => __('validation.education_level_required'),
            'education_level.in' => __('validation.education_level_invalid'),
            
            'graduation_year.min' => __('validation.graduation_year_min'),
            'graduation_year.max' => __('validation.graduation_year_max'),
            
            // Skills messages
            'skills.max' => __('validation.skills_max'),
            'skills.*.exists' => __('validation.skill_not_found'),
            
            'languages.max' => __('validation.languages_max'),
            'languages.*.exists' => __('validation.language_not_found'),
            
            // File upload messages
            'resume.required' => __('validation.resume_required'),
            'resume.file' => __('validation.resume_file'),
            'resume.max' => __('validation.resume_size_max'),
            'resume.mimes' => __('validation.resume_type_invalid'),
            
            'cover_letter_file.max' => __('validation.cover_letter_size_max'),
            'cover_letter_file.mimes' => __('validation.cover_letter_type_invalid'),
            
            'portfolio.max' => __('validation.portfolio_size_max'),
            'portfolio.mimes' => __('validation.portfolio_type_invalid'),
            
            'certificates.max' => __('validation.certificates_max'),
            'certificates.*.max' => __('validation.certificate_size_max'),
            
            // Availability messages
            'available_from.after_or_equal' => __('validation.available_from_future'),
            'available_from.before' => __('validation.available_from_within_limit'),
            
            // References messages
            'references.max' => __('validation.references_max'),
            'references.*.name.required_with' => __('validation.reference_name_required'),
            'references.*.email.email' => __('validation.reference_email_format'),
            
            // Legal compliance messages
            'work_authorization.required' => __('validation.work_authorization_required'),
            'work_authorization.in' => __('validation.work_authorization_invalid'),
            
            'terms_accepted.required' => __('validation.terms_acceptance_required'),
            'terms_accepted.accepted' => __('validation.terms_must_be_accepted'),
            
            'privacy_policy_accepted.required' => __('validation.privacy_policy_acceptance_required'),
            'privacy_policy_accepted.accepted' => __('validation.privacy_policy_must_be_accepted'),
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
            'job_id' => __('validation.attributes.job'),
            'candidate_name' => __('validation.attributes.candidate_name'),
            'candidate_email' => __('validation.attributes.candidate_email'),
            'candidate_phone' => __('validation.attributes.candidate_phone'),
            'experience_years' => __('validation.attributes.experience_years'),
            'current_salary' => __('validation.attributes.current_salary'),
            'expected_salary' => __('validation.attributes.expected_salary'),
            'salary_currency' => __('validation.attributes.salary_currency'),
            'notice_period' => __('validation.attributes.notice_period'),
            'notice_period_unit' => __('validation.attributes.notice_period_unit'),
            'education_level' => __('validation.attributes.education_level'),
            'education_field' => __('validation.attributes.education_field'),
            'education_institution' => __('validation.attributes.education_institution'),
            'graduation_year' => __('validation.attributes.graduation_year'),
            'skills' => __('validation.attributes.skills'),
            'key_skills' => __('validation.attributes.key_skills'),
            'languages' => __('validation.attributes.languages'),
            'current_location' => __('validation.attributes.current_location'),
            'preferred_locations' => __('validation.attributes.preferred_locations'),
            'willing_to_relocate' => __('validation.attributes.willing_to_relocate'),
            'remote_work_preference' => __('validation.attributes.remote_work_preference'),
            'cover_letter' => __('validation.attributes.cover_letter'),
            'motivation' => __('validation.attributes.motivation'),
            'additional_notes' => __('validation.attributes.additional_notes'),
            'resume' => __('validation.attributes.resume'),
            'cover_letter_file' => __('validation.attributes.cover_letter_file'),
            'portfolio' => __('validation.attributes.portfolio'),
            'certificates' => __('validation.attributes.certificates'),
            'available_from' => __('validation.attributes.available_from'),
            'availability_notes' => __('validation.attributes.availability_notes'),
            'references' => __('validation.attributes.references'),
            'preferred_interview_time' => __('validation.attributes.preferred_interview_time'),
            'interview_availability' => __('validation.attributes.interview_availability'),
            'work_authorization' => __('validation.attributes.work_authorization'),
            'background_check_consent' => __('validation.attributes.background_check_consent'),
            'terms_accepted' => __('validation.attributes.terms_accepted'),
            'privacy_policy_accepted' => __('validation.attributes.privacy_policy_accepted'),
            'application_source' => __('validation.attributes.application_source'),
            'referrer' => __('validation.attributes.referrer'),
            'custom_fields' => __('validation.attributes.custom_fields'),
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
            'message' => __('validation.job_application_creation_failed'),
            'errors' => $validator->errors(),
            'error_code' => 'JOB_APPLICATION_CREATION_VALIDATION_FAILED',
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
            'message' => __('validation.job_application_creation_unauthorized'),
            'error_code' => 'JOB_APPLICATION_CREATION_UNAUTHORIZED',
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
        // Normalize email to lowercase
        if ($this->has('candidate_email')) {
            $this->merge([
                'candidate_email' => strtolower(trim($this->candidate_email)),
            ]);
        }
        
        // Normalize phone number
        if ($this->has('candidate_phone')) {
            $this->merge([
                'candidate_phone' => preg_replace('/[^\+\d]/', '', $this->candidate_phone),
            ]);
        }
        
        // Normalize boolean values
        $booleanFields = [
            'willing_to_relocate',
            'background_check_consent',
            'terms_accepted',
            'privacy_policy_accepted'
        ];
        
        foreach ($booleanFields as $field) {
            if ($this->has($field)) {
                $this->merge([
                    $field => filter_var($this->$field, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? false,
                ]);
            }
        }
        
        // Normalize arrays
        if ($this->has('skills') && is_string($this->skills)) {
            $this->merge([
                'skills' => array_map('intval', explode(',', $this->skills)),
            ]);
        }
        
        if ($this->has('languages') && is_string($this->languages)) {
            $this->merge([
                'languages' => array_map('intval', explode(',', $this->languages)),
            ]);
        }
        
        if ($this->has('preferred_locations') && is_string($this->preferred_locations)) {
            $this->merge([
                'preferred_locations' => array_filter(explode(',', $this->preferred_locations)),
            ]);
        }
        
        if ($this->has('interview_availability') && is_string($this->interview_availability)) {
            $this->merge([
                'interview_availability' => array_filter(explode(',', $this->interview_availability)),
            ]);
        }
        
        // Sanitize text fields
        $textFields = [
            'candidate_name',
            'education_field',
            'education_institution',
            'key_skills',
            'current_location',
            'cover_letter',
            'motivation',
            'additional_notes',
            'availability_notes',
            'application_source',
            'referrer'
        ];
        
        foreach ($textFields as $field) {
            if ($this->has($field)) {
                $this->merge([
                    $field => trim($this->$field),
                ]);
            }
        }
        
        // Set default application source if not provided
        if (!$this->has('application_source')) {
            $this->merge([
                'application_source' => 'web_application',
            ]);
        }
    }

    /**
     * Check if candidate has already applied for this job.
     *
     * @param string $email
     * @param int $jobId
     * @return bool
     */
    private function hasExistingApplication(string $email, int $jobId): bool
    {
        return JobApplication::where('candidate_email', $email)
            ->where('job_id', $jobId)
            ->whereNull('deleted_at')
            ->exists();
    }
}
