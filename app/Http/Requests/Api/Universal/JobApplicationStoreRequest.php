<?php

namespace App\Http\Requests\Api\Universal;

use App\Models\Job;
use App\Models\JobApplication;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class JobApplicationStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'job_id' => 'required|integer|exists:jobs,id',
            'candidate_id' => 'required|integer|exists:candidates,id',
            'resume_id' => 'sometimes|integer|exists:resumes,id',
            'cover_letter' => 'sometimes|string|max:2000',
            'expected_salary' => 'sometimes|numeric|min:0',
            'salary_currency_id' => 'required_with:expected_salary|integer|exists:salary_currencies,id',
            'availability_date' => 'sometimes|date|after_or_equal:today',
            'notes' => 'sometimes|string|max:1000',
            'portfolio_url' => 'sometimes|url|max:255',
            'linkedin_url' => 'sometimes|url|max:255',
            'github_url' => 'sometimes|url|max:255',
            'website_url' => 'sometimes|url|max:255',
            'phone' => 'sometimes|string|max:20',
            'email' => 'sometimes|email|max:255',
            'is_willing_to_relocate' => 'sometimes|boolean',
            'notice_period_days' => 'sometimes|integer|min:0|max:365',
            'attachments' => 'sometimes|array|max:5',
            'attachments.*' => 'file|mimes:pdf,doc,docx,txt|max:5120',
        ];
    }

    public function messages(): array
    {
        return [
            'job_id.required' => 'Job selection is required.',
            'job_id.exists' => 'The selected job does not exist.',
            'candidate_id.required' => 'Candidate information is required.',
            'candidate_id.exists' => 'The candidate profile does not exist.',
            'resume_id.exists' => 'The selected resume does not exist.',
            'cover_letter.max' => 'Cover letter cannot exceed 2000 characters.',
            'expected_salary.numeric' => 'Expected salary must be a valid number.',
            'salary_currency_id.required_with' => 'Currency is required when salary is specified.',
            'availability_date.after_or_equal' => 'Availability date cannot be in the past.',
            'notes.max' => 'Notes cannot exceed 1000 characters.',
            'portfolio_url.url' => 'Portfolio URL must be a valid URL.',
            'notice_period_days.max' => 'Notice period cannot exceed 365 days.',
            'attachments.max' => 'You can upload maximum 5 attachments.',
            'attachments.*.mimes' => 'Attachments must be PDF, DOC, DOCX, or TXT files.',
            'attachments.*.max' => 'Each attachment cannot exceed 5MB.',
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            // Check if user already applied for this job
            if ($this->has(['job_id', 'candidate_id'])) {
                $existingApplication = JobApplication::where('job_id', $this->job_id)
                    ->where('candidate_id', $this->candidate_id)
                    ->exists()
                ;

                if ($existingApplication) {
                    $validator->errors()->add('job_id', 'You have already applied for this job.');
                }
            }

            // Validate job is still accepting applications
            if ($this->has('job_id')) {
                $job = Job::find($this->job_id);
                if ($job && $job->deadline && $job->deadline->isPast()) {
                    $validator->errors()->add('job_id', 'This job is no longer accepting applications.');
                }
                if ($job && 'published' !== $job->status) {
                    $validator->errors()->add('job_id', 'This job is not currently available for applications.');
                }
            }
        });
    }

    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(
            response()->json([
                'success' => false,
                'message' => 'Job application validation failed',
                'errors' => $validator->errors(),
            ], 422)
        );
    }

    protected function prepareForValidation(): void
    {
        // Clean phone number
        if ($this->has('phone')) {
            $this->merge([
                'phone' => preg_replace('/[^0-9+\-\s]/', '', $this->phone),
            ]);
        }

        // Convert boolean strings
        foreach (['is_willing_to_relocate'] as $field) {
            if ($this->has($field)) {
                $this->merge([
                    $field => filter_var($this->{$field}, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE),
                ]);
            }
        }
    }
}
