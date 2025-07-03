<?php

namespace App\Http\Resources\Universal;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CompanyShowResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'name' => $this->name,
            'slug' => $this->slug,
            'email' => $this->email,
            'phone' => $this->phone,
            'website' => $this->website,
            'description' => $this->description,
            'logo' => $this->getLogoUrl(),
            'cover_image' => $this->getCoverImageUrl(),
            'industry' => [
                'id' => $this->industry_id,
                'name' => $this->industry?->name,
                'slug' => $this->industry?->slug,
            ],
            'company_size' => [
                'id' => $this->company_size_id,
                'name' => $this->companySize?->name,
                'range' => $this->companySize?->range,
            ],
            'ownership_type' => [
                'id' => $this->ownership_type_id,
                'name' => $this->ownershipType?->name,
            ],
            'employee_count' => $this->employee_count,
            'founded_year' => $this->founded_year,
            'revenue' => [
                'amount' => $this->revenue,
                'currency' => $this->revenueCurrency?->name,
                'currency_id' => $this->revenue_currency_id,
                'currency_symbol' => $this->revenueCurrency?->symbol,
                'formatted' => $this->getFormattedRevenue(),
            ],
            'location' => [
                'address' => $this->address,
                'country' => $this->country?->name,
                'country_id' => $this->country_id,
                'state' => $this->state?->name,
                'state_id' => $this->state_id,
                'city' => $this->city?->name,
                'city_id' => $this->city_id,
                'postal_code' => $this->postal_code,
                'latitude' => $this->latitude,
                'longitude' => $this->longitude,
                'full_address' => $this->getFullAddress(),
            ],
            'social_links' => [
                'facebook' => $this->social_facebook,
                'twitter' => $this->social_twitter,
                'linkedin' => $this->social_linkedin,
                'instagram' => $this->social_instagram,
                'youtube' => $this->social_youtube,
            ],
            'profile' => [
                'benefits' => $this->benefits,
                'specialties' => $this->specialties,
                'culture_tags' => $this->culture_tags,
                'is_featured' => $this->is_featured,
                'is_verified' => $this->is_verified,
                'is_remote_friendly' => $this->is_remote_friendly,
                'is_active' => $this->is_active,
                'status' => $this->status,
                'profile_completion' => $this->getProfileCompletionPercentage(),
            ],
            'jobs' => $this->when($this->relationLoaded('jobs'), function () {
                return $this->jobs->map(function ($job) {
                    return [
                        'id' => $job->id,
                        'title' => $job->title,
                        'type' => $job->jobType?->name,
                        'location' => $job->city?->name.', '.$job->state?->name,
                        'salary_min' => $job->salary_from,
                        'salary_max' => $job->salary_to,
                        'is_featured' => $job->is_featured,
                        'created_at' => $job->created_at?->format('Y-m-d'),
                    ];
                });
            }),
            'statistics' => [
                'total_jobs' => $this->jobs()->count(),
                'active_jobs' => $this->jobs()->where('status', 'active')->count(),
                'total_applications' => $this->getTotalApplications(),
                'profile_views' => $this->profile_views ?? 0,
                'follower_count' => $this->followers()->count(),
            ],
            'contact_person' => $this->when($this->relationLoaded('user'), function () {
                return [
                    'name' => $this->user?->name,
                    'email' => $this->user?->email,
                    'role' => $this->user?->roles->first()?->name,
                ];
            }),
            'ratings' => [
                'average_rating' => $this->getAverageRating(),
                'total_reviews' => $this->reviews()->count(),
                'rating_breakdown' => $this->getRatingBreakdown(),
            ],
            'timestamps' => [
                'created_at' => $this->created_at?->toISOString(),
                'updated_at' => $this->updated_at?->toISOString(),
                'verified_at' => $this->verified_at?->toISOString(),
            ],
        ];
    }

    /**
     * Get additional data that should be returned with the resource array.
     */
    public function with(Request $request): array
    {
        return [
            'meta' => [
                'version' => '1.0',
                'generated_at' => now()->toISOString(),
                'resource_type' => 'company_detail',
                'includes' => $this->getIncludedRelations(),
            ],
        ];
    }

    /**
     * Customize the outgoing response for the resource.
     *
     * @param  mixed  $response
     */
    public function withResponse(Request $request, $response): void
    {
        $response->header('X-Resource-Type', 'CompanyShowResource');
        $response->header('Cache-Control', 'public, max-age=600'); // 10 minutes cache
    }

    /**
     * Get logo URL.
     */
    private function getLogoUrl(): ?string
    {
        if ($this->logo) {
            return asset('storage/'.$this->logo);
        }

        // Default logo with company initial
        $initial = substr($this->name, 0, 1);

        return "https://ui-avatars.com/api/?name={$initial}&size=200&background=random&format=svg";
    }

    /**
     * Get cover image URL.
     */
    private function getCoverImageUrl(): ?string
    {
        if ($this->cover_image) {
            return asset('storage/'.$this->cover_image);
        }

        return null;
    }

    /**
     * Get formatted revenue.
     */
    private function getFormattedRevenue(): ?string
    {
        if (! $this->revenue || ! $this->revenueCurrency) {
            return null;
        }

        return $this->revenueCurrency->symbol.number_format($this->revenue, 0);
    }

    /**
     * Get full address string.
     */
    private function getFullAddress(): ?string
    {
        $parts = array_filter([
            $this->address,
            $this->city?->name,
            $this->state?->name,
            $this->country?->name,
            $this->postal_code,
        ]);

        return ! empty($parts) ? implode(', ', $parts) : null;
    }

    /**
     * Get profile completion percentage.
     */
    private function getProfileCompletionPercentage(): int
    {
        $fields = [
            'name', 'email', 'phone', 'website', 'description', 'logo',
            'industry_id', 'company_size_id', 'employee_count', 'founded_year',
            'address', 'country_id', 'state_id', 'city_id',
        ];

        $completedFields = 0;
        foreach ($fields as $field) {
            if (! empty($this->{$field})) {
                $completedFields++;
            }
        }

        // Bonus points for social links and benefits
        if (! empty($this->social_linkedin)) {
            $completedFields += 0.5;
        }
        if (! empty($this->benefits)) {
            $completedFields += 0.5;
        }

        return round(($completedFields / count($fields)) * 100);
    }

    /**
     * Get total applications across all jobs.
     */
    private function getTotalApplications(): int
    {
        return $this->jobs()->withCount('applications')->get()->sum('applications_count');
    }

    /**
     * Get average rating.
     */
    private function getAverageRating(): float
    {
        return $this->reviews()->avg('rating') ?? 0.0;
    }

    /**
     * Get rating breakdown.
     */
    private function getRatingBreakdown(): array
    {
        $breakdown = [];
        for ($i = 1; $i <= 5; $i++) {
            $breakdown[$i] = $this->reviews()->where('rating', $i)->count();
        }

        return $breakdown;
    }

    /**
     * Get included relations.
     */
    private function getIncludedRelations(): array
    {
        $included = [];

        if ($this->relationLoaded('jobs')) {
            $included[] = 'jobs';
        }
        if ($this->relationLoaded('user')) {
            $included[] = 'contact_person';
        }
        if ($this->relationLoaded('reviews')) {
            $included[] = 'reviews';
        }
        if ($this->relationLoaded('followers')) {
            $included[] = 'followers';
        }

        return $included;
    }
}
