<?php

namespace App\Http\Resources\JobApplication;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

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
            'application_number' => $this->getApplicationNumber(),
            
            // Job Information
            'job' => [
                'id' => $this->job_id,
                'title' => $this->job?->title,
                'slug' => $this->job?->slug,
                'company_name' => $this->job?->company?->name,
                'company_logo' => $this->job?->company?->logo_url,
                'location' => $this->job?->getFormattedLocation(),
                'is_active' => $this->job?->is_active,
                'is_expired' => $this->job?->isExpired(),
            ],
            
            // Candidate Information
            'candidate' => [
                'id' => $this->candidate_id,
                'name' => $this->candidate?->user?->getFullName(),
                'email' => $this->candidate?->user?->email,
                'phone' => $this->candidate?->user?->phone,
                'avatar_url' => $this->candidate?->user?->getAvatarUrl(),
                'location' => $this->candidate?->getFormattedLocation(),
            ],
            
            // Application Details
            'application_details' => [
                'cover_letter' => $this->cover_letter,
                'cover_letter_excerpt' => $this->getCoverLetterExcerpt(),
                'resume' => $this->when($this->resume_id, [
                    'id' => $this->resume_id,
                    'title' => $this->resume?->title,
                    'file_url' => $this->resume?->file_url,
                ]),
                'portfolio_url' => $this->portfolio_url,
                'linkedin_url' => $this->linkedin_url,
                'additional_info' => $this->additional_info,
            ],
            
            // Salary Information
            'salary_expectations' => [
                'amount' => $this->expected_salary,
                'currency' => $this->when($this->salary_currency_id, [
                    'id' => $this->salary_currency_id,
                    'name' => $this->salaryCurrency?->currency_name,
                    'code' => $this->salaryCurrency?->currency_code,
                    'icon' => $this->salaryCurrency?->currency_icon,
                ]),
                'period' => $this->when($this->salary_period_id, [
                    'id' => $this->salary_period_id,
                    'name' => $this->salaryPeriod?->period,
                ]),
                'formatted' => $this->getFormattedSalary(),
            ],
            
            // Availability Information
            'availability' => [
                'available_from' => $this->available_from?->toDateString(),
                'notice_period' => $this->notice_period,
                'notice_period_label' => $this->getNoticePeriodLabel(),
                'formatted_available_from' => $this->available_from?->format(__('formats.date')),
            ],
            
            // Application Status
            'status' => [
                'current_status' => $this->status,
                'status_label' => $this->getStatusLabel(),
                'status_color' => $this->getStatusColor(),
                'is_pending' => $this->status === 'applied',
                'is_shortlisted' => $this->status === 'shortlisted',
                'is_interviewed' => $this->status === 'interviewed',
                'is_hired' => $this->status === 'hired',
                'is_rejected' => $this->status === 'rejected',
            ],
            
            // Timeline and History
            'timeline' => [
                'applied_at' => $this->created_at?->toISOString(),
                'last_updated' => $this->updated_at?->toISOString(),
                'status_changed_at' => $this->status_changed_at?->toISOString(),
                'formatted_applied_at' => $this->created_at?->format(__('formats.date_time')),
                'formatted_last_updated' => $this->updated_at?->format(__('formats.date_time')),
                'time_since_applied' => $this->created_at?->diffForHumans(),
                'days_since_applied' => $this->created_at?->diffInDays(now()),
            ],
            
            // Interview Information (if applicable)
            'interview' => $this->when($this->interview_scheduled_at, [
                'scheduled_at' => $this->interview_scheduled_at?->toISOString(),
                'interview_type' => $this->interview_type,
                'interview_location' => $this->interview_location,
                'interview_notes' => $this->interview_notes,
                'formatted_interview_date' => $this->interview_scheduled_at?->format(__('formats.date_time')),
            ]),
            
            // Feedback and Notes
            'feedback' => [
                'employer_notes' => $this->employer_notes,
                'rejection_reason' => $this->rejection_reason,
                'rating' => $this->rating,
                'rating_label' => $this->getRatingLabel(),
                'has_feedback' => !empty($this->employer_notes) || !empty($this->rejection_reason),
            ],
            
            // Statistics
            'statistics' => [
                'profile_views' => $this->profile_views ?? 0,
                'resume_downloads' => $this->resume_downloads ?? 0,
                'response_time' => $this->getResponseTime(),
            ],
            
            // Permissions
            'permissions' => [
                'can_view' => $request->user()?->can('view', $this->resource) ?? false,
                'can_update' => $request->user()?->can('update', $this->resource) ?? false,
                'can_delete' => $request->user()?->can('delete', $this->resource) ?? false,
                'can_shortlist' => $request->user()?->can('shortlist', $this->resource) ?? false,
                'can_interview' => $request->user()?->can('interview', $this->resource) ?? false,
                'can_hire' => $request->user()?->can('hire', $this->resource) ?? false,
                'can_reject' => $request->user()?->can('reject', $this->resource) ?? false,
            ],
            
            // Links
            'links' => [
                'self' => route('api.job-applications.show', $this->id),
                'job' => route('api.jobs.show', $this->job_id),
                'candidate' => route('api.candidates.show', $this->candidate_id),
                'resume' => $this->resume?->file_url,
                'portfolio' => $this->portfolio_url,
                'linkedin' => $this->linkedin_url,
            ],
        ];
    }

    /**
     * Get application number.
     */
    private function getApplicationNumber(): string
    {
        return 'APP-' . str_pad($this->id, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Get cover letter excerpt.
     */
    private function getCoverLetterExcerpt(int $length = 150): ?string
    {
        if (!$this->cover_letter) {
            return null;
        }

        return \Str::limit(strip_tags($this->cover_letter), $length);
    }

    /**
     * Get formatted salary expectation.
     */
    private function getFormattedSalary(): ?string
    {
        if (!$this->expected_salary) {
            return __('job_applications.salary.not_specified');
        }

        $currency = $this->salaryCurrency?->currency_icon ?? '$';
        $period = $this->salaryPeriod?->period ?? __('job_applications.salary.period.default');

        return __('job_applications.salary.format', [
            'currency' => $currency,
            'amount' => number_format($this->expected_salary),
            'period' => $period,
        ]);
    }

    /**
     * Get notice period label.
     */
    private function getNoticePeriodLabel(): ?string
    {
        if (!$this->notice_period) {
            return __('job_applications.availability.immediate');
        }

        if ($this->notice_period == 1) {
            return __('job_applications.availability.one_day');
        }

        if ($this->notice_period <= 7) {
            return __('job_applications.availability.days', ['count' => $this->notice_period]);
        }

        if ($this->notice_period <= 30) {
            $weeks = ceil($this->notice_period / 7);
            return __('job_applications.availability.weeks', ['count' => $weeks]);
        }

        $months = ceil($this->notice_period / 30);
        return __('job_applications.availability.months', ['count' => $months]);
    }

    /**
     * Get status label.
     */
    private function getStatusLabel(): string
    {
        return __('job_applications.status.' . $this->status);
    }

    /**
     * Get status color.
     */
    private function getStatusColor(): string
    {
        return match($this->status) {
            'applied' => 'blue',
            'shortlisted' => 'yellow',
            'interviewed' => 'purple',
            'hired' => 'green',
            'rejected' => 'red',
            default => 'gray',
        };
    }

    /**
     * Get rating label.
     */
    private function getRatingLabel(): ?string
    {
        if (!$this->rating) {
            return null;
        }

        return match($this->rating) {
            1 => __('job_applications.rating.poor'),
            2 => __('job_applications.rating.fair'),
            3 => __('job_applications.rating.good'),
            4 => __('job_applications.rating.very_good'),
            5 => __('job_applications.rating.excellent'),
            default => null,
        };
    }

    /**
     * Get response time in hours.
     */
    private function getResponseTime(): ?int
    {
        if (!$this->status_changed_at || $this->status === 'applied') {
            return null;
        }

        return $this->created_at->diffInHours($this->status_changed_at);
    }
} 