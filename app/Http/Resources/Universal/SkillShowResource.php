<?php

namespace App\Http\Resources\Universal;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SkillShowResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'category' => $this->category,
            'level' => $this->level,
            'icon' => $this->icon,
            'color' => $this->color,
            'synonyms' => $this->synonyms,
            'status' => [
                'is_verified' => $this->is_verified,
                'is_popular' => $this->is_popular,
                'is_active' => $this->is_active,
            ],
            'statistics' => [
                'usage_count' => $this->getUsageCount(),
                'job_count' => $this->jobs()->count(),
                'candidate_count' => $this->candidates()->count(),
                'demand_score' => $this->getDemandScore(),
                'trending_score' => $this->getTrendingScore(),
            ],
            'related_skills' => $this->when($this->relationLoaded('relatedSkills'), function() {
                return $this->relatedSkills->take(10)->map(function($skill) {
                    return [
                        'id' => $skill->id,
                        'name' => $skill->name,
                        'slug' => $skill->slug,
                        'similarity_score' => $skill->pivot->similarity_score ?? null,
                    ];
                });
            }),
            'recent_jobs' => $this->when($this->relationLoaded('jobs'), function() {
                return $this->jobs()->latest()->take(5)->get()->map(function($job) {
                    return [
                        'id' => $job->id,
                        'title' => $job->title,
                        'company' => $job->company?->name,
                        'location' => $job->city?->name,
                        'salary_range' => $job->getFormattedSalaryRange(),
                        'created_at' => $job->created_at?->format('Y-m-d'),
                    ];
                });
            }),
            'salary_insights' => [
                'average_salary' => $this->getAverageSalary(),
                'salary_range' => $this->getSalaryRange(),
                'currency' => 'USD', // Default currency
                'data_points' => $this->getSalaryDataPoints(),
            ],
            'learning_resources' => $this->when($this->relationLoaded('learningResources'), function() {
                return $this->learningResources->map(function($resource) {
                    return [
                        'id' => $resource->id,
                        'title' => $resource->title,
                        'type' => $resource->type,
                        'url' => $resource->url,
                        'rating' => $resource->rating,
                        'difficulty' => $resource->difficulty,
                    ];
                });
            }),
            'timestamps' => [
                'created_at' => $this->created_at?->toISOString(),
                'updated_at' => $this->updated_at?->toISOString(),
                'verified_at' => $this->verified_at?->toISOString(),
            ],
        ];
    }

    public function with(Request $request): array
    {
        return [
            'meta' => [
                'version' => '1.0',
                'generated_at' => now()->toISOString(),
                'resource_type' => 'skill_detail',
                'includes' => $this->getIncludedRelations(),
            ],
        ];
    }

    public function withResponse(Request $request, $response): void
    {
        $response->header('X-Resource-Type', 'SkillShowResource');
        $response->header('Cache-Control', 'public, max-age=1800'); // 30 minutes cache
    }

    private function getUsageCount(): int
    {
        return $this->jobs()->count() + $this->candidates()->count();
    }

    private function getDemandScore(): float
    {
        $jobCount = $this->jobs()->where('created_at', '>=', now()->subMonths(3))->count();
        $candidateCount = $this->candidates()->where('created_at', '>=', now()->subMonths(3))->count();
        
        if ($candidateCount == 0) return 100.0;
        
        return min(100, ($jobCount / $candidateCount) * 100);
    }

    private function getTrendingScore(): float
    {
        $recentJobs = $this->jobs()->where('created_at', '>=', now()->subMonth())->count();
        $previousJobs = $this->jobs()->whereBetween('created_at', [now()->subMonths(2), now()->subMonth()])->count();
        
        if ($previousJobs == 0) return $recentJobs > 0 ? 100.0 : 0.0;
        
        return (($recentJobs - $previousJobs) / $previousJobs) * 100;
    }

    private function getAverageSalary(): ?float
    {
        return $this->jobs()
            ->whereNotNull('salary_from')
            ->whereNotNull('salary_to')
            ->selectRaw('AVG((salary_from + salary_to) / 2) as avg_salary')
            ->value('avg_salary');
    }

    private function getSalaryRange(): array
    {
        $salaries = $this->jobs()
            ->whereNotNull('salary_from')
            ->whereNotNull('salary_to')
            ->selectRaw('MIN(salary_from) as min_salary, MAX(salary_to) as max_salary')
            ->first();

        return [
            'min' => $salaries->min_salary ?? null,
            'max' => $salaries->max_salary ?? null,
        ];
    }

    private function getSalaryDataPoints(): int
    {
        return $this->jobs()
            ->whereNotNull('salary_from')
            ->whereNotNull('salary_to')
            ->count();
    }

    private function getIncludedRelations(): array
    {
        $included = [];
        
        if ($this->relationLoaded('relatedSkills')) $included[] = 'related_skills';
        if ($this->relationLoaded('jobs')) $included[] = 'recent_jobs';
        if ($this->relationLoaded('learningResources')) $included[] = 'learning_resources';

        return $included;
    }
} 