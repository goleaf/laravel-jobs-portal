<?php

namespace App\Services;

use App\Models\JobType;
use App\Models\Job;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class JobTypeService
{
    /**
     * Cache TTL in seconds
     */
    private const CACHE_TTL = 3600; // 1 hour

    /**
     * Get filtered and paginated job types
     */
    public function getJobTypes(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $cacheKey = 'job_types_' . md5(serialize($filters) . $perPage);
        
        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($filters, $perPage) {
            $query = JobType::query();

            // Apply filters
            $this->applyFilters($query, $filters);

            // Apply sorting
            $this->applySorting($query, $filters['sort'] ?? 'name');

            // Include relationships if requested
            if (!empty($filters['include'])) {
                $query->with($this->parseIncludes($filters['include']));
            }

            // Include counts if requested
            if (!empty($filters['include_counts'])) {
                $query->withCount([
                    'jobs',
                    'jobs as active_jobs_count' => fn($q) => $q->where('is_active', true)
                ]);
            }

            return $query->paginate($perPage);
        });
    }

    /**
     * Create a new job type
     */
    public function createJobType(array $data): JobType
    {
        DB::beginTransaction();
        
        try {
            // Prepare data
            $data = $this->prepareJobTypeData($data);
            
            // Create job type
            $jobType = JobType::create($data);
            
            // Handle post-creation tasks
            $this->handlePostCreation($jobType, $data);
            
            // Clear related caches
            $this->clearJobTypeCaches();
            
            DB::commit();
            
            Log::info('Job type created successfully', [
                'job_type_id' => $jobType->id,
                'name' => $jobType->name
            ]);
            
            return $jobType->fresh();
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create job type', [
                'data' => $data,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Update an existing job type
     */
    public function updateJobType(JobType $jobType, array $data): JobType
    {
        DB::beginTransaction();
        
        try {
            $originalData = $jobType->toArray();
            
            // Prepare data
            $data = $this->prepareJobTypeData($data, $jobType);
            
            // Update job type
            $jobType->update($data);
            
            // Handle post-update tasks
            $this->handlePostUpdate($jobType, $originalData, $data);
            
            // Clear related caches
            $this->clearJobTypeCaches($jobType->id);
            
            DB::commit();
            
            Log::info('Job type updated successfully', [
                'job_type_id' => $jobType->id,
                'changes' => array_diff_assoc($data, $originalData)
            ]);
            
            return $jobType->fresh();
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update job type', [
                'job_type_id' => $jobType->id,
                'data' => $data,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Delete a job type
     */
    public function deleteJobType(JobType $jobType, bool $force = false): bool
    {
        // Check if job type is in use
        if (!$force && $this->isJobTypeInUse($jobType)) {
            throw new \InvalidArgumentException('Cannot delete job type that is currently in use');
        }

        DB::beginTransaction();
        
        try {
            $jobTypeId = $jobType->id;
            $jobTypeName = $jobType->name;
            
            // Handle related jobs if force delete
            if ($force && $this->isJobTypeInUse($jobType)) {
                $this->handleForceDelete($jobType);
            }
            
            // Delete the job type
            $deleted = $jobType->delete();
            
            // Clear related caches
            $this->clearJobTypeCaches($jobTypeId);
            
            DB::commit();
            
            Log::info('Job type deleted successfully', [
                'job_type_id' => $jobTypeId,
                'name' => $jobTypeName,
                'force' => $force
            ]);
            
            return $deleted;
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to delete job type', [
                'job_type_id' => $jobType->id,
                'force' => $force,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Bulk update job types
     */
    public function bulkUpdate(array $jobTypeIds, string $action, array $data = []): int
    {
        $jobTypes = JobType::whereIn('id', $jobTypeIds)->get();
        
        if ($jobTypes->isEmpty()) {
            return 0;
        }

        DB::beginTransaction();
        
        try {
            $updatedCount = 0;
            
            foreach ($jobTypes as $jobType) {
                $updated = $this->performBulkAction($jobType, $action, $data);
                if ($updated) {
                    $updatedCount++;
                }
            }
            
            // Clear caches
            $this->clearJobTypeCaches();
            
            DB::commit();
            
            Log::info('Bulk job type update completed', [
                'action' => $action,
                'requested_count' => count($jobTypeIds),
                'updated_count' => $updatedCount
            ]);
            
            return $updatedCount;
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Bulk job type update failed', [
                'action' => $action,
                'job_type_ids' => $jobTypeIds,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Get job type statistics
     */
    public function getStatistics(): array
    {
        return Cache::remember('job_types_statistics', self::CACHE_TTL, function () {
            return [
                'total' => JobType::count(),
                'active' => JobType::active()->count(),
                'inactive' => JobType::inactive()->count(),
                'default' => JobType::default()->count(),
                'custom' => JobType::custom()->count(),
                'featured' => JobType::where('is_featured', true)->count(),
                'with_jobs' => JobType::withJobs()->count(),
                'without_jobs' => JobType::whereDoesntHave('jobs')->count(),
                'high_demand' => JobType::highDemand(50)->count(),
                'popular' => JobType::popular(10)->with('jobs')->get()->map(function ($jobType) {
                    return [
                        'id' => $jobType->id,
                        'name' => $jobType->name,
                        'jobs_count' => $jobType->jobs_count,
                        'color' => $jobType->color,
                        'icon' => $jobType->icon
                    ];
                }),
                'trending' => JobType::trending()->limit(5)->get(['id', 'name', 'color', 'icon']),
                'recent' => JobType::recent(30)->count(),
                'demand_distribution' => $this->getDemandDistribution(),
                'usage_stats' => $this->getUsageStatistics(),
            ];
        });
    }

    /**
     * Search job types with advanced filters
     */
    public function searchJobTypes(string $query, array $filters = [], int $limit = 20): Collection
    {
        $cacheKey = 'job_types_search_' . md5($query . serialize($filters) . $limit);
        
        return Cache::remember($cacheKey, 600, function () use ($query, $filters, $limit) {
            $searchQuery = JobType::search($query);
            
            // Apply filters
            $this->applySearchFilters($searchQuery, $filters);
            
            return $searchQuery
                ->withCount('jobs')
                ->orderByDesc('jobs_count')
                ->limit($limit)
                ->get();
        });
    }

    /**
     * Get related job types
     */
    public function getRelatedJobTypes(JobType $jobType, int $limit = 5): Collection
    {
        $cacheKey = "job_type_{$jobType->id}_related_{$limit}";
        
        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($jobType, $limit) {
            return JobType::where('id', '!=', $jobType->id)
                ->active()
                ->withCount('jobs')
                ->orderByDesc('jobs_count')
                ->limit($limit)
                ->get();
        });
    }

    /**
     * Duplicate a job type
     */
    public function duplicateJobType(JobType $originalJobType, array $overrides = []): JobType
    {
        $data = $originalJobType->toArray();
        
        // Remove fields that shouldn't be duplicated
        unset($data['id'], $data['created_at'], $data['updated_at']);
        
        // Apply overrides
        $data = array_merge($data, $overrides);
        
        // Ensure unique name and slug
        $data['name'] = $overrides['name'] ?? ($data['name'] . ' (Copy)');
        $data['slug'] = \Illuminate\Support\Str::slug($data['name']);
        
        // Ensure slug uniqueness
        $baseSlug = $data['slug'];
        $counter = 1;
        while (JobType::where('slug', $data['slug'])->exists()) {
            $data['slug'] = $baseSlug . '-' . $counter;
            $counter++;
        }
        
        return $this->createJobType($data);
    }

    /**
     * Get job type autocomplete suggestions
     */
    public function getAutocompleteSuggestions(string $query, int $limit = 10): array
    {
        $cacheKey = "job_types_autocomplete_" . md5($query . $limit);
        
        return Cache::remember($cacheKey, 1800, function () use ($query, $limit) {
            return JobType::where('name', 'like', "%{$query}%")
                ->active()
                ->orderBy('name')
                ->limit($limit)
                ->get(['id', 'name', 'slug', 'icon', 'color'])
                ->toArray();
        });
    }

    /**
     * Clear job type caches
     */
    public function clearJobTypeCaches(?int $jobTypeId = null): void
    {
        $tags = ['job_types'];
        
        if ($jobTypeId) {
            $tags[] = "job_type-{$jobTypeId}";
            Cache::forget("job_type.{$jobTypeId}");
        }
        
        Cache::tags($tags)->flush();
        
        // Clear specific cache keys
        Cache::forget('job_types_statistics');
        Cache::forget('job_types_popular');
        Cache::forget('job_types_trending');
    }

    /**
     * Warm up caches
     */
    public function warmUpCaches(): void
    {
        // Warm up statistics
        $this->getStatistics();
        
        // Warm up popular job types
        $this->getJobTypes(['sort' => 'popularity'], 15);
        
        // Warm up active job types
        $this->getJobTypes(['status' => 'active'], 15);
        
        Log::info('Job type caches warmed up successfully');
    }

    /**
     * Apply filters to query
     */
    private function applyFilters($query, array $filters): void
    {
        if (!empty($filters['search'])) {
            $query->search($filters['search']);
        }

        if (!empty($filters['status'])) {
            $filters['status'] === 'active' ? $query->active() : $query->inactive();
        }

        if (isset($filters['is_default'])) {
            $filters['is_default'] ? $query->default() : $query->custom();
        }

        if (isset($filters['is_featured'])) {
            $query->where('is_featured', $filters['is_featured']);
        }

        if (!empty($filters['type'])) {
            match ($filters['type']) {
                'full_time' => $query->fullTime(),
                'part_time' => $query->partTime(),
                'contract' => $query->contract(),
                'temporary' => $query->temporary(),
                'internship' => $query->internship(),
                'freelance' => $query->freelance(),
                'remote' => $query->remote(),
                default => null,
            };
        }

        if (!empty($filters['demand'])) {
            match ($filters['demand']) {
                'high' => $query->highDemand(50),
                'medium' => $query->minUsage(10)->withCount('jobs')->having('jobs_count', '<', 50),
                'low' => $query->minUsage(1)->withCount('jobs')->having('jobs_count', '<', 10),
                default => null,
            };
        }
    }

    /**
     * Apply sorting to query
     */
    private function applySorting($query, string $sort): void
    {
        match ($sort) {
            'name' => $query->alphabetical(),
            'popularity' => $query->popular(),
            'recent' => $query->recent(),
            'trending' => $query->trending(),
            'usage' => $query->withCount('jobs')->orderByDesc('jobs_count'),
            default => $query->orderBy('sort_order')->orderBy('name'),
        };
    }

    /**
     * Prepare job type data
     */
    private function prepareJobTypeData(array $data, ?JobType $jobType = null): array
    {
        // Auto-generate slug if not provided
        if (empty($data['slug']) && !empty($data['name'])) {
            $data['slug'] = \Illuminate\Support\Str::slug($data['name']);
        }

        // Ensure slug uniqueness
        if (!empty($data['slug'])) {
            $query = JobType::where('slug', $data['slug']);
            if ($jobType) {
                $query->where('id', '!=', $jobType->id);
            }
            
            if ($query->exists()) {
                $baseSlug = $data['slug'];
                $counter = 1;
                do {
                    $data['slug'] = $baseSlug . '-' . $counter;
                    $counter++;
                } while (JobType::where('slug', $data['slug'])->exists());
            }
        }

        // Set defaults
        $data['is_active'] = $data['is_active'] ?? true;
        $data['is_default'] = $data['is_default'] ?? false;
        $data['is_featured'] = $data['is_featured'] ?? false;

        return $data;
    }

    /**
     * Check if job type is in use
     */
    private function isJobTypeInUse(JobType $jobType): bool
    {
        return $jobType->jobs()->exists();
    }

    /**
     * Handle post-creation tasks
     */
    private function handlePostCreation(JobType $jobType, array $data): void
    {
        // Handle any post-creation logic here
        // For example, creating default job type configurations
    }

    /**
     * Handle post-update tasks
     */
    private function handlePostUpdate(JobType $jobType, array $originalData, array $newData): void
    {
        // Handle any post-update logic here
        // For example, updating related jobs if job type name changed
    }

    /**
     * Handle force delete
     */
    private function handleForceDelete(JobType $jobType): void
    {
        // Handle what happens to related jobs when force deleting
        // This could involve reassigning to a default job type or marking as archived
        $defaultJobType = JobType::default()->active()->first();
        
        if ($defaultJobType) {
            $jobType->jobs()->update(['job_type_id' => $defaultJobType->id]);
        }
    }

    /**
     * Perform bulk action on job type
     */
    private function performBulkAction(JobType $jobType, string $action, array $data): bool
    {
        return match ($action) {
            'activate' => $jobType->update(['is_active' => true]),
            'deactivate' => $jobType->update(['is_active' => false]),
            'feature' => $jobType->update(['is_featured' => true]),
            'unfeature' => $jobType->update(['is_featured' => false]),
            'delete' => !$this->isJobTypeInUse($jobType) && $jobType->delete(),
            'update' => $jobType->update($data),
            default => false,
        };
    }

    /**
     * Get demand distribution
     */
    private function getDemandDistribution(): array
    {
        return [
            'high' => JobType::highDemand(50)->count(),
            'medium' => JobType::minUsage(10)->withCount('jobs')->having('jobs_count', '<', 50)->count(),
            'low' => JobType::minUsage(1)->withCount('jobs')->having('jobs_count', '<', 10)->count(),
            'none' => JobType::whereDoesntHave('jobs')->count(),
        ];
    }

    /**
     * Get usage statistics
     */
    private function getUsageStatistics(): array
    {
        $stats = DB::table('job_types')
            ->leftJoin('jobs', 'job_types.id', '=', 'jobs.job_type_id')
            ->selectRaw('
                COUNT(DISTINCT job_types.id) as total_types,
                COUNT(jobs.id) as total_jobs,
                AVG(CASE WHEN jobs.id IS NOT NULL THEN 1 ELSE 0 END) as average_usage,
                MAX(jobs_count.count) as max_usage,
                MIN(jobs_count.count) as min_usage
            ')
            ->leftJoinSub(
                DB::table('jobs')->select('job_type_id', DB::raw('COUNT(*) as count'))->groupBy('job_type_id'),
                'jobs_count',
                'job_types.id',
                '=',
                'jobs_count.job_type_id'
            )
            ->first();

        return [
            'total_types' => $stats->total_types ?? 0,
            'total_jobs' => $stats->total_jobs ?? 0,
            'average_usage' => round($stats->average_usage ?? 0, 2),
            'max_usage' => $stats->max_usage ?? 0,
            'min_usage' => $stats->min_usage ?? 0,
        ];
    }

    /**
     * Parse includes parameter
     */
    private function parseIncludes(string $includes): array
    {
        return array_filter(explode(',', $includes));
    }

    /**
     * Apply search filters
     */
    private function applySearchFilters($query, array $filters): void
    {
        if (!empty($filters['status'])) {
            $filters['status'] === 'active' ? $query->active() : $query->inactive();
        }

        if (!empty($filters['type'])) {
            match ($filters['type']) {
                'default' => $query->default(),
                'custom' => $query->custom(),
                'featured' => $query->where('is_featured', true),
                default => null,
            };
        }
    }
} 