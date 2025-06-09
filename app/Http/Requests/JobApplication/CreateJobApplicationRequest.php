<?php

namespace App\Http\Requests\JobApplication;

use App\Models\Job;
use App\Models\Resume;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateJobApplicationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = $this->user();
        
        // Only authenticated candidates can apply for jobs
        if (!$user || !$user->hasRole('candidate')) {
            return false;
        }
        
        // Check if job exists and is active
        $jobId = $this->input('job_id');
        if (!$jobId) {
            return false;
        }
        
        $job = Job::find($jobId);
        if (!$job || !$job->is_active || $job->deadline < now()) {
            return false;
        }
        
        // Check if user already applied for this job
        $existingApplication = $user->jobApplications()
            ->where('job_id', $jobId)
            ->exists();
            
        if ($existingApplication) {
            return false;
        }
        
        // Check if user has a complete profile
        if (!$user->candidate || !$user->candidate->is_profile_complete) {
            return false;
        }
        
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // Required fields
            'job_id' => [
                'required',
                'integer',
                'exists:jobs,id',
                function ($attribute, $value, $fail) {
                    $job = Job::find($value);
                    if (!$job) {
                        $fail(__('validation.job_not_found'));
                        return;
                    }
                    
                    if (!$job->is_active) {
                        $fail(__('validation.job_not_active'));
                        return;
                    }
                    
                    if ($job->deadline && $job->deadline < now()) {
                        $fail(__('validation.job_deadline_passed'));
                        return;
                    }
                    
                    // Check if user already applied
                    $existingApplication = $this->user()->jobApplications()
                        ->where('job_id', $value)
                        ->exists();
                        
                    if ($existingApplication) {
                        $fail(__('validation.already_applied_for_job'));
                    }
                }
            ],
            
            'resume_id' => [
                'required',
                'integer',
                'exists:resumes,id',
                function ($attribute, $value, $fail) {
                    $resume = Resume::find($value);
                    if (!$resume || $resume->candidate_id !== $this->user()->id) {
                        $fail(__('validation.resume_not_owned'));
                    }
                }
            ],
            
            // Optional fields
            'cover_letter' => ['nullable', 'string', 'max:5000'],
            'expected_salary' => ['nullable', 'numeric', 'min:0', 'max:99999999.99'],
            'salary_currency' => ['nullable', 'string', 'size:3', 'exists:salary_currencies,iso_code'],
            'availability_date' => ['nullable', 'date', 'after_or_equal:today'],
            'notice_period' => ['nullable', 'integer', 'min:0', 'max:365'],
            'willing_to_relocate' => ['nullable', 'boolean'],
            'willing_to_travel' => ['nullable', 'boolean'],
            'remote_work_preference' => ['nullable', 'string', Rule::in(['no', 'partial', 'full'])],
            
            // Additional information
            'motivation' => ['nullable', 'string', 'max:2000'],
            'relevant_experience' => ['nullable', 'string', 'max:3000'],
            'additional_notes' => ['nullable', 'string', 'max:1000'],
            
            // Skills and qualifications
            'skills' => ['nullable', 'array', 'max:20'],
            'skills.*' => ['integer', 'exists:skills,id'],
            'certifications' => ['nullable', 'array', 'max:10'],
            'certifications.*' => ['string', 'max:255'],
            'languages' => ['nullable', 'array', 'max:10'],
            'languages.*' => ['integer', 'exists:languages,id'],
            
            // Portfolio and documents
            'portfolio_url' => ['nullable', 'url', 'max:255'],
            'linkedin_profile' => ['nullable', 'url', 'max:255'],
            'github_profile' => ['nullable', 'url', 'max:255'],
            'personal_website' => ['nullable', 'url', 'max:255'],
            
            // File uploads
            'additional_documents' => ['nullable', 'array', 'max:5'],
            'additional_documents.*' => [
                'file',
                'mimes:pdf,doc,docx,txt',
                'max:5120', // 5MB
            ],
            
            // Application preferences
            'preferred_interview_time' => ['nullable', 'string', Rule::in(['morning', 'afternoon', 'evening', 'flexible'])],
            'preferred_interview_method' => ['nullable', 'string', Rule::in(['in_person', 'video_call', 'phone_call', 'flexible'])],
            'preferred_contact_method' => ['nullable', 'string', Rule::in(['email', 'phone', 'sms', 'whatsapp'])],
            
            // Privacy and consent
            'consent_data_processing' => ['required', 'accepted'],
            'consent_marketing_communications' => ['nullable', 'boolean'],
            'consent_profile_sharing' => ['nullable', 'boolean'],
            
            // Tracking and analytics
            'source' => ['nullable', 'string', 'max:100'],
            'referrer_url' => ['nullable', 'url', 'max:500'],
            'utm_source' => ['nullable', 'string', 'max:100'],
            'utm_medium' => ['nullable', 'string', 'max:100'],
            'utm_campaign' => ['nullable', 'string', 'max:100'],
            
            // reCAPTCHA (if enabled)
            'g-recaptcha-response' => [
                Rule::requiredIf(function () {
                    return config('services.recaptcha.enabled', false);
                }),
                'string',
            ],
            
            // Questionnaire responses (if job has custom questions)
            'questionnaire_responses' => ['nullable', 'array'],
            'questionnaire_responses.*.question_id' => ['required_with:questionnaire_responses', 'integer'],
            'questionnaire_responses.*.answer' => ['required_with:questionnaire_responses', 'string', 'max:1000'],
            
            // Timezone and locale
            'timezone' => ['nullable', 'string', 'max:50'],
            'locale' => ['nullable', 'string', 'size:2', Rule::in(['en', 'ar', 'es', 'fr', 'de', 'pt', 'ru', 'tr', 'zh'])],
        ];
    }

    /**
     * Get custom error messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'job_id.required' => __('validation.job_id_required'),
            'job_id.integer' => __('validation.job_id_must_be_integer'),
            'job_id.exists' => __('validation.job_not_found'),
            
            'resume_id.required' => __('validation.resume_id_required'),
            'resume_id.integer' => __('validation.resume_id_must_be_integer'),
            'resume_id.exists' => __('validation.resume_not_found'),
            
            'cover_letter.string' => __('validation.cover_letter_must_be_string'),
            'cover_letter.max' => __('validation.cover_letter_too_long'),
            
            'expected_salary.numeric' => __('validation.expected_salary_must_be_numeric'),
            'expected_salary.min' => __('validation.expected_salary_negative'),
            'expected_salary.max' => __('validation.expected_salary_too_high'),
            
            'salary_currency.string' => __('validation.salary_currency_must_be_string'),
            'salary_currency.size' => __('validation.salary_currency_invalid_length'),
            'salary_currency.exists' => __('validation.salary_currency_not_found'),
            
            'availability_date.date' => __('validation.availability_date_invalid'),
            'availability_date.after_or_equal' => __('validation.availability_date_past'),
            
            'notice_period.integer' => __('validation.notice_period_must_be_integer'),
            'notice_period.min' => __('validation.notice_period_negative'),
            'notice_period.max' => __('validation.notice_period_too_long'),
            
            'willing_to_relocate.boolean' => __('validation.willing_to_relocate_must_be_boolean'),
            'willing_to_travel.boolean' => __('validation.willing_to_travel_must_be_boolean'),
            'remote_work_preference.in' => __('validation.invalid_remote_work_preference'),
            
            'motivation.string' => __('validation.motivation_must_be_string'),
            'motivation.max' => __('validation.motivation_too_long'),
            
            'relevant_experience.string' => __('validation.relevant_experience_must_be_string'),
            'relevant_experience.max' => __('validation.relevant_experience_too_long'),
            
            'additional_notes.string' => __('validation.additional_notes_must_be_string'),
            'additional_notes.max' => __('validation.additional_notes_too_long'),
            
            'skills.array' => __('validation.skills_must_be_array'),
            'skills.max' => __('validation.too_many_skills'),
            'skills.*.integer' => __('validation.skill_id_must_be_integer'),
            'skills.*.exists' => __('validation.skill_not_found'),
            
            'certifications.array' => __('validation.certifications_must_be_array'),
            'certifications.max' => __('validation.too_many_certifications'),
            'certifications.*.string' => __('validation.certification_must_be_string'),
            'certifications.*.max' => __('validation.certification_too_long'),
            
            'languages.array' => __('validation.languages_must_be_array'),
            'languages.max' => __('validation.too_many_languages'),
            'languages.*.integer' => __('validation.language_id_must_be_integer'),
            'languages.*.exists' => __('validation.language_not_found'),
            
            'portfolio_url.url' => __('validation.portfolio_url_invalid'),
            'portfolio_url.max' => __('validation.portfolio_url_too_long'),
            
            'linkedin_profile.url' => __('validation.linkedin_profile_invalid'),
            'linkedin_profile.max' => __('validation.linkedin_profile_too_long'),
            
            'github_profile.url' => __('validation.github_profile_invalid'),
            'github_profile.max' => __('validation.github_profile_too_long'),
            
            'personal_website.url' => __('validation.personal_website_invalid'),
            'personal_website.max' => __('validation.personal_website_too_long'),
            
            'additional_documents.array' => __('validation.additional_documents_must_be_array'),
            'additional_documents.max' => __('validation.too_many_additional_documents'),
            'additional_documents.*.file' => __('validation.additional_document_must_be_file'),
            'additional_documents.*.mimes' => __('validation.additional_document_invalid_type'),
            'additional_documents.*.max' => __('validation.additional_document_too_large'),
            
            'preferred_interview_time.in' => __('validation.invalid_preferred_interview_time'),
            'preferred_interview_method.in' => __('validation.invalid_preferred_interview_method'),
            'preferred_contact_method.in' => __('validation.invalid_preferred_contact_method'),
            
            'consent_data_processing.required' => __('validation.consent_data_processing_required'),
            'consent_data_processing.accepted' => __('validation.consent_data_processing_must_be_accepted'),
            
            'consent_marketing_communications.boolean' => __('validation.consent_marketing_communications_must_be_boolean'),
            'consent_profile_sharing.boolean' => __('validation.consent_profile_sharing_must_be_boolean'),
            
            'source.string' => __('validation.source_must_be_string'),
            'source.max' => __('validation.source_too_long'),
            
            'referrer_url.url' => __('validation.referrer_url_invalid'),
            'referrer_url.max' => __('validation.referrer_url_too_long'),
            
            'utm_source.string' => __('validation.utm_source_must_be_string'),
            'utm_source.max' => __('validation.utm_source_too_long'),
            
            'utm_medium.string' => __('validation.utm_medium_must_be_string'),
            'utm_medium.max' => __('validation.utm_medium_too_long'),
            
            'utm_campaign.string' => __('validation.utm_campaign_must_be_string'),
            'utm_campaign.max' => __('validation.utm_campaign_too_long'),
            
            'g-recaptcha-response.required' => __('validation.recaptcha_required'),
            'g-recaptcha-response.string' => __('validation.recaptcha_invalid'),
            
            'questionnaire_responses.array' => __('validation.questionnaire_responses_must_be_array'),
            'questionnaire_responses.*.question_id.required_with' => __('validation.question_id_required'),
            'questionnaire_responses.*.question_id.integer' => __('validation.question_id_must_be_integer'),
            'questionnaire_responses.*.answer.required_with' => __('validation.answer_required'),
            'questionnaire_responses.*.answer.string' => __('validation.answer_must_be_string'),
            'questionnaire_responses.*.answer.max' => __('validation.answer_too_long'),
            
            'timezone.string' => __('validation.timezone_must_be_string'),
            'timezone.max' => __('validation.timezone_too_long'),
            
            'locale.string' => __('validation.locale_must_be_string'),
            'locale.size' => __('validation.locale_invalid_length'),
            'locale.in' => __('validation.locale_not_supported'),
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
            'job_id' => __('attributes.job'),
            'resume_id' => __('attributes.resume'),
            'cover_letter' => __('attributes.cover_letter'),
            'expected_salary' => __('attributes.expected_salary'),
            'salary_currency' => __('attributes.salary_currency'),
            'availability_date' => __('attributes.availability_date'),
            'notice_period' => __('attributes.notice_period'),
            'willing_to_relocate' => __('attributes.willing_to_relocate'),
            'willing_to_travel' => __('attributes.willing_to_travel'),
            'remote_work_preference' => __('attributes.remote_work_preference'),
            'motivation' => __('attributes.motivation'),
            'relevant_experience' => __('attributes.relevant_experience'),
            'additional_notes' => __('attributes.additional_notes'),
            'skills' => __('attributes.skills'),
            'certifications' => __('attributes.certifications'),
            'languages' => __('attributes.languages'),
            'portfolio_url' => __('attributes.portfolio_url'),
            'linkedin_profile' => __('attributes.linkedin_profile'),
            'github_profile' => __('attributes.github_profile'),
            'personal_website' => __('attributes.personal_website'),
            'additional_documents' => __('attributes.additional_documents'),
            'preferred_interview_time' => __('attributes.preferred_interview_time'),
            'preferred_interview_method' => __('attributes.preferred_interview_method'),
            'preferred_contact_method' => __('attributes.preferred_contact_method'),
            'consent_data_processing' => __('attributes.consent_data_processing'),
            'consent_marketing_communications' => __('attributes.consent_marketing_communications'),
            'consent_profile_sharing' => __('attributes.consent_profile_sharing'),
            'source' => __('attributes.source'),
            'referrer_url' => __('attributes.referrer_url'),
            'utm_source' => __('attributes.utm_source'),
            'utm_medium' => __('attributes.utm_medium'),
            'utm_campaign' => __('attributes.utm_campaign'),
            'g-recaptcha-response' => __('attributes.recaptcha'),
            'questionnaire_responses' => __('attributes.questionnaire_responses'),
            'timezone' => __('attributes.timezone'),
            'locale' => __('attributes.locale'),
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Set default values
        $this->merge([
            'salary_currency' => $this->input('salary_currency', config('app.currency', 'USD')),
            'remote_work_preference' => $this->input('remote_work_preference', 'no'),
            'preferred_interview_time' => $this->input('preferred_interview_time', 'flexible'),
            'preferred_interview_method' => $this->input('preferred_interview_method', 'flexible'),
            'preferred_contact_method' => $this->input('preferred_contact_method', 'email'),
            'consent_marketing_communications' => $this->input('consent_marketing_communications', false),
            'consent_profile_sharing' => $this->input('consent_profile_sharing', false),
            'willing_to_relocate' => $this->input('willing_to_relocate', false),
            'willing_to_travel' => $this->input('willing_to_travel', false),
            'locale' => $this->input('locale', app()->getLocale()),
            'timezone' => $this->input('timezone', $this->user()?->timezone ?? config('app.timezone')),
        ]);

        // Clean and format expected salary
        if ($this->filled('expected_salary')) {
            $salary = str_replace([',', ' '], '', $this->input('expected_salary'));
            $this->merge(['expected_salary' => $salary]);
        }

        // Convert string booleans to actual booleans
        $booleanFields = [
            'willing_to_relocate', 'willing_to_travel', 'consent_data_processing',
            'consent_marketing_communications', 'consent_profile_sharing'
        ];
        
        foreach ($booleanFields as $field) {
            if ($this->has($field)) {
                $this->merge([
                    $field => filter_var($this->input($field), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE),
                ]);
            }
        }

        // Handle comma-separated skills
        if ($this->has('skills') && is_string($this->input('skills'))) {
            $this->merge([
                'skills' => array_map('intval', array_filter(explode(',', $this->input('skills')))),
            ]);
        }

        // Handle comma-separated languages
        if ($this->has('languages') && is_string($this->input('languages'))) {
            $this->merge([
                'languages' => array_map('intval', array_filter(explode(',', $this->input('languages')))),
            ]);
        }

        // Handle comma-separated certifications
        if ($this->has('certifications') && is_string($this->input('certifications'))) {
            $this->merge([
                'certifications' => array_filter(explode(',', $this->input('certifications'))),
            ]);
        }

        // Clean URLs
        $urlFields = ['portfolio_url', 'linkedin_profile', 'github_profile', 'personal_website', 'referrer_url'];
        foreach ($urlFields as $field) {
            if ($this->filled($field)) {
                $url = trim($this->input($field));
                if (!str_starts_with($url, 'http://') && !str_starts_with($url, 'https://')) {
                    $url = 'https://' . $url;
                }
                $this->merge([$field => $url]);
            }
        }
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $user = $this->user();
            
            // Validate user profile completeness
            if (!$user->candidate || !$user->candidate->is_profile_complete) {
                $validator->errors()->add('profile', __('validation.profile_incomplete'));
            }
            
            // Validate job requirements match
            $jobId = $this->input('job_id');
            if ($jobId) {
                $job = Job::with(['requiredSkills', 'requiredDegreeLevel', 'careerLevel'])->find($jobId);
                if ($job) {
                    // Check required skills
                    if ($job->requiredSkills->isNotEmpty() && $this->filled('skills')) {
                        $userSkills = $this->input('skills');
                        $requiredSkills = $job->requiredSkills->pluck('id')->toArray();
                        $matchingSkills = array_intersect($userSkills, $requiredSkills);
                        
                        if (empty($matchingSkills)) {
                            $validator->errors()->add('skills', __('validation.no_matching_required_skills'));
                        }
                    }
                    
                    // Check minimum experience
                    if ($job->min_experience && $user->candidate) {
                        $userExperience = $user->candidate->total_experience ?? 0;
                        if ($userExperience < $job->min_experience) {
                            $validator->errors()->add('experience', 
                                __('validation.insufficient_experience', [
                                    'required' => $job->min_experience,
                                    'current' => $userExperience
                                ])
                            );
                        }
                    }
                    
                    // Check salary expectations
                    if ($this->filled('expected_salary') && $job->max_salary) {
                        $expectedSalary = $this->input('expected_salary');
                        if ($expectedSalary > $job->max_salary * 1.2) { // Allow 20% buffer
                            $validator->errors()->add('expected_salary', 
                                __('validation.salary_expectation_too_high', [
                                    'max_salary' => number_format($job->max_salary)
                                ])
                            );
                        }
                    }
                }
            }
            
            // Validate reCAPTCHA if enabled
            if (config('services.recaptcha.enabled') && $this->filled('g-recaptcha-response')) {
                $recaptchaResponse = $this->input('g-recaptcha-response');
                $secretKey = config('services.recaptcha.secret_key');
                
                $response = \Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
                    'secret' => $secretKey,
                    'response' => $recaptchaResponse,
                    'remoteip' => $this->ip(),
                ]);
                
                $result = $response->json();
                if (!$result['success']) {
                    $validator->errors()->add('g-recaptcha-response', __('validation.recaptcha_failed'));
                }
            }
            
            // Validate file uploads
            if ($this->hasFile('additional_documents')) {
                $files = $this->file('additional_documents');
                foreach ($files as $index => $file) {
                    if ($file && $file->isValid()) {
                        // Check for malicious content
                        $content = file_get_contents($file->getPathname());
                        if ($this->containsSuspiciousContent($content)) {
                            $validator->errors()->add("additional_documents.{$index}", 
                                __('validation.suspicious_file_content')
                            );
                        }
                    }
                }
            }
            
            // Validate timezone
            if ($this->filled('timezone')) {
                $timezone = $this->input('timezone');
                if (!in_array($timezone, timezone_identifiers_list())) {
                    $validator->errors()->add('timezone', __('validation.invalid_timezone'));
                }
            }
        });
    }

    /**
     * Check if content contains suspicious patterns.
     */
    private function containsSuspiciousContent(string $content): bool
    {
        $suspiciousPatterns = [
            '/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/mi',
            '/javascript:/i',
            '/vbscript:/i',
            '/onload\s*=/i',
            '/onerror\s*=/i',
            '/onclick\s*=/i',
        ];
        
        foreach ($suspiciousPatterns as $pattern) {
            if (preg_match($pattern, $content)) {
                return true;
            }
        }
        
        return false;
    }
} 