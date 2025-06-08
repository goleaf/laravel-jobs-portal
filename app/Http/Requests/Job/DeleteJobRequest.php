<?php

namespace App\Http\Requests\Job;

use App\Models\Job;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\Validator;

/**
 * Context7 Enhanced Form Request for Job destroy
 * Implements Laravel 12 best practices with Context7 MCP patterns
 * Auto-generated for Level 4 Complex System Transformation
 */
class DeleteJobRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $job = $this->route('job');
        
        return auth()->check() && 
               auth()->user()->is_active &&
               $job &&
               (
                   // Admin can delete any job
                   auth()->user()->hasRole('admin') ||
                   // Employer can delete their own company's job
                   (auth()->user()->hasRole('employer') && $job->company_id === auth()->user()->company?->id)
               );
    }

    /**
     * Get the validation rules that apply to the request.
     * Context7 Pattern: Comprehensive validation with security
     */
    public function rules(): array
    {
        return [
            'force_delete' => [
                'boolean'
            ],
            'reason' => [
                'nullable',
                'string',
                'max:500',
                'required_if:force_delete,true'
            ]
        ];
    }

    /**
     * Get custom validation messages with multilanguage support.
     */
    public function messages(): array
    {
        return [
            'force_delete.boolean' => __('validation.job.force_delete_boolean'),
            'reason.string' => __('validation.job.reason_string'),
            'reason.max' => __('validation.job.reason_max'),
            'reason.required_if' => __('validation.job.reason_required_if'),
        ];
    }

    /**
     * Get custom attributes for validator errors.
     * Context7 Pattern: User-friendly field names
     */
    public function attributes(): array
    {
        return [
            'force_delete' => __('form.job.force_delete'),
            'reason' => __('form.job.deletion_reason'),
        ];
    }

    /**
     * Prepare the data for validation.
     * Context7 Pattern: Data normalization
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'force_delete' => $this->boolean('force_delete'),
        ]);
    }

    /**
     * Configure the validator instance.
     * Context7 Pattern: Enhanced validation logic
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            $job = $this->route('job');
            
            if (!$job) {
                $validator->errors()->add('job', __('validation.job.not_found'));
                return;
            }

            // Check if job has active applications
            $activeApplicationsCount = $job->jobApplications()
                                          ->whereIn('status', [
                                              \App\Models\JobApplication::STATUS_APPLIED,
                                              \App\Models\JobApplication::STATUS_IN_PROGRESS,
                                              \App\Models\JobApplication::STATUS_COMPLETED
                                          ])
                                          ->count();

            if ($activeApplicationsCount > 0 && !$this->force_delete) {
                $validator->errors()->add('general', __('validation.job.has_active_applications', [
                    'count' => $activeApplicationsCount
                ]));
            }

            // Check if job is featured and still has time left
            if ($job->is_featured && !$this->force_delete) {
                $featuredRecord = $job->activeFeatured;
                if ($featuredRecord && $featuredRecord->end_date > now()) {
                    $validator->errors()->add('general', __('validation.job.featured_time_remaining'));
                }
            }
        });
    }

    /**
     * Get error messages for failed authorization.
     */
    protected function failedAuthorization(): void
    {
        throw new \Illuminate\Auth\Access\AuthorizationException(
            __('validation.job.unauthorized_deletion')
        );
    }

    /**
     * Handle a failed validation attempt.
     * Context7 Pattern: Enhanced error handling with security monitoring
     */
    protected function failedValidation(Validator $validator): void
    {
        logger()->warning('Context7 validation failed for DeleteJobRequest', [
            'errors' => $validator->errors()->toArray(),
            'controller' => 'JobController',
            'method' => 'destroy',
            'user_id' => $this->user()?->id,
            'ip' => $this->ip(),
            'user_agent' => $this->userAgent(),
        ]);

        parent::failedValidation($validator);
    }
}