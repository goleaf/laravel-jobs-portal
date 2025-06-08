<?php

namespace App\Http\Requests\JobApplication;

use App\Models\JobApplication;
use App\Models\Job;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateJobApplicationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $job = Job::find($this->input('job_id'));
        return $job && $this->user()->can('apply', $job);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'job_id' => 'required|integer|exists:jobs,id',
            'resume_id' => 'nullable|integer|exists:candidate_resumes,id',
            'cover_letter' => 'nullable|string|min:50|max:2000',
            'expected_salary' => 'nullable|numeric|min:0|max:999999999.99',
            'salary_currency_id' => 'nullable|integer|exists:salary_currencies,id',
            'salary_period_id' => 'nullable|integer|exists:salary_periods,id',
            'available_from' => 'nullable|date|after_or_equal:today',
            'notice_period' => 'nullable|integer|min:0|max:365',
            'portfolio_url' => 'nullable|url|max:255',
            'linkedin_url' => 'nullable|url|max:255',
            'additional_info' => 'nullable|string|max:1000',
            'terms_accepted' => 'required|accepted',
            'privacy_accepted' => 'required|accepted',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'job_id.required' => __('validation.job_application.job_id.required'),
            'job_id.exists' => __('validation.job_application.job_id.exists'),
            'resume_id.exists' => __('validation.job_application.resume_id.exists'),
            'cover_letter.string' => __('validation.job_application.cover_letter.string'),
            'cover_letter.min' => __('validation.job_application.cover_letter.min'),
            'cover_letter.max' => __('validation.job_application.cover_letter.max'),
            'expected_salary.numeric' => __('validation.job_application.expected_salary.numeric'),
            'expected_salary.min' => __('validation.job_application.expected_salary.min'),
            'expected_salary.max' => __('validation.job_application.expected_salary.max'),
            'salary_currency_id.exists' => __('validation.job_application.salary_currency_id.exists'),
            'salary_period_id.exists' => __('validation.job_application.salary_period_id.exists'),
            'available_from.date' => __('validation.job_application.available_from.date'),
            'available_from.after_or_equal' => __('validation.job_application.available_from.after_or_equal'),
            'notice_period.integer' => __('validation.job_application.notice_period.integer'),
            'notice_period.min' => __('validation.job_application.notice_period.min'),
            'notice_period.max' => __('validation.job_application.notice_period.max'),
            'portfolio_url.url' => __('validation.job_application.portfolio_url.url'),
            'portfolio_url.max' => __('validation.job_application.portfolio_url.max'),
            'linkedin_url.url' => __('validation.job_application.linkedin_url.url'),
            'linkedin_url.max' => __('validation.job_application.linkedin_url.max'),
            'additional_info.string' => __('validation.job_application.additional_info.string'),
            'additional_info.max' => __('validation.job_application.additional_info.max'),
            'terms_accepted.required' => __('validation.job_application.terms_accepted.required'),
            'terms_accepted.accepted' => __('validation.job_application.terms_accepted.accepted'),
            'privacy_accepted.required' => __('validation.job_application.privacy_accepted.required'),
            'privacy_accepted.accepted' => __('validation.job_application.privacy_accepted.accepted'),
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
            'job_id' => __('attributes.job_application.job'),
            'resume_id' => __('attributes.job_application.resume'),
            'cover_letter' => __('attributes.job_application.cover_letter'),
            'expected_salary' => __('attributes.job_application.expected_salary'),
            'salary_currency_id' => __('attributes.job_application.salary_currency'),
            'salary_period_id' => __('attributes.job_application.salary_period'),
            'available_from' => __('attributes.job_application.available_from'),
            'notice_period' => __('attributes.job_application.notice_period'),
            'portfolio_url' => __('attributes.job_application.portfolio_url'),
            'linkedin_url' => __('attributes.job_application.linkedin_url'),
            'additional_info' => __('attributes.job_application.additional_info'),
            'terms_accepted' => __('attributes.job_application.terms_accepted'),
            'privacy_accepted' => __('attributes.job_application.privacy_accepted'),
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Clean salary value
        if ($this->has('expected_salary')) {
            $salary = str_replace(',', '', $this->input('expected_salary'));
            $this->merge(['expected_salary' => $salary]);
        }

        // Ensure URLs have proper protocol
        $urlFields = ['portfolio_url', 'linkedin_url'];
        foreach ($urlFields as $field) {
            if ($this->has($field) && $this->input($field)) {
                $url = $this->input($field);
                if (!preg_match('/^https?:\/\//', $url)) {
                    $url = 'https://' . $url;
                }
                $this->merge([$field => $url]);
            }
        }

        // Set default values
        $this->merge([
            'candidate_id' => $this->user()->candidate?->id,
            'status' => JobApplication::STATUS_APPLIED,
        ]);
    }

    /**
     * Handle a passed validation attempt.
     */
    protected function passedValidation(): void
    {
        // Log job application attempt for security
        \Log::info('Job application submitted', [
            'user_id' => $this->user()->id,
            'candidate_id' => $this->user()->candidate?->id,
            'job_id' => $this->input('job_id'),
            'ip' => $this->ip(),
        ]);
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            // Check if user has already applied for this job
            if ($this->input('job_id') && $this->user()->candidate) {
                $existingApplication = JobApplication::where('job_id', $this->input('job_id'))
                    ->where('candidate_id', $this->user()->candidate->id)
                    ->exists();

                if ($existingApplication) {
                    $validator->errors()->add('job_id', __('validation.job_application.already_applied'));
                }
            }

            // Check if job is still active and not expired
            if ($this->input('job_id')) {
                $job = Job::find($this->input('job_id'));
                if ($job) {
                    if (!$job->is_active) {
                        $validator->errors()->add('job_id', __('validation.job_application.job_inactive'));
                    }

                    if ($job->expiry_date && $job->expiry_date->isPast()) {
                        $validator->errors()->add('job_id', __('validation.job_application.job_expired'));
                    }

                    if ($job->is_suspended) {
                        $validator->errors()->add('job_id', __('validation.job_application.job_suspended'));
                    }
                }
            }

            // Validate LinkedIn URL format
            if ($this->input('linkedin_url') && !str_contains($this->input('linkedin_url'), 'linkedin.com')) {
                $validator->errors()->add('linkedin_url', __('validation.job_application.linkedin_url_invalid'));
            }

            // Validate salary currency and period consistency
            if ($this->input('expected_salary') && !$this->input('salary_currency_id')) {
                $validator->errors()->add('salary_currency_id', __('validation.job_application.salary_currency_required'));
            }

            if ($this->input('expected_salary') && !$this->input('salary_period_id')) {
                $validator->errors()->add('salary_period_id', __('validation.job_application.salary_period_required'));
            }

            // Validate notice period is reasonable
            if ($this->input('notice_period') && $this->input('notice_period') > 90) {
                $validator->errors()->add('notice_period', __('validation.job_application.notice_period_too_long'));
            }
        });
    }
} 