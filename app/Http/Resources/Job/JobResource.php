<?php

namespace App\Http\Resources\Job;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class JobResource
 * 
 * API resource for Job model with conditional field loading,
 * performance optimization, and multilingual support.
 */
class JobResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $user = $request->user();
        $isOwner = $user && $this->company_id === optional($user->candidate)->company_id;
        $isAdmin = $user && $user->hasRole('admin');
        $isEmployer = $user && $user->hasRole('employer');

        return [
            // Basic job information (always included)
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'description' => $this->when(
                $request->has('include_description') || $request->routeIs('jobs.show'),
                $this->description
            ),
            'status' => $this->status,
            'is_active' => $this->is_active,
            'is_featured' => $this->is_featured,
            'is_urgent' => $this->is_urgent,

            // Job categorization
            'job_category' => $this->whenLoaded('jobCategory', function () {
                return [
                    'id' => $this->jobCategory->id,
                    'name' => $this->jobCategory->name,
                    'icon' => $this->jobCategory->icon,
                    'color' => $this->jobCategory->color,
                ];
            }),

            'job_type' => $this->whenLoaded('jobType', function () {
                return [
                    'id' => $this->jobType->id,
                    'name' => $this->jobType->name,
                    'is_remote_friendly' => $this->jobType->is_remote_friendly,
                ];
            }),

            'job_shift' => $this->whenLoaded('jobShift', function () {
                return [
                    'id' => $this->jobShift->id,
                    'shift' => $this->jobShift->shift,
                    'start_time' => $this->jobShift->start_time,
                    'end_time' => $this->jobShift->end_time,
                    'is_flexible' => $this->jobShift->is_flexible,
                ];
            }),

            'career_level' => $this->whenLoaded('careerLevel', function () {
                return [
                    'id' => $this->careerLevel->id,
                    'level_name' => $this->careerLevel->level_name,
                    'min_experience' => $this->careerLevel->min_experience,
                    'max_experience' => $this->careerLevel->max_experience,
                ];
            }),

            'degree_level' => $this->whenLoaded('degreeLevel', function () {
                return [
                    'id' => $this->degreeLevel->id,
                    'name' => $this->degreeLevel->name,
                    'level_order' => $this->degreeLevel->level_order,
                    'years_required' => $this->degreeLevel->years_required,
                ];
            }),

            // Company information
            'company' => $this->whenLoaded('company', function () {
                return [
                    'id' => $this->company->id,
                    'name' => $this->company->name,
                    'slug' => $this->company->slug,
                    'logo' => $this->company->logo_url,
                    'website' => $this->company->website,
                    'established_year' => $this->company->established_year,
                    'company_size' => $this->whenLoaded('company.companySize', [
                        'id' => $this->company->companySize->id ?? null,
                        'size' => $this->company->companySize->size ?? null,
                    ]),
                    'industry' => $this->whenLoaded('company.industry', [
                        'id' => $this->company->industry->id ?? null,
                        'name' => $this->company->industry->name ?? null,
                    ]),
                ];
            }),

            // Location information
            'location' => [
                'country' => $this->whenLoaded('country', [
                    'id' => $this->country->id ?? null,
                    'name' => $this->country->name ?? null,
                    'iso2' => $this->country->iso2 ?? null,
                ]),
                'state' => $this->whenLoaded('state', [
                    'id' => $this->state->id ?? null,
                    'name' => $this->state->name ?? null,
                ]),
                'city' => $this->whenLoaded('city', [
                    'id' => $this->city->id ?? null,
                    'name' => $this->city->name ?? null,
                ]),
                'address' => $this->when($isOwner || $isAdmin, $this->address),
                'is_remote' => $this->is_remote,
                'remote_percentage' => $this->when($this->is_remote, $this->remote_percentage),
            ],

            // Salary information (conditional based on settings and permissions)
            'salary' => $this->when(
                !$this->hide_salary || $isOwner || $isAdmin,
                [
                    'salary_from' => $this->salary_from,
                    'salary_to' => $this->salary_to,
                    'currency' => $this->whenLoaded('salaryCurrency', [
                        'id' => $this->salaryCurrency->id ?? null,
                        'code' => $this->salaryCurrency->currency_code ?? null,
                        'symbol' => $this->salaryCurrency->currency_symbol ?? null,
                    ]),
                    'salary_period' => $this->whenLoaded('salaryPeriod', [
                        'id' => $this->salaryPeriod->id ?? null,
                        'period' => $this->salaryPeriod->period ?? null,
                    ]),
                    'hide_salary' => $this->hide_salary,
                    'is_negotiable' => $this->is_negotiable,
                    'formatted_range' => $this->formatted_salary_range,
                ]
            ),

            // Experience and requirements
            'requirements' => [
                'experience_required' => $this->experience,
                'min_experience' => $this->min_experience,
                'max_experience' => $this->max_experience,
                'skills' => $this->whenLoaded('skills', function () {
                    return $this->skills->map(function ($skill) {
                        return [
                            'id' => $skill->id,
                            'name' => $skill->name,
                            'proficiency_level' => $skill->pivot->proficiency_level ?? null,
                            'is_required' => $skill->pivot->is_required ?? false,
                        ];
                    });
                }),
                'benefits' => $this->when(
                    $request->has('include_benefits'),
                    $this->benefits
                ),
                'other_requirements' => $this->when(
                    $request->has('include_requirements'),
                    $this->other_requirements
                ),
            ],

            // Application information
            'application' => [
                'application_deadline' => $this->expired_at,
                'is_expired' => $this->is_expired,
                'days_until_expiry' => $this->days_until_expiry,
                'total_positions' => $this->total_position,
                'applications_count' => $this->when(
                    $isOwner || $isAdmin || $request->has('include_stats'),
                    $this->applications_count ?? $this->jobApplications()->count()
                ),
                'views_count' => $this->when(
                    $isOwner || $isAdmin || $request->has('include_stats'),
                    $this->views_count ?? 0
                ),
                'can_apply' => $this->can_apply,
                'user_applied' => $this->when(
                    $user && $user->candidate,
                    $this->user_applied
                ),
                'user_favorited' => $this->when(
                    $user && $user->candidate,
                    $this->user_favorited
                ),
            ],

            // SEO and metadata
            'seo' => $this->when(
                $request->has('include_seo'),
                [
                    'meta_title' => $this->meta_title,
                    'meta_description' => $this->meta_description,
                    'keywords' => $this->keywords,
                    'og_image' => $this->og_image_url,
                ]
            ),

            // Timestamps and admin data
            'timestamps' => [
                'created_at' => $this->created_at,
                'updated_at' => $this->updated_at,
                'published_at' => $this->published_at,
                'featured_until' => $this->when($this->is_featured, $this->featured_until),
            ],

            // Admin-only information
            'admin_data' => $this->when(
                $isAdmin || $isOwner,
                [
                    'status_history' => $this->when(
                        $request->has('include_history'),
                        $this->status_history
                    ),
                    'promotion_data' => [
                        'is_promoted' => $this->is_promoted,
                        'promotion_start_date' => $this->promotion_start_date,
                        'promotion_end_date' => $this->promotion_end_date,
                    ],
                    'internal_notes' => $this->when(
                        $isAdmin,
                        $this->internal_notes
                    ),
                ]
            ),

            // Performance metrics (for analytics)
            'metrics' => $this->when(
                ($isOwner || $isAdmin) && $request->has('include_metrics'),
                [
                    'performance_score' => $this->performance_score,
                    'click_through_rate' => $this->click_through_rate,
                    'application_rate' => $this->application_rate,
                    'quality_score' => $this->quality_score,
                ]
            ),

            // Related data
            'related' => $this->when(
                $request->has('include_related'),
                [
                    'similar_jobs_count' => $this->similar_jobs_count,
                    'company_other_jobs_count' => $this->company_other_jobs_count,
                ]
            ),

            // API metadata
            'meta' => [
                'api_version' => '1.0',
                'resource_type' => 'job',
                'last_modified' => $this->updated_at,
                'cache_key' => $this->cache_key,
                'permissions' => [
                    'can_edit' => $isOwner || $isAdmin,
                    'can_delete' => $isOwner || $isAdmin,
                    'can_feature' => $isOwner || $isAdmin,
                    'can_view_applications' => $isOwner || $isAdmin,
                    'can_apply' => $user && $user->candidate && $this->can_apply,
                ],
            ],
        ];
    }

    /**
     * Get additional data that should be returned with the resource array.
     */
    public function with(Request $request): array
    {
        return [
            'links' => [
                'self' => route('api.jobs.show', $this->resource),
                'apply' => $this->when(
                    $request->user() && $request->user()->candidate,
                    route('api.jobs.apply', $this->resource)
                ),
                'favorite' => $this->when(
                    $request->user() && $request->user()->candidate,
                    route('api.jobs.favorite', $this->resource)
                ),
                'company' => route('api.companies.show', $this->company_id),
                'similar' => route('api.jobs.similar', $this->resource),
            ],
            'included' => $this->getIncludedRelationships($request),
        ];
    }

    /**
     * Get included relationships based on request parameters.
     */
    protected function getIncludedRelationships(Request $request): array
    {
        $included = [];
        $includes = explode(',', $request->get('include', ''));

        foreach ($includes as $include) {
            switch (trim($include)) {
                case 'company':
                    $included['company'] = $this->whenLoaded('company');
                    break;
                case 'skills':
                    $included['skills'] = $this->whenLoaded('skills');
                    break;
                case 'applications':
                    if ($this->canViewApplications($request->user())) {
                        $included['applications'] = $this->whenLoaded('jobApplications');
                    }
                    break;
                case 'similar_jobs':
                    $included['similar_jobs'] = $this->when(
                        $request->has('include_similar'),
                        $this->getSimilarJobs()
                    );
                    break;
            }
        }

        return $included;
    }

    /**
     * Check if user can view job applications.
     */
    protected function canViewApplications($user): bool
    {
        if (!$user) {
            return false;
        }

        return $user->hasRole('admin') || 
               ($user->candidate && $this->company_id === $user->candidate->company_id);
    }

    /**
     * Get similar jobs (cached for performance).
     */
    protected function getSimilarJobs()
    {
        return cache()->remember(
            "job_{$this->id}_similar",
            3600,
            function () {
                return $this->resource->getSimilarJobs(5);
            }
        );
    }
} 