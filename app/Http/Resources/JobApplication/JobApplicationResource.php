<?php

namespace App\Http\Resources\JobApplication;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class JobApplicationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'application_number' => $this->application_number,
            'status' => $this->status,
            'status_text' => $this->getStatusText(),
            'status_badge_class' => $this->getStatusBadgeClass(),
            
            // Job information
            'job' => [
                'id' => $this->job->id,
                'title' => $this->job->title,
                'slug' => $this->job->slug,
                'company' => [
                    'id' => $this->job->company->id,
                    'name' => $this->job->company->name,
                    'slug' => $this->job->company->slug,
                    'logo_url' => $this->job->company->logo_thumb_url,
                ],
                'location' => $this->job->getFullLocation(),
                'job_type' => $this->job->jobType?->name,
                'salary_range' => $this->when(!$this->job->hide_salary, $this->job->getFormattedSalaryRange()),
                'is_featured' => $this->job->is_featured,
                'is_urgent' => $this->job->isUrgent(),
                'expires_at' => $this->job->job_expiry_date?->toISOString(),
                'url' => route('jobs.show', $this->job->slug),
            ],
            
            // Candidate information (role-based visibility)
            'candidate' => $this->when($this->canViewCandidateInfo(), [
                'id' => $this->candidate->id,
                'name' => $this->candidate->getFullName(),
                'email' => $this->candidate->email,
                'phone' => $this->candidate->phone,
                'avatar_url' => $this->candidate->getAvatarUrl(),
                'location' => $this->candidate->getFullLocation(),
                'experience_level' => $this->candidate->experience_level,
                'experience_text' => $this->candidate->getExperienceLevelText(),
                'profile_completion' => $this->candidate->getProfileCompletionPercentage(),
                'is_verified' => $this->candidate->is_verified,
                'profile_url' => route('candidates.show', $this->candidate->id),
            ]),
            
            // Application details
            'application_details' => [
                'cover_letter' => $this->when($this->canViewApplicationDetails(), $this->cover_letter),
                'expected_salary' => $this->when($this->expected_salary, [
                    'amount' => $this->expected_salary,
                    'currency' => $this->salaryCurrency?->symbol,
                    'period' => $this->salaryPeriod?->name,
                    'formatted' => $this->getFormattedExpectedSalary(),
                ]),
                'availability' => [
                    'available_from' => $this->available_from?->toISOString(),
                    'notice_period_days' => $this->notice_period_days,
                    'notice_period_text' => $this->getNoticePeriodText(),
                ],
                'preferences' => [
                    'is_willing_to_relocate' => $this->is_willing_to_relocate,
                    'is_open_to_remote' => $this->is_open_to_remote,
                    'preferred_work_type' => $this->preferred_work_type,
                ],
                'additional_info' => $this->when($this->canViewApplicationDetails(), $this->additional_info),
            ],
            
            // Resume information
            'resume' => $this->whenLoaded('resume', [
                'id' => $this->resume->id,
                'title' => $this->resume->title,
                'file_url' => $this->when($this->canViewResume(), $this->resume->file_url),
                'file_size' => $this->resume->file_size,
                'updated_at' => $this->resume->updated_at?->toISOString(),
                'download_url' => $this->when($this->canViewResume(), route('resumes.download', $this->resume->id)),
            ]),
            
            // Portfolio and links
            'portfolio' => $this->when($this->hasPortfolioLinks(), [
                'portfolio_url' => $this->portfolio_url,
                'github_url' => $this->github_url,
                'linkedin_url' => $this->linkedin_url,
            ]),
            
            // Application timeline
            'timeline' => [
                'applied_at' => $this->applied_at?->toISOString(),
                'viewed_at' => $this->viewed_at?->toISOString(),
                'shortlisted_at' => $this->shortlisted_at?->toISOString(),
                'interviewed_at' => $this->interviewed_at?->toISOString(),
                'rejected_at' => $this->rejected_at?->toISOString(),
                'hired_at' => $this->hired_at?->toISOString(),
                'last_updated' => $this->updated_at?->toISOString(),
                'days_since_applied' => $this->applied_at?->diffInDays(now()),
            ],
            
            // Application source and metadata
            'metadata' => [
                'application_source' => $this->application_source,
                'referral_source' => $this->referral_source,
                'utm_source' => $this->utm_source,
                'utm_medium' => $this->utm_medium,
                'utm_campaign' => $this->utm_campaign,
                'device_type' => $this->device_type,
                'browser' => $this->browser,
                'ip_address' => $this->when($this->canViewMetadata(), $this->ip_address),
                'user_agent' => $this->when($this->canViewMetadata(), $this->user_agent),
            ],
            
            // Employer feedback (if available)
            'feedback' => $this->when($this->canViewFeedback(), [
                'rating' => $this->rating,
                'feedback_notes' => $this->feedback_notes,
                'interview_feedback' => $this->interview_feedback,
                'rejection_reason' => $this->rejection_reason,
                'feedback_given_at' => $this->feedback_given_at?->toISOString(),
                'feedback_by' => $this->feedbackBy?->getFullName(),
            ]),
            
            // Communication history
            'communications' => $this->when($this->canViewCommunications(), function () {
                return $this->communications->map(function ($communication) {
                    return [
                        'id' => $communication->id,
                        'type' => $communication->type,
                        'subject' => $communication->subject,
                        'message' => $communication->message,
                        'sent_at' => $communication->sent_at?->toISOString(),
                        'sent_by' => $communication->sentBy?->getFullName(),
                        'is_read' => $communication->is_read,
                    ];
                });
            }),
            
            // Interview information
            'interviews' => $this->when($this->relationLoaded('interviews'), function () {
                return $this->interviews->map(function ($interview) {
                    return [
                        'id' => $interview->id,
                        'type' => $interview->type,
                        'scheduled_at' => $interview->scheduled_at?->toISOString(),
                        'duration_minutes' => $interview->duration_minutes,
                        'location' => $interview->location,
                        'meeting_link' => $interview->meeting_link,
                        'status' => $interview->status,
                        'interviewer' => $interview->interviewer?->getFullName(),
                        'notes' => $this->when($this->canViewInterviewNotes(), $interview->notes),
                        'feedback' => $this->when($this->canViewInterviewFeedback(), $interview->feedback),
                    ];
                });
            }),
            
            // Documents and attachments
            'documents' => $this->when($this->canViewDocuments(), function () {
                return $this->documents->map(function ($document) {
                    return [
                        'id' => $document->id,
                        'name' => $document->name,
                        'type' => $document->type,
                        'file_url' => $document->file_url,
                        'file_size' => $document->file_size,
                        'uploaded_at' => $document->created_at?->toISOString(),
                        'download_url' => route('documents.download', $document->id),
                    ];
                });
            }),
            
            // Application statistics
            'statistics' => $this->when($this->canViewStatistics(), [
                'profile_views' => $this->profile_views_count,
                'resume_downloads' => $this->resume_downloads_count,
                'email_opens' => $this->email_opens_count,
                'link_clicks' => $this->link_clicks_count,
                'response_time_hours' => $this->getResponseTimeHours(),
            ]),
            
            // Flags and status indicators
            'flags' => [
                'is_viewed' => $this->is_viewed,
                'is_shortlisted' => $this->is_shortlisted,
                'is_interviewed' => $this->is_interviewed,
                'is_hired' => $this->is_hired,
                'is_rejected' => $this->is_rejected,
                'is_withdrawn' => $this->is_withdrawn,
                'is_archived' => $this->is_archived,
                'is_starred' => $this->is_starred,
                'requires_attention' => $this->requiresAttention(),
                'is_overdue' => $this->isOverdue(),
            ],
            
            // URLs and actions
            'urls' => [
                'view' => route('job-applications.show', $this->id),
                'edit' => $this->when($this->canEdit(), route('job-applications.edit', $this->id)),
                'withdraw' => $this->when($this->canWithdraw(), route('job-applications.withdraw', $this->id)),
                'api' => route('api.job-applications.show', $this->id),
            ],
            
            // User permissions
            'permissions' => $this->when(Auth::check(), [
                'can_view' => $this->canView(),
                'can_edit' => $this->canEdit(),
                'can_withdraw' => $this->canWithdraw(),
                'can_view_details' => $this->canViewApplicationDetails(),
                'can_view_resume' => $this->canViewResume(),
                'can_contact_candidate' => $this->canContactCandidate(),
                'can_schedule_interview' => $this->canScheduleInterview(),
                'can_provide_feedback' => $this->canProvideFeedback(),
                'can_change_status' => $this->canChangeStatus(),
                'can_archive' => $this->canArchive(),
            ]),
            
            // Next actions (for workflow)
            'next_actions' => $this->when($this->canViewNextActions(), $this->getNextActions()),
        ];
    }

    /**
     * Check if user can view candidate information.
     */
    private function canViewCandidateInfo(): bool
    {
        if (!Auth::check()) {
            return false;
        }

        // Candidate can view their own info
        if (Auth::id() === $this->candidate_id) {
            return true;
        }

        // Employer can view candidate info for their jobs
        if (Auth::user()->hasRole('Employer') && Auth::id() === $this->job->company->user_id) {
            return true;
        }

        // Admin can view all
        return Auth::user()->hasRole('Admin');
    }

    /**
     * Check if user can view application details.
     */
    private function canViewApplicationDetails(): bool
    {
        if (!Auth::check()) {
            return false;
        }

        // Candidate can view their own application details
        if (Auth::id() === $this->candidate_id) {
            return true;
        }

        // Employer can view details for their jobs
        if (Auth::user()->hasRole('Employer') && Auth::id() === $this->job->company->user_id) {
            return true;
        }

        // Admin can view all
        return Auth::user()->hasRole('Admin');
    }

    /**
     * Check if user can view resume.
     */
    private function canViewResume(): bool
    {
        if (!Auth::check()) {
            return false;
        }

        // Candidate can view their own resume
        if (Auth::id() === $this->candidate_id) {
            return true;
        }

        // Employer can view resume if application is shortlisted or beyond
        if (Auth::user()->hasRole('Employer') && 
            Auth::id() === $this->job->company->user_id && 
            in_array($this->status, ['shortlisted', 'interviewed', 'hired'])) {
            return true;
        }

        // Admin can view all
        return Auth::user()->hasRole('Admin');
    }

    /**
     * Check if user can view metadata.
     */
    private function canViewMetadata(): bool
    {
        if (!Auth::check()) {
            return false;
        }

        return Auth::user()->hasRole('Admin');
    }

    /**
     * Check if user can view feedback.
     */
    private function canViewFeedback(): bool
    {
        if (!Auth::check()) {
            return false;
        }

        // Candidate can view feedback on their application
        if (Auth::id() === $this->candidate_id && $this->feedback_notes) {
            return true;
        }

        // Employer can view feedback they provided
        if (Auth::user()->hasRole('Employer') && Auth::id() === $this->job->company->user_id) {
            return true;
        }

        // Admin can view all
        return Auth::user()->hasRole('Admin');
    }

    /**
     * Check if user can view communications.
     */
    private function canViewCommunications(): bool
    {
        return $this->canViewApplicationDetails();
    }

    /**
     * Check if user can view interview notes.
     */
    private function canViewInterviewNotes(): bool
    {
        if (!Auth::check()) {
            return false;
        }

        // Employer can view notes for their jobs
        if (Auth::user()->hasRole('Employer') && Auth::id() === $this->job->company->user_id) {
            return true;
        }

        // Admin can view all
        return Auth::user()->hasRole('Admin');
    }

    /**
     * Check if user can view interview feedback.
     */
    private function canViewInterviewFeedback(): bool
    {
        if (!Auth::check()) {
            return false;
        }

        // Candidate can view feedback on their interview
        if (Auth::id() === $this->candidate_id) {
            return true;
        }

        // Employer can view feedback they provided
        if (Auth::user()->hasRole('Employer') && Auth::id() === $this->job->company->user_id) {
            return true;
        }

        // Admin can view all
        return Auth::user()->hasRole('Admin');
    }

    /**
     * Check if user can view documents.
     */
    private function canViewDocuments(): bool
    {
        return $this->canViewApplicationDetails();
    }

    /**
     * Check if user can view statistics.
     */
    private function canViewStatistics(): bool
    {
        if (!Auth::check()) {
            return false;
        }

        // Employer can view statistics for their jobs
        if (Auth::user()->hasRole('Employer') && Auth::id() === $this->job->company->user_id) {
            return true;
        }

        // Admin can view all
        return Auth::user()->hasRole('Admin');
    }

    /**
     * Check if user can view next actions.
     */
    private function canViewNextActions(): bool
    {
        if (!Auth::check()) {
            return false;
        }

        // Employer can view next actions for their jobs
        if (Auth::user()->hasRole('Employer') && Auth::id() === $this->job->company->user_id) {
            return true;
        }

        // Admin can view all
        return Auth::user()->hasRole('Admin');
    }

    /**
     * Check if application has portfolio links.
     */
    private function hasPortfolioLinks(): bool
    {
        return $this->portfolio_url || $this->github_url || $this->linkedin_url;
    }

    /**
     * Check if user can edit the application.
     */
    private function canEdit(): bool
    {
        if (!Auth::check()) {
            return false;
        }

        // Candidate can edit their own application if it's still in applied status
        if (Auth::id() === $this->candidate_id && $this->status === 'applied') {
            return true;
        }

        return false;
    }

    /**
     * Check if user can withdraw the application.
     */
    private function canWithdraw(): bool
    {
        if (!Auth::check()) {
            return false;
        }

        // Candidate can withdraw their own application if not hired/rejected
        if (Auth::id() === $this->candidate_id && 
            !in_array($this->status, ['hired', 'rejected', 'withdrawn'])) {
            return true;
        }

        return false;
    }

    /**
     * Check if user can view the application.
     */
    private function canView(): bool
    {
        if (!Auth::check()) {
            return false;
        }

        // Candidate can view their own application
        if (Auth::id() === $this->candidate_id) {
            return true;
        }

        // Employer can view applications for their jobs
        if (Auth::user()->hasRole('Employer') && Auth::id() === $this->job->company->user_id) {
            return true;
        }

        // Admin can view all
        return Auth::user()->hasRole('Admin');
    }

    /**
     * Additional permission methods would be implemented here...
     */
} 