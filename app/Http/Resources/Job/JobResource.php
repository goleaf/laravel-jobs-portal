<?php

namespace App\Http\Resources\Job;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class JobResource extends JsonResource
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
            'excerpt' => $this->getExcerpt(),
            
            // Company Information
            'company' => [
                'id' => $this->company_id,
                'name' => $this->company?->name,
                'logo_url' => $this->company?->logo_url,
                'slug' => $this->company?->slug,
            ],
            
            // Job Details
            'category' => [
                'id' => $this->job_category_id,
                'name' => $this->jobCategory?->name,
            ],
            'type' => [
                'id' => $this->job_type_id,
                'name' => $this->jobType?->name,
            ],
            'shift' => $this->when($this->job_shift_id, [
                'id' => $this->job_shift_id,
                'name' => $this->jobShift?->shift,
            ]),
            'career_level' => $this->when($this->career_level_id, [
                'id' => $this->career_level_id,
                'name' => $this->careerLevel?->level_name,
            ]),
            'functional_area' => $this->when($this->functional_area_id, [
                'id' => $this->functional_area_id,
                'name' => $this->functionalArea?->name,
            ]),
            
            // Salary Information
            'salary' => [
                'from' => $this->when(!$this->hide_salary, $this->salary_from),
                'to' => $this->when(!$this->hide_salary, $this->salary_to),
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
                'is_hidden' => $this->hide_salary,
            ],
            
            // Location Information
            'location' => [
                'country' => [
                    'id' => $this->country_id,
                    'name' => $this->country?->name,
                    'code' => $this->country?->short_code,
                ],
                'state' => $this->when($this->state_id, [
                    'id' => $this->state_id,
                    'name' => $this->state?->name,
                ]),
                'city' => $this->when($this->city_id, [
                    'id' => $this->city_id,
                    'name' => $this->city?->name,
                ]),
                'formatted' => $this->getFormattedLocation(),
                'is_remote' => $this->is_remote ?? false,
            ],
            
            // Job Requirements
            'requirements' => [
                'experience' => $this->experience,
                'degree_level' => $this->when($this->degree_level_id, [
                    'id' => $this->degree_level_id,
                    'name' => $this->degreeLevel?->name,
                ]),
                'position_count' => $this->position ?? 1,
                'skills' => $this->whenLoaded('skills', function () {
                    return $this->skills->map(function ($skill) {
                        return [
                            'id' => $skill->id,
                            'name' => $skill->name,
                        ];
                    });
                }),
            ],
            
            // Status and Flags
            'status' => [
                'is_active' => $this->is_active ?? true,
                'is_featured' => $this->is_featured ?? false,
                'is_freelance' => $this->is_freelance ?? false,
                'is_suspended' => $this->is_suspended ?? false,
                'is_expired' => $this->isExpired(),
                'status_label' => $this->getStatusLabel(),
            ],
            
            // Dates
            'dates' => [
                'created_at' => $this->created_at?->toISOString(),
                'updated_at' => $this->updated_at?->toISOString(),
                'expiry_date' => $this->expiry_date?->toISOString(),
                'formatted_created_at' => $this->created_at?->format(__('formats.date_time')),
                'formatted_expiry_date' => $this->expiry_date?->format(__('formats.date')),
                'days_until_expiry' => $this->getDaysUntilExpiry(),
                'time_ago' => $this->created_at?->diffForHumans(),
            ],
            
            // Statistics
            'statistics' => [
                'applications_count' => $this->whenCounted('jobApplications'),
                'views_count' => $this->views_count ?? 0,
                'shares_count' => $this->shares_count ?? 0,
            ],
            
            // Tags
            'tags' => $this->whenLoaded('tags', function () {
                return $this->tags->pluck('name');
            }),
            
            // Permissions
            'permissions' => [
                'can_view' => $request->user()?->can('view', $this->resource) ?? true,
                'can_update' => $request->user()?->can('update', $this->resource) ?? false,
                'can_delete' => $request->user()?->can('delete', $this->resource) ?? false,
                'can_apply' => $request->user()?->can('apply', $this->resource) ?? false,
            ],
            
            // Links
            'links' => [
                'self' => route('api.jobs.show', $this->id),
                'public' => route('jobs.show', $this->slug ?? $this->id),
                'company' => route('api.companies.show', $this->company_id),
                'applications' => route('api.jobs.applications', $this->id),
                'apply' => route('api.jobs.apply', $this->id),
            ],
        ];
    }

    /**
     * Get job description excerpt.
     */
    private function getExcerpt(int $length = 150): string
    {
        return \Str::limit(strip_tags($this->description), $length);
    }

    /**
     * Get formatted salary range.
     */
    private function getFormattedSalary(): ?string
    {
        if ($this->hide_salary || (!$this->salary_from && !$this->salary_to)) {
            return __('jobs.salary.negotiable');
        }

        $currency = $this->salaryCurrency?->currency_icon ?? '$';
        $period = $this->salaryPeriod?->period ?? __('jobs.salary.period.default');

        if ($this->salary_from && $this->salary_to) {
            return __('jobs.salary.range_format', [
                'currency' => $currency,
                'from' => number_format($this->salary_from),
                'to' => number_format($this->salary_to),
                'period' => $period,
            ]);
        }

        if ($this->salary_from) {
            return __('jobs.salary.from_format', [
                'currency' => $currency,
                'amount' => number_format($this->salary_from),
                'period' => $period,
            ]);
        }

        if ($this->salary_to) {
            return __('jobs.salary.up_to_format', [
                'currency' => $currency,
                'amount' => number_format($this->salary_to),
                'period' => $period,
            ]);
        }

        return __('jobs.salary.negotiable');
    }

    /**
     * Get formatted location.
     */
    private function getFormattedLocation(): string
    {
        $parts = array_filter([
            $this->city?->name,
            $this->state?->name,
            $this->country?->name,
        ]);

        $location = implode(', ', $parts);

        if ($this->is_remote ?? false) {
            $location = __('jobs.location.remote') . ($location ? " ({$location})" : '');
        }

        return $location ?: __('jobs.location.not_specified');
    }

    /**
     * Check if job is expired.
     */
    private function isExpired(): bool
    {
        return $this->expiry_date && $this->expiry_date->isPast();
    }

    /**
     * Get status label.
     */
    private function getStatusLabel(): string
    {
        if ($this->is_suspended) {
            return __('jobs.status.suspended');
        }

        if ($this->isExpired()) {
            return __('jobs.status.expired');
        }

        if (!($this->is_active ?? true)) {
            return __('jobs.status.inactive');
        }

        if ($this->is_featured) {
            return __('jobs.status.featured');
        }

        return __('jobs.status.active');
    }

    /**
     * Get days until expiry.
     */
    private function getDaysUntilExpiry(): ?int
    {
        if (!$this->expiry_date) {
            return null;
        }

        return max(0, now()->diffInDays($this->expiry_date, false));
    }
} 