<?php

namespace App\Http\Resources\Universal;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CandidateShowResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'full_name' => $this->first_name . ' ' . $this->last_name,
            'phone' => $this->phone,
            'email' => $this->user?->email,
            'date_of_birth' => $this->date_of_birth?->format('Y-m-d'),
            'age' => $this->date_of_birth ? now()->diffInYears($this->date_of_birth) : null,
            'gender' => $this->gender,
            'address' => $this->address,
            'location' => [
                'country' => $this->country?->name,
                'country_id' => $this->country_id,
                'state' => $this->state?->name,
                'state_id' => $this->state_id,
                'city' => $this->city?->name,
                'city_id' => $this->city_id,
                'postal_code' => $this->postal_code,
                'full_address' => $this->getFullAddress(),
            ],
            'career' => [
                'level' => $this->careerLevel?->name,
                'level_id' => $this->career_level_id,
                'industry' => $this->industry?->name,
                'industry_id' => $this->industry_id,
                'experience_years' => $this->experience_years,
                'job_experience' => $this->jobExperience?->name,
                'job_experience_id' => $this->job_experience_id,
            ],
            'salary' => [
                'current' => $this->current_salary,
                'expected' => $this->expected_salary,
                'currency' => $this->salaryCurrency?->name,
                'currency_id' => $this->salary_currency_id,
                'currency_symbol' => $this->salaryCurrency?->symbol,
                'formatted_current' => $this->getFormattedSalary('current'),
                'formatted_expected' => $this->getFormattedSalary('expected'),
            ],
            'availability' => [
                'is_immediate_available' => $this->is_immediate_available,
                'notice_period' => $this->notice_period,
                'preferred_start_date' => $this->preferred_start_date?->format('Y-m-d'),
            ],
            'profile' => [
                'bio' => $this->bio,
                'avatar' => $this->getAvatarUrl(),
                'visibility' => $this->visibility,
                'is_active' => $this->is_active,
                'is_featured' => $this->is_featured,
                'profile_completion' => $this->getProfileCompletionPercentage(),
            ],
            'social_links' => [
                'linkedin_url' => $this->linkedin_url,
                'github_url' => $this->github_url,
                'website_url' => $this->website_url,
                'portfolio_url' => $this->portfolio_url,
            ],
            'skills' => $this->when($this->relationLoaded('skills'), function() {
                return $this->skills->map(function($skill) {
                    return [
                        'id' => $skill->id,
                        'name' => $skill->name,
                        'level' => $skill->pivot->level ?? null,
                        'years_experience' => $skill->pivot->years_experience ?? null,
                    ];
                });
            }),
            'languages' => $this->when($this->relationLoaded('languages'), function() {
                return $this->languages->map(function($language) {
                    return [
                        'id' => $language->id,
                        'name' => $language->name,
                        'proficiency' => $language->pivot->proficiency ?? null,
                    ];
                });
            }),
            'education' => $this->when($this->relationLoaded('educations'), function() {
                return $this->educations->map(function($education) {
                    return [
                        'id' => $education->id,
                        'degree_level' => $education->degreeLevel?->name,
                        'degree_title' => $education->degree_title,
                        'institution' => $education->institution,
                        'start_date' => $education->start_date?->format('Y-m-d'),
                        'end_date' => $education->end_date?->format('Y-m-d'),
                        'is_current' => $education->is_current,
                        'gpa' => $education->gpa,
                    ];
                });
            }),
            'experience' => $this->when($this->relationLoaded('experiences'), function() {
                return $this->experiences->map(function($experience) {
                    return [
                        'id' => $experience->id,
                        'company' => $experience->company,
                        'position' => $experience->position,
                        'start_date' => $experience->start_date?->format('Y-m-d'),
                        'end_date' => $experience->end_date?->format('Y-m-d'),
                        'is_current' => $experience->is_current,
                        'description' => $experience->description,
                        'location' => $experience->location,
                    ];
                });
            }),
            'applications_count' => $this->when($this->relationLoaded('applications'), function() {
                return $this->applications->count();
            }),
            'statistics' => [
                'profile_views' => $this->profile_views ?? 0,
                'applications_sent' => $this->applications()->count(),
                'interviews_scheduled' => $this->applications()->where('status', 'interview')->count(),
                'offers_received' => $this->applications()->where('status', 'offered')->count(),
            ],
            'timestamps' => [
                'created_at' => $this->created_at?->toISOString(),
                'updated_at' => $this->updated_at?->toISOString(),
                'last_active_at' => $this->last_active_at?->toISOString(),
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
                'resource_type' => 'candidate_detail',
                'includes' => $this->getIncludedRelations(),
            ],
        ];
    }

    /**
     * Customize the outgoing response for the resource.
     */
    public function withResponse(Request $request, $response): void
    {
        $response->header('X-Resource-Type', 'CandidateShowResource');
        $response->header('Cache-Control', 'public, max-age=300'); // 5 minutes cache
    }

    /**
     * Get the full address string
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

        return !empty($parts) ? implode(', ', $parts) : null;
    }

    /**
     * Get formatted salary
     */
    private function getFormattedSalary(string $type): ?string
    {
        $amount = $type === 'current' ? $this->current_salary : $this->expected_salary;
        
        if (!$amount || !$this->salaryCurrency) {
            return null;
        }

        return $this->salaryCurrency->symbol . number_format($amount, 0);
    }

    /**
     * Get avatar URL
     */
    private function getAvatarUrl(): ?string
    {
        if ($this->avatar) {
            return asset('storage/' . $this->avatar);
        }
        
        // Default avatar with initials
        $initials = substr($this->first_name, 0, 1) . substr($this->last_name, 0, 1);
        return "https://ui-avatars.com/api/?name={$initials}&size=200&background=random";
    }

    /**
     * Get profile completion percentage
     */
    private function getProfileCompletionPercentage(): int
    {
        $fields = [
            'first_name', 'last_name', 'phone', 'date_of_birth', 'gender',
            'address', 'country_id', 'state_id', 'city_id', 'career_level_id',
            'industry_id', 'current_salary', 'expected_salary', 'bio',
            'linkedin_url', 'experience_years'
        ];

        $completedFields = 0;
        foreach ($fields as $field) {
            if (!empty($this->$field)) {
                $completedFields++;
            }
        }

        return round(($completedFields / count($fields)) * 100);
    }

    /**
     * Get included relations
     */
    private function getIncludedRelations(): array
    {
        $included = [];
        
        if ($this->relationLoaded('skills')) $included[] = 'skills';
        if ($this->relationLoaded('languages')) $included[] = 'languages';
        if ($this->relationLoaded('educations')) $included[] = 'education';
        if ($this->relationLoaded('experiences')) $included[] = 'experience';
        if ($this->relationLoaded('applications')) $included[] = 'applications';

        return $included;
    }
} 