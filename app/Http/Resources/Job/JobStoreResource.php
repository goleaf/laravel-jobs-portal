<?php

namespace App\Http\Resources\Job;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class JobStoreResource extends JsonResource
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
            'job_id' => $this->job_id,
            'job_title' => $this->job_title,
            'slug' => $this->slug,
            'status' => [
                'code' => $this->status,
                'label' => $this->getStatusLabel(),
                'color' => $this->getStatusColor(),
                'badge_class' => $this->getStatusBadgeClass(),
            ],
            'company' => [
                'id' => $this->company->id,
                'name' => $this->company->user->name ?? $this->company->name,
                'logo_url' => $this->company->company_url,
                'slug' => $this->company->slug,
                'is_verified' => $this->company->is_verified,
                'is_featured' => $this->company->is_featured,
            ],
            'location' => [
                'country' => [
                    'id' => $this->country_id,
                    'name' => $this->country_name,
                ],
                'state' => [
                    'id' => $this->state_id,
                    'name' => $this->state_name,
                ],
                'city' => [
                    'id' => $this->city_id,
                    'name' => $this->city_name,
                ],
                'full_location' => $this->full_location,
            ],
            'categories' => [
                'job_category' => [
                    'id' => $this->job_category_id,
                    'name' => $this->jobCategory->name ?? null,
                ],
                'job_type' => [
                    'id' => $this->job_type_id,
                    'name' => $this->jobType->name ?? null,
                ],
                'career_level' => $this->when($this->career_level_id, [
                    'id' => $this->career_level_id,
                    'name' => $this->careerLevel->name ?? null,
                ]),
                'functional_area' => [
                    'id' => $this->functional_area_id,
                    'name' => $this->functionalArea->name ?? null,
                ],
                'job_shift' => $this->when($this->job_shift_id, [
                    'id' => $this->job_shift_id,
                    'name' => $this->jobShift->name ?? null,
                ]),
            ],
            'salary' => $this->when(!$this->hide_salary, [
                'from' => $this->salary_from,
                'to' => $this->salary_to,
                'currency' => [
                    'id' => $this->currency_id,
                    'code' => $this->currency->currency_code ?? null,
                    'symbol' => $this->currency->currency_symbol ?? null,
                ],
                'period' => [
                    'id' => $this->salary_period_id,
                    'name' => $this->salaryPeriod->period ?? null,
                ],
                'formatted' => $this->formatted_salary_range,
                'is_hidden' => false,
            ]),
            'salary_hidden' => $this->when($this->hide_salary, [
                'is_hidden' => true,
                'message' => __('jobs.salary_negotiable'),
            ]),
            'requirements' => [
                'experience' => [
                    'years' => $this->experience,
                    'formatted' => $this->getFormattedExperience(),
                ],
                'degree_level' => $this->when($this->degree_level_id, [
                    'id' => $this->degree_level_id,
                    'name' => $this->degreeLevel->name ?? null,
                ]),
                'position' => $this->position,
                'gender_preference' => [
                    'code' => $this->no_preference,
                    'label' => $this->getGenderPreferenceLabel(),
                ],
            ],
            'job_details' => [
                'description' => $this->description,
                'key_responsibilities' => $this->when($this->key_responsibilities, $this->key_responsibilities),
                'benefits' => $this->when($this->benefits, $this->benefits),
                'requirements' => $this->when($this->requirements, $this->requirements),
            ],
            'features' => [
                'is_freelance' => $this->is_freelance,
                'is_featured' => $this->isFeatured(),
                'is_urgent' => $this->is_urgent ?? false,
                'is_remote' => $this->is_remote ?? false,
            ],
            'dates' => [
                'expiry_date' => $this->job_expiry_date?->format('Y-m-d'),
                'expiry_formatted' => $this->job_expiry_date?->format('M d, Y'),
                'days_remaining' => $this->getDaysUntilExpiry(),
                'is_expired' => $this->isExpired(),
                'created_at' => $this->created_at->format('Y-m-d H:i:s'),
                'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
            ],
            'skills' => $this->whenLoaded('jobsSkill', function () {
                return $this->jobsSkill->map(function ($skill) {
                    return [
                        'id' => $skill->id,
                        'name' => $skill->name,
                        'slug' => $skill->slug ?? null,
                    ];
                });
            }),
            'tags' => $this->whenLoaded('jobsTag', function () {
                return $this->jobsTag->map(function ($tag) {
                    return [
                        'id' => $tag->id,
                        'name' => $tag->name,
                        'slug' => $tag->slug ?? null,
                    ];
                });
            }),
            'statistics' => [
                'applications_count' => $this->getApplicationsCount(),
                'views_count' => $this->views_count ?? 0,
            ],
            'urls' => [
                'show' => route('jobs.show', $this->id),
                'edit' => $this->when($this->canUserEdit(), route('jobs.edit', $this->id)),
                'apply' => route('jobs.apply', $this->id),
                'company_profile' => route('companies.show', $this->company_id),
            ],
            'permissions' => [
                'can_edit' => $this->canUserEdit(),
                'can_delete' => $this->canUserDelete(),
                'can_apply' => $this->canUserApply(),
                'can_feature' => $this->canUserFeature(),
            ],
            'metadata' => [
                'job_id' => $this->job_id,
                'unique_slug' => $this->slug,
                'seo_title' => $this->getSeoTitle(),
                'seo_description' => $this->getSeoDescription(),
                'language' => app()->getLocale(),
                'created_by_admin' => $this->is_created_by_admin ?? false,
            ],
        ];
    }

    /**
     * Additional resource data for successful creation.
     */
    public function with(Request $request): array
    {
        return [
            'message' => __('jobs.created_successfully'),
            'success' => true,
            'timestamp' => now()->toISOString(),
            'next_actions' => $this->getNextActions(),
        ];
    }

    /**
     * Get status label with multilingual support.
     */
    protected function getStatusLabel(): string
    {
        return match ($this->status) {
            0 => __('jobs.status.draft'),
            1 => __('jobs.status.live'),
            2 => __('jobs.status.closed'),
            3 => __('jobs.status.paused'),
            4 => __('jobs.status.suspended'),
            default => __('jobs.status.unknown')
        };
    }

    /**
     * Get status color for UI.
     */
    protected function getStatusColor(): string
    {
        return match ($this->status) {
            0 => 'warning',
            1 => 'success',
            2 => 'danger',
            3 => 'primary',
            4 => 'secondary',
            default => 'light'
        };
    }

    /**
     * Get status badge CSS class.
     */
    protected function getStatusBadgeClass(): string
    {
        return 'badge badge-'.$this->getStatusColor();
    }

    /**
     * Get formatted experience string.
     */
    protected function getFormattedExperience(): string
    {
        if (0 == $this->experience) {
            return __('jobs.experience.entry_level');
        }
        if (1 == $this->experience) {
            return __('jobs.experience.one_year');
        }

        return __('jobs.experience.years', ['years' => $this->experience]);
    }

    /**
     * Get gender preference label.
     */
    protected function getGenderPreferenceLabel(): string
    {
        return match ($this->no_preference) {
            0 => __('jobs.gender.female'),
            1 => __('jobs.gender.male'),
            2 => __('jobs.gender.both'),
            default => __('jobs.gender.both')
        };
    }

    /**
     * Get days until expiry.
     */
    protected function getDaysUntilExpiry(): ?int
    {
        if (!$this->job_expiry_date) {
            return null;
        }

        $now = now();
        $expiry = $this->job_expiry_date;

        if ($expiry->isFuture()) {
            return $now->diffInDays($expiry);
        }

        return 0;
    }

    /**
     * Check if user can edit this job.
     */
    protected function canUserEdit(): bool
    {
        if (!auth()->check()) {
            return false;
        }

        $user = auth()->user();

        if ($user->hasRole('admin')) {
            return true;
        }

        if ($user->hasRole('employer') && $user->company) {
            return $this->company_id === $user->company->id;
        }

        return false;
    }

    /**
     * Check if user can delete this job.
     */
    protected function canUserDelete(): bool
    {
        if (!$this->canUserEdit()) {
            return false;
        }

        // Cannot delete if there are active applications
        return 0 === $this->getApplicationsCount();
    }

    /**
     * Check if user can apply to this job.
     */
    protected function canUserApply(): bool
    {
        if (!auth()->check()) {
            return true; // Allow guests to see apply button (will redirect to login)
        }

        $user = auth()->user();

        // Only candidates can apply
        if (!$user->hasRole('candidate')) {
            return false;
        }

        // Cannot apply to own company's jobs
        if ($user->company && $this->company_id === $user->company->id) {
            return false;
        }

        // Cannot apply if job is not active
        if (1 !== $this->status || $this->isExpired()) {
            return false;
        }

        // Check if already applied
        return !$this->appliedJobs()
            ->where('user_id', $user->id)
            ->exists()
        ;
    }

    /**
     * Check if user can feature this job.
     */
    protected function canUserFeature(): bool
    {
        return auth()->check() && auth()->user()->hasRole('admin');
    }

    /**
     * Get SEO title.
     */
    protected function getSeoTitle(): string
    {
        return $this->job_title.' - '.($this->company->user->name ?? $this->company->name);
    }

    /**
     * Get SEO description.
     */
    protected function getSeoDescription(): string
    {
        $description = strip_tags($this->description);

        return \Str::limit($description, 160);
    }

    /**
     * Get suggested next actions for the user.
     */
    protected function getNextActions(): array
    {
        $actions = [];

        if (0 === $this->status) { // Draft
            $actions[] = [
                'action' => 'publish',
                'label' => __('jobs.actions.publish_job'),
                'url' => route('jobs.publish', $this->id),
                'method' => 'POST',
            ];
        }

        $actions[] = [
            'action' => 'view',
            'label' => __('jobs.actions.view_job'),
            'url' => route('jobs.show', $this->id),
            'method' => 'GET',
        ];

        $actions[] = [
            'action' => 'edit',
            'label' => __('jobs.actions.edit_job'),
            'url' => route('jobs.edit', $this->id),
            'method' => 'GET',
        ];

        if (auth()->user()->hasRole('admin')
            || (auth()->user()->hasRole('employer') && auth()->user()->can('feature-jobs'))) {
            $actions[] = [
                'action' => 'feature',
                'label' => __('jobs.actions.make_featured'),
                'url' => route('jobs.feature', $this->id),
                'method' => 'POST',
            ];
        }

        return $actions;
    }
}
