<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class JobResource extends JsonResource
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
            'job_id' => $this->job_id,
            'title' => $this->job_title,
            'description' => $this->description,
            'status' => [
                'value' => $this->status,
                'label' => $this->status_text,
                'badge_class' => $this->status_badge_class,
            ],
            'salary' => [
                'from' => $this->salary_from,
                'to' => $this->salary_to,
                'formatted' => $this->formatted_salary,
                'currency' => new SalaryCurrencyResource($this->whenLoaded('currency')),
                'period' => new SalaryPeriodResource($this->whenLoaded('salaryPeriod')),
                'is_hidden' => $this->hide_salary,
            ],
            'location' => [
                'full_location' => $this->full_location,
                'country' => new CountryResource($this->whenLoaded('country')),
                'state' => new StateResource($this->whenLoaded('state')),
                'city' => new CityResource($this->whenLoaded('city')),
            ],
            'company' => new CompanyResource($this->whenLoaded('company')),
            'category' => new JobCategoryResource($this->whenLoaded('jobCategory')),
            'job_type' => new JobTypeResource($this->whenLoaded('jobType')),
            'career_level' => new CareerLevelResource($this->whenLoaded('careerLevel')),
            'functional_area' => new FunctionalAreaResource($this->whenLoaded('functionalArea')),
            'job_shift' => new JobShiftResource($this->whenLoaded('jobShift')),
            'degree_level' => new RequiredDegreeLevelResource($this->whenLoaded('degreeLevel')),
            'experience' => $this->experience,
            'position' => $this->position,
            'job_expiry_date' => $this->job_expiry_date?->format('Y-m-d'),
            'is_expired' => $this->isExpired(),
            'is_active' => $this->isActive(),
            'is_featured' => $this->isFeatured(),
            'is_freelance' => $this->is_freelance,
            'is_suspended' => $this->is_suspended,
            'no_preference' => $this->no_preference,
            'skills' => SkillResource::collection($this->whenLoaded('jobsSkill')),
            'tags' => TagResource::collection($this->whenLoaded('jobsTag')),
            'applications_count' => $this->when(
                $this->relationLoaded('appliedJobs'),
                fn() => $this->applied_jobs_count ?? $this->appliedJobs->count()
            ),
            'featured_details' => new FeaturedRecordResource($this->whenLoaded('activeFeatured')),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
            'created_at_human' => $this->created_at?->diffForHumans(),
            'updated_at_human' => $this->updated_at?->diffForHumans(),
            'urls' => [
                'show' => route('jobs.show', $this->id),
                'apply' => route('jobs.apply', $this->id),
                'company' => route('companies.show', $this->company_id),
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
            ],
        ];
    }
} 