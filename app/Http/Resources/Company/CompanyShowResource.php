<?php

namespace App\Http\Resources\Company;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class CompanyShowResource extends JsonResource
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
            'name' => $this->name,
            'slug' => $this->slug,
            'email' => $this->when($this->canViewContactInfo(), $this->email),
            'phone' => $this->when($this->canViewContactInfo(), $this->phone),
            'website' => $this->website,
            
            // Detailed information
            'details' => $this->details,
            'established_in' => $this->established_in,
            'no_of_employees' => $this->no_of_employees,
            'company_age' => $this->when($this->established_in, $this->getCompanyAge()),
            
            // Complete location information
            'location' => [
                'address' => $this->location,
                'full_address' => $this->getFullLocation(),
                'country' => $this->whenLoaded('country', function () {
                    return [
                        'id' => $this->country->id,
                        'name' => $this->country->name,
                        'code' => $this->country->code,
                        'flag_url' => $this->country->flag_url,
                    ];
                }),
                'state' => $this->whenLoaded('state', function () {
                    return [
                        'id' => $this->state->id,
                        'name' => $this->state->name,
                        'code' => $this->state->code,
                    ];
                }),
                'city' => $this->whenLoaded('city', function () {
                    return [
                        'id' => $this->city->id,
                        'name' => $this->city->name,
                        'timezone' => $this->city->timezone,
                    ];
                }),
            ],
            
            // Categories and classifications
            'industry' => $this->whenLoaded('industry', function () {
                return [
                    'id' => $this->industry->id,
                    'name' => $this->industry->name,
                    'description' => $this->industry->description,
                    'icon' => $this->industry->icon_url,
                ];
            }),
            'company_size' => $this->whenLoaded('companySize', function () {
                return [
                    'id' => $this->companySize->id,
                    'size' => $this->companySize->size,
                    'description' => $this->companySize->description,
                    'min_employees' => $this->companySize->min_employees,
                    'max_employees' => $this->companySize->max_employees,
                ];
            }),
            'ownership_type' => $this->whenLoaded('ownershipType', function () {
                return [
                    'id' => $this->ownershipType->id,
                    'name' => $this->ownershipType->name,
                    'description' => $this->ownershipType->description,
                ];
            }),
            
            // Media and branding
            'media' => [
                'logo' => [
                    'original' => $this->logo_url,
                    'thumbnail' => $this->logo_thumb_url,
                    'medium' => $this->logo_medium_url,
                    'large' => $this->logo_large_url,
                ],
                'banner' => [
                    'url' => $this->banner_url,
                    'thumbnail' => $this->banner_thumb_url,
                ],
                'gallery' => $this->when($this->relationLoaded('media'), function () {
                    return $this->media->map(function ($media) {
                        return [
                            'id' => $media->id,
                            'url' => $media->getUrl(),
                            'thumbnail' => $media->getUrl('thumbnail'),
                            'type' => $media->mime_type,
                            'size' => $media->size,
                        ];
                    });
                }),
            ],
            
            // Comprehensive statistics
            'statistics' => [
                'jobs' => [
                    'total' => $this->whenCounted('jobs'),
                    'active' => $this->whenCounted('activeJobs'),
                    'featured' => $this->when(
                        $this->relationLoaded('jobs'),
                        $this->jobs->where('is_featured', true)->count()
                    ),
                    'recent' => $this->when(
                        $this->relationLoaded('jobs'),
                        $this->jobs->where('created_at', '>=', now()->subDays(30))->count()
                    ),
                ],
                'applications' => [
                    'total' => $this->when(
                        $this->relationLoaded('jobs'),
                        $this->jobs->sum('applications_count')
                    ),
                    'this_month' => $this->when(
                        $this->canViewStatistics(),
                        $this->getApplicationsThisMonth()
                    ),
                    'success_rate' => $this->when(
                        $this->canViewStatistics(),
                        $this->getApplicationSuccessRate()
                    ),
                ],
                'engagement' => [
                    'views_count' => $this->when($this->canViewStatistics(), $this->views_count),
                    'profile_views' => $this->when($this->canViewStatistics(), $this->profile_views),
                    'followers_count' => $this->when($this->canViewStatistics(), $this->followers_count),
                    'rating' => $this->when($this->canViewStatistics(), $this->average_rating),
                    'reviews_count' => $this->when($this->canViewStatistics(), $this->reviews_count),
                ],
            ],
            
            // Status and verification
            'status' => [
                'is_active' => $this->is_active,
                'is_featured' => $this->is_featured,
                'is_verified' => $this->is_verified,
                'is_premium' => $this->is_premium,
                'verification_status' => $this->verification_status,
                'subscription_status' => $this->subscription_status,
                'account_type' => $this->account_type,
            ],
            
            // Social media presence
            'social_links' => [
                'facebook' => $this->facebook_url,
                'twitter' => $this->twitter_url,
                'linkedin' => $this->linkedin_url,
                'google_plus' => $this->google_plus_url,
                'pinterest' => $this->pinterest_url,
                'instagram' => $this->instagram_url,
                'youtube' => $this->youtube_url,
                'has_social_presence' => $this->hasSocialLinks(),
            ],
            
            // Contact information (role-based)
            'contact' => $this->when($this->canViewContactInfo(), [
                'primary_email' => $this->email,
                'secondary_email' => $this->secondary_email,
                'phone' => $this->phone,
                'mobile' => $this->mobile_phone,
                'fax' => $this->fax,
                'contact_person' => [
                    'name' => $this->contact_person_name,
                    'title' => $this->contact_person_title,
                    'email' => $this->contact_person_email,
                    'phone' => $this->contact_person_phone,
                ],
            ]),
            
            // Jobs information
            'jobs' => $this->when($request->boolean('include_jobs'), function () {
                return $this->whenLoaded('jobs', function () {
                    return $this->jobs->map(function ($job) {
                        return [
                            'id' => $job->id,
                            'title' => $job->title,
                            'slug' => $job->slug,
                            'status' => $job->status,
                            'location' => $job->getFullLocation(),
                            'job_type' => $job->jobType?->name,
                            'salary_range' => $job->getFormattedSalaryRange(),
                            'applications_count' => $job->applications_count,
                            'created_at' => $job->created_at?->toISOString(),
                            'expires_at' => $job->job_expiry_date?->toISOString(),
                            'is_featured' => $job->is_featured,
                            'is_urgent' => $job->isUrgent(),
                            'url' => route('jobs.show', $job->slug),
                        ];
                    });
                });
            }),
            
            // Team and culture (if available)
            'culture' => $this->when($this->relationLoaded('culture'), [
                'values' => $this->culture?->values,
                'benefits' => $this->culture?->benefits,
                'work_environment' => $this->culture?->work_environment,
                'diversity_inclusion' => $this->culture?->diversity_inclusion,
                'remote_policy' => $this->culture?->remote_policy,
            ]),
            
            // Important dates
            'dates' => [
                'created' => $this->created_at?->toISOString(),
                'updated' => $this->updated_at?->toISOString(),
                'verified_at' => $this->verified_at?->toISOString(),
                'last_activity' => $this->last_activity_at?->toISOString(),
                'subscription_expires' => $this->subscription_expires_at?->toISOString(),
            ],
            
            // SEO and metadata
            'seo' => [
                'meta_title' => $this->meta_title ?: $this->name,
                'meta_description' => $this->meta_description ?: str($this->details)->limit(160),
                'keywords' => $this->meta_keywords,
                'canonical_url' => route('companies.show', $this->slug),
                'og_image' => $this->logo_url,
            ],
            
            // Navigation URLs
            'urls' => [
                'view' => route('companies.show', $this->slug),
                'jobs' => route('companies.jobs', $this->slug),
                'contact' => route('companies.contact', $this->slug),
                'follow' => route('companies.follow', $this->id),
                'api' => route('api.companies.show', $this->id),
                'edit' => $this->when($this->canEdit(), route('companies.edit', $this->id)),
                'admin' => $this->when($this->canManage(), route('admin.companies.show', $this->id)),
            ],
            
            // User permissions and actions
            'permissions' => $this->when(Auth::check(), [
                'can_view' => $this->canView(),
                'can_edit' => $this->canEdit(),
                'can_delete' => $this->canDelete(),
                'can_manage' => $this->canManage(),
                'can_feature' => $this->canFeature(),
                'can_verify' => $this->canVerify(),
                'can_contact' => $this->canContact(),
                'can_follow' => $this->canFollow(),
                'can_review' => $this->canReview(),
                'can_report' => $this->canReport(),
            ]),
            
            // User interactions
            'user_interactions' => $this->when(Auth::check(), [
                'is_following' => $this->isFollowedByUser(Auth::id()),
                'has_reviewed' => $this->hasUserReviewed(Auth::id()),
                'has_applied_to_jobs' => $this->hasUserAppliedToJobs(Auth::id()),
                'last_interaction' => $this->getLastUserInteraction(Auth::id()),
            ]),
            
            // Related companies (similar industry/location)
            'related_companies' => $this->when($request->boolean('include_related'), function () {
                return $this->getRelatedCompanies(5)->map(function ($company) {
                    return [
                        'id' => $company->id,
                        'name' => $company->name,
                        'slug' => $company->slug,
                        'logo_url' => $company->logo_thumb_url,
                        'industry' => $company->industry?->name,
                        'location' => $company->getShortLocation(),
                        'jobs_count' => $company->active_jobs_count,
                        'url' => route('companies.show', $company->slug),
                    ];
                });
            }),
        ];
    }

    /**
     * Check if user can view contact information.
     */
    private function canViewContactInfo(): bool
    {
        if (!Auth::check()) {
            return false;
        }

        // Admin can view all contact info
        if (Auth::user()->hasRole('Admin')) {
            return true;
        }

        // Company owner can view their own contact info
        if (Auth::user()->id === $this->user_id) {
            return true;
        }

        // Premium users can view contact info
        if (Auth::user()->isPremium()) {
            return true;
        }

        // Users who have applied to company jobs can view basic contact info
        if ($this->hasUserAppliedToJobs(Auth::id())) {
            return true;
        }

        return false;
    }

    /**
     * Check if user can view detailed statistics.
     */
    private function canViewStatistics(): bool
    {
        if (!Auth::check()) {
            return false;
        }

        // Admin can view all statistics
        if (Auth::user()->hasRole('Admin')) {
            return true;
        }

        // Company owner can view their own statistics
        return Auth::user()->id === $this->user_id;
    }

    /**
     * Check if user can edit the company.
     */
    private function canEdit(): bool
    {
        if (!Auth::check()) {
            return false;
        }

        if (Auth::user()->hasRole('Admin')) {
            return true;
        }

        return Auth::user()->id === $this->user_id;
    }

    /**
     * Check if user can manage the company.
     */
    private function canManage(): bool
    {
        if (!Auth::check()) {
            return false;
        }

        return Auth::user()->hasAnyRole(['Admin', 'Super Admin']);
    }

    /**
     * Check if user can delete the company.
     */
    private function canDelete(): bool
    {
        if (!Auth::check()) {
            return false;
        }

        return Auth::user()->hasRole('Admin');
    }

    /**
     * Check if user can feature the company.
     */
    private function canFeature(): bool
    {
        if (!Auth::check()) {
            return false;
        }

        return Auth::user()->hasRole('Admin');
    }

    /**
     * Check if user can verify the company.
     */
    private function canVerify(): bool
    {
        if (!Auth::check()) {
            return false;
        }

        return Auth::user()->hasRole('Admin');
    }

    /**
     * Check if user can contact the company.
     */
    private function canContact(): bool
    {
        if (!Auth::check()) {
            return false;
        }

        // Can't contact own company
        if (Auth::user()->id === $this->user_id) {
            return false;
        }

        return $this->is_active;
    }

    /**
     * Check if user can follow the company.
     */
    private function canFollow(): bool
    {
        if (!Auth::check()) {
            return false;
        }

        // Can't follow own company
        if (Auth::user()->id === $this->user_id) {
            return false;
        }

        return $this->is_active;
    }

    /**
     * Check if user can review the company.
     */
    private function canReview(): bool
    {
        if (!Auth::check()) {
            return false;
        }

        // Can't review own company
        if (Auth::user()->id === $this->user_id) {
            return false;
        }

        // Must have applied to at least one job
        return $this->hasUserAppliedToJobs(Auth::id());
    }

    /**
     * Check if user can report the company.
     */
    private function canReport(): bool
    {
        if (!Auth::check()) {
            return false;
        }

        // Can't report own company
        return Auth::user()->id !== $this->user_id;
    }

    /**
     * Check if user can view the company.
     */
    private function canView(): bool
    {
        if (!Auth::check()) {
            return $this->is_active;
        }

        if (Auth::user()->hasRole('Admin')) {
            return true;
        }

        if (Auth::user()->id === $this->user_id) {
            return true;
        }

        return $this->is_active;
    }
}
