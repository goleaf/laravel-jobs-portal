<?php

namespace App\Http\Resources\Job;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class JobShowResource extends JsonResource
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
            'title' => $this->title,
            'slug' => $this->slug,
            'description' => $this->description,
            'job_requirements' => $this->job_requirements,
            'benefits' => $this->benefits,
            'status' => $this->status,
            'status_label' => $this->getStatusLabel(),

            // Job details
            'details' => [
                'job_type' => [
                    'id' => $this->jobType?->id,
                    'name' => $this->jobType?->name,
                ],
                'category' => [
                    'id' => $this->jobCategory?->id,
                    'name' => $this->jobCategory?->name,
                ],
                'functional_area' => [
                    'id' => $this->functionalArea?->id,
                    'name' => $this->functionalArea?->name,
                ],
                'career_level' => [
                    'id' => $this->careerLevel?->id,
                    'name' => $this->careerLevel?->name,
                ],
                'experience' => [
                    'min_years' => $this->experience,
                    'max_years' => $this->experience_max,
                    'description' => $this->experience_description,
                ],
            ],

            // Location information
            'location' => [
                'country' => [
                    'id' => $this->country?->id,
                    'name' => $this->country?->name,
                    'code' => $this->country?->iso2,
                ],
                'state' => [
                    'id' => $this->state?->id,
                    'name' => $this->state?->name,
                ],
                'city' => [
                    'id' => $this->city?->id,
                    'name' => $this->city?->name,
                ],
                'address' => $this->when($this->canViewSensitiveData($request->user()), $this->address),
                'is_remote' => $this->is_remote,
                'is_freelance' => $this->is_freelance,
            ],

            // Salary information
            'salary' => [
                'from' => $this->hide_salary ? null : $this->salary_from,
                'to' => $this->hide_salary ? null : $this->salary_to,
                'currency' => $this->salaryCurrency?->currency_code,
                'period' => $this->salaryPeriod?->period,
                'is_disclosed' => ! $this->hide_salary,
                'formatted' => $this->getFormattedSalaryRange(),
            ],

            // Company information
            'company' => [
                'id' => $this->company?->id,
                'name' => $this->company?->name,
                'logo' => $this->company?->logo_url,
                'website' => $this->company?->website,
                'description' => $this->company?->details,
                'location' => $this->company?->location,
                'is_featured' => $this->company?->is_featured,
                'established_in' => $this->company?->established_in,
                'no_of_employees' => $this->company?->no_of_employees,
            ],

            // Skills and tags
            'skills' => $this->whenLoaded('jobsSkill', function () {
                return $this->jobsSkill->map(function ($skill) {
                    return [
                        'id' => $skill->id,
                        'name' => $skill->name,
                        'is_required' => $skill->pivot->is_required ?? false,
                    ];
                });
            }),
            'tags' => $this->whenLoaded('jobsTag', function () {
                return $this->jobsTag->pluck('name');
            }),

            // Dates and timing
            'dates' => [
                'created' => $this->created_at?->format('Y-m-d H:i:s'),
                'updated' => $this->updated_at?->format('Y-m-d H:i:s'),
                'expires' => $this->expires_at?->format('Y-m-d'),
                'published' => $this->published_at?->format('Y-m-d H:i:s'),
                'days_until_expiry' => $this->expires_at ? $this->expires_at->diffInDays(now()) : null,
                'is_expired' => $this->expires_at ? $this->expires_at->isPast() : false,
                'is_urgent' => $this->is_urgent,
            ],

            // Statistics (only for authorized users)
            'statistics' => $this->when($this->canViewStatistics($request->user()), [
                'applications_count' => $this->job_applications_count ?? 0,
                'views_count' => $this->views_count ?? 0,
                'shortlisted_count' => $this->shortlisted_applications_count ?? 0,
                'hired_count' => $this->hired_applications_count ?? 0,
                'average_application_score' => $this->average_application_score ?? 0,
            ]),

            // Flags and settings
            'flags' => [
                'is_featured' => $this->is_featured,
                'is_suspended' => $this->is_suspended,
                'is_freelance' => $this->is_freelance,
                'is_remote' => $this->is_remote,
                'is_urgent' => $this->is_urgent,
                'hide_salary' => $this->hide_salary,
                'require_cover_letter' => $this->require_cover_letter,
                'allow_remote_work' => $this->allow_remote_work,
            ],

            // User permissions and actions
            'permissions' => [
                'can_edit' => $this->canUserEdit($request->user()),
                'can_delete' => $this->canUserDelete($request->user()),
                'can_apply' => $this->canUserApply($request->user()),
                'can_feature' => $this->canUserFeature($request->user()),
                'can_suspend' => $this->canUserSuspend($request->user()),
                'can_close' => $this->canUserClose($request->user()),
                'can_view_applications' => $this->canViewApplications($request->user()),
            ],

            // URLs and navigation
            'urls' => [
                'public' => route('front.job.details', $this->slug),
                'apply' => $this->when($this->canUserApply($request->user()), route('front.apply.job', $this->slug)),
                'edit' => $this->when($this->canUserEdit($request->user()), route('jobs.edit', $this->id)),
                'applications' => $this->when($this->canViewApplications($request->user()), route('job.applications', $this->id)),
                'share' => [
                    'facebook' => 'https://www.facebook.com/sharer/sharer.php?u='.urlencode(route('front.job.details', $this->slug)),
                    'twitter' => 'https://twitter.com/intent/tweet?url='.urlencode(route('front.job.details', $this->slug)).'&text='.urlencode($this->title),
                    'linkedin' => 'https://www.linkedin.com/sharing/share-offsite/?url='.urlencode(route('front.job.details', $this->slug)),
                ],
            ],

            // Related jobs (only show limited data)
            'related_jobs' => $this->when($request->has('include_related'), function () {
                return $this->getRelatedJobs()->map(function ($job) {
                    return [
                        'id' => $job->id,
                        'title' => $job->title,
                        'slug' => $job->slug,
                        'company_name' => $job->company?->name,
                        'location' => $job->city?->name.', '.$job->country?->name,
                        'url' => route('front.job.details', $job->slug),
                    ];
                });
            }),

            // SEO and meta information
            'seo' => [
                'title' => $this->title.' - '.$this->company?->name,
                'description' => substr(strip_tags($this->description), 0, 160),
                'keywords' => $this->jobsSkill?->pluck('name')->implode(', '),
                'og_image' => $this->company?->logo_url,
            ],

            // Metadata
            'meta' => [
                'resource_type' => 'job_detail',
                'generated_at' => now()->toISOString(),
                'locale' => app()->getLocale(),
                'cached' => false,
                'version' => '1.0',
            ],
        ];
    }

    /**
     * Get formatted salary range.
     */
    private function getFormattedSalaryRange(): ?string
    {
        if ($this->hide_salary || (! $this->salary_from && ! $this->salary_to)) {
            return null;
        }

        $currency = $this->salaryCurrency?->currency_code ?? 'USD';
        $period = $this->salaryPeriod?->period ?? 'month';

        if ($this->salary_from && $this->salary_to) {
            return number_format($this->salary_from).' - '.number_format($this->salary_to).' '.$currency.'/'.$period;
        }
        if ($this->salary_from) {
            return 'From '.number_format($this->salary_from).' '.$currency.'/'.$period;
        }
        if ($this->salary_to) {
            return 'Up to '.number_format($this->salary_to).' '.$currency.'/'.$period;
        }

        return null;
    }

    /**
     * Get the status label for display.
     */
    private function getStatusLabel(): string
    {
        return match ($this->status) {
            'open' => __('jobs.status.open'),
            'closed' => __('jobs.status.closed'),
            'drafted' => __('jobs.status.drafted'),
            'paused' => __('jobs.status.paused'),
            default => __('jobs.status.unknown'),
        };
    }

    /**
     * Check if user can view sensitive data.
     *
     * @param  mixed  $user
     */
    private function canViewSensitiveData($user): bool
    {
        if (! $user) {
            return false;
        }

        return $user->hasRole('Admin')
               || ($user->hasRole('Employer') && $this->company?->user_id === $user->id);
    }

    /**
     * Check if user can view statistics.
     *
     * @param  mixed  $user
     */
    private function canViewStatistics($user): bool
    {
        return $this->canViewSensitiveData($user);
    }

    /**
     * Check if user can edit this job.
     *
     * @param  mixed  $user
     */
    private function canUserEdit($user): bool
    {
        if (! $user) {
            return false;
        }

        return $user->hasRole('Admin')
               || ($user->hasRole('Employer') && $this->company?->user_id === $user->id);
    }

    /**
     * Check if user can delete this job.
     *
     * @param  mixed  $user
     */
    private function canUserDelete($user): bool
    {
        if (! $user) {
            return false;
        }

        return $user->hasRole('Admin')
               || ($user->hasRole('Employer') && $this->company?->user_id === $user->id && $this->job_applications_count === 0);
    }

    /**
     * Check if user can apply to this job.
     *
     * @param  mixed  $user
     */
    private function canUserApply($user): bool
    {
        if (! $user) {
            return false;
        }

        return $user->hasRole('Candidate')
               && $this->status === 'open'
               && ! $this->is_suspended
               && ($this->expires_at === null || $this->expires_at->isFuture());
    }

    /**
     * Check if user can feature this job.
     *
     * @param  mixed  $user
     */
    private function canUserFeature($user): bool
    {
        if (! $user) {
            return false;
        }

        return $user->hasRole('Admin')
               || ($user->hasRole('Employer') && $this->company?->user_id === $user->id);
    }

    /**
     * Check if user can suspend this job.
     *
     * @param  mixed  $user
     */
    private function canUserSuspend($user): bool
    {
        if (! $user) {
            return false;
        }

        return $user->hasRole('Admin');
    }

    /**
     * Check if user can close this job.
     *
     * @param  mixed  $user
     */
    private function canUserClose($user): bool
    {
        return $this->canUserEdit($user);
    }

    /**
     * Check if user can view applications.
     *
     * @param  mixed  $user
     */
    private function canViewApplications($user): bool
    {
        return $this->canViewSensitiveData($user);
    }

    /**
     * Get related jobs (simplified - implement actual logic as needed).
     */
    private function getRelatedJobs()
    {
        // This would typically use a proper related jobs algorithm
        return collect([]);
    }
}
