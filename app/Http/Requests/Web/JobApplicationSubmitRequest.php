<?php

declare(strict_types=1);

namespace App\Http\Requests\Web;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Class JobApplicationSubmitRequest
 * Enterprise-grade validation for Web Job application submissions
 */
class JobApplicationSubmitRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Public access for job applications
    }

    public function rules(): array
    {
        return [
            'job_id' => [
                'required',
                'integer',
                'min:1',
                Rule::exists('jobs', 'id')->where('status', 'open'),
            ],
            'resume_id' => [
                'required',
                'integer',
                'min:1',
                Rule::exists('candidate_resumes', 'id'),
            ],
            'cover_letter' => [
                'sometimes',
                'string',
                'max:2000',
            ],
            'expected_salary' => [
                'sometimes',
                'integer',
                'min:0',
                'max:10000000',
            ],
            'salary_currency' => [
                'sometimes',
                'string',
                'size:3',
                'regex:/^[A-Z]{3}$/',
            ],
            'available_from' => [
                'sometimes',
                'date',
                'after_or_equal:today',
            ],
            'notice_period' => [
                'sometimes',
                'integer',
                'min:0',
                'max:365',
            ],
            'willingness_to_relocate' => [
                'sometimes',
                'boolean',
            ],
            'remote_work_preference' => [
                'sometimes',
                'string',
                'in:on_site,remote,hybrid,flexible',
            ],
            'application_type' => [
                'sometimes',
                'string',
                'in:submit,draft',
            ],
            'additional_questions' => [
                'sometimes',
                'array',
                'max:20',
            ],
            'additional_questions.*.question_id' => [
                'integer',
                'min:1',
                Rule::exists('job_questions', 'id'),
            ],
            'additional_questions.*.answer' => [
                'string',
                'max:1000',
            ],
            'portfolio_links' => [
                'sometimes',
                'array',
                'max:10',
            ],
            'portfolio_links.*' => [
                'url',
                'max:255',
            ],
            'references' => [
                'sometimes',
                'array',
                'max:5',
            ],
            'references.*.name' => [
                'string',
                'max:100',
                'regex:/^[\p{L}\s\-\'\.]+$/u',
            ],
            'references.*.email' => [
                'email:rfc,dns',
                'max:255',
            ],
            'references.*.phone' => [
                'string',
                'max:20',
                'regex:/^\+?[1-9]\d{1,14}$/',
            ],
            'references.*.relationship' => [
                'string',
                'max:100',
            ],
            'consent_data_processing' => [
                'required',
                'boolean',
                'accepted',
            ],
            'consent_marketing' => [
                'sometimes',
                'boolean',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'job_id.required' => __('validation.custom.application.job_required'),
            'job_id.exists' => __('validation.custom.application.job_not_available'),
            'resume_id.required' => __('validation.custom.application.resume_required'),
            'resume_id.exists' => __('validation.custom.application.resume_not_found'),
            'expected_salary.max' => __('validation.custom.application.salary_too_high'),
            'salary_currency.regex' => __('validation.custom.application.currency_format'),
            'available_from.after_or_equal' => __('validation.custom.application.available_date_invalid'),
            'notice_period.max' => __('validation.custom.application.notice_period_too_long'),
            'portfolio_links.*.url' => __('validation.custom.application.portfolio_url_invalid'),
            'references.*.email.email' => __('validation.custom.application.reference_email_invalid'),
            'consent_data_processing.required' => __('validation.custom.application.consent_required'),
            'consent_data_processing.accepted' => __('validation.custom.application.consent_must_accept'),
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('cover_letter')) {
            $this->merge(['cover_letter' => trim($this->cover_letter)]);
        }

        if ($this->has('salary_currency')) {
            $this->merge(['salary_currency' => strtoupper(trim($this->salary_currency))]);
        }

        if (! $this->has('application_type')) {
            $this->merge(['application_type' => 'submit']);
        }

        // Sanitize references data
        if ($this->has('references')) {
            $references = $this->input('references', []);
            foreach ($references as $key => $reference) {
                if (isset($reference['name'])) {
                    $references[$key]['name'] = trim($reference['name']);
                }
                if (isset($reference['email'])) {
                    $references[$key]['email'] = strtolower(trim($reference['email']));
                }
            }
            $this->merge(['references' => $references]);
        }
    }

    public function getBusinessContext(): array
    {
        return [
            'operation' => 'job_application_submit',
            'job_id' => $this->input('job_id'),
            'application_type' => $this->input('application_type', 'submit'),
            'has_cover_letter' => $this->filled('cover_letter'),
            'has_salary_expectation' => $this->filled('expected_salary'),
            'has_portfolio' => $this->filled('portfolio_links'),
            'has_references' => $this->filled('references'),
            'consent_given' => $this->boolean('consent_data_processing'),
            'marketing_consent' => $this->boolean('consent_marketing'),
        ];
    }
}
