<?php

namespace App\Http\Requests\Job;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class ApplyJobJobApplicationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Clean up expected salary (remove commas, formatting)
        if ($this->has('expected_salary')) {
            $expectedSalary = removeCommaFromNumbers($this->input('expected_salary'));
            $this->merge(['expected_salary' => $expectedSalary]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            'job_id' => [
                'required',
                'integer',
                'exists:jobs,id'
            ],
            'resume_id' => [
                'required',
                'integer',
                'exists:resumes,id'
            ],
            'expected_salary' => [
                'required',
                'numeric',
                'min:0',
                'max:9999999999'
            ],
            'notes' => [
                'nullable',
                'string',
                'max:2000'
            ]
        ];

        // Add Google reCAPTCHA validation if enabled
        if (getSettingValue('enable_google_recaptcha')) {
            $rules['g-recaptcha-response'] = [
                'required',
                'string'
            ];
        }

        return $rules;
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'job_id.required' => __('messages.apply_job.job_id_required'),
            'job_id.exists' => __('messages.apply_job.job_not_found'),
            'resume_id.required' => __('messages.flash.resume_field_required'),
            'resume_id.exists' => __('messages.apply_job.resume_not_found'),
            'expected_salary.required' => __('messages.apply_job.expected_salary_required'),
            'expected_salary.numeric' => __('messages.apply_job.expected_salary_numeric'),
            'expected_salary.min' => __('messages.apply_job.expected_salary_positive'),
            'expected_salary.max' => __('messages.apply_job.expected_salary_too_large'),
            'notes.max' => __('messages.apply_job.notes_too_long'),
            'g-recaptcha-response.required' => __('messages.flash.verify_google_recaptcha'),
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
            'job_id' => __('messages.job.job'),
            'resume_id' => __('messages.apply_job.resume'),
            'expected_salary' => __('messages.candidate.expected_salary'),
            'notes' => __('messages.apply_job.notes'),
            'g-recaptcha-response' => __('messages.common.captcha'),
        ];
    }
}
