<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Job;
use App\Models\Company;
use App\Models\JobCategory;
use App\Models\JobType;
use App\Models\Skill;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

/**
 * Enhanced Job Repository
 * 
 * Comprehensive job management repository with advanced patterns
 */
class JobRepository extends BaseRepository
{
    /**
     * Cache duration for job-related queries (in minutes)
     */
    private const CACHE_DURATION = 60;

    /**
     * Maximum jobs per page for pagination
     */
    private const MAX_PER_PAGE = 100;

    protected array $defaultWith = [
        'company', 
        'jobCategory', 
        'jobType', 
        'jobShift',
        'careerLevel',
        'functionalArea',
        'skills',
        'salaryCurrency'
    ];
    
    protected array $searchableFields = [
        'job_title',
        'description',
        'key_responsibilities',
        'benefits'
    ];
    
    protected array $filterableFields = [
        'company_id',
        'job_category_id',
        'job_type_id',
        'job_shift_id',
        'career_level_id',
        'functional_area_id',
        'salary_currency_id',
        'country_id',
        'state_id',
        'city_id',
        'is_active',
        'is_featured',
        'status'
    ];
    
    protected array $sortableFields = [
        'id',
        'job_title',
        'created_at',
        'updated_at',
        'salary_from',
        'salary_to',
        'job_expiry_date',
        'last_change'
    ];

    protected function getModelClass(): string
    {
        return Job::class;
    }

    public function __construct(Job $model)
    {
        parent::__construct($model);
    }

    /**
     * Find jobs with advanced filtering and search capabilities
     */
    public function findJobsWithFilters(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $cacheKey = 'jobs:filtered:' . md5(serialize($filters) . $perPage);

        return Cache::remember($cacheKey, self::CACHE_DURATION, function () use ($filters, $perPage) {
            $query = $this->model->newQuery()
                ->with(['company', 'jobType', 'jobCategory', 'skills', 'salaryPeriod', 'requiredDegreeLevel'])
                ->where('is_active', true)
                ->where('is_suspended', false);

            // Apply search filters
            $this->applySearchFilters($query, $filters);
            
            // Apply location filters
            $this->applyLocationFilters($query, $filters);
            
            // Apply job attribute filters
            $this->applyJobAttributeFilters($query, $filters);
            
            // Apply salary filters
            $this->applySalaryFilters($query, $filters);
            
            // Apply company filters
            $this->applyCompanyFilters($query, $filters);
            
            // Apply date filters
            $this->applyDateFilters($query, $filters);

            // Apply sorting
            $this->applySorting($query, $filters['sort'] ?? 'relevance');

            $perPage = min($perPage, self::MAX_PER_PAGE);

            return $query->paginate($perPage);
        });
    }

    /**
     * Get popular jobs based on application count and views
     */
    public function getPopularJobs(int $limit = 10): Collection
    {
        $cacheKey = "jobs:popular:{$limit}";

        return Cache::remember($cacheKey, self::CACHE_DURATION, function () use ($limit) {
            return $this->model->newQuery()
                ->select([
                    'jobs.*',
                    DB::raw('(SELECT COUNT(*) FROM job_applications WHERE job_applications.job_id = jobs.id) as applications_count'),
                    DB::raw('COALESCE(jobs.views_count, 0) as views_count'),
                    DB::raw('(
                        (SELECT COUNT(*) FROM job_applications WHERE job_applications.job_id = jobs.id) * 2 +
                        COALESCE(jobs.views_count, 0) +
                        (CASE WHEN jobs.is_featured = 1 THEN 50 ELSE 0 END) +
                        (CASE WHEN jobs.created_at >= ? THEN 25 ELSE 0 END)
                    ) as popularity_score')
                ])
                ->with(['company', 'jobType', 'jobCategory'])
                ->where('is_active', true)
                ->where('is_suspended', false)
                ->where('expire_date', '>=', now())
                ->addBinding(now()->subDays(7)->toDateTimeString(), 'select')
                ->orderBy('popularity_score', 'desc')
                ->orderBy('created_at', 'desc')
                ->limit($limit)
                ->get();
        });
    }

