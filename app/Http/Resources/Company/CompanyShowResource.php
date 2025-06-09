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
        $user = $request->user();
        $isOwner = $user && $user->id === $this->user_id;
        $isAdmin = $user && $user->hasRole('admin');
        $canViewPrivate = $isOwner || $isAdmin;

        return [
            // Basic Information
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'email' => $this->email,
            'phone' => $this->phone,
            'website' => $this->website,
            'established_in' => $this->established_in,
            'description' => $this->description,
            'vision' => $this->vision,
            'mission' => $this->mission,

            // Location Information
            'location' => [
                'address' => $this->address,
                'address2' => $this->address2,
                'city' => $this->whenLoaded('city', function () {
                    return [
                        'id' => $this->city->id,
                        'name' => $this->city->name,
                        'state' => $this->whenLoaded('city.state', function () {
                            return [
                                'id' => $this->city->state->id,
                                'name' => $this->city->state->name,
                                'country' => $this->whenLoaded('city.state.country', function () {
                                    return [
                                        'id' => $this->city->state->country->id,
                                        'name' => $this->city->state->country->name,
                                        'iso_code' => $this->city->state->country->iso_code,
                                    ];
                                }),
                            ];
                        }),
                    ];
                }),
                'postal_code' => $this->postal_code,
                'full_address' => $this->getFullAddress(),
            ],

            // Company Details
            'details' => [
                'company_size' => $this->whenLoaded('companySize', function () {
                    return [
                        'id' => $this->companySize->id,
                        'size' => $this->companySize->size,
                        'from' => $this->companySize->from,
                        'to' => $this->companySize->to,
                    ];
                }),
                'ownership_type' => $this->whenLoaded('ownershipType', function () {
                    return [
                        'id' => $this->ownershipType->id,
                        'name' => $this->ownershipType->name,
                    ];
                }),
                'industry' => $this->whenLoaded('industry', function () {
                    return [
                        'id' => $this->industry->id,
                        'name' => $this->industry->name,
                    ];
                }),
                'organization_type' => $this->whenLoaded('organizationType', function () {
                    return [
                        'id' => $this->organizationType->id,
                        'name' => $this->organizationType->name,
                    ];
                }),
                'team_size' => $this->team_size,
                'date_of_incorporation' => $this->date_of_incorporation,
            ],

            // Media & Branding
            'media' => [
                'logo' => $this->logo ? [
                    'url' => $this->logo,
                    'alt' => __('company.logo_alt', ['name' => $this->name]),
                ] : null,
                'banner' => $this->banner ? [
                    'url' => $this->banner,
                    'alt' => __('company.banner_alt', ['name' => $this->name]),
                ] : null,
                'gallery' => $this->whenLoaded('media', function () {
                    return $this->media->map(function ($media) {
                        return [
                            'id' => $media->id,
                            'url' => $media->getUrl(),
                            'name' => $media->name,
                            'mime_type' => $media->mime_type,
                            'size' => $media->size,
                        ];
                    });
                }),
            ],

            // Social Media
            'social_media' => [
                'facebook_url' => $this->facebook_url,
                'twitter_url' => $this->twitter_url,
                'linkedin_url' => $this->linkedin_url,
                'google_plus_url' => $this->google_plus_url,
                'pinterest_url' => $this->pinterest_url,
            ],

            // Status & Flags
            'status' => [
                'is_active' => $this->is_active,
                'is_featured' => $this->is_featured,
                'is_profile_verified' => $this->is_profile_verified,
                'status_label' => $this->is_active ? __('common.active') : __('common.inactive'),
                'verification_status' => $this->is_profile_verified ? __('company.verified') : __('company.unverified'),
            ],

            // Statistics (Public)
            'statistics' => [
                'total_jobs' => $this->whenCounted('jobs'),
                'active_jobs' => $this->jobs_count ?? 0,
                'total_applications' => $this->whenCounted('jobApplications'),
                'followers_count' => $this->followers_count ?? 0,
                'views_count' => $this->views_count ?? 0,
            ],

            // Private Information (Owner/Admin Only)
            'private_info' => $this->when($canViewPrivate, function () {
                return [
                    'user_id' => $this->user_id,
                    'employer' => $this->whenLoaded('user', function () {
                        return [
                            'id' => $this->user->id,
                            'name' => $this->user->name,
                            'email' => $this->user->email,
                            'phone' => $this->user->phone,
                            'last_login_at' => $this->user->last_login_at,
                        ];
                    }),
                    'subscription' => $this->whenLoaded('activePlan', function () {
                        return [
                            'plan_id' => $this->activePlan->id,
                            'plan_name' => $this->activePlan->label,
                            'expires_at' => $this->activePlan->pivot->expires_at,
                            'is_trial' => $this->activePlan->pivot->is_trial,
                            'jobs_quota' => $this->activePlan->job_allowed,
                            'featured_jobs_quota' => $this->activePlan->featured_job_allowed,
                        ];
                    }),
                ];
            }),

            // Recent Jobs (Limited)
            'recent_jobs' => $this->whenLoaded('jobs', function () {
                return $this->jobs->take(5)->map(function ($job) {
                    return [
                        'id' => $job->id,
                        'title' => $job->title,
                        'slug' => $job->slug,
                        'status' => $job->status,
                        'created_at' => $job->created_at,
                        'expires_at' => $job->deadline,
                        'applications_count' => $job->job_applications_count ?? 0,
                    ];
                });
            }),

            // SEO & Meta
            'seo' => [
                'meta_title' => $this->name . ' - ' . __('company.company_profile'),
                'meta_description' => $this->description ? 
                    \Str::limit(strip_tags($this->description), 160) : 
                    __('company.default_meta_description', ['name' => $this->name]),
                'canonical_url' => route('company.show', $this->slug),
                'og_image' => $this->logo ?: asset('images/default-company-logo.png'),
            ],

            // Timestamps
            'timestamps' => [
                'created_at' => $this->created_at,
                'updated_at' => $this->updated_at,
                'created_at_human' => $this->created_at->diffForHumans(),
                'updated_at_human' => $this->updated_at->diffForHumans(),
            ],

            // Relationships Count
            'relationships_count' => [
                'jobs_count' => $this->jobs_count ?? 0,
                'followers_count' => $this->followers_count ?? 0,
                'reviews_count' => $this->reviews_count ?? 0,
                'media_count' => $this->media_count ?? 0,
            ],

            // Additional Context
            'context' => [
                'can_edit' => $canViewPrivate,
                'can_follow' => $user && !$isOwner,
                'is_following' => $user ? $this->isFollowedBy($user) : false,
                'can_contact' => $this->is_active && $this->email,
                'can_view_jobs' => $this->is_active,
                'locale' => app()->getLocale(),
                'currency' => config('app.currency', 'USD'),
            ],
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
            'meta' => [
                'version' => '1.0',
                'generated_at' => now()->toISOString(),
                'locale' => app()->getLocale(),
                'user_timezone' => $request->user()?->timezone ?? config('app.timezone'),
            ],
            'links' => [
                'self' => route('api.companies.show', $this->id),
                'edit' => route('api.companies.edit', $this->id),
                'jobs' => route('api.companies.jobs', $this->id),
                'public_profile' => route('company.show', $this->slug),
            ],
        ];
    }
}
