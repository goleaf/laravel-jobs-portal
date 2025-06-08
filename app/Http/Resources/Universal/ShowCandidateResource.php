<?php

namespace App\Http\Resources\Universal;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ShowCandidateResource extends JsonResource
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
            'user_id' => $this->user_id,
            
            // Personal Information
            'personal' => [
                'first_name' => $this->first_name,
                'last_name' => $this->last_name,
                'full_name' => $this->full_name,
                'phone' => $this->phone,
                'date_of_birth' => $this->when($this->date_of_birth, function () {
                    return $this->date_of_birth?->format('Y-m-d');
                }),
                'gender' => $this->gender,
                'marital_status' => $this->when($this->maritalStatus, function () {
                    return [
                        'id' => $this->maritalStatus->id,
                        'name' => $this->maritalStatus->marital_status,
                    ];
                }),
                'nationality' => $this->nationality,
                'national_id' => $this->when(auth()->check() && auth()->id() === $this->user_id, $this->national_id),
            ],

            // Professional Information
            'professional' => [
                'current_salary' => $this->current_salary,
                'expected_salary' => $this->expected_salary,
                'salary_currency' => $this->when($this->salaryCurrency, function () {
                    return [
                        'id' => $this->salaryCurrency->id,
                        'name' => $this->salaryCurrency->currency_name,
                        'code' => $this->salaryCurrency->currency_code,
                        'symbol' => $this->salaryCurrency->currency_symbol,
                    ];
                }),
                'career_level' => $this->when($this->careerLevel, function () {
                    return [
                        'id' => $this->careerLevel->id,
                        'name' => $this->careerLevel->level_name,
                    ];
                }),
                'industry' => $this->when($this->industry, function () {
                    return [
                        'id' => $this->industry->id,
                        'name' => $this->industry->name,
                    ];
                }),
                'functional_area' => $this->when($this->functionalArea, function () {
                    return [
                        'id' => $this->functionalArea->id,
                        'name' => $this->functionalArea->name,
                    ];
                }),
                'experience_years' => $this->experience,
                'job_preference' => $this->job_preference,
                'willing_to_relocate' => (bool) $this->is_relocate,
                'available_for_remote' => (bool) $this->is_remote_job,
            ],

            // Location Information
            'location' => [
                'address' => $this->address,
                'country' => $this->when($this->country, function () {
                    return [
                        'id' => $this->country->id,
                        'name' => $this->country->name,
                        'code' => $this->country->iso2,
                    ];
                }),
                'state' => $this->when($this->state, function () {
                    return [
                        'id' => $this->state->id,
                        'name' => $this->state->name,
                    ];
                }),
                'city' => $this->when($this->city, function () {
                    return [
                        'id' => $this->city->id,
                        'name' => $this->city->name,
                    ];
                }),
                'postal_code' => $this->postal_code,
            ],

            // Profile Information
            'profile' => [
                'avatar' => $this->user && $this->user->avatar ? [
                    'url' => $this->user->avatar_url,
                    'thumbnail' => $this->user->avatar_thumbnail,
                ] : null,
                'bio' => $this->summary,
                'website' => $this->website,
                'linkedin' => $this->linkedin_url,
                'github' => $this->github_url,
                'portfolio' => $this->portfolio_url,
                'visibility' => $this->visibility ?? 'public',
                'availability_status' => $this->availability_status ?? 'available',
                'immediate_available' => (bool) $this->immediate_available,
            ],

            // Skills and Languages (when included)
            'skills' => $this->when($request->include && str_contains($request->include, 'skills'), function () {
                return $this->candidateSkills->map(function ($skill) {
                    return [
                        'id' => $skill->skill->id,
                        'name' => $skill->skill->name,
                        'proficiency' => $skill->proficiency_level,
                        'experience_years' => $skill->experience_years,
                    ];
                });
            }),

            'languages' => $this->when($request->include && str_contains($request->include, 'languages'), function () {
                return $this->candidateLanguages->map(function ($language) {
                    return [
                        'id' => $language->language->id,
                        'name' => $language->language->name,
                        'proficiency' => $language->proficiency,
                        'is_native' => (bool) $language->is_native,
                    ];
                });
            }),

            // Education (when included)
            'educations' => $this->when($request->include && str_contains($request->include, 'educations'), function () {
                return $this->candidateEducations->map(function ($education) {
                    return [
                        'id' => $education->id,
                        'degree_level' => $education->degreeLevel->level_name ?? null,
                        'degree_title' => $education->degree_title,
                        'institution' => $education->institute,
                        'field_of_study' => $education->field_of_study,
                        'year' => $education->year,
                        'grade' => $education->grade,
                        'currently_studying' => (bool) $education->is_studying,
                    ];
                });
            }),

            // Work Experience (when included)
            'experiences' => $this->when($request->include && str_contains($request->include, 'experiences'), function () {
                return $this->candidateExperiences->map(function ($experience) {
                    return [
                        'id' => $experience->id,
                        'title' => $experience->experience_title,
                        'company' => $experience->company,
                        'location' => $experience->location,
                        'description' => $experience->description,
                        'start_date' => $experience->start_date?->format('Y-m-d'),
                        'end_date' => $experience->end_date?->format('Y-m-d'),
                        'currently_working' => (bool) $experience->currently_working,
                        'duration_months' => $experience->duration_months,
                    ];
                });
            }),

            // Resumes (when included)
            'resumes' => $this->when($request->include && str_contains($request->include, 'resumes'), function () {
                return $this->resumes->map(function ($resume) {
                    return [
                        'id' => $resume->id,
                        'name' => $resume->name,
                        'file_path' => $resume->file_path,
                        'file_url' => $resume->file_url,
                        'is_default' => (bool) $resume->is_default,
                        'uploaded_at' => $resume->created_at?->format('Y-m-d H:i:s'),
                    ];
                });
            }),

            // Applications (when included and authorized)
            'applications' => $this->when(
                $request->include && str_contains($request->include, 'applications') && 
                (auth()->check() && (auth()->id() === $this->user_id || auth()->user()->hasRole(['admin', 'employer']))),
                function () {
                    return $this->jobApplications->map(function ($application) {
                        return [
                            'id' => $application->id,
                            'job_title' => $application->job->title ?? null,
                            'company_name' => $application->job->company->name ?? null,
                            'status' => $application->status,
                            'applied_at' => $application->created_at?->format('Y-m-d H:i:s'),
                        ];
                    });
                }
            ),

            // Statistics
            'statistics' => $this->when($request->with_stats, function () {
                return [
                    'profile_completion' => $this->getProfileCompletionPercentage(),
                    'total_applications' => $this->jobApplications->count(),
                    'active_applications' => $this->jobApplications->whereIn('status', ['pending', 'shortlisted', 'interviewing'])->count(),
                    'profile_views' => $this->profile_views ?? 0,
                    'response_rate' => $this->calculateResponseRate(),
                    'last_activity' => $this->updated_at?->format('Y-m-d H:i:s'),
                ];
            }),

            // User Information (when included)
            'user' => $this->when($request->include && str_contains($request->include, 'user'), function () {
                return [
                    'id' => $this->user->id,
                    'email' => $this->user->email,
                    'username' => $this->user->username,
                    'email_verified_at' => $this->user->email_verified_at?->format('Y-m-d H:i:s'),
                    'is_active' => (bool) $this->user->is_active,
                    'last_login' => $this->user->last_login_at?->format('Y-m-d H:i:s'),
                ];
            }),

            // Timestamps
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
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
            'message' => 'Candidate details retrieved successfully',
            'meta' => [
                'profile_type' => 'candidate',
                'visibility' => $this->visibility ?? 'public',
                'data_version' => '1.0',
                'cache_ttl' => 3600, // 1 hour
            ],
        ];
    }

    /**
     * Customize the outgoing response for the resource.
     */
    public function withResponse(Request $request, $response): void
    {
        $response->header('X-Resource-Type', 'ShowCandidateResource');
        $response->header('Cache-Control', 'public, max-age=1800'); // 30 minutes
    }

    /**
     * Calculate profile completion percentage
     */
    private function getProfileCompletionPercentage(): int
    {
        $totalFields = 20;
        $completedFields = 0;

        // Basic information
        if ($this->first_name) $completedFields++;
        if ($this->last_name) $completedFields++;
        if ($this->phone) $completedFields++;
        if ($this->summary) $completedFields++;
        if ($this->address) $completedFields++;

        // Professional information
        if ($this->expected_salary) $completedFields++;
        if ($this->career_level_id) $completedFields++;
        if ($this->industry_id) $completedFields++;
        if ($this->functional_area_id) $completedFields++;
        if ($this->experience) $completedFields++;

        // Location
        if ($this->country_id) $completedFields++;
        if ($this->state_id) $completedFields++;
        if ($this->city_id) $completedFields++;

        // Profile enhancements
        if ($this->user && $this->user->avatar) $completedFields++;
        if ($this->website) $completedFields++;
        if ($this->linkedin_url) $completedFields++;

        // Related data
        if ($this->candidateSkills->count() > 0) $completedFields++;
        if ($this->candidateEducations->count() > 0) $completedFields++;
        if ($this->candidateExperiences->count() > 0) $completedFields++;
        if ($this->resumes->count() > 0) $completedFields++;

        return round(($completedFields / $totalFields) * 100);
    }

    /**
     * Calculate response rate for applications
     */
    private function calculateResponseRate(): float
    {
        $totalApplications = $this->jobApplications->count();
        if ($totalApplications === 0) {
            return 0.0;
        }

        $responsedApplications = $this->jobApplications->whereNotIn('status', ['pending'])->count();
        return round(($responsedApplications / $totalApplications) * 100, 2);
    }
} 