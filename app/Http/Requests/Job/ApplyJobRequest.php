<?php

namespace App\Http\Requests\Job;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Class ApplyJobRequest
 * 
 * Handles job application form validation with comprehensive business rules,
 * security checks, and multilingual error messages.
 */
class ApplyJobRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Check if user is authenticated and has candidate role
        if (!auth()->check()) {
            return false;
        }

        // Check if user has candidate profile
        $user = auth()->user();
        if (!$user->candidate) {
            return false;
        }

        // Check if job exists and is active
        $job = $this->route('job');
        if (!$job || !$job->is_active) {
            return false;
        }

        // Check if application deadline has not passed
        if ($job->expired_at && $job->expired_at < now()) {
            return false;
        }

        // Check if user has not already applied for this job
        $existingApplication = $user->candidate->jobApplications()
            ->where('job_id', $job->id)
            ->exists();

        if ($existingApplication) {
            return false;
        }

        // Check if user is not the job poster (can't apply to own job)
        if ($job->company_id === $user->candidate->company_id) {
            return false;
        }

        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $job = $this->route('job');
        $candidate = auth()->user()->candidate ?? null;

        return [
            // Job reference validation
            'job_id' => [
                'required',
                'integer',
                Rule::exists('jobs', 'id')->where(function ($query) {
                    $query->where('is_active', true)
                          ->where('expired_at', '>', now());
                }),
            ],

            // Resume selection validation
            'resume_id' => [
                'required',
                'integer',
                Rule::exists('resumes', 'id')->where(function ($query) use ($candidate) {
                    if ($candidate) {
                        $query->where('candidate_id', $candidate->id)
                              ->where('is_active', true);
                    }
                }),
            ],

            // Cover letter validation
            'cover_letter' => [
                'nullable',
                'string',
                'min:50',
                'max:2000',
                'regex:/^[\p{L}\p{N}\p{P}\p{Z}\s]+$/u', // Allow unicode characters, numbers, punctuation, and spaces
            ],

            // Expected salary validation
            'expected_salary' => [
                'nullable',
                'numeric',
                'min:0',
                'max:9999999999',
                function ($attribute, $value, $fail) use ($job) {
                    if ($value && $job) {
                        // Check if expected salary is not unreasonably high compared to job salary
                        $maxJobSalary = max($job->salary_from ?? 0, $job->salary_to ?? 0);
                        if ($maxJobSalary > 0 && $value > ($maxJobSalary * 2)) {
                            $fail(__('validation.job_application.expected_salary_too_high'));
                        }

                        // Check if expected salary is not too low (below minimum wage equivalent)
                        if ($value < 1000) {
                            $fail(__('validation.job_application.expected_salary_too_low'));
                        }
                    }
                },
            ],

            // Availability date validation
            'available_from' => [
                'nullable',
                'date',
                'after_or_equal:today',
                'before:+2 years',
            ],

            // Additional documents validation
            'additional_documents.*' => [
                'nullable',
                'file',
                'mimes:pdf,doc,docx,jpg,jpeg,png',
                'max:5120', // 5MB max per file
            ],

            // Notice period validation
            'notice_period' => [
                'nullable',
                'integer',
                'min:0',
                'max:365', // Maximum 1 year notice period
            ],

            // Willingness to relocate
            'willing_to_relocate' => [
                'boolean',
            ],

            // Reference contact validation
            'reference_name' => [
                'nullable',
                'string',
                'max:255',
                'regex:/^[\p{L}\s\-\.]+$/u', // Only letters, spaces, hyphens, dots
            ],

            'reference_email' => [
                'nullable',
                'email',
                'max:255',
                Rule::requiredIf(function () {
                    return !empty($this->reference_name);
                }),
            ],

            'reference_phone' => [
                'nullable',
                'string',
                'max:20',
                'regex:/^[\+]?[0-9\s\-\(\)]+$/', // Phone number format
            ],

            // Portfolio/LinkedIn validation
            'portfolio_url' => [
                'nullable',
                'url',
                'max:500',
                'regex:/^https?:\/\/.+/', // Must be HTTP/HTTPS
            ],

            'linkedin_url' => [
                'nullable',
                'url',
                'max:500',
                'regex:/^https?:\/\/(www\.)?linkedin\.com\/.*/', // Must be LinkedIn URL
            ],

            // Additional questions (job-specific)
            'answers' => [
                'nullable',
                'array',
            ],

            'answers.*' => [
                'nullable',
                'string',
                'max:1000',
            ],

            // Terms and conditions
            'terms_accepted' => [
                'required',
                'accepted',
            ],

            // Privacy policy acceptance
            'privacy_accepted' => [
                'required',
                'accepted',
            ],

            // Anti-spam validation
            'g-recaptcha-response' => [
                Rule::requiredIf(function () {
                    return config('settings.enable_recaptcha_job_application', false);
                }),
                'string',
            ],

            // Honeypot field (should be empty)
            'website' => [
                'nullable',
                'max:0', // Honeypot field should be empty
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            // Job validation messages
            'job_id.required' => __('validation.job_application.job_id.required'),
            'job_id.integer' => __('validation.job_application.job_id.integer'),
            'job_id.exists' => __('validation.job_application.job_id.exists'),

            // Resume validation messages
            'resume_id.required' => __('validation.job_application.resume_id.required'),
            'resume_id.integer' => __('validation.job_application.resume_id.integer'),
            'resume_id.exists' => __('validation.job_application.resume_id.exists'),

            // Cover letter validation messages
            'cover_letter.min' => __('validation.job_application.cover_letter.min'),
            'cover_letter.max' => __('validation.job_application.cover_letter.max'),
            'cover_letter.regex' => __('validation.job_application.cover_letter.format'),

            // Expected salary validation messages
            'expected_salary.numeric' => __('validation.job_application.expected_salary.numeric'),
            'expected_salary.min' => __('validation.job_application.expected_salary.min'),
            'expected_salary.max' => __('validation.job_application.expected_salary.max'),

            // Availability validation messages
            'available_from.date' => __('validation.job_application.available_from.date'),
            'available_from.after_or_equal' => __('validation.job_application.available_from.after_or_equal'),
            'available_from.before' => __('validation.job_application.available_from.before'),

            // Document validation messages
            'additional_documents.*.file' => __('validation.job_application.additional_documents.file'),
            'additional_documents.*.mimes' => __('validation.job_application.additional_documents.mimes'),
            'additional_documents.*.max' => __('validation.job_application.additional_documents.max'),

            // Notice period validation messages
            'notice_period.integer' => __('validation.job_application.notice_period.integer'),
            'notice_period.min' => __('validation.job_application.notice_period.min'),
            'notice_period.max' => __('validation.job_application.notice_period.max'),

            // Reference validation messages
            'reference_name.regex' => __('validation.job_application.reference_name.format'),
            'reference_email.email' => __('validation.job_application.reference_email.format'),
            'reference_email.required_if' => __('validation.job_application.reference_email.required_if'),
            'reference_phone.regex' => __('validation.job_application.reference_phone.format'),

            // URL validation messages
            'portfolio_url.url' => __('validation.job_application.portfolio_url.format'),
            'portfolio_url.regex' => __('validation.job_application.portfolio_url.protocol'),
            'linkedin_url.url' => __('validation.job_application.linkedin_url.format'),
            'linkedin_url.regex' => __('validation.job_application.linkedin_url.linkedin'),

            // Terms and privacy messages
            'terms_accepted.required' => __('validation.job_application.terms_accepted.required'),
            'terms_accepted.accepted' => __('validation.job_application.terms_accepted.accepted'),
            'privacy_accepted.required' => __('validation.job_application.privacy_accepted.required'),
            'privacy_accepted.accepted' => __('validation.job_application.privacy_accepted.accepted'),

            // Security validation messages
            'g-recaptcha-response.required_if' => __('validation.job_application.recaptcha.required'),
            'website.max' => __('validation.job_application.honeypot.detected'),

            // Answer validation messages
            'answers.array' => __('validation.job_application.answers.format'),
            'answers.*.max' => __('validation.job_application.answers.max'),
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'job_id' => __('validation.attributes.job'),
            'resume_id' => __('validation.attributes.resume'),
            'cover_letter' => __('validation.attributes.cover_letter'),
            'expected_salary' => __('validation.attributes.expected_salary'),
            'available_from' => __('validation.attributes.available_from'),
            'additional_documents' => __('validation.attributes.additional_documents'),
            'notice_period' => __('validation.attributes.notice_period'),
            'willing_to_relocate' => __('validation.attributes.willing_to_relocate'),
            'reference_name' => __('validation.attributes.reference_name'),
            'reference_email' => __('validation.attributes.reference_email'),
            'reference_phone' => __('validation.attributes.reference_phone'),
            'portfolio_url' => __('validation.attributes.portfolio_url'),
            'linkedin_url' => __('validation.attributes.linkedin_url'),
            'answers' => __('validation.attributes.additional_answers'),
            'terms_accepted' => __('validation.attributes.terms_conditions'),
            'privacy_accepted' => __('validation.attributes.privacy_policy'),
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Clean and format expected salary
        if ($this->has('expected_salary') && !empty($this->expected_salary)) {
            $cleanSalary = preg_replace('/[^\d.]/', '', $this->expected_salary);
            $this->merge([
                'expected_salary' => $cleanSalary ? (float) $cleanSalary : null,
            ]);
        }

        // Clean notice period
        if ($this->has('notice_period') && !empty($this->notice_period)) {
            $this->merge([
                'notice_period' => (int) $this->notice_period,
            ]);
        }

        // Ensure boolean fields are properly cast
        $this->merge([
            'willing_to_relocate' => $this->boolean('willing_to_relocate'),
            'terms_accepted' => $this->boolean('terms_accepted'),
            'privacy_accepted' => $this->boolean('privacy_accepted'),
        ]);

        // Set job_id from route if not provided
        if (!$this->has('job_id') && $this->route('job')) {
            $this->merge([
                'job_id' => $this->route('job')->id,
            ]);
        }
    }

    /**
     * Handle a failed validation attempt.
     */
    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator): void
    {
        // Log failed validation attempt for security monitoring
        logger()->warning('Job application validation failed', [
            'user_id' => auth()->id(),
            'job_id' => $this->route('job')?->id,
            'errors' => $validator->errors()->toArray(),
            'ip' => request()->ip(),
        ]);

        parent::failedValidation($validator);
    }

    /**
     * Handle a failed authorization attempt.
     */
    protected function failedAuthorization(): void
    {
        // Log failed authorization for security monitoring
        logger()->warning('Job application authorization failed', [
            'user_id' => auth()->id(),
            'job_id' => $this->route('job')?->id,
            'ip' => request()->ip(),
            'reason' => $this->getAuthorizationFailureReason(),
        ]);

        parent::failedAuthorization();
    }

    /**
     * Get reason for authorization failure (for logging).
     */
    private function getAuthorizationFailureReason(): string
    {
        if (!auth()->check()) {
            return 'user_not_authenticated';
        }

        $user = auth()->user();
        if (!$user->candidate) {
            return 'user_not_candidate';
        }

        $job = $this->route('job');
        if (!$job || !$job->is_active) {
            return 'job_not_active';
        }

        if ($job->expired_at && $job->expired_at < now()) {
            return 'job_expired';
        }

        $existingApplication = $user->candidate->jobApplications()
            ->where('job_id', $job->id)
            ->exists();

        if ($existingApplication) {
            return 'already_applied';
        }

        if ($job->company_id === $user->candidate->company_id) {
            return 'own_job_application';
        }

        return 'unknown_reason';
    }
} 