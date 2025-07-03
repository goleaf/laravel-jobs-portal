<?php

namespace App\Http\Resources\Company;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class CompanyIndexResource extends JsonResource
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

            // Basic information
            'details' => $this->when($this->details, str($this->details)->limit(200)),
            'established_in' => $this->established_in,
            'no_of_employees' => $this->no_of_employees,

            // Location
            'location' => [
                'address' => $this->location,
                'country' => $this->whenLoaded('country', function () {
                    return [
                        'id' => $this->country->id,
                        'name' => $this->country->name,
                        'code' => $this->country->code,
                    ];
                }),
                'state' => $this->whenLoaded('state', function () {
                    return [
                        'id' => $this->state->id,
                        'name' => $this->state->name,
                    ];
                }),
                'city' => $this->whenLoaded('city', function () {
                    return [
                        'id' => $this->city->id,
                        'name' => $this->city->name,
                    ];
                }),
            ],

            // Categories
            'industry' => $this->whenLoaded('industry', function () {
                return [
                    'id' => $this->industry->id,
                    'name' => $this->industry->name,
                ];
            }),
            'company_size' => $this->whenLoaded('companySize', function () {
                return [
                    'id' => $this->companySize->id,
                    'size' => $this->companySize->size,
                    'description' => $this->companySize->description,
                ];
            }),
            'ownership_type' => $this->whenLoaded('ownershipType', function () {
                return [
                    'id' => $this->ownershipType->id,
                    'name' => $this->ownershipType->name,
                ];
            }),

            // Media
            'logo' => [
                'url' => $this->logo_url,
                'thumb' => $this->logo_thumb_url,
            ],

            // Statistics
            'statistics' => [
                'jobs_count' => $this->whenCounted('jobs'),
                'active_jobs_count' => $this->whenCounted('activeJobs'),
                'total_applications' => $this->when(
                    $this->relationLoaded('jobs'),
                    $this->jobs->sum('applications_count')
                ),
                'views_count' => $this->when($this->canViewStatistics(), $this->views_count),
            ],

            // Status flags
            'flags' => [
                'is_active' => $this->is_active,
                'is_featured' => $this->is_featured,
                'is_verified' => $this->is_verified,
                'is_premium' => $this->is_premium,
            ],

            // Dates
            'dates' => [
                'created' => $this->created_at?->toISOString(),
                'updated' => $this->updated_at?->toISOString(),
                'verified_at' => $this->verified_at?->toISOString(),
            ],

            // URLs
            'urls' => [
                'view' => route('companies.show', $this->slug),
                'jobs' => route('companies.jobs', $this->slug),
                'api' => route('api.companies.show', $this->id),
            ],

            // Permissions (for authenticated users)
            'permissions' => $this->when(Auth::check(), function () {
                return [
                    'can_view' => $this->canView(),
                    'can_edit' => $this->canEdit(),
                    'can_delete' => $this->canDelete(),
                    'can_feature' => $this->canFeature(),
                    'can_verify' => $this->canVerify(),
                    'can_contact' => $this->canContact(),
                ];
            }),

            // Social links (if public)
            'social_links' => $this->when($this->hasPublicSocialLinks(), [
                'facebook' => $this->facebook_url,
                'twitter' => $this->twitter_url,
                'linkedin' => $this->linkedin_url,
                'google_plus' => $this->google_plus_url,
                'pinterest' => $this->pinterest_url,
            ]),
        ];
    }

    /**
     * Check if user can view contact information.
     */
    private function canViewContactInfo(): bool
    {
        if (! Auth::check()) {
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

        return false;
    }

    /**
     * Check if user can view statistics.
     */
    private function canViewStatistics(): bool
    {
        if (! Auth::check()) {
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
     * Check if company has public social links.
     */
    private function hasPublicSocialLinks(): bool
    {
        return $this->facebook_url
               || $this->twitter_url
               || $this->linkedin_url
               || $this->google_plus_url
               || $this->pinterest_url;
    }

    /**
     * Check if user can view the company.
     */
    private function canView(): bool
    {
        if (! Auth::check()) {
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

    /**
     * Check if user can edit the company.
     */
    private function canEdit(): bool
    {
        if (! Auth::check()) {
            return false;
        }

        if (Auth::user()->hasRole('Admin')) {
            return true;
        }

        return Auth::user()->id === $this->user_id;
    }

    /**
     * Check if user can delete the company.
     */
    private function canDelete(): bool
    {
        if (! Auth::check()) {
            return false;
        }

        return Auth::user()->hasRole('Admin');
    }

    /**
     * Check if user can feature the company.
     */
    private function canFeature(): bool
    {
        if (! Auth::check()) {
            return false;
        }

        return Auth::user()->hasRole('Admin');
    }

    /**
     * Check if user can verify the company.
     */
    private function canVerify(): bool
    {
        if (! Auth::check()) {
            return false;
        }

        return Auth::user()->hasRole('Admin');
    }

    /**
     * Check if user can contact the company.
     */
    private function canContact(): bool
    {
        if (! Auth::check()) {
            return false;
        }

        // Can't contact own company
        if (Auth::user()->id === $this->user_id) {
            return false;
        }

        return $this->is_active;
    }
}
