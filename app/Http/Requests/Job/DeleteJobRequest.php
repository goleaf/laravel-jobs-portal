<?php

namespace App\Http\Requests\Job;

use App\Models\Job;
use App\Models\JobApplication;
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
        $user = auth()->user();
        
        // Admin can always delete jobs
        if ($user->hasRole('admin')) {
            return true;
        }
        
        // Employer can only delete own company's jobs
        if ($user->hasRole('employer') && $user->company) {
            return $job->company_id === $user->company->id && $user->company->is_active;
        }
        
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     * Context7 Pattern: Comprehensive validation with security
     */
    public function rules(): array
    {
        return [
            'force_delete' => [
                'nullable',
                'boolean'
            ],
            'reason' => [
                'nullable',
                'string',
                'max:500',
                'required_if:force_delete,true'
            ],
            'notify_applicants' => [
                'nullable',
                'boolean'
            ],
            'notification_message' => [
                'nullable',
                'string',
                'max:1000',
                'required_if:notify_applicants,true'
            ]
        ];
    }

    /**
     * Get custom validation messages with multilingual support.
     */
    public function messages(): array
    {
        return [
            'reason.required_if' => __('validation.required_if', [
                'attribute' => __('jobs.deletion_reason'),
                'other' => __('jobs.force_delete'),
                'value' => 'true'
            ]),
            'reason.max' => __('validation.max.string', [
                'attribute' => __('jobs.deletion_reason'),
                'max' => 500
            ]),
            'notification_message.required_if' => __('validation.required_if', [
                'attribute' => __('jobs.notification_message'),
                'other' => __('jobs.notify_applicants'),
                'value' => 'true'
            ]),
            'notification_message.max' => __('validation.max.string', [
                'attribute' => __('jobs.notification_message'),
                'max' => 1000
            ])
        ];
    }

    /**
     * Get custom attribute names for multilingual support.
     */
    public function attributes(): array
    {
        return [
            'force_delete' => __('jobs.force_delete'),
            'reason' => __('jobs.deletion_reason'),
            'notify_applicants' => __('jobs.notify_applicants'),
            'notification_message' => __('jobs.notification_message')
        ];
    }

    /**
     * Prepare the data for validation.
     * Context7 Pattern: Data normalization
     */
    protected function prepareForValidation(): void
    {
        // Set default values
        $this->merge([
            'force_delete' => $this->boolean('force_delete', false),
            'notify_applicants' => $this->boolean('notify_applicants', true)
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
            
            // Check if job can be safely deleted
            if (!$this->force_delete && !$this->canSafelyDelete($job)) {
                $this->addJobDeletionErrors($validator, $job);
            }
            
            // Additional validation for forced deletion
            if ($this->force_delete && !auth()->user()->hasRole('admin')) {
                $validator->errors()->add('force_delete', __('jobs.force_delete_admin_only'));
            }
        });
    }

    /**
     * Check if job can be safely deleted without impacting applications.
     */
    protected function canSafelyDelete(Job $job): bool
    {
        // Check for active applications
        $activeApplications = $job->appliedJobs()
            ->whereIn('status', [
                JobApplication::STATUS_APPLIED,
                JobApplication::STATUS_INTERVIEW,
                JobApplication::STATUS_SHORTLISTED
            ])
            ->count();

        return $activeApplications === 0;
    }

    /**
     * Add specific errors for job deletion constraints.
     */
    protected function addJobDeletionErrors($validator, Job $job): void
    {
        $activeApplications = $job->appliedJobs()
            ->whereIn('status', [
                JobApplication::STATUS_APPLIED,
                JobApplication::STATUS_INTERVIEW,
                JobApplication::STATUS_SHORTLISTED
            ])
            ->count();

        if ($activeApplications > 0) {
            $validator->errors()->add('job', __('jobs.cannot_delete_with_active_applications', [
                'count' => $activeApplications
            ]));
        }

        // Check for featured status
        if ($job->isFeatured()) {
            $validator->errors()->add('job', __('jobs.cannot_delete_featured_job'));
        }

        // Check if job is part of ongoing campaigns
        if ($this->hasOngoingCampaigns($job)) {
            $validator->errors()->add('job', __('jobs.cannot_delete_with_campaigns'));
        }
    }

    /**
     * Check if job is part of ongoing marketing campaigns.
     */
    protected function hasOngoingCampaigns(Job $job): bool
    {
        // Check for active featured records
        return $job->activeFeatured()->exists();
    }

    /**
     * Get statistics about job deletion impact.
     */
    public function getDeletionImpact(): array
    {
        $job = $this->route('job');
        
        $totalApplications = $job->appliedJobs()->count();
        $activeApplications = $job->appliedJobs()
            ->whereIn('status', [
                JobApplication::STATUS_APPLIED,
                JobApplication::STATUS_INTERVIEW,
                JobApplication::STATUS_SHORTLISTED
            ])
            ->count();
        
        $completedApplications = $job->appliedJobs()
            ->whereIn('status', [
                JobApplication::STATUS_HIRED,
                JobApplication::STATUS_REJECTED,
                JobApplication::STATUS_DECLINED
            ])
            ->count();

        return [
            'total_applications' => $totalApplications,
            'active_applications' => $activeApplications,
            'completed_applications' => $completedApplications,
            'is_featured' => $job->isFeatured(),
            'has_campaigns' => $this->hasOngoingCampaigns($job),
            'can_safely_delete' => $this->canSafelyDelete($job),
            'views_count' => $job->views_count ?? 0
        ];
    }

    /**
     * Get applicants who should be notified.
     */
    public function getApplicantsToNotify(): array
    {
        if (!$this->notify_applicants) {
            return [];
        }

        $job = $this->route('job');
        
        return $job->appliedJobs()
            ->with(['user:id,name,email'])
            ->whereIn('status', [
                JobApplication::STATUS_APPLIED,
                JobApplication::STATUS_INTERVIEW,
                JobApplication::STATUS_SHORTLISTED
            ])
            ->get()
            ->map(function ($application) {
                return [
                    'id' => $application->id,
                    'user_id' => $application->user_id,
                    'user_name' => $application->user->name,
                    'user_email' => $application->user->email,
                    'application_status' => $application->status,
                    'applied_at' => $application->created_at
                ];
            })
            ->toArray();
    }

    /**
     * Get deletion summary for logging.
     */
    public function getDeletionSummary(): array
    {
        $job = $this->route('job');
        $impact = $this->getDeletionImpact();
        
        return [
            'job_id' => $job->id,
            'job_title' => $job->job_title,
            'company_id' => $job->company_id,
            'company_name' => $job->company->user->name ?? $job->company->name,
            'deleted_by' => [
                'user_id' => auth()->id(),
                'user_name' => auth()->user()->name,
                'user_role' => auth()->user()->roles->pluck('name')->first()
            ],
            'deletion_details' => [
                'force_delete' => $this->force_delete,
                'reason' => $this->reason,
                'notify_applicants' => $this->notify_applicants,
                'notification_message' => $this->notification_message
            ],
            'impact' => $impact,
            'applicants_to_notify' => $this->notify_applicants ? count($this->getApplicantsToNotify()) : 0,
            'deletion_timestamp' => now()->toISOString()
        ];
    }

    /**
     * Check if deletion requires additional confirmation.
     */
    public function requiresConfirmation(): bool
    {
        $impact = $this->getDeletionImpact();
        
        return $impact['active_applications'] > 0 || 
               $impact['is_featured'] || 
               $impact['has_campaigns'] ||
               $impact['total_applications'] > 10;
    }

    /**
     * Get confirmation message for UI.
     */
    public function getConfirmationMessage(): string
    {
        $impact = $this->getDeletionImpact();
        $job = $this->route('job');
        
        $messages = [];
        
        if ($impact['active_applications'] > 0) {
            $messages[] = __('jobs.delete_confirmation.active_applications', [
                'count' => $impact['active_applications']
            ]);
        }
        
        if ($impact['is_featured']) {
            $messages[] = __('jobs.delete_confirmation.featured_job');
        }
        
        if ($impact['has_campaigns']) {
            $messages[] = __('jobs.delete_confirmation.ongoing_campaigns');
        }
        
        if ($impact['total_applications'] > 10) {
            $messages[] = __('jobs.delete_confirmation.many_applications', [
                'count' => $impact['total_applications']
            ]);
        }
        
        if (empty($messages)) {
            return __('jobs.delete_confirmation.default', [
                'job_title' => $job->job_title
            ]);
        }
        
        return __('jobs.delete_confirmation.warning', [
            'job_title' => $job->job_title,
            'issues' => implode(' ', $messages)
        ]);
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