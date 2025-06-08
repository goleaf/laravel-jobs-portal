<?php

namespace App\Http\Requests\JobApplication;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use App\Models\Job;
use App\Models\JobApplication;

class CreateJobApplicationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Only authenticated candidates can apply for jobs
        if (!Auth::check()) {
            return false;
        }

        // Check if user has candidate role
        if (!Auth::user()->hasRole('Candidate')) {
            return false;
        }

        $job = $this->route('job');
        
        // Check if job exists and is active
        if (!$job || !$job->isActive()) {
            return false;
        }

        // Check if user hasn't already applied
        $existingApplication = JobApplication::where('job_id', $job->id)
            ->where('candidate_id', Auth::id())
            ->exists();

        if ($existingApplication) {
            return false;
        }

        // Check if job is not expired
        if ($job->isExpired()) {
            return false;
        }

        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            // Job reference
            'job_id' => [
                'required',
                'integer',
                'exists:jobs,id',
                function ($attribute, $value, $fail) {
                    $job = Job::find($value);
                    if (!$job || !$job->isActive()) {
                        $fail(__('job_applications.validation.job_not_available'));
                    }
                    if ($job && $job->isExpired()) {
                        $fail(__('job_applications.validation.job_expired'));
                    }
                },
            ],
            
            // Resume selection
            'resume_id' => [
                'required',
                'integer',
                'exists:resumes,id',
                function ($attribute, $value, $fail) {
                    $resume = \App\Models\Resume::where('id', $value)
                        ->where('user_id', Auth::id())
                        ->first();
                    
                    if (!$resume) {
                        $fail(__('job_applications.validation.resume_not_found'));
                    }
                    
                    if ($resume && !$resume->is_active) {
                        $fail(__('job_applications.validation.resume_not_active'));
                    }
                },
            ],
            
            // Cover letter
            'cover_letter' => [
                'nullable',
                'string',
                'min:50',
                'max:5000',
                function ($attribute, $value, $fail) {
                    if ($value && $this->hasSuspiciousContent($value)) {
                        $fail(__('job_applications.validation.cover_letter_suspicious'));
                    }
                },
            ],
            
            // Expected salary
            'expected_salary' => [
                'nullable',
                'numeric',
                'min:0',
                'max:9999999.99',
            ],
            'salary_currency_id' => [
                'nullable',
                'integer',
                'exists:salary_currencies,id',
                'required_with:expected_salary',
            ],
            'salary_period_id' => [
                'nullable',
                'integer',
                'exists:salary_periods,id',
                'required_with:expected_salary',
            ],
            
            // Availability
            'available_from' => [
                'nullable',
                'date',
                'after_or_equal:today',
            ],
            'notice_period_days' => [
                'nullable',
                'integer',
                'min:0',
                'max:365',
            ],
            
            // Additional information
            'additional_info' => [
                'nullable',
                'string',
                'max:2000',
            ],
            
            // Portfolio/work samples
            'portfolio_url' => [
                'nullable',
                'url',
                'max:255',
                'regex:/^https?:\/\/([\da-z\.-]+)\.([a-z\.]{2,6})([\/\w \.-]*)*\/?$/',
            ],
            'github_url' => [
                'nullable',
                'url',
                'max:255',
                'regex:/^https?:\/\/(www\.)?github\.com\/.*$/',
            ],
            'linkedin_url' => [
                'nullable',
                'url',
                'max:255',
                'regex:/^https?:\/\/(www\.)?linkedin\.com\/.*$/',
            ],
            
            // File attachments
            'additional_documents' => [
                'nullable',
                'array',
                'max:5',
            ],
            'additional_documents.*' => [
                'file',
                'mimes:pdf,doc,docx,txt',
                'max:5120', // 5MB
            ],
            
            // Application preferences
            'is_willing_to_relocate' => [
                'sometimes',
                'boolean',
            ],
            'is_open_to_remote' => [
                'sometimes',
                'boolean',
            ],
            'preferred_work_type' => [
                'nullable',
                'string',
                Rule::in(['full_time', 'part_time', 'contract', 'freelance', 'internship']),
            ],
            
            // Privacy and consent
            'consent_to_contact' => [
                'required',
                'boolean',
                'accepted',
            ],
            'consent_to_share_resume' => [
                'required',
                'boolean',
                'accepted',
            ],
            'privacy_policy_accepted' => [
                'required',
                'boolean',
                'accepted',
            ],
            
            // Anti-spam
            'g-recaptcha-response' => [
                'nullable',
                function ($attribute, $value, $fail) {
                    if (config('app.recaptcha_enabled', false) && empty($value)) {
                        $fail(__('validation.recaptcha_required'));
                    }
                },
            ],
            
            // Honeypot fields (should be empty)
            'website' => [
                'nullable',
                'max:0',
            ],
            'phone_number' => [
                'nullable',
                'max:0',
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'job_id.required' => __('job_applications.validation.job_required'),
            'job_id.exists' => __('job_applications.validation.job_not_found'),
            'resume_id.required' => __('job_applications.validation.resume_required'),
            'resume_id.exists' => __('job_applications.validation.resume_not_found'),
            'cover_letter.min' => __('job_applications.validation.cover_letter_min'),
            'cover_letter.max' => __('job_applications.validation.cover_letter_max'),
            'expected_salary.numeric' => __('job_applications.validation.expected_salary_numeric'),
            'expected_salary.min' => __('job_applications.validation.expected_salary_min'),
            'expected_salary.max' => __('job_applications.validation.expected_salary_max'),
            'salary_currency_id.required_with' => __('job_applications.validation.salary_currency_required'),
            'salary_period_id.required_with' => __('job_applications.validation.salary_period_required'),
            'available_from.date' => __('job_applications.validation.available_from_date'),
            'available_from.after_or_equal' => __('job_applications.validation.available_from_future'),
            'notice_period_days.integer' => __('job_applications.validation.notice_period_integer'),
            'notice_period_days.min' => __('job_applications.validation.notice_period_min'),
            'notice_period_days.max' => __('job_applications.validation.notice_period_max'),
            'additional_info.max' => __('job_applications.validation.additional_info_max'),
            'portfolio_url.url' => __('job_applications.validation.portfolio_url_format'),
            'github_url.regex' => __('job_applications.validation.github_url_format'),
            'linkedin_url.regex' => __('job_applications.validation.linkedin_url_format'),
            'additional_documents.max' => __('job_applications.validation.additional_documents_max'),
            'additional_documents.*.file' => __('job_applications.validation.additional_document_file'),
            'additional_documents.*.mimes' => __('job_applications.validation.additional_document_mimes'),
            'additional_documents.*.max' => __('job_applications.validation.additional_document_size'),
            'preferred_work_type.in' => __('job_applications.validation.preferred_work_type_invalid'),
            'consent_to_contact.accepted' => __('job_applications.validation.consent_to_contact_required'),
            'consent_to_share_resume.accepted' => __('job_applications.validation.consent_to_share_resume_required'),
            'privacy_policy_accepted.accepted' => __('job_applications.validation.privacy_policy_required'),
            'website.max' => __('job_applications.validation.honeypot_triggered'),
            'phone_number.max' => __('job_applications.validation.honeypot_triggered'),
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'job_id' => __('job_applications.attributes.job'),
            'resume_id' => __('job_applications.attributes.resume'),
            'cover_letter' => __('job_applications.attributes.cover_letter'),
            'expected_salary' => __('job_applications.attributes.expected_salary'),
            'salary_currency_id' => __('job_applications.attributes.salary_currency'),
            'salary_period_id' => __('job_applications.attributes.salary_period'),
            'available_from' => __('job_applications.attributes.available_from'),
            'notice_period_days' => __('job_applications.attributes.notice_period'),
            'additional_info' => __('job_applications.attributes.additional_info'),
            'portfolio_url' => __('job_applications.attributes.portfolio_url'),
            'github_url' => __('job_applications.attributes.github_url'),
            'linkedin_url' => __('job_applications.attributes.linkedin_url'),
            'additional_documents' => __('job_applications.attributes.additional_documents'),
            'preferred_work_type' => __('job_applications.attributes.preferred_work_type'),
            'consent_to_contact' => __('job_applications.attributes.consent_to_contact'),
            'consent_to_share_resume' => __('job_applications.attributes.consent_to_share_resume'),
            'privacy_policy_accepted' => __('job_applications.attributes.privacy_policy'),
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Set candidate ID from authenticated user
        $this->merge([
            'candidate_id' => Auth::id(),
        ]);

        // Clean and format salary
        if ($this->has('expected_salary')) {
            $salary = str_replace([',', ' '], '', $this->input('expected_salary'));
            $this->merge(['expected_salary' => $salary]);
        }

        // Convert string booleans to actual booleans
        foreach (['is_willing_to_relocate', 'is_open_to_remote', 'consent_to_contact', 'consent_to_share_resume', 'privacy_policy_accepted'] as $field) {
            if ($this->has($field)) {
                $this->merge([
                    $field => filter_var($this->input($field), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE),
                ]);
            }
        }
    }

    /**
     * Get the processed data for creating the application.
     */
    public function getProcessedData(): array
    {
        $data = $this->validated();
        
        // Add metadata
        $data['applied_at'] = now();
        $data['status'] = JobApplication::STATUS_APPLIED;
        $data['application_source'] = 'website';
        $data['ip_address'] = $this->ip();
        $data['user_agent'] = $this->userAgent();
        
        return $data;
    }

    /**
     * Check for suspicious content in cover letter.
     */
    private function hasSuspiciousContent(string $content): bool
    {
        $suspiciousPatterns = [
            '/\b(click here|visit now|urgent|guaranteed|100%|free money)\b/i',
            '/\b(viagra|casino|lottery|winner|congratulations)\b/i',
            '/(http|https):\/\/[^\s]+/i', // URLs in cover letter
            '/\b\d{4}[\s-]?\d{4}[\s-]?\d{4}[\s-]?\d{4}\b/', // Credit card patterns
        ];

        foreach ($suspiciousPatterns as $pattern) {
            if (preg_match($pattern, $content)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if application meets quality standards.
     */
    public function meetsQualityStandards(): bool
    {
        $coverLetter = $this->input('cover_letter');
        
        // Minimum quality checks
        if ($coverLetter && strlen($coverLetter) < 100) {
            return false;
        }

        // Check for generic content
        $genericPhrases = [
            'to whom it may concern',
            'dear sir/madam',
            'i am writing to apply',
            'please find my resume attached',
        ];

        if ($coverLetter) {
            $lowerContent = strtolower($coverLetter);
            foreach ($genericPhrases as $phrase) {
                if (strpos($lowerContent, $phrase) !== false) {
                    return false;
                }
            }
        }

        return true;
    }
} 