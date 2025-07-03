<?php

namespace App\Http\Requests\Api\Universal;

use App\Models\Job;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class DestroyJobRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Check if user can delete jobs
        // This should be restricted to admins or job owners (employers)
        return $this->user() && (
            $this->user()->hasRole('admin')
            || $this->user()->hasRole('employer') && $this->userOwnsJob()
        );
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'confirm' => 'required|boolean|accepted',
            'reason' => 'sometimes|string|max:500',
            'notify_applicants' => 'sometimes|boolean',
            'refund_featured' => 'sometimes|boolean',
        ];
    }

    /**
     * Get custom validation messages.
     */
    public function messages(): array
    {
        return [
            'confirm.required' => __('validation.required', ['attribute' => __('validation.attributes.confirm')]),
            'confirm.boolean' => __('validation.boolean', ['attribute' => __('validation.attributes.confirm')]),
            'confirm.accepted' => __('validation.accepted', ['attribute' => __('validation.attributes.confirm')]),
            'reason.string' => __('validation.string', ['attribute' => __('validation.attributes.reason')]),
            'reason.max' => __('validation.max.string', ['attribute' => __('validation.attributes.reason'), 'max' => 500]),
            'notify_applicants.boolean' => __('validation.boolean', ['attribute' => __('validation.attributes.notify_applicants')]),
            'refund_featured.boolean' => __('validation.boolean', ['attribute' => __('validation.attributes.refund_featured')]),
        ];
    }

    /**
     * Get custom attribute names for validation errors.
     */
    public function attributes(): array
    {
        return [
            'confirm' => __('validation.attributes.confirm'),
            'reason' => __('validation.attributes.reason'),
            'notify_applicants' => __('validation.attributes.notify_applicants'),
            'refund_featured' => __('validation.attributes.refund_featured'),
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            $jobId = $this->route('id');
            $job = Job::find($jobId);

            if (! $job) {
                $validator->errors()->add('job', __('validation.exists', ['attribute' => __('validation.attributes.job')]));

                return;
            }

            // Check if job has active applications
            $activeApplicationsCount = $job->applications()->whereIn('status', ['pending', 'reviewing', 'shortlisted'])->count();

            if ($activeApplicationsCount > 0) {
                // Require notification decision for active applications
                if (! $this->has('notify_applicants')) {
                    $validator->errors()->add('notify_applicants', __('validation.required_when_active_applications'));
                }
            }

            // Check if job is featured and eligible for refund
            if ($job->is_featured && $job->featured_until && $job->featured_until->isFuture()) {
                if (! $this->has('refund_featured')) {
                    $validator->errors()->add('refund_featured', __('validation.required_when_featured_active'));
                }
            }

            // Check if user owns the job (for non-admins)
            if ($this->user() && ! $this->user()->hasRole('admin')) {
                if (! $this->userOwnsJob()) {
                    $validator->errors()->add('authorization', __('auth.forbidden'));
                }
            }

            // Validate job status allows deletion
            if ($job->status === 'closed' && $activeApplicationsCount === 0) {
                // Allow deletion of closed jobs with no active applications
            } elseif ($job->status === 'draft') {
                // Allow deletion of draft jobs
            } elseif (! $this->user()->hasRole('admin')) {
                // Only admins can delete active jobs with applications
                $validator->errors()->add('status', __('validation.job_deletion_not_allowed'));
            }
        });
    }

    /**
     * Handle a failed validation attempt for API requests.
     */
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json([
                'success' => false,
                'message' => __('validation.failed'),
                'errors' => $validator->errors(),
            ], 422)
        );
    }

    /**
     * Handle a failed authorization attempt.
     */
    protected function failedAuthorization()
    {
        throw new HttpResponseException(
            response()->json([
                'success' => false,
                'message' => __('auth.forbidden'),
                'errors' => ['authorization' => [__('auth.forbidden')]],
            ], 403)
        );
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Convert confirm to boolean if provided
        if ($this->has('confirm')) {
            $this->merge([
                'confirm' => filter_var($this->confirm, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE),
            ]);
        }

        // Convert notify_applicants to boolean if provided
        if ($this->has('notify_applicants')) {
            $this->merge([
                'notify_applicants' => filter_var($this->notify_applicants, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE),
            ]);
        }

        // Convert refund_featured to boolean if provided
        if ($this->has('refund_featured')) {
            $this->merge([
                'refund_featured' => filter_var($this->refund_featured, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE),
            ]);
        }

        // Clean reason if provided
        if ($this->has('reason')) {
            $this->merge([
                'reason' => trim($this->input('reason')),
            ]);
        }
    }

    /**
     * Check if the authenticated user owns the job.
     */
    private function userOwnsJob(): bool
    {
        $jobId = $this->route('id');
        $user = $this->user();

        if (! $user || ! $jobId) {
            return false;
        }

        // Check if user's company owns the job
        $job = Job::find($jobId);
        if (! $job) {
            return false;
        }

        // Check if user is associated with the company that posted the job
        return $user->company_id === $job->company_id
               || $user->companies()->where('companies.id', $job->company_id)->exists();
    }
}
