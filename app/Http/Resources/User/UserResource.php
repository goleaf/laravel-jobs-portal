<?php

namespace App\Http\Resources\User;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'full_name' => $this->getFullName(),
            'email' => $this->email,
            'phone' => $this->phone,
            'avatar_url' => $this->getAvatarUrl(),
            
            // Personal Information
            'personal_info' => [
                'date_of_birth' => $this->dob?->toDateString(),
                'age' => $this->dob?->age,
                'gender' => $this->gender,
                'gender_label' => $this->getGenderLabel(),
                'marital_status' => $this->when($this->marital_status_id, [
                    'id' => $this->marital_status_id,
                    'name' => $this->maritalStatus?->marital_status,
                ]),
                'nationality' => $this->when($this->nationality_id, [
                    'id' => $this->nationality_id,
                    'name' => $this->nationality?->name,
                    'code' => $this->nationality?->short_code,
                ]),
            ],
            
            // Location Information
            'location' => [
                'country' => $this->when($this->country_id, [
                    'id' => $this->country_id,
                    'name' => $this->country?->name,
                    'code' => $this->country?->short_code,
                ]),
                'state' => $this->when($this->state_id, [
                    'id' => $this->state_id,
                    'name' => $this->state?->name,
                ]),
                'city' => $this->when($this->city_id, [
                    'id' => $this->city_id,
                    'name' => $this->city?->name,
                ]),
                'formatted' => $this->getFormattedLocation(),
            ],
            
            // Account Information
            'account' => [
                'language' => $this->language ?? app()->getLocale(),
                'language_label' => $this->getLanguageLabel(),
                'timezone' => $this->timezone ?? config('app.timezone'),
                'is_active' => $this->is_active ?? true,
                'is_verified' => $this->is_verified ?? false,
                'email_verified_at' => $this->email_verified_at?->toISOString(),
                'status_label' => $this->getStatusLabel(),
            ],
            
            // Role and Permissions
            'role_info' => [
                'primary_role' => $this->getPrimaryRole(),
                'roles' => $this->whenLoaded('roles', function () {
                    return $this->roles->pluck('name');
                }),
                'permissions' => $this->whenLoaded('permissions', function () {
                    return $this->permissions->pluck('name');
                }),
                'is_admin' => $this->hasRole('admin'),
                'is_employer' => $this->hasRole('employer'),
                'is_candidate' => $this->hasRole('candidate'),
            ],
            
            // Profile Completion
            'profile' => [
                'completion_percentage' => $this->getProfileCompletionPercentage(),
                'missing_fields' => $this->getMissingProfileFields(),
                'is_complete' => $this->isProfileComplete(),
                'last_profile_update' => $this->updated_at?->toISOString(),
            ],
            
            // Statistics (conditional based on role)
            'statistics' => $this->when($this->hasRole('candidate'), [
                'applications_count' => $this->whenCounted('jobApplications'),
                'saved_jobs_count' => $this->whenCounted('savedJobs'),
                'profile_views' => $this->profile_views ?? 0,
            ]) ?: $this->when($this->hasRole('employer'), [
                'companies_count' => $this->whenCounted('companies'),
                'jobs_posted' => $this->whenCounted('postedJobs'),
                'active_jobs' => $this->whenCounted('activeJobs'),
            ]),
            
            // Dates
            'dates' => [
                'created_at' => $this->created_at?->toISOString(),
                'updated_at' => $this->updated_at?->toISOString(),
                'last_login_at' => $this->last_login_at?->toISOString(),
                'formatted_created_at' => $this->created_at?->format(__('formats.date_time')),
                'formatted_last_login' => $this->last_login_at?->format(__('formats.date_time')),
                'member_since' => $this->created_at?->diffForHumans(),
                'last_active' => $this->last_login_at?->diffForHumans(),
            ],
            
            // Relationships (conditionally loaded)
            'candidate' => $this->whenLoaded('candidate'),
            'companies' => $this->whenLoaded('companies'),
            'job_applications' => $this->whenLoaded('jobApplications'),
            
            // Permissions for current user
            'permissions' => [
                'can_view_profile' => $request->user()?->can('view', $this->resource) ?? false,
                'can_update' => $request->user()?->can('update', $this->resource) ?? false,
                'can_delete' => $request->user()?->can('delete', $this->resource) ?? false,
                'can_impersonate' => $request->user()?->can('impersonate', $this->resource) ?? false,
            ],
            
            // Links
            'links' => [
                'self' => route('api.users.show', $this->id),
                'profile' => $this->getProfileUrl(),
                'avatar' => $this->getAvatarUrl(),
                'edit' => route('api.users.edit', $this->id),
            ],
        ];
    }

    /**
     * Get user's full name.
     */
    private function getFullName(): string
    {
        return trim($this->first_name . ' ' . $this->last_name);
    }

    /**
     * Get user's avatar URL.
     */
    private function getAvatarUrl(): string
    {
        if ($this->avatar) {
            return asset('storage/' . $this->avatar);
        }

        // Generate Gravatar URL
        $hash = md5(strtolower(trim($this->email)));
        return "https://www.gravatar.com/avatar/{$hash}?d=mp&s=200";
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

        return implode(', ', $parts) ?: __('users.location.not_specified');
    }

    /**
     * Get gender label.
     */
    private function getGenderLabel(): ?string
    {
        if (!$this->gender) {
            return null;
        }

        return __('users.gender.' . $this->gender);
    }

    /**
     * Get language label.
     */
    private function getLanguageLabel(): string
    {
        $language = $this->language ?? app()->getLocale();
        return __('languages.' . $language);
    }

    /**
     * Get status label.
     */
    private function getStatusLabel(): string
    {
        if (!($this->is_active ?? true)) {
            return __('users.status.inactive');
        }

        if (!($this->is_verified ?? false)) {
            return __('users.status.unverified');
        }

        return __('users.status.active');
    }

    /**
     * Get primary role.
     */
    private function getPrimaryRole(): ?string
    {
        if ($this->hasRole('admin')) {
            return 'admin';
        }

        if ($this->hasRole('employer')) {
            return 'employer';
        }

        if ($this->hasRole('candidate')) {
            return 'candidate';
        }

        return null;
    }

    /**
     * Get profile completion percentage.
     */
    private function getProfileCompletionPercentage(): int
    {
        $fields = [
            'first_name', 'last_name', 'email', 'phone', 'dob', 'gender',
            'country_id', 'state_id', 'city_id', 'avatar'
        ];

        $completed = 0;
        foreach ($fields as $field) {
            if (!empty($this->$field)) {
                $completed++;
            }
        }

        return round(($completed / count($fields)) * 100);
    }

    /**
     * Get missing profile fields.
     */
    private function getMissingProfileFields(): array
    {
        $fields = [
            'phone' => __('attributes.user.phone'),
            'dob' => __('attributes.user.date_of_birth'),
            'gender' => __('attributes.user.gender'),
            'country_id' => __('attributes.user.country'),
            'avatar' => __('attributes.user.avatar'),
        ];

        $missing = [];
        foreach ($fields as $field => $label) {
            if (empty($this->$field)) {
                $missing[] = $label;
            }
        }

        return $missing;
    }

    /**
     * Check if profile is complete.
     */
    private function isProfileComplete(): bool
    {
        return $this->getProfileCompletionPercentage() >= 80;
    }

    /**
     * Get profile URL.
     */
    private function getProfileUrl(): string
    {
        $role = $this->getPrimaryRole();
        
        switch ($role) {
            case 'candidate':
                return route('candidates.show', $this->id);
            case 'employer':
                return route('employers.show', $this->id);
            default:
                return route('users.show', $this->id);
        }
    }
} 