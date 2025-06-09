<?php

namespace App\Http\Resources\Company;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CompanyResource extends JsonResource
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

        return [
            // Core Information
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'email' => $this->email,
            'phone' => $this->phone,
            'website' => $this->website,
            'description' => $this->when(
                $this->description,
                \Str::limit(strip_tags($this->description), 200)
            ),

            // Location
            'location' => [
                'city' => $this->whenLoaded('city', function () {
                    return [
                        'id' => $this->city->id,
                        'name' => $this->city->name,
                    ];
                }),
                'state' => $this->whenLoaded('city.state', function () {
                    return [
                        'id' => $this->city->state->id,
                        'name' => $this->city->state->name,
                    ];
                }),
                'country' => $this->whenLoaded('city.state.country', function () {
                    return [
                        'id' => $this->city->state->country->id,
                        'name' => $this->city->state->country->name,
                        'iso_code' => $this->city->state->country->iso_code,
                    ];
                }),
                'address' => $this->address,
                'full_location' => $this->getFullLocation(),
            ],

            // Company Details
            'company_size' => $this->whenLoaded('companySize', function () {
                return [
                    'id' => $this->companySize->id,
                    'size' => $this->companySize->size,
                    'range' => $this->companySize->from . '-' . $this->companySize->to,
                ];
            }),

            'industry' => $this->whenLoaded('industry', function () {
                return [
                    'id' => $this->industry->id,
                    'name' => $this->industry->name,
                ];
            }),

            'ownership_type' => $this->whenLoaded('ownershipType', function () {
                return [
                    'id' => $this->ownershipType->id,
                    'name' => $this->ownershipType->name,
                ];
            }),

            // Media
            'logo' => $this->logo ? [
                'url' => $this->logo,
                'alt' => __('company.logo_alt', ['name' => $this->name]),
            ] : null,

            'banner' => $this->banner ? [
                'url' => $this->banner,
                'alt' => __('company.banner_alt', ['name' => $this->name]),
            ] : null,

            // Status & Flags
            'is_active' => $this->is_active,
            'is_featured' => $this->is_featured,
            'is_profile_verified' => $this->is_profile_verified,
            'established_in' => $this->established_in,

            // Statistics
            'statistics' => [
                'jobs_count' => $this->jobs_count ?? 0,
                'active_jobs_count' => $this->active_jobs_count ?? 0,
                'followers_count' => $this->followers_count ?? 0,
                'views_count' => $this->views_count ?? 0,
            ],

            // Social Media (condensed)
            'social_links' => array_filter([
                'facebook' => $this->facebook_url,
                'twitter' => $this->twitter_url,
                'linkedin' => $this->linkedin_url,
            ]),

            // Timestamps
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'created_at_human' => $this->created_at->diffForHumans(),

            // User Context
            'user_context' => [
                'can_edit' => $isOwner || $isAdmin,
                'can_follow' => $user && !$isOwner,
                'is_following' => $user ? $this->isFollowedBy($user) : false,
                'can_contact' => $this->is_active && $this->email,
            ],

            // Links
            'links' => [
                'show' => route('api.companies.show', $this->id),
                'public_profile' => route('company.show', $this->slug),
                'jobs' => route('api.companies.jobs', $this->id),
                'logo_url' => $this->logo,
                'website_url' => $this->website,
            ],

            // SEO
            'seo' => [
                'title' => $this->name,
                'description' => $this->description ? 
                    \Str::limit(strip_tags($this->description), 160) : 
                    __('company.default_description', ['name' => $this->name]),
                'image' => $this->logo ?: asset('images/default-company-logo.png'),
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
                'locale' => app()->getLocale(),
                'currency' => config('app.currency', 'USD'),
                'timezone' => $request->user()?->timezone ?? config('app.timezone'),
            ],
        ];
    }
}
