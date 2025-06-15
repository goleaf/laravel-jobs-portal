<?php

namespace App\Http\Resources\Universal;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ShowCompanyResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'company' => [
                'id' => $this->id,
                'name' => $this->name,
                'slug' => $this->slug ?? null,
                'description' => $this->description ?? null,
                'website' => $this->website ?? null,
                'email' => $this->email ?? null,
                'phone' => $this->phone ?? null,
                'logo' => [
                    'original' => $this->logo ?? null,
                    'thumbnail' => $this->logo_thumbnail ?? null,
                    'medium' => $this->logo_medium ?? null,
                ],
                'location' => [
                    'address' => $this->address ?? null,
                    'city' => $this->city ?? null,
                    'state' => $this->state ?? null,
                    'country' => $this->country ?? null,
                    'postal_code' => $this->postal_code ?? null,
                    'latitude' => $this->latitude ?? null,
                    'longitude' => $this->longitude ?? null,
                ],
                'company_info' => [
                    'industry' => $this->industry ?? null,
                    'size' => $this->company_size ?? null,
                    'founded_year' => $this->founded_year ?? null,
                    'type' => $this->company_type ?? null,
                    'ownership' => $this->ownership_type ?? null,
                ],
                'social_media' => [
                    'linkedin' => $this->linkedin_url ?? null,
                    'twitter' => $this->twitter_url ?? null,
                    'facebook' => $this->facebook_url ?? null,
                    'instagram' => $this->instagram_url ?? null,
                    'youtube' => $this->youtube_url ?? null,
                ],
                'statistics' => [
                    'total_jobs' => $this->jobs_count ?? 0,
                    'active_jobs' => $this->active_jobs_count ?? 0,
                    'total_employees' => $this->employees_count ?? 0,
                    'total_applications' => $this->applications_count ?? 0,
                    'profile_views' => $this->profile_views ?? 0,
                ],
                'verification' => [
                    'is_verified' => $this->is_verified ?? false,
                    'verified_at' => $this->verified_at?->toISOString(),
                    'verification_badge' => $this->verification_badge ?? null,
                ],
                'status' => [
                    'is_active' => $this->is_active ?? true,
                    'is_featured' => $this->is_featured ?? false,
                    'is_hiring' => $this->is_hiring ?? false,
                    'profile_completed' => $this->isProfileComplete(),
                    'completion_percentage' => $this->getProfileCompletionPercentage(),
                ],
                'timestamps' => [
                    'created_at' => $this->created_at?->toISOString(),
                    'updated_at' => $this->updated_at?->toISOString(),
                    'last_active' => $this->last_active_at?->toISOString(),
                ],
            ],

            // Include relationships if requested
            'user' => $this->whenLoaded('user', function () {
                return [
                    'id' => $this->user->id,
                    'name' => $this->user->name,
                    'email' => $this->user->email,
                    'role' => $this->user->getRoleNames()->first(),
                    'email_verified_at' => $this->user->email_verified_at?->toISOString(),
                ];
            }),

            'jobs' => $this->whenLoaded('jobs', function () {
                return $this->jobs->map(function ($job) {
                    return [
                        'id' => $job->id,
                        'title' => $job->title,
                        'type' => $job->job_type,
                        'status' => $job->status,
                        'applications_count' => $job->applications_count ?? 0,
                        'posted_at' => $job->created_at?->toISOString(),
                        'expires_at' => $job->expiry_date?->toISOString(),
                    ];
                });
            }),

            'employees' => $this->whenLoaded('employees', function () {
                return $this->employees->map(function ($employee) {
                    return [
                        'id' => $employee->id,
                        'name' => $employee->name,
                        'email' => $employee->email,
                        'position' => $employee->position ?? null,
                        'department' => $employee->department ?? null,
                        'joined_at' => $employee->created_at?->toISOString(),
                    ];
                });
            }),

            'industry' => $this->whenLoaded('industry', function () {
                return [
                    'id' => $this->industry->id,
                    'name' => $this->industry->name,
                    'category' => $this->industry->category ?? null,
                ];
            }),

            'size_info' => $this->whenLoaded('size', function () {
                return [
                    'id' => $this->size->id,
                    'name' => $this->size->name,
                    'range' => $this->size->range ?? null,
                ];
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
            'message' => __('messages.company_retrieved'),
            'meta' => [
                'timestamp' => now()->toISOString(),
                'version' => '1.0',
                'endpoint' => 'companies/show',
                'includes' => $request->input('include', []),
                'cache_ttl' => 300, // 5 minutes
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
        $response->header('Cache-Control', 'public, max-age=300'); // 5 minutes cache
    }

    /**
     * Check if company profile is complete.
     */
    private function isProfileComplete(): bool
    {
        $requiredFields = ['name', 'description', 'website', 'industry', 'company_size', 'address'];

        foreach ($requiredFields as $field) {
            if (empty($this->{$field})) {
                return false;
            }
        }

        return true;
    }

    /**
     * Get profile completion percentage.
     */
    private function getProfileCompletionPercentage(): int
    {
        $fields = [
            'name', 'description', 'website', 'logo', 'industry',
            'company_size', 'founded_year', 'address', 'phone', 'email',
            'linkedin_url', 'twitter_url',
        ];

        $completedFields = 0;

        foreach ($fields as $field) {
            if (!empty($this->{$field})) {
                ++$completedFields;
            }
        }

        return (int) round(($completedFields / count($fields)) * 100);
    }
}
