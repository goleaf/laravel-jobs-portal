<?php

namespace App\Http\Resources\CareerLevel;

use App\Models\CareerLevel;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CareerLevelResource extends JsonResource
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
            'level_name' => $this->level_name,
            'description' => $this->when($this->description, $this->description),
            'display_name' => $this->getDisplayName(),
            'is_default' => $this->is_default,
            'is_active' => $this->is_active,

            // Counts
            'jobs_count' => $this->whenCounted('jobs'),
            'candidates_count' => $this->whenCounted('candidates'),
            'active_jobs_count' => $this->whenCounted('activeJobs'),

            // Timestamps
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
            'formatted_created_at' => $this->created_at?->format(__('formats.date_time')),
            'formatted_updated_at' => $this->updated_at?->format(__('formats.date_time')),

            // Status labels
            'status_label' => $this->is_active ? __('common.active') : __('common.inactive'),
            'type_label' => $this->is_default ? __('common.default') : __('common.custom'),
            'level_category' => $this->getLevelCategory(),

            // Relationships
            'jobs' => $this->whenLoaded('jobs'),
            'candidates' => $this->whenLoaded('candidates'),

            // Computed attributes
            'experience_range' => $this->getExperienceRange(),
            'seniority_level' => $this->getSeniorityLevel(),
            'career_progression' => $this->getCareerProgression(),

            // Permissions
            'can_update' => $request->user()?->can('update', $this->resource),
            'can_delete' => $request->user()?->can('delete', $this->resource),

            // Links
            'links' => [
                'self' => route('api.career-levels.show', $this->id),
                'jobs' => route('api.jobs.index', ['career_level_id' => $this->id]),
                'candidates' => route('api.candidates.index', ['career_level_id' => $this->id]),
            ],

            // Statistics (when requested)
            'statistics' => $this->when($request->has('include_statistics'), function () {
                return [
                    'job_applications_count' => $this->getJobApplicationsCount(),
                    'average_salary' => $this->getAverageSalary(),
                    'top_skills' => $this->getTopSkills(),
                    'industry_distribution' => $this->getIndustryDistribution(),
                ];
            }),
        ];
    }

    /**
     * Get additional data when collection.
     */
    public function with(Request $request): array
    {
        return [
            'meta' => [
                'total_career_levels' => $this->collection?->count(),
                'active_career_levels' => $this->collection?->where('is_active', true)->count(),
                'default_career_levels' => $this->collection?->where('is_default', true)->count(),
                'level_categories' => $this->collection?->groupBy(fn ($item) => $item->getLevelCategory())->map->count(),
            ],
        ];
    }

    /**
     * Get display name for the career level.
     */
    private function getDisplayName(): string
    {
        return __('career_levels.display_format', ['level' => $this->level_name]);
    }

    /**
     * Get level category (entry, mid, senior, executive).
     */
    private function getLevelCategory(): string
    {
        $level = strtolower($this->level_name);

        if (preg_match('/(entry|junior|trainee|intern|beginner)/', $level)) {
            return __('career_levels.categories.entry');
        }

        if (preg_match('/(senior|lead|principal|architect)/', $level)) {
            return __('career_levels.categories.senior');
        }

        if (preg_match('/(manager|director|executive|ceo|cto|cfo)/', $level)) {
            return __('career_levels.categories.executive');
        }

        return __('career_levels.categories.mid');
    }

    /**
     * Get experience range description.
     */
    private function getExperienceRange(): string
    {
        $category = $this->getLevelCategory();

        return match ($category) {
            __('career_levels.categories.entry') => __('career_levels.experience.entry'),
            __('career_levels.categories.mid') => __('career_levels.experience.mid'),
            __('career_levels.categories.senior') => __('career_levels.experience.senior'),
            __('career_levels.categories.executive') => __('career_levels.experience.executive'),
            default => __('career_levels.experience.varies'),
        };
    }

    /**
     * Get seniority level numeric value.
     */
    private function getSeniorityLevel(): int
    {
        $category = $this->getLevelCategory();

        return match ($category) {
            __('career_levels.categories.entry') => 1,
            __('career_levels.categories.mid') => 2,
            __('career_levels.categories.senior') => 3,
            __('career_levels.categories.executive') => 4,
            default => 2,
        };
    }

    /**
     * Get career progression information.
     */
    private function getCareerProgression(): array
    {
        return [
            'current_level' => $this->getSeniorityLevel(),
            'next_level' => $this->getNextLevelSuggestions(),
            'skills_needed' => $this->getSkillsForProgression(),
        ];
    }

    /**
     * Get job applications count for this career level.
     */
    private function getJobApplicationsCount(): int
    {
        return cache()->remember("career_level.{$this->id}.applications_count", 3600, function () {
            return $this->jobs()->withCount('appliedJobs')->get()->sum('applied_jobs_count');
        });
    }

    /**
     * Get average salary for this career level.
     */
    private function getAverageSalary(): ?float
    {
        return cache()->remember("career_level.{$this->id}.average_salary", 3600, function () {
            return $this->jobs()
                ->whereNotNull('salary_from')
                ->whereNotNull('salary_to')
                ->selectRaw('AVG((salary_from + salary_to) / 2) as avg_salary')
                ->value('avg_salary')
            ;
        });
    }

    /**
     * Get top skills for this career level.
     */
    private function getTopSkills(): array
    {
        return cache()->remember("career_level.{$this->id}.top_skills", 3600, function () {
            // This would require a more complex query joining through job_skills
            return [];
        });
    }

    /**
     * Get industry distribution for this career level.
     */
    private function getIndustryDistribution(): array
    {
        return cache()->remember("career_level.{$this->id}.industry_distribution", 3600, function () {
            return $this->jobs()
                ->join('companies', 'jobs.company_id', '=', 'companies.id')
                ->join('industries', 'companies.industry_id', '=', 'industries.id')
                ->groupBy('industries.name')
                ->selectRaw('industries.name, COUNT(*) as job_count')
                ->pluck('job_count', 'name')
                ->toArray()
            ;
        });
    }

    /**
     * Get next level suggestions.
     */
    private function getNextLevelSuggestions(): array
    {
        $currentLevel = $this->getSeniorityLevel();

        return CareerLevel::active()
            ->where('id', '!=', $this->id)
            ->get()
            ->filter(function ($level) use ($currentLevel) {
                $levelResource = new self($level);

                return $levelResource->getSeniorityLevel() === $currentLevel + 1;
            })
            ->map(function ($level) {
                return [
                    'id' => $level->id,
                    'level_name' => $level->level_name,
                    'display_name' => $level->getDisplayName(),
                ];
            })
            ->values()
            ->toArray()
        ;
    }

    /**
     * Get skills needed for career progression.
     */
    private function getSkillsForProgression(): array
    {
        // This would be implemented based on skill analysis
        return [];
    }
}
