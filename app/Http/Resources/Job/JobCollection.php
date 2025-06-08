<?php

namespace App\Http\Resources\Job;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

/**
 * Class JobCollection
 * 
 * API resource collection for Job listings with pagination,
 * filtering metadata, and performance optimization.
 */
class JobCollection extends ResourceCollection
{
    /**
     * The resource that this resource collects.
     *
     * @var string
     */
    public $collects = JobResource::class;

    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'data' => $this->collection,
            'meta' => $this->getMeta($request),
            'filters' => $this->getFilterMeta($request),
            'aggregations' => $this->getAggregations($request),
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
            'links' => $this->getNavigationLinks($request),
            'included' => $this->getIncludedData($request),
            'performance' => $this->getPerformanceMetrics($request),
        ];
    }

    /**
     * Get collection metadata.
     */
    protected function getMeta(Request $request): array
    {
        $pagination = $this->resource->toArray();
        
        return [
            // Pagination information
            'pagination' => [
                'current_page' => $pagination['current_page'] ?? 1,
                'last_page' => $pagination['last_page'] ?? 1,
                'per_page' => $pagination['per_page'] ?? 15,
                'total' => $pagination['total'] ?? $this->collection->count(),
                'from' => $pagination['from'] ?? null,
                'to' => $pagination['to'] ?? null,
                'has_more_pages' => $pagination['current_page'] < ($pagination['last_page'] ?? 1),
            ],

            // Collection statistics
            'statistics' => [
                'total_jobs' => $pagination['total'] ?? $this->collection->count(),
                'active_jobs' => $this->collection->where('is_active', true)->count(),
                'featured_jobs' => $this->collection->where('is_featured', true)->count(),
                'urgent_jobs' => $this->collection->where('is_urgent', true)->count(),
                'remote_jobs' => $this->collection->where('is_remote', true)->count(),
                'new_jobs_today' => $this->collection->where('created_at', '>=', today())->count(),
                'new_jobs_week' => $this->collection->where('created_at', '>=', now()->subWeek())->count(),
            ],

            // Salary statistics (if not hidden)
            'salary_stats' => $this->getSalaryStatistics(),

            // Location distribution
            'location_distribution' => $this->getLocationDistribution(),

            // Collection info
            'collection_info' => [
                'generated_at' => now()->toISOString(),
                'cache_key' => $this->getCacheKey($request),
                'data_freshness' => $this->getDataFreshness(),
                'api_version' => '1.0',
            ],
        ];
    }

    /**
     * Get filter metadata to help with UI construction.
     */
    protected function getFilterMeta(Request $request): array
    {
        return [
            'applied_filters' => $this->getAppliedFilters($request),
            'available_filters' => $this->getAvailableFilters(),
            'search' => [
                'query' => $request->get('search'),
                'results_count' => $this->collection->count(),
                'suggestions' => $this->getSearchSuggestions($request),
            ],
            'sorting' => [
                'current_sort' => $request->get('sort', 'created_at'),
                'current_direction' => $request->get('direction', 'desc'),
                'available_sorts' => [
                    'created_at' => __('job.sort.newest'),
                    'title' => __('job.sort.title'),
                    'salary_from' => __('job.sort.salary'),
                    'experience' => __('job.sort.experience'),
                    'applications_count' => __('job.sort.popularity'),
                    'views_count' => __('job.sort.views'),
                    'featured' => __('job.sort.featured'),
                ],
            ],
        ];
    }

    /**
     * Get aggregations for faceted search.
     */
    protected function getAggregations(Request $request): array
    {
        if (!$request->has('include_aggregations')) {
            return [];
        }

        return [
            'categories' => $this->getCategoryAggregation(),
            'job_types' => $this->getJobTypeAggregation(),
            'locations' => $this->getLocationAggregation(),
            'salary_ranges' => $this->getSalaryRangeAggregation(),
            'experience_levels' => $this->getExperienceLevelAggregation(),
            'companies' => $this->getCompanyAggregation(),
            'skills' => $this->getSkillAggregation(),
            'career_levels' => $this->getCareerLevelAggregation(),
        ];
    }

    /**
     * Get navigation links for pagination.
     */
    protected function getNavigationLinks(Request $request): array
    {
        $pagination = $this->resource->toArray();
        
        return [
            'first' => $this->buildPageUrl($request, 1),
            'last' => $this->buildPageUrl($request, $pagination['last_page'] ?? 1),
            'prev' => $pagination['prev_page_url'] ?? null,
            'next' => $pagination['next_page_url'] ?? null,
            'self' => $request->url(),
        ];
    }

    /**
     * Get included data based on request parameters.
     */
    protected function getIncludedData(Request $request): array
    {
        $included = [];
        $includes = explode(',', $request->get('include', ''));

        foreach ($includes as $include) {
            switch (trim($include)) {
                case 'categories':
                    $included['categories'] = $this->getUniqueCategories();
                    break;
                case 'companies':
                    $included['companies'] = $this->getUniqueCompanies();
                    break;
                case 'locations':
                    $included['locations'] = $this->getUniqueLocations();
                    break;
                case 'skills':
                    $included['skills'] = $this->getPopularSkills();
                    break;
            }
        }

        return $included;
    }

    /**
     * Get performance metrics for debugging and optimization.
     */
    protected function getPerformanceMetrics(Request $request): array
    {
        if (!$request->has('include_performance') || !config('app.debug')) {
            return [];
        }

        return [
            'query_count' => \DB::getQueryLog() ? count(\DB::getQueryLog()) : null,
            'memory_usage' => memory_get_usage(true),
            'peak_memory' => memory_get_peak_usage(true),
            'execution_time' => microtime(true) - LARAVEL_START,
            'cache_hits' => cache()->get('api_cache_hits', 0),
            'cache_misses' => cache()->get('api_cache_misses', 0),
        ];
    }

    /**
     * Get salary statistics for the collection.
     */
    protected function getSalaryStatistics(): array
    {
        $jobsWithSalary = $this->collection->filter(function ($job) {
            return !$job['salary']['hide_salary'] && 
                   $job['salary']['salary_from'] && 
                   $job['salary']['salary_to'];
        });

        if ($jobsWithSalary->isEmpty()) {
            return ['available' => false];
        }

        $salaries = $jobsWithSalary->map(function ($job) {
            return ($job['salary']['salary_from'] + $job['salary']['salary_to']) / 2;
        });

        return [
            'available' => true,
            'min_salary' => $salaries->min(),
            'max_salary' => $salaries->max(),
            'avg_salary' => round($salaries->avg()),
            'median_salary' => $salaries->median(),
            'jobs_with_salary' => $jobsWithSalary->count(),
            'jobs_without_salary' => $this->collection->count() - $jobsWithSalary->count(),
        ];
    }

    /**
     * Get location distribution.
     */
    protected function getLocationDistribution(): array
    {
        $locations = $this->collection->groupBy('location.country.name')
            ->map(function ($jobs, $country) {
                return [
                    'country' => $country,
                    'count' => $jobs->count(),
                    'cities' => $jobs->groupBy('location.city.name')
                        ->map(function ($cityJobs, $city) {
                            return [
                                'city' => $city,
                                'count' => $cityJobs->count(),
                            ];
                        })
                        ->values()
                        ->take(5) // Top 5 cities per country
                        ->toArray(),
                ];
            })
            ->values()
            ->take(10) // Top 10 countries
            ->toArray();

        return $locations;
    }

    /**
     * Get applied filters from request.
     */
    protected function getAppliedFilters(Request $request): array
    {
        $filters = [];
        
        $filterParams = [
            'category' => 'job_category_id',
            'type' => 'job_type_id',
            'location' => 'city_id',
            'salary_min' => 'salary_from',
            'salary_max' => 'salary_to',
            'experience_min' => 'min_experience',
            'experience_max' => 'max_experience',
            'company' => 'company_id',
            'remote' => 'is_remote',
            'featured' => 'is_featured',
        ];

        foreach ($filterParams as $param => $field) {
            if ($request->has($param)) {
                $filters[$param] = $request->get($param);
            }
        }

        return $filters;
    }

    /**
     * Get available filters for the UI.
     */
    protected function getAvailableFilters(): array
    {
        return [
            'categories' => cache()->remember('filter_categories', 3600, function () {
                return \App\Models\JobCategory::active()->pluck('name', 'id');
            }),
            'job_types' => cache()->remember('filter_job_types', 3600, function () {
                return \App\Models\JobType::active()->pluck('name', 'id');
            }),
            'career_levels' => cache()->remember('filter_career_levels', 3600, function () {
                return \App\Models\CareerLevel::active()->pluck('level_name', 'id');
            }),
            'experience_ranges' => [
                '0-1' => __('job.experience.entry_level'),
                '1-3' => __('job.experience.junior'),
                '3-5' => __('job.experience.mid_level'),
                '5-10' => __('job.experience.senior'),
                '10+' => __('job.experience.executive'),
            ],
            'salary_ranges' => [
                '0-30000' => __('job.salary.entry_level'),
                '30000-60000' => __('job.salary.mid_level'),
                '60000-100000' => __('job.salary.senior'),
                '100000+' => __('job.salary.executive'),
            ],
        ];
    }

    /**
     * Get search suggestions based on query.
     */
    protected function getSearchSuggestions(Request $request): array
    {
        $query = $request->get('search');
        
        if (!$query || strlen($query) < 3) {
            return [];
        }

        return cache()->remember("search_suggestions_{$query}", 1800, function () use ($query) {
            return [
                'job_titles' => \App\Models\Job::where('title', 'like', "%{$query}%")
                    ->active()
                    ->pluck('title')
                    ->unique()
                    ->take(5)
                    ->values()
                    ->toArray(),
                'companies' => \App\Models\Company::where('name', 'like', "%{$query}%")
                    ->active()
                    ->pluck('name')
                    ->unique()
                    ->take(5)
                    ->values()
                    ->toArray(),
                'skills' => \App\Models\Skill::where('name', 'like', "%{$query}%")
                    ->active()
                    ->pluck('name')
                    ->unique()
                    ->take(5)
                    ->values()
                    ->toArray(),
            ];
        });
    }

    /**
     * Get category aggregation data.
     */
    protected function getCategoryAggregation(): array
    {
        return $this->collection->groupBy('job_category.id')
            ->map(function ($jobs, $categoryId) {
                $category = $jobs->first()['job_category'] ?? null;
                return [
                    'id' => $categoryId,
                    'name' => $category['name'] ?? __('job.category.unknown'),
                    'count' => $jobs->count(),
                    'icon' => $category['icon'] ?? null,
                ];
            })
            ->values()
            ->sortByDesc('count')
            ->take(20)
            ->toArray();
    }

    /**
     * Get job type aggregation data.
     */
    protected function getJobTypeAggregation(): array
    {
        return $this->collection->groupBy('job_type.id')
            ->map(function ($jobs, $typeId) {
                $type = $jobs->first()['job_type'] ?? null;
                return [
                    'id' => $typeId,
                    'name' => $type['name'] ?? __('job.type.unknown'),
                    'count' => $jobs->count(),
                ];
            })
            ->values()
            ->sortByDesc('count')
            ->toArray();
    }

    /**
     * Build page URL with current query parameters.
     */
    protected function buildPageUrl(Request $request, int $page): string
    {
        $query = $request->query();
        $query['page'] = $page;
        
        return $request->url() . '?' . http_build_query($query);
    }

    /**
     * Get cache key for this collection.
     */
    protected function getCacheKey(Request $request): string
    {
        $params = $request->query();
        ksort($params);
        
        return 'jobs_collection_' . md5(serialize($params));
    }

    /**
     * Get data freshness indicator.
     */
    protected function getDataFreshness(): string
    {
        $newestJob = $this->collection->max('updated_at');
        
        if (!$newestJob) {
            return 'unknown';
        }

        $timeDiff = now()->diffInMinutes($newestJob);
        
        return match (true) {
            $timeDiff < 5 => 'real_time',
            $timeDiff < 30 => 'fresh',
            $timeDiff < 120 => 'recent',
            default => 'cached'
        };
    }

    // Additional aggregation methods...
    protected function getLocationAggregation(): array
    {
        return $this->collection->groupBy('location.city.name')
            ->map(function ($jobs, $city) {
                return [
                    'city' => $city,
                    'count' => $jobs->count(),
                ];
            })
            ->values()
            ->sortByDesc('count')
            ->take(15)
            ->toArray();
    }

    protected function getSalaryRangeAggregation(): array
    {
        $ranges = [
            '0-30000' => ['min' => 0, 'max' => 30000],
            '30000-60000' => ['min' => 30000, 'max' => 60000],
            '60000-100000' => ['min' => 60000, 'max' => 100000],
            '100000+' => ['min' => 100000, 'max' => PHP_INT_MAX],
        ];

        $result = [];
        foreach ($ranges as $label => $range) {
            $count = $this->collection->filter(function ($job) use ($range) {
                $salary = $job['salary']['salary_from'] ?? 0;
                return $salary >= $range['min'] && $salary < $range['max'];
            })->count();

            $result[] = [
                'range' => $label,
                'count' => $count,
            ];
        }

        return $result;
    }

    protected function getExperienceLevelAggregation(): array
    {
        return $this->collection->groupBy('requirements.min_experience')
            ->map(function ($jobs, $experience) {
                return [
                    'experience' => $experience,
                    'count' => $jobs->count(),
                ];
            })
            ->values()
            ->sortBy('experience')
            ->toArray();
    }

    protected function getCompanyAggregation(): array
    {
        return $this->collection->groupBy('company.id')
            ->map(function ($jobs, $companyId) {
                $company = $jobs->first()['company'] ?? null;
                return [
                    'id' => $companyId,
                    'name' => $company['name'] ?? __('job.company.unknown'),
                    'count' => $jobs->count(),
                ];
            })
            ->values()
            ->sortByDesc('count')
            ->take(20)
            ->toArray();
    }

    protected function getSkillAggregation(): array
    {
        $skills = collect();
        
        $this->collection->each(function ($job) use ($skills) {
            if (isset($job['requirements']['skills'])) {
                foreach ($job['requirements']['skills'] as $skill) {
                    $skills->push($skill);
                }
            }
        });

        return $skills->groupBy('id')
            ->map(function ($skillGroup, $skillId) {
                $skill = $skillGroup->first();
                return [
                    'id' => $skillId,
                    'name' => $skill['name'],
                    'count' => $skillGroup->count(),
                ];
            })
            ->values()
            ->sortByDesc('count')
            ->take(30)
            ->toArray();
    }

    protected function getCareerLevelAggregation(): array
    {
        return $this->collection->groupBy('career_level.id')
            ->map(function ($jobs, $levelId) {
                $level = $jobs->first()['career_level'] ?? null;
                return [
                    'id' => $levelId,
                    'name' => $level['level_name'] ?? __('job.career_level.unknown'),
                    'count' => $jobs->count(),
                ];
            })
            ->values()
            ->sortByDesc('count')
            ->toArray();
    }

    protected function getUniqueCategories(): array
    {
        return $this->collection->pluck('job_category')
            ->filter()
            ->unique('id')
            ->values()
            ->toArray();
    }

    protected function getUniqueCompanies(): array
    {
        return $this->collection->pluck('company')
            ->filter()
            ->unique('id')
            ->values()
            ->toArray();
    }

    protected function getUniqueLocations(): array
    {
        return $this->collection->pluck('location')
            ->filter()
            ->unique('city.id')
            ->values()
            ->toArray();
    }

    protected function getPopularSkills(): array
    {
        $skills = collect();
        
        $this->collection->each(function ($job) use ($skills) {
            if (isset($job['requirements']['skills'])) {
                foreach ($job['requirements']['skills'] as $skill) {
                    $skills->push($skill);
                }
            }
        });

        return $skills->groupBy('id')
            ->map(function ($skillGroup) {
                $skill = $skillGroup->first();
                $skill['usage_count'] = $skillGroup->count();
                return $skill;
            })
            ->sortByDesc('usage_count')
            ->take(20)
            ->values()
            ->toArray();
    }
} 