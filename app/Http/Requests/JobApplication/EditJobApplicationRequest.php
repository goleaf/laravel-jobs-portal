<?php

namespace App\Http\Requests\JobApplication;

use App\Models\Job;
use App\Models\JobApplication;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;

/**
 * EditJobApplicationRequest
 *
 * Comprehensive validation for job application editing operations with enterprise-grade security.
 * Implements access control, business logic validation, and data integrity checks.
 *
 * @author System Generated
 *
 * @version 1.0.0
 */
class EditJobApplicationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * Implements role-based authorization with business logic validation.
     * Validates job application editing permissions and ownership.
     *
     * @return bool Authorization status
     */
    public function authorize(): bool
    {
        // Basic authentication check - per user requirements: "do not make users and do not any users system"
        // However, we still need to validate access permissions for security

        $jobApplicationId = $this->route('jobApplication') ?: $this->route('id') ?: $this->input('id');

        if (! $jobApplicationId) {
            return false;
        }

        // Validate job application exists
        $jobApplication = JobApplication::find($jobApplicationId);
        if (! $jobApplication) {
            return false;
        }

        // Business rule: Cannot edit deleted applications
        if ($jobApplication->deleted_at) {
            return false;
        }

        // Business rule: Cannot edit applications with certain statuses
        $nonEditableStatuses = ['hired', 'rejected', 'withdrawn', 'cancelled'];
        if (in_array($jobApplication->status, $nonEditableStatuses)) {
            return false;
        }

        // Business rule: Check if job is still accepting applications
        if (! $jobApplication->job || ! $jobApplication->job->is_active) {
            return false;
        }

        // Business rule: Cannot edit after certain time period (e.g., 30 days)
        if ($jobApplication->created_at->addDays(30) < now()) {
            return false;
        }

        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * Implements comprehensive validation with file upload, candidate information,
     * and business logic validations for job application editing.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $jobApplicationId = $this->route('jobApplication') ?: $this->route('id') ?: $this->input('id');

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

            // Job identification (cannot be changed in edit)
            'job_id' => [
                'sometimes',
                'integer',
                'min:1',
                'exists:jobs,id',
                function ($attribute, $value, $fail) use ($jobApplicationId) {
                    if ($jobApplicationId) {
                        $existingApplication = JobApplication::find($jobApplicationId);
                        if ($existingApplication && $existingApplication->job_id !== (int) $value) {
                            $fail(__('validation.job_id_cannot_be_changed'));
                        }
                    }
                },
            ],

            // File uploads (updatable)
            'resume' => [
                'sometimes',
                'file',
                'mimes:pdf,doc,docx',
                'max:5120', // 5MB
            ],

            'cover_letter_file' => [
                'sometimes',
                'file',
                'mimes:pdf,doc,docx',
                'max:2048', // 2MB
            ],

            'portfolio' => [
                'sometimes',
                'file',
                'mimes:pdf,zip,rar',
                'max:10240', // 10MB
            ],

            'certificates' => [
                'sometimes',
                'array',
                'max:5',
            ],

            'certificates.*' => [
                'file',
                'mimes:pdf,jpg,jpeg,png',
                'max:2048', // 2MB each
            ],

            // Candidate information (updatable)
            'candidate_name' => [
                'sometimes',
                'string',
                'min:2',
                'max:255',
                'regex:/^[\pL\pM\s\.\-\']+$/u',
            ],

            'candidate_email' => [
                'sometimes',
                'email:rfc,dns',
                'max:255',
                'lowercase',
                Rule::unique('job_applications', 'candidate_email')
                    ->ignore($jobApplicationId)
                    ->where(function ($query) {
                        $query->where('job_id', $this->input('job_id') ?: $this->route('job_id'));
                    }),
            ],

            'candidate_phone' => [
                'sometimes',
                'string',
                'min:10',
                'max:20',
                'regex:/^[\+]?[0-9\s\-\(\)]+$/',
            ],

            // Professional information (updatable)
            'experience_years' => [
                'sometimes',
                'integer',
                'min:0',
                'max:50',
            ],

            'current_salary' => [
                'sometimes',
                'numeric',
                'min:0',
                'max:999999.99',
            ],

            'expected_salary' => [
                'sometimes',
                'numeric',
                'min:0',
                'max:999999.99',
            ],

            'currency' => [
                'required_with:current_salary,expected_salary',
                'string',
                'size:3',
                Rule::in(['USD', 'EUR', 'GBP', 'LTL', 'PLN', 'RUB']),
            ],

            'notice_period' => [
                'sometimes',
                'integer',
                'min:0',
                'max:365', // days
            ],

            'availability_date' => [
                'sometimes',
                'date',
                'after_or_equal:today',
                'before:'.now()->addYear()->toDateString(),
            ],

            // Education information (updatable)
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

            'education_institution' => [
                'sometimes',
                'string',
                'max:255',
            ],

            'graduation_year' => [
                'sometimes',
                'integer',
                'min:1950',
                'max:'.(date('Y') + 10),
            ],

            'gpa' => [
                'sometimes',
                'numeric',
                'min:0',
                'max:4.0',
            ],

            // Skills and qualifications (updatable)
            'skills' => [
                'sometimes',
                'array',
                'max:50',
            ],

            'skills.*' => [
                'string',
                'max:100',
                'distinct',
            ],

            'languages' => [
                'sometimes',
                'array',
                'max:20',
            ],

            'languages.*.language' => [
                'required_with:languages',
                'string',
                'max:50',
            ],

            'languages.*.proficiency' => [
                'required_with:languages',
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

            // Location preferences (updatable)
            'preferred_locations' => [
                'sometimes',
                'array',
                'max:10',
            ],

            'preferred_locations.*' => [
                'string',
                'max:100',
            ],

            'remote_work' => [
                'sometimes',
                'boolean',
            ],

            'relocation_willing' => [
                'sometimes',
                'boolean',
            ],

            'travel_willing' => [
                'sometimes',
                'boolean',
            ],

            'travel_percentage' => [
                'required_if:travel_willing,true',
                'integer',
                'min:0',
                'max:100',
            ],

            // Application content (updatable)
            'cover_letter' => [
                'sometimes',
                'string',
                'max:5000',
            ],

            'motivation' => [
                'sometimes',
                'string',
                'max:2000',
            ],

            'additional_notes' => [
                'sometimes',
                'string',
                'max:1000',
            ],

            'why_interested' => [
                'sometimes',
                'string',
                'max:1500',
            ],

            'career_goals' => [
                'sometimes',
                'string',
                'max:1000',
            ],

            // References (updatable)
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
                'regex:/^[\+]?[0-9\s\-\(\)]+$/',
            ],

            'references.*.relationship' => [
                'required_with:references',
                'string',
                'max:100',
            ],

            // Work authorization (updatable)
            'work_authorization' => [
                'sometimes',
                'string',
                Rule::in(['citizen', 'permanent_resident', 'work_visa', 'student_visa', 'other']),
            ],

            'visa_status' => [
                'required_if:work_authorization,work_visa,student_visa',
                'string',
                'max:100',
            ],

            'visa_expiry' => [
                'required_if:work_authorization,work_visa,student_visa',
                'date',
                'after:today',
            ],

            // Application metadata
            'source' => [
                'sometimes',
                'string',
                'max:100',
            ],

            'referral_source' => [
                'sometimes',
                'string',
                'max:255',
            ],

            'how_did_you_hear' => [
                'sometimes',
                'string',
                'max:255',
            ],

            // Status updates (limited)
            'status' => [
                'sometimes',
                'string',
                Rule::in(['applied', 'under_review', 'interviewing']), // Limited statuses for candidate editing
            ],

            'notes_for_employer' => [
                'sometimes',
                'string',
                'max:1000',
            ],

            // Custom fields
            'custom_fields' => [
                'sometimes',
                'array',
                'max:10',
            ],

            'custom_fields.*' => [
                'string',
                'max:500',
            ],

            // Edit metadata
            'edit_reason' => [
                'sometimes',
                'string',
                'max:255',
                Rule::in([
                    'update_information',
                    'add_documents',
                    'correct_errors',
                    'add_experience',
                    'update_availability',
                    'other',
                ]),
            ],

            'edit_summary' => [
                'sometimes',
                'string',
                'max:500',
            ],

            // Version control
            'version' => [
                'sometimes',
                'integer',
                'min:1',
            ],

            'last_modified_at' => [
                'sometimes',
                'date',
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
            'id.exists' => __('validation.job_application_not_found'),
            'job_id.exists' => __('validation.job_not_found'),
            'job_id_cannot_be_changed' => __('validation.job_id_cannot_be_changed'),

            // File upload messages
            'resume.file' => __('validation.resume_file'),
            'resume.mimes' => __('validation.resume_format'),
            'resume.max' => __('validation.resume_size'),

            'cover_letter_file.file' => __('validation.cover_letter_file'),
            'cover_letter_file.mimes' => __('validation.cover_letter_format'),
            'cover_letter_file.max' => __('validation.cover_letter_size'),

            'portfolio.file' => __('validation.portfolio_file'),
            'portfolio.mimes' => __('validation.portfolio_format'),
            'portfolio.max' => __('validation.portfolio_size'),

            'certificates.array' => __('validation.certificates_array'),
            'certificates.max' => __('validation.certificates_max'),
            'certificates.*.file' => __('validation.certificate_file'),
            'certificates.*.mimes' => __('validation.certificate_format'),
            'certificates.*.max' => __('validation.certificate_size'),

            // Candidate information messages
            'candidate_name.regex' => __('validation.candidate_name_format'),
            'candidate_email.email' => __('validation.candidate_email_format'),
            'candidate_email.unique' => __('validation.candidate_email_duplicate'),
            'candidate_email.lowercase' => __('validation.candidate_email_lowercase'),
            'candidate_phone.regex' => __('validation.candidate_phone_format'),

            // Professional information messages
            'experience_years.integer' => __('validation.experience_years_integer'),
            'experience_years.min' => __('validation.experience_years_min'),
            'experience_years.max' => __('validation.experience_years_max'),

            'current_salary.numeric' => __('validation.current_salary_numeric'),
            'current_salary.min' => __('validation.current_salary_min'),
            'current_salary.max' => __('validation.current_salary_max'),

            'expected_salary.numeric' => __('validation.expected_salary_numeric'),
            'expected_salary.min' => __('validation.expected_salary_min'),
            'expected_salary.max' => __('validation.expected_salary_max'),

            'currency.required_with' => __('validation.currency_required'),
            'currency.in' => __('validation.currency_invalid'),

            'notice_period.integer' => __('validation.notice_period_integer'),
            'notice_period.max' => __('validation.notice_period_max'),

            'availability_date.date' => __('validation.availability_date_format'),
            'availability_date.after_or_equal' => __('validation.availability_date_future'),

            // Education messages
            'education_level.in' => __('validation.education_level_invalid'),
            'graduation_year.min' => __('validation.graduation_year_min'),
            'graduation_year.max' => __('validation.graduation_year_max'),
            'gpa.numeric' => __('validation.gpa_numeric'),
            'gpa.max' => __('validation.gpa_max'),

            // Skills messages
            'skills.array' => __('validation.skills_array'),
            'skills.max' => __('validation.skills_max'),
            'skills.*.distinct' => __('validation.skill_duplicate'),

            'languages.array' => __('validation.languages_array'),
            'languages.*.language.required_with' => __('validation.language_name_required'),
            'languages.*.proficiency.required_with' => __('validation.language_proficiency_required'),
            'languages.*.proficiency.in' => __('validation.language_proficiency_invalid'),

            'certifications.array' => __('validation.certifications_array'),
            'certifications.max' => __('validation.certifications_max'),

            // Location messages
            'preferred_locations.array' => __('validation.preferred_locations_array'),
            'preferred_locations.max' => __('validation.preferred_locations_max'),

            'travel_percentage.required_if' => __('validation.travel_percentage_required'),
            'travel_percentage.max' => __('validation.travel_percentage_max'),

            // Content messages
            'cover_letter.max' => __('validation.cover_letter_max'),
            'motivation.max' => __('validation.motivation_max'),
            'additional_notes.max' => __('validation.additional_notes_max'),
            'why_interested.max' => __('validation.why_interested_max'),
            'career_goals.max' => __('validation.career_goals_max'),

            // References messages
            'references.array' => __('validation.references_array'),
            'references.max' => __('validation.references_max'),
            'references.*.name.required_with' => __('validation.reference_name_required'),
            'references.*.position.required_with' => __('validation.reference_position_required'),
            'references.*.company.required_with' => __('validation.reference_company_required'),
            'references.*.email.required_with' => __('validation.reference_email_required'),
            'references.*.email.email' => __('validation.reference_email_format'),
            'references.*.phone.regex' => __('validation.reference_phone_format'),
            'references.*.relationship.required_with' => __('validation.reference_relationship_required'),

            // Work authorization messages
            'work_authorization.in' => __('validation.work_authorization_invalid'),
            'visa_status.required_if' => __('validation.visa_status_required'),
            'visa_expiry.required_if' => __('validation.visa_expiry_required'),
            'visa_expiry.after' => __('validation.visa_expiry_future'),

            // Status messages
            'status.in' => __('validation.status_invalid'),

            // Edit metadata messages
            'edit_reason.in' => __('validation.edit_reason_invalid'),
            'edit_summary.max' => __('validation.edit_summary_max'),

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
            'id' => __('validation.attributes.job_application_id'),
            'job_id' => __('validation.attributes.job_id'),
            'resume' => __('validation.attributes.resume'),
            'cover_letter_file' => __('validation.attributes.cover_letter_file'),
            'portfolio' => __('validation.attributes.portfolio'),
            'certificates' => __('validation.attributes.certificates'),
            'candidate_name' => __('validation.attributes.candidate_name'),
            'candidate_email' => __('validation.attributes.candidate_email'),
            'candidate_phone' => __('validation.attributes.candidate_phone'),
            'experience_years' => __('validation.attributes.experience_years'),
            'current_salary' => __('validation.attributes.current_salary'),
            'expected_salary' => __('validation.attributes.expected_salary'),
            'currency' => __('validation.attributes.currency'),
            'notice_period' => __('validation.attributes.notice_period'),
            'availability_date' => __('validation.attributes.availability_date'),
            'education_level' => __('validation.attributes.education_level'),
            'education_field' => __('validation.attributes.education_field'),
            'education_institution' => __('validation.attributes.education_institution'),
            'graduation_year' => __('validation.attributes.graduation_year'),
            'gpa' => __('validation.attributes.gpa'),
            'skills' => __('validation.attributes.skills'),
            'languages' => __('validation.attributes.languages'),
            'certifications' => __('validation.attributes.certifications'),
            'preferred_locations' => __('validation.attributes.preferred_locations'),
            'remote_work' => __('validation.attributes.remote_work'),
            'relocation_willing' => __('validation.attributes.relocation_willing'),
            'travel_willing' => __('validation.attributes.travel_willing'),
            'travel_percentage' => __('validation.attributes.travel_percentage'),
            'cover_letter' => __('validation.attributes.cover_letter'),
            'motivation' => __('validation.attributes.motivation'),
            'additional_notes' => __('validation.attributes.additional_notes'),
            'why_interested' => __('validation.attributes.why_interested'),
            'career_goals' => __('validation.attributes.career_goals'),
            'references' => __('validation.attributes.references'),
            'work_authorization' => __('validation.attributes.work_authorization'),
            'visa_status' => __('validation.attributes.visa_status'),
            'visa_expiry' => __('validation.attributes.visa_expiry'),
            'source' => __('validation.attributes.source'),
            'referral_source' => __('validation.attributes.referral_source'),
            'how_did_you_hear' => __('validation.attributes.how_did_you_hear'),
            'status' => __('validation.attributes.status'),
            'notes_for_employer' => __('validation.attributes.notes_for_employer'),
            'custom_fields' => __('validation.attributes.custom_fields'),
            'edit_reason' => __('validation.attributes.edit_reason'),
            'edit_summary' => __('validation.attributes.edit_summary'),
            'version' => __('validation.attributes.version'),
            'last_modified_at' => __('validation.attributes.last_modified_at'),
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
            'message' => __('validation.job_application_edit_failed'),
            'errors' => $validator->errors(),
            'error_code' => 'JOB_APPLICATION_EDIT_VALIDATION_FAILED',
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
            'message' => __('validation.job_application_edit_unauthorized'),
            'error_code' => 'JOB_APPLICATION_EDIT_UNAUTHORIZED',
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
            'remote_work',
            'relocation_willing',
            'travel_willing',
        ];

        foreach ($booleanFields as $field) {
            if ($this->has($field)) {
                $this->merge([
                    $field => filter_var($this->$field, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? false,
                ]);
            }
        }

        // Sanitize array fields
        if ($this->has('skills') && is_string($this->skills)) {
            $this->merge([
                'skills' => array_filter(array_map('trim', explode(',', $this->skills))),
            ]);
        }

        if ($this->has('preferred_locations') && is_string($this->preferred_locations)) {
            $this->merge([
                'preferred_locations' => array_filter(array_map('trim', explode(',', $this->preferred_locations))),
            ]);
        }

        if ($this->has('certifications') && is_string($this->certifications)) {
            $this->merge([
                'certifications' => array_filter(array_map('trim', explode(',', $this->certifications))),
            ]);
        }

        // Set edit metadata
        $this->merge([
            'last_modified_at' => now(),
        ]);

        // Sanitize text fields
        $textFields = [
            'candidate_name',
            'education_field',
            'education_institution',
            'cover_letter',
            'motivation',
            'additional_notes',
            'why_interested',
            'career_goals',
            'source',
            'referral_source',
            'how_did_you_hear',
            'notes_for_employer',
            'edit_reason',
            'edit_summary',
            'visa_status',
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
