<?php

namespace App\Http\Resources\Universal;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class JobShowResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'description' => $this->description,
            'responsibilities' => $this->responsibilities,
            'requirements' => $this->requirements,
            'benefits' => $this->benefits,
            'company' => [
                'id' => $this->company_id,
                'name' => $this->company?->name,
                'slug' => $this->company?->slug,
                'logo' => $this->company?->getLogoUrl(),
                'location' => $this->company?->city?->name . ', ' . $this->company?->country?->name,
                'industry' => $this->company?->industry?->name,
                'size' => $this->company?->companySize?->name,
                'is_verified' => $this->company?->is_verified,
            ],
            'job_details' => [
                'type' => $this->jobType?->name,
                'type_id' => $this->job_type_id,
                'category' => $this->jobCategory?->name,
                'category_id' => $this->job_category_id,
                'shift' => $this->jobShift?->name,
                'shift_id' => $this->job_shift_id,
                'career_level' => $this->careerLevel?->name,
                'career_level_id' => $this->career_level_id,
                'functional_area' => $this->functionalArea?->name,
                'functional_area_id' => $this->functional_area_id,
                'experience' => $this->jobExperience?->name,
                'experience_id' => $this->job_experience_id,
            ],
            'salary' => [
                'from' => $this->salary_from,
                'to' => $this->salary_to,
                'currency' => $this->salaryCurrency?->name,
                'currency_id' => $this->salary_currency_id,
                'currency_symbol' => $this->salaryCurrency?->symbol,
                'period' => $this->salaryPeriod?->name,
                'period_id' => $this->salary_period_id,
                'hide_salary' => $this->hide_salary,
                'formatted_range' => $this->getFormattedSalaryRange(),
            ],
            'location' => [
                'country' => $this->country?->name,
                'country_id' => $this->country_id,
                'state' => $this->state?->name,
                'state_id' => $this->state_id,
                'city' => $this->city?->name,
                'city_id' => $this->city_id,
                'is_remote' => $this->is_remote,
                'full_location' => $this->getFullLocation(),
            ],
            'requirements_detail' => [
                'degree_level' => $this->requiredDegreeLevel?->name,
                'degree_level_id' => $this->required_degree_level_id,
                'experience_years_min' => $this->experience_years_min,
                'experience_years_max' => $this->experience_years_max,
                'experience_range' => $this->getExperienceRange(),
            ],
            'skills' => $this->when($this->relationLoaded('skills'), function() {
                return $this->skills->map(function($skill) {
                    return [
                        'id' => $skill->id,
                        'name' => $skill->name,
                        'is_required' => $skill->pivot->is_required ?? false,
                    ];
                });
            }),
            'tags' => $this->tags,
            'application_info' => [
                'deadline' => $this->deadline?->format('Y-m-d'),
                'days_remaining' => $this->deadline ? now()->diffInDays($this->deadline, false) : null,
                'is_expired' => $this->deadline ? $this->deadline->isPast() : false,
                'total_applications' => $this->applications()->count(),
                'can_apply' => $this->canUserApply(auth()->user()),
            ],
            'status_info' => [
                'status' => $this->status,
                'is_featured' => $this->is_featured,
                'is_freelance' => $this->is_freelance,
                'is_active' => $this->status === 'published',
                'views_count' => $this->views_count ?? 0,
            ],
            'similar_jobs' => $this->when($this->relationLoaded('similarJobs'), function() {
                return $this->similarJobs->take(5)->map(function($job) {
                    return [
                        'id' => $job->id,
                        'title' => $job->title,
                        'company' => $job->company?->name,
                        'location' => $job->city?->name,
                        'salary_range' => $job->getFormattedSalaryRange(),
                    ];
                });
            }),
            'timestamps' => [
                'created_at' => $this->created_at?->toISOString(),
                'updated_at' => $this->updated_at?->toISOString(),
                'published_at' => $this->published_at?->toISOString(),
                'expires_at' => $this->deadline?->toISOString(),
            ],
        ];
    }

    public function with(Request $request): array
    {
        return [
            'meta' => [
                'version' => '1.0',
                'generated_at' => now()->toISOString(),
                'resource_type' => 'job_detail',
                'includes' => $this->getIncludedRelations(),
            ],
        ];
    }

    public function withResponse(Request $request, $response): void
    {
        $response->header('X-Resource-Type', 'JobShowResource');
        $response->header('Cache-Control', 'public, max-age=300');
    }

    private function getFormattedSalaryRange(): ?string
    {
        if ($this->hide_salary || (!$this->salary_from && !$this->salary_to)) {
            return null;
        }

        $symbol = $this->salaryCurrency?->symbol ?? '$';
        
        if ($this->salary_from && $this->salary_to) {
            return $symbol . number_format($this->salary_from) . ' - ' . $symbol . number_format($this->salary_to);
        }
        
        if ($this->salary_from) {
            return 'From ' . $symbol . number_format($this->salary_from);
        }
        
        if ($this->salary_to) {
            return 'Up to ' . $symbol . number_format($this->salary_to);
        }

        return null;
    }

    private function getFullLocation(): string
    {
        if ($this->is_remote) {
            return 'Remote';
        }

        $parts = array_filter([
            $this->city?->name,
            $this->state?->name,
            $this->country?->name,
        ]);

        return implode(', ', $parts);
    }

    private function getExperienceRange(): ?string
    {
        if (!$this->experience_years_min && !$this->experience_years_max) {
            return null;
        }

        if ($this->experience_years_min && $this->experience_years_max) {
            return $this->experience_years_min . '-' . $this->experience_years_max . ' years';
        }

        if ($this->experience_years_min) {
            return $this->experience_years_min . '+ years';
        }

        return 'Up to ' . $this->experience_years_max . ' years';
    }

    private function canUserApply($user): bool
    {
        if (!$user) return false;
        if ($this->status !== 'published') return false;
        if ($this->deadline && $this->deadline->isPast()) return false;
        
        return !$this->applications()->where('candidate_id', $user->candidate?->id)->exists();
    }

    private function getIncludedRelations(): array
    {
        $included = [];
        
        if ($this->relationLoaded('skills')) $included[] = 'skills';
        if ($this->relationLoaded('similarJobs')) $included[] = 'similar_jobs';
        if ($this->relationLoaded('applications')) $included[] = 'applications';

        return $included;
    }
} 