<?php

namespace App\Http\Resources\Universal;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ShowJobResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'job' => [
                'id' => $this->id,
                'title' => $this->title,
                'slug' => $this->slug ?? null,
                'description' => $this->description ?? null,
                'requirements' => $this->requirements ?? null,
                'benefits' => $this->benefits ?? null,
                'responsibilities' => $this->responsibilities ?? null,

                'employment' => [
                    'type' => $this->job_type ?? null,
                    'level' => $this->career_level ?? null,
                    'shift' => $this->job_shift ?? null,
                    'experience_required' => $this->experience_required ?? null,
                    'education_required' => $this->degree_level ?? null,
                ],

                'compensation' => [
                    'salary_min' => $this->salary_from ?? null,
                    'salary_max' => $this->salary_to ?? null,
                    'currency' => $this->salary_currency ?? null,
                    'salary_type' => $this->salary_type ?? null,
                    'hide_salary' => $this->hide_salary ?? false,
                    'negotiable' => $this->is_negotiable ?? false,
                ],

                'location' => [
                    'country' => $this->country ?? null,
                    'state' => $this->state ?? null,
                    'city' => $this->city ?? null,
                    'address' => $this->address ?? null,
                    'postal_code' => $this->postal_code ?? null,
                    'remote_ok' => $this->is_remote ?? false,
                    'hybrid' => $this->is_hybrid ?? false,
                ],

                'application' => [
                    'deadline' => $this->expiry_date?->toISOString(),
                    'method' => $this->apply_type ?? 'internal',
                    'external_url' => $this->external_apply_url ?? null,
                    'email' => $this->apply_email ?? null,
                    'instructions' => $this->application_instructions ?? null,
                ],

                'status' => [
                    'current' => $this->status ?? 'draft',
                    'is_active' => 'active' === $this->status,
                    'is_featured' => $this->is_featured ?? false,
                    'is_urgent' => $this->is_urgent ?? false,
                    'featured_until' => $this->featured_until?->toISOString(),
                ],

                'statistics' => [
                    'views' => $this->views_count ?? 0,
                    'applications' => $this->applications_count ?? 0,
                    'shortlisted' => $this->shortlisted_count ?? 0,
                    'hired' => $this->hired_count ?? 0,
                    'days_posted' => $this->created_at ? $this->created_at->diffInDays(now()) : 0,
                ],

                'seo' => [
                    'meta_title' => $this->meta_title ?? null,
                    'meta_description' => $this->meta_description ?? null,
                    'keywords' => $this->keywords ?? null,
                ],

                'timestamps' => [
                    'posted_at' => $this->created_at?->toISOString(),
                    'updated_at' => $this->updated_at?->toISOString(),
                    'expires_at' => $this->expiry_date?->toISOString(),
                    'last_activity' => $this->last_activity_at?->toISOString(),
                ],
            ],

            // Include relationships if requested
            'company' => $this->whenLoaded('company', function () {
                return [
                    'id' => $this->company->id,
                    'name' => $this->company->name,
                    'slug' => $this->company->slug ?? null,
                    'logo' => $this->company->logo ?? null,
                    'website' => $this->company->website ?? null,
                    'industry' => $this->company->industry ?? null,
                    'size' => $this->company->company_size ?? null,
                    'location' => $this->company->city ?? null,
                    'verified' => $this->company->is_verified ?? false,
                ];
            }),

            'category' => $this->whenLoaded('category', function () {
                return [
                    'id' => $this->category->id,
                    'name' => $this->category->name,
                    'slug' => $this->category->slug ?? null,
                    'icon' => $this->category->icon ?? null,
                ];
            }),

            'skills' => $this->whenLoaded('skills', function () {
                return $this->skills->map(function ($skill) {
                    return [
                        'id' => $skill->id,
                        'name' => $skill->name,
                        'required' => $skill->pivot->is_required ?? false,
                        'level' => $skill->pivot->level ?? null,
                    ];
                });
            }),

            'applications' => $this->whenLoaded('applications', function () {
                return $this->applications->map(function ($application) {
                    return [
                        'id' => $application->id,
                        'candidate_name' => $application->candidate->name ?? null,
                        'status' => $application->status,
                        'applied_at' => $application->created_at?->toISOString(),
                        'rating' => $application->rating ?? null,
                    ];
                });
            }),

            'similar_jobs' => $this->when($request->input('include_similar'), function () {
                return $this->getSimilarJobs()->map(function ($job) {
                    return [
                        'id' => $job->id,
                        'title' => $job->title,
                        'company' => $job->company->name ?? null,
                        'location' => $job->city ?? null,
                        'salary_range' => $this->formatSalaryRange($job),
                        'posted_at' => $job->created_at?->toISOString(),
                    ];
                });
            }),
        ];
    }

    /**
     * Get additional data that should be returned with the resource array.
     *
     * @return array<string, mixed>
     */
    public function with(Request $request): array
    {
        return [
            'success' => true,
            'message' => __('messages.job_retrieved'),
            'meta' => [
                'timestamp' => now()->toISOString(),
                'version' => '1.0',
                'endpoint' => 'jobs/show',
                'includes' => $request->input('include', []),
                'view_tracked' => $request->input('track_view', false),
                'cache_ttl' => 600, // 10 minutes
            ],
            'actions' => [
                'can_apply' => $this->canUserApply($request->user()),
                'can_edit' => $this->canUserEdit($request->user()),
                'can_delete' => $this->canUserDelete($request->user()),
                'can_feature' => $this->canUserFeature($request->user()),
            ],
        ];
    }

    /**
     * Customize the response for the resource.
     *
     * @param mixed $response
     */
    public function withResponse(Request $request, $response): void
    {
        $response->setStatusCode(200);
        $response->header('Cache-Control', 'public, max-age=600'); // 10 minutes cache

        // Track view if requested
        if ($request->input('track_view', false) && $request->user()) {
            $this->trackJobView($request->user());
        }
    }

    /**
     * Check if user can apply to this job.
     *
     * @param mixed $user
     */
    private function canUserApply($user): bool
    {
        if (!$user || 'active' !== $this->status) {
            return false;
        }

        // Check if user is a candidate
        if (!$user->hasRole('candidate')) {
            return false;
        }

        // Check if already applied
        if ($this->applications()->where('candidate_id', $user->candidate?->id)->exists()) {
            return false;
        }

        // Check if job is expired
        if ($this->expiry_date && $this->expiry_date->isPast()) {
            return false;
        }

        return true;
    }

    /**
     * Check if user can edit this job.
     *
     * @param mixed $user
     */
    private function canUserEdit($user): bool
    {
        if (!$user) {
            return false;
        }

        return $user->hasRole('admin')
               || ($user->hasRole('employer') && $user->company_id === $this->company_id);
    }

    /**
     * Check if user can delete this job.
     *
     * @param mixed $user
     */
    private function canUserDelete($user): bool
    {
        if (!$user) {
            return false;
        }

        return $user->hasRole('admin')
               || ($user->hasRole('employer') && $user->company_id === $this->company_id);
    }

    /**
     * Check if user can feature this job.
     *
     * @param mixed $user
     */
    private function canUserFeature($user): bool
    {
        if (!$user || $this->is_featured) {
            return false;
        }

        return $user->hasRole('admin')
               || ($user->hasRole('employer') && $user->company_id === $this->company_id);
    }

    /**
     * Track job view.
     *
     * @param mixed $user
     */
    private function trackJobView($user): void
    {
        // Implementation would track the view in database
        // This is a placeholder for the actual tracking logic
    }

    /**
     * Get similar jobs.
     */
    private function getSimilarJobs()
    {
        // Implementation would return similar jobs based on category, skills, location
        // This is a placeholder for the actual similar jobs logic
        return collect([]);
    }

    /**
     * Format salary range.
     *
     * @param mixed $job
     */
    private function formatSalaryRange($job): ?string
    {
        if (!$job->salary_from && !$job->salary_to) {
            return null;
        }

        $currency = $job->salary_currency ?? 'USD';

        if ($job->salary_from && $job->salary_to) {
            return "{$currency} {$job->salary_from} - {$job->salary_to}";
        }

        if ($job->salary_from) {
            return "{$currency} {$job->salary_from}+";
        }

        return "{$currency} {$job->salary_to}";
    }
}