    /**
     * Get featured jobs
     */
    public function getFeaturedJobs(int $limit = 10): Collection
    {
        $cacheKey = "jobs:featured:{$limit}";

        return Cache::remember($cacheKey, self::CACHE_DURATION, function () use ($limit) {
            return $this->model->newQuery()
                ->with(['company', 'jobType', 'jobCategory'])
                ->where('is_active', true)
                ->where('is_suspended', false)
                ->where('is_featured', true)
                ->where('expire_date', '>=', now())
                ->orderBy('featured_until', 'desc')
                ->orderBy('created_at', 'desc')
                ->limit($limit)
                ->get();
        });
    }

    /**
     * Get similar jobs based on category, skills, and location
     */
    public function getSimilarJobs(Job $job, int $limit = 5): Collection
    {
        $cacheKey = "jobs:similar:{$job->id}:{$limit}";

        return Cache::remember($cacheKey, self::CACHE_DURATION / 2, function () use ($job, $limit) {
            $skillIds = $job->skills->pluck('id')->toArray();
            
            $query = $this->model->newQuery()
                ->with(['company', 'jobType', 'jobCategory'])
                ->where('id', '!=', $job->id)
                ->where('is_active', true)
                ->where('is_suspended', false)
                ->where('expire_date', '>=', now());

            // Score by relevance
            $query->select([
                'jobs.*',
                DB::raw('(
                    (CASE WHEN job_category_id = ? THEN 30 ELSE 0 END) +
                    (CASE WHEN country = ? THEN 20 ELSE 0 END) +
                    (CASE WHEN state = ? THEN 15 ELSE 0 END) +
                    (CASE WHEN city = ? THEN 10 ELSE 0 END) +
                    (SELECT COUNT(*) * 5 FROM jobs_skill WHERE jobs_skill.job_id = jobs.id AND skill_id IN (' . 
                    str_repeat('?,', count($skillIds) - 1) . '?)) +
                    (CASE WHEN company_id = ? THEN 25 ELSE 0 END)
                ) as relevance_score')
            ]);

            // Bind parameters for relevance scoring
            $bindings = [
                $job->job_category_id,
                $job->country,
                $job->state,
                $job->city,
                ...$skillIds,
                $job->company_id
            ];

            foreach ($bindings as $binding) {
                $query->addBinding($binding, 'select');
            }

            return $query->having('relevance_score', '>', 0)
                ->orderBy('relevance_score', 'desc')
                ->orderBy('created_at', 'desc')
                ->limit($limit)
                ->get();
        });
    }

    /**
     * Get jobs by company with pagination
     */
    public function getJobsByCompany(Company $company, int $perPage = 10): LengthAwarePaginator
    {
        return $this->model->newQuery()
            ->with(['jobType', 'jobCategory', 'skills'])
            ->where('company_id', $company->id)
            ->where('is_active', true)
            ->where('is_suspended', false)
            ->where('expire_date', '>=', now())
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Get job statistics for dashboard
     */
    public function getJobStatistics(): array
    {
        $cacheKey = 'jobs:statistics';

        return Cache::remember($cacheKey, self::CACHE_DURATION, function () {
            $totalJobs = $this->model->where('is_active', true)->count();
            $activeJobs = $this->model->where('is_active', true)
                ->where('is_suspended', false)
                ->where('expire_date', '>=', now())
                ->count();
            
            $featuredJobs = $this->model->where('is_active', true)
                ->where('is_featured', true)
                ->where('expire_date', '>=', now())
                ->count();

            $expiredJobs = $this->model->where('is_active', true)
                ->where('expire_date', '<', now())
                ->count();

            $todayJobs = $this->model->where('is_active', true)
                ->whereDate('created_at', today())
                ->count();

            $weekJobs = $this->model->where('is_active', true)
                ->where('created_at', '>=', now()->subDays(7))
                ->count();

            return [
                'total_jobs' => $totalJobs,
                'active_jobs' => $activeJobs,
                'featured_jobs' => $featuredJobs,
                'expired_jobs' => $expiredJobs,
                'today_jobs' => $todayJobs,
                'week_jobs' => $weekJobs,
                'expiring_soon' => $this->getExpiringJobs(7)->count(),
            ];
        });
    }

    /**
     * Get jobs expiring within specified days
     */
    public function getExpiringJobs(int $days = 7): Collection
    {
        return $this->model->newQuery()
            ->with(['company'])
            ->where('is_active', true)
            ->where('is_suspended', false)
            ->where('expire_date', '>=', now())
            ->where('expire_date', '<=', now()->addDays($days))
            ->orderBy('expire_date', 'asc')
            ->get();
    }

    /**
     * Search jobs by keyword with relevance scoring
     */
    public function searchJobsByKeyword(string $keyword, int $perPage = 15): LengthAwarePaginator
    {
        $cacheKey = "jobs:search:" . md5($keyword) . ":{$perPage}";

        return Cache::remember($cacheKey, self::CACHE_DURATION / 2, function () use ($keyword, $perPage) {
            $searchTerms = explode(' ', trim($keyword));
            $searchPattern = '%' . implode('%', $searchTerms) . '%';

            return $this->model->newQuery()
                ->select([
                    'jobs.*',
                    DB::raw('(
                        (CASE WHEN jobs.title LIKE ? THEN 50 ELSE 0 END) +
                        (CASE WHEN jobs.description LIKE ? THEN 30 ELSE 0 END) +
                        (CASE WHEN jobs.key_responsibilities LIKE ? THEN 20 ELSE 0 END) +
                        (SELECT COUNT(*) * 15 FROM jobs_skill js 
                         JOIN skills s ON js.skill_id = s.id 
                         WHERE js.job_id = jobs.id AND s.name LIKE ?) +
                        (SELECT CASE WHEN c.name LIKE ? THEN 25 ELSE 0 END 
                         FROM companies c WHERE c.id = jobs.company_id) +
                        (SELECT CASE WHEN jc.name LIKE ? THEN 20 ELSE 0 END 
                         FROM job_categories jc WHERE jc.id = jobs.job_category_id)
                    ) as relevance_score')
                ])
                ->with(['company', 'jobType', 'jobCategory', 'skills'])
                ->where('is_active', true)
                ->where('is_suspended', false)
                ->where('expire_date', '>=', now())
                ->addBinding($searchPattern, 'select') // title
                ->addBinding($searchPattern, 'select') // description
                ->addBinding($searchPattern, 'select') // key_responsibilities
                ->addBinding($searchPattern, 'select') // skills
                ->addBinding($searchPattern, 'select') // company
                ->addBinding($searchPattern, 'select') // category
                ->having('relevance_score', '>', 0)
                ->orderBy('relevance_score', 'desc')
                ->orderBy('created_at', 'desc')
                ->paginate($perPage);
        });
    }

    /**
     * Apply search filters to query
     */
    private function applySearchFilters(Builder $query, array $filters): void
    {
        if (!empty($filters['keyword'])) {
            $keyword = '%' . $filters['keyword'] . '%';
            $query->where(function ($q) use ($keyword) {
                $q->where('title', 'like', $keyword)
                  ->orWhere('description', 'like', $keyword)
                  ->orWhere('key_responsibilities', 'like', $keyword)
                  ->orWhereHas('skills', function ($skillQuery) use ($keyword) {
                      $skillQuery->where('name', 'like', $keyword);
                  })
                  ->orWhereHas('company', function ($companyQuery) use ($keyword) {
                      $companyQuery->where('name', 'like', $keyword);
                  });
            });
        }

        if (!empty($filters['skills'])) {
            $skillIds = is_array($filters['skills']) ? $filters['skills'] : [$filters['skills']];
            $query->whereHas('skills', function ($skillQuery) use ($skillIds) {
                $skillQuery->whereIn('skill_id', $skillIds);
            });
        }
    }

    /**
     * Apply location filters to query
     */
    private function applyLocationFilters(Builder $query, array $filters): void
    {
        if (!empty($filters['country'])) {
            $query->where('country', $filters['country']);
        }

        if (!empty($filters['state'])) {
            $query->where('state', $filters['state']);
        }

        if (!empty($filters['city'])) {
            $query->where('city', $filters['city']);
        }

        if (!empty($filters['remote'])) {
            $query->where('is_remote', true);
        }
    }

    /**
     * Apply job attribute filters to query
     */
    private function applyJobAttributeFilters(Builder $query, array $filters): void
    {
        if (!empty($filters['job_category_id'])) {
            $categoryIds = is_array($filters['job_category_id']) 
                ? $filters['job_category_id'] 
                : [$filters['job_category_id']];
            $query->whereIn('job_category_id', $categoryIds);
        }

        if (!empty($filters['job_type_id'])) {
            $typeIds = is_array($filters['job_type_id']) 
                ? $filters['job_type_id'] 
                : [$filters['job_type_id']];
            $query->whereIn('job_type_id', $typeIds);
        }

        if (!empty($filters['career_level_id'])) {
            $query->where('career_level_id', $filters['career_level_id']);
        }

        if (!empty($filters['functional_area_id'])) {
            $query->where('functional_area_id', $filters['functional_area_id']);
        }

        if (!empty($filters['experience_min'])) {
            $query->where('experience', '>=', (int) $filters['experience_min']);
        }

        if (!empty($filters['experience_max'])) {
            $query->where('experience', '<=', (int) $filters['experience_max']);
        }

        if (!empty($filters['is_featured'])) {
            $query->where('is_featured', (bool) $filters['is_featured']);
        }
    }

    /**
     * Apply salary filters to query
     */
    private function applySalaryFilters(Builder $query, array $filters): void
    {
        if (!empty($filters['salary_from'])) {
            $query->where('salary_from', '>=', (float) $filters['salary_from']);
        }

        if (!empty($filters['salary_to'])) {
            $query->where('salary_to', '<=', (float) $filters['salary_to']);
        }

        if (!empty($filters['salary_currency_id'])) {
            $query->where('salary_currency_id', $filters['salary_currency_id']);
        }

        if (!empty($filters['hide_salary']) && $filters['hide_salary'] === false) {
            $query->where('hide_salary', false);
        }
    }

    /**
     * Apply company filters to query
     */
    private function applyCompanyFilters(Builder $query, array $filters): void
    {
        if (!empty($filters['company_id'])) {
            $companyIds = is_array($filters['company_id']) 
                ? $filters['company_id'] 
                : [$filters['company_id']];
            $query->whereIn('company_id', $companyIds);
        }

        if (!empty($filters['company_size_id'])) {
            $query->whereHas('company', function ($companyQuery) use ($filters) {
                $companyQuery->where('company_size_id', $filters['company_size_id']);
            });
        }

        if (!empty($filters['industry_id'])) {
            $query->whereHas('company', function ($companyQuery) use ($filters) {
                $companyQuery->where('industry_id', $filters['industry_id']);
            });
        }
    }

    /**
     * Apply date filters to query
     */
    private function applyDateFilters(Builder $query, array $filters): void
    {
        if (!empty($filters['posted_since'])) {
            $days = (int) $filters['posted_since'];
            $query->where('created_at', '>=', now()->subDays($days));
        }

        if (!empty($filters['expire_after'])) {
            $date = Carbon::parse($filters['expire_after']);
            $query->where('expire_date', '>=', $date);
        }

        if (!empty($filters['created_from'])) {
            $date = Carbon::parse($filters['created_from']);
            $query->where('created_at', '>=', $date);
        }

        if (!empty($filters['created_to'])) {
            $date = Carbon::parse($filters['created_to']);
            $query->where('created_at', '<=', $date);
        }
    }

    /**
     * Apply sorting to query
     */
    private function applySorting(Builder $query, string $sort): void
    {
        switch ($sort) {
            case 'latest':
                $query->orderBy('created_at', 'desc');
                break;

            case 'oldest':
                $query->orderBy('created_at', 'asc');
                break;

            case 'salary_high':
                $query->orderBy('salary_to', 'desc')->orderBy('salary_from', 'desc');
                break;

            case 'salary_low':
                $query->orderBy('salary_from', 'asc')->orderBy('salary_to', 'asc');
                break;

            case 'title_asc':
                $query->orderBy('title', 'asc');
                break;

            case 'title_desc':
                $query->orderBy('title', 'desc');
                break;

            case 'company':
                $query->join('companies', 'jobs.company_id', '=', 'companies.id')
                      ->orderBy('companies.name', 'asc')
                      ->select('jobs.*');
                break;

            case 'expiring':
                $query->orderBy('expire_date', 'asc');
                break;

            case 'featured':
                $query->orderBy('is_featured', 'desc')->orderBy('created_at', 'desc');
                break;

            case 'relevance':
            default:
                $query->orderBy('is_featured', 'desc')
                      ->orderBy('created_at', 'desc');
                break;
        }
    }

    /**
     * Increment job view count
     */
    public function incrementViews(int $jobId): bool
    {
        try {
            $updated = $this->model->where('id', $jobId)
                ->increment('views_count', 1, ['last_viewed_at' => now()]);

            // Clear related caches
            $this->clearJobCaches($jobId);

            return $updated > 0;
        } catch (\Exception $e) {
            $this->logError('Failed to increment job views', [
                'job_id' => $jobId,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Mark job as featured
     */
    public function markAsFeatured(int $jobId, ?Carbon $featuredUntil = null): bool
    {
        try {
            $data = [
                'is_featured' => true,
                'featured_at' => now(),
                'featured_until' => $featuredUntil ?? now()->addDays(30)
            ];

            $updated = $this->model->where('id', $jobId)->update($data);
            
            $this->clearJobCaches($jobId);
            
            return $updated > 0;
        } catch (\Exception $e) {
            $this->logError('Failed to mark job as featured', [
                'job_id' => $jobId,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Extend job expiry date
     */
    public function extendExpiry(int $jobId, int $days): bool
    {
        try {
            $job = $this->findById($jobId);
            if (!$job) {
                return false;
            }

            $newExpiryDate = Carbon::parse($job->expire_date)->addDays($days);
            
            $updated = $this->model->where('id', $jobId)
                ->update(['expire_date' => $newExpiryDate]);

            $this->clearJobCaches($jobId);
            
            return $updated > 0;
        } catch (\Exception $e) {
            $this->logError('Failed to extend job expiry', [
                'job_id' => $jobId,
                'days' => $days,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Get jobs by multiple IDs with caching
     */
    public function findJobsByIds(array $jobIds): Collection
    {
        if (empty($jobIds)) {
            return new Collection();
        }

        $cacheKey = 'jobs:multiple:' . md5(implode(',', $jobIds));

        return Cache::remember($cacheKey, self::CACHE_DURATION, function () use ($jobIds) {
            return $this->model->newQuery()
                ->with(['company', 'jobType', 'jobCategory', 'skills'])
                ->whereIn('id', $jobIds)
                ->where('is_active', true)
                ->get()
                ->keyBy('id');
        });
    }

    /**
     * Clear job-specific caches
     */
    private function clearJobCaches(int $jobId): void
    {
        $patterns = [
            "jobs:*",
            "jobs:job:{$jobId}",
            "jobs:similar:{$jobId}:*",
            "jobs:statistics",
            "jobs:popular:*",
            "jobs:featured:*",
        ];

        foreach ($patterns as $pattern) {
            Cache::forget($pattern);
        }
    }

    /**
     * Bulk update job status
     */
    public function bulkUpdateStatus(array $jobIds, bool $isActive): int
    {
        try {
            $updated = $this->model->whereIn('id', $jobIds)
                ->update([
                    'is_active' => $isActive,
                    'updated_at' => now()
                ]);

            // Clear all job caches after bulk update
            Cache::tags(['jobs'])->flush();

            $this->logActivity('Bulk status update', [
                'job_ids' => $jobIds,
                'is_active' => $isActive,
                'updated_count' => $updated
            ]);

            return $updated;
        } catch (\Exception $e) {
            $this->logError('Failed bulk status update', [
                'job_ids' => $jobIds,
                'is_active' => $isActive,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }
}