<?php

namespace App\Http\Requests\JobApplication;

use App\Models\Job;
use App\Models\Resume;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class CreateJobApplicationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Allow only authenticated candidates or public job applications
        return true; // Based on user requirements: no auth system
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<mixed>|string|ValidationRule>
     */
    public function rules(): array
    {
        return [
            // Job ID - required for application
            'job_id' => [
                'required',
                'integer',
                'min:1',
                'exists:jobs,id',
                function ($attribute, $value, $fail) {
                    if (!$this->validateJobActive($value)) {
                        $fail(__('validation.job_not_active'));
                    }
                    if ($this->hasExistingApplication($value)) {
                        $fail(__('validation.already_applied'));
                    }
                },
            ],

            // Applicant information
            'first_name' => [
                'required',
                'string',
                'min:2',
                'max:50',
                'regex:/^[\p{L}\s\-\'\.]+$/u',
            ],

            'last_name' => [
                'required',
                'string',
                'min:2',
                'max:50',
                'regex:/^[\p{L}\s\-\'\.]+$/u',
            ],

            'email' => [
                'required',
                'email:rfc,dns',
                'max:100',
                'regex:/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/',
            ],

            'phone' => [
                'required',
                'string',
                'min:10',
                'max:20',
                'regex:/^[\+]?[0-9\s\-\(\)]+$/',
            ],

            // Resume/CV - required
            'resume' => [
                'required',
                'file',
                'max:5120', // 5MB
                'mimes:pdf,doc,docx',
                function ($attribute, $value, $fail) {
                    if ($value && !$this->validateResumeContent($value)) {
                        $fail(__('validation.resume_content_invalid'));
                    }
                },
            ],

            // Cover letter - optional but validated if provided
            'cover_letter' => [
                'sometimes',
                'string',
                'min:50',
                'max:2000',
            ],

            // Expected salary
            'expected_salary' => [
                'sometimes',
                'numeric',
                'min:0',
                'max:9999999999',
            ],

            'salary_currency' => [
                'sometimes',
                'string',
                'size:3',
                'exists:salary_currencies,currency_code',
            ],

            'salary_type' => [
                'sometimes',
                'string',
                Rule::in(['hourly', 'daily', 'weekly', 'monthly', 'yearly']),
            ],

            // Experience and qualifications
            'years_experience' => [
                'sometimes',
                'integer',
                'min:0',
                'max:50',
            ],

            'highest_degree' => [
                'sometimes',
                'string',
                'max:100',
            ],

            'field_of_study' => [
                'sometimes',
                'string',
                'max:100',
            ],

            // Portfolio/website
            'portfolio_url' => [
                'sometimes',
                'url',
                'max:255',
                'regex:/^https?:\/\/.+/',
            ],

            'linkedin_url' => [
                'sometimes',
                'url',
                'max:255',
                'regex:/^https:\/\/[a-z]{2,3}\.linkedin\.com\/.*/',
            ],

            // Skills (array of skill IDs)
            'skills' => [
                'sometimes',
                'array',
                'max:20',
            ],

            'skills.*' => [
                'integer',
                'exists:skills,id',
            ],

            // Custom answers for job-specific questions
            'custom_answers' => [
                'sometimes',
                'array',
                'max:10',
            ],

            'custom_answers.*.question' => [
                'required_with:custom_answers',
                'string',
                'max:500',
            ],

            'custom_answers.*.answer' => [
                'required_with:custom_answers',
                'string',
                'max:1000',
            ],

            // Availability
            'available_start_date' => [
                'sometimes',
                'date',
                'after:today',
            ],

            'notice_period' => [
                'sometimes',
                'string',
                Rule::in(['immediate', '1_week', '2_weeks', '1_month', '2_months', '3_months']),
            ],

            // Location preferences
            'willing_to_relocate' => [
                'sometimes',
                'boolean',
            ],

            'remote_work_preference' => [
                'sometimes',
                'string',
                Rule::in(['onsite', 'remote', 'hybrid', 'flexible']),
            ],

            // Source tracking
            'application_source' => [
                'sometimes',
                'string',
                'max:50',
            ],

            // Terms and conditions
            'accept_terms' => [
                'required',
                'boolean',
                'accepted',
            ],

            'subscribe_newsletter' => [
                'sometimes',
                'boolean',
            ],

            // Additional documents
            'additional_documents' => [
                'sometimes',
                'array',
                'max:3',
            ],

            'additional_documents.*' => [
                'file',
                'max:5120', // 5MB
                'mimes:pdf,doc,docx,jpg,jpeg,png',
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
            'job_id.required' => __('validation.required_field', ['field' => __('validation.attributes.job')]),
            'job_id.exists' => __('validation.exists', ['attribute' => __('validation.attributes.job')]),
            
            'first_name.required' => __('validation.required_field', ['field' => __('validation.attributes.first_name')]),
            'first_name.regex' => __('validation.name_format', ['attribute' => __('validation.attributes.first_name')]),
            
            'last_name.required' => __('validation.required_field', ['field' => __('validation.attributes.last_name')]),
            'last_name.regex' => __('validation.name_format', ['attribute' => __('validation.attributes.last_name')]),
            
            'email.required' => __('validation.required_field', ['field' => __('validation.attributes.email')]),
            'email.email' => __('validation.valid_email'),
            'email.regex' => __('validation.email_format'),
            
            'phone.required' => __('validation.required_field', ['field' => __('validation.attributes.phone')]),
            'phone.regex' => __('validation.phone_format'),
            
            'resume.required' => __('validation.required_field', ['field' => __('validation.attributes.resume')]),
            'resume.file' => __('validation.file_upload', ['attribute' => __('validation.attributes.resume')]),
            'resume.max' => __('validation.max_file_size', ['attribute' => __('validation.attributes.resume'), 'max' => '5MB']),
            'resume.mimes' => __('validation.file_types', ['attribute' => __('validation.attributes.resume'), 'types' => 'PDF, DOC, DOCX']),
            
            'cover_letter.min' => __('validation.min_chars', ['attribute' => __('validation.attributes.cover_letter'), 'min' => 50]),
            'cover_letter.max' => __('validation.max_chars', ['attribute' => __('validation.attributes.cover_letter'), 'max' => 2000]),
            
            'expected_salary.numeric' => __('validation.numeric', ['attribute' => __('validation.attributes.expected_salary')]),
            'expected_salary.min' => __('validation.min_value', ['attribute' => __('validation.attributes.expected_salary'), 'min' => 0]),
            
            'portfolio_url.url' => __('validation.valid_url', ['attribute' => __('validation.attributes.portfolio_url')]),
            'linkedin_url.regex' => __('validation.linkedin_url_format'),
            
            'skills.max' => __('validation.max_items', ['attribute' => __('validation.attributes.skills'), 'max' => 20]),
            'skills.*.exists' => __('validation.exists', ['attribute' => __('validation.attributes.skill')]),
            
            'accept_terms.required' => __('validation.required_field', ['field' => __('validation.attributes.accept_terms')]),
            'accept_terms.accepted' => __('validation.must_accept_terms'),
            
            'additional_documents.max' => __('validation.max_files', ['attribute' => __('validation.attributes.additional_documents'), 'max' => 3]),
            'additional_documents.*.max' => __('validation.max_file_size', ['max' => '5MB']),
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
            'job_id' => __('validation.attributes.job'),
            'first_name' => __('validation.attributes.first_name'),
            'last_name' => __('validation.attributes.last_name'),
            'email' => __('validation.attributes.email'),
            'phone' => __('validation.attributes.phone'),
            'resume' => __('validation.attributes.resume'),
            'cover_letter' => __('validation.attributes.cover_letter'),
            'expected_salary' => __('validation.attributes.expected_salary'),
            'salary_currency' => __('validation.attributes.salary_currency'),
            'salary_type' => __('validation.attributes.salary_type'),
            'years_experience' => __('validation.attributes.years_experience'),
            'highest_degree' => __('validation.attributes.highest_degree'),
            'field_of_study' => __('validation.attributes.field_of_study'),
            'portfolio_url' => __('validation.attributes.portfolio_url'),
            'linkedin_url' => __('validation.attributes.linkedin_url'),
            'skills' => __('validation.attributes.skills'),
            'available_start_date' => __('validation.attributes.available_start_date'),
            'notice_period' => __('validation.attributes.notice_period'),
            'willing_to_relocate' => __('validation.attributes.willing_to_relocate'),
            'remote_work_preference' => __('validation.attributes.remote_work_preference'),
            'accept_terms' => __('validation.attributes.accept_terms'),
            'additional_documents' => __('validation.attributes.additional_documents'),
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Set defaults
        $this->merge([
            'salary_currency' => $this->salary_currency ?? 'USD',
            'salary_type' => $this->salary_type ?? 'yearly',
            'years_experience' => $this->years_experience ?? 0,
            'willing_to_relocate' => $this->boolean('willing_to_relocate', false),
            'subscribe_newsletter' => $this->boolean('subscribe_newsletter', false),
            'remote_work_preference' => $this->remote_work_preference ?? 'flexible',
            'notice_period' => $this->notice_period ?? '2_weeks',
        ]);

        // Clean names
        if ($this->has('first_name')) {
                $this->merge([
                'first_name' => trim(ucwords(strtolower($this->first_name))),
                ]);
        }

        if ($this->has('last_name')) {
            $this->merge([
                'last_name' => trim(ucwords(strtolower($this->last_name))),
            ]);
        }

        // Clean email
        if ($this->has('email')) {
            $this->merge([
                'email' => strtolower(trim($this->email)),
            ]);
        }

        // Clean phone
        if ($this->has('phone')) {
            $this->merge([
                'phone' => preg_replace('/[^\d\+\-\(\)\s]/', '', $this->phone),
            ]);
        }

        // Log application attempt
        Log::info('Job application creation attempt', [
            'job_id' => $this->job_id,
            'applicant_email' => $this->email ?? null,
            'ip' => $this->ip(),
            'timestamp' => now(),
        ]);
    }

    /**
     * Handle a passed validation attempt.
     */
    protected function passedValidation(): void
    {
        // Generate application reference
        $this->merge([
            'reference_number' => 'APP-' . date('Ymd') . '-' . strtoupper(substr(md5($this->email . time()), 0, 6)),
            'applied_at' => now(),
            'status' => 'pending',
            'validated_at' => now(),
        ]);
    }

    /**
     * Validate if job is active and accepting applications.
     */
    private function validateJobActive($jobId): bool
    {
        return \DB::table('jobs')
            ->where('id', $jobId)
            ->where('status', 'active')
            ->where('deadline', '>', now())
            ->exists();
    }

    /**
     * Check if user has already applied for this job.
     */
    private function hasExistingApplication($jobId): bool
    {
        if (!$this->has('email')) {
            return false;
        }

        return \DB::table('job_applications')
            ->where('job_id', $jobId)
            ->where('email', $this->email)
            ->whereNull('deleted_at')
            ->exists();
    }

    /**
     * Basic resume content validation.
     */
    private function validateResumeContent($file): bool
    {
        // Basic file validation - could be enhanced with more sophisticated checks
        $allowedMimes = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
        return in_array($file->getMimeType(), $allowedMimes);
    }
}
