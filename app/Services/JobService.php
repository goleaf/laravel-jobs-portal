<?php

namespace App\Services;

use App\Models\Job;
use App\Models\Company;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class JobService
{
    /**
     * Get active jobs with caching.
     */
    public function getActiveJobs(int $perPage = 15): LengthAwarePaginator
    {
        return Cache::tags(['jobs'])->remember('jobs.active.page.' . request('page', 1), 3600, function () use ($perPage) {
            return Job::with(['company', 'jobCategory', 'jobType', 'currency', 'salaryPeriod'])
                     ->active()
                     ->latest()
                     ->paginate($perPage);
        });
    }

    /**
     * Get featured jobs with caching.
     */
    public function getFeaturedJobs(int $limit = 10): Collection
    {
        return Cache::tags(['jobs', 'featured'])->remember('jobs.featured', 3600, function () use ($limit) {
            return Job::with(['company', 'jobCategory', 'jobType'])
                     ->featured()
                     ->latest()
                     ->limit($limit)
                     ->get();
        });
    }

    /**
     * Search jobs with filters.
     */
    public function searchJobs(array $filters, int $perPage = 15): LengthAwarePaginator
    {
        $query = Job::with(['company', 'jobCategory', 'jobType', 'currency'])
                   ->active();

        // Apply filters
        if (!empty($filters['keyword'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('job_title', 'like', '%' . $filters['keyword'] . '%')
                  ->orWhere('description', 'like', '%' . $filters['keyword'] . '%');
            });
        }

        if (!empty($filters['category_id'])) {
            $query->byCategory($filters['category_id']);
        }

        if (!empty($filters['job_type_id'])) {
            $query->where('job_type_id', $filters['job_type_id']);
        }

        if (!empty($filters['country_id']) || !empty($filters['state_id']) || !empty($filters['city_id'])) {
            $query->byLocation(
                $filters['country_id'] ?? null,
                $filters['state_id'] ?? null,
                $filters['city_id'] ?? null
            );
        }

        if (!empty($filters['min_salary']) || !empty($filters['max_salary'])) {
            $query->bySalaryRange(
                $filters['min_salary'] ?? null,
                $filters['max_salary'] ?? null
            );
        }

        if (!empty($filters['skills'])) {
            $query->withSkills($filters['skills']);
        }

        return $query->latest()->paginate($perPage);
    }

    /**
     * Create a new job.
     */
    public function createJob(array $data): Job
    {
        return DB::transaction(function () use ($data) {
            $job = Job::create($data);

            // Attach skills if provided
            if (!empty($data['skills'])) {
                $job->jobsSkill()->attach($data['skills']);
            }

            // Attach tags if provided
            if (!empty($data['tags'])) {
                $job->jobsTag()->attach($data['tags']);
            }

            // Clear cache
            Cache::tags(['jobs'])->flush();

            Log::info('Job created successfully', [
                'job_id' => $job->id,
                'job_title' => $job->job_title,
                'company_id' => $job->company_id
            ]);

            return $job;
        });
    }

    /**
     * Update an existing job.
     */
    public function updateJob(Job $job, array $data): Job
    {
        return DB::transaction(function () use ($job, $data) {
            $job->update($data);

            // Update skills if provided
            if (array_key_exists('skills', $data)) {
                $job->jobsSkill()->sync($data['skills'] ?? []);
            }

            // Update tags if provided
            if (array_key_exists('tags', $data)) {
                $job->jobsTag()->sync($data['tags'] ?? []);
            }

            // Clear cache
            Cache::tags(['jobs', 'job-' . $job->id])->flush();

            Log::info('Job updated successfully', [
                'job_id' => $job->id,
                'changes' => $job->getChanges()
            ]);

            return $job->fresh();
        });
    }

    /**
     * Delete a job (soft delete).
     */
    public function deleteJob(Job $job): bool
    {
        return DB::transaction(function () use ($job) {
            $deleted = $job->delete();

            if ($deleted) {
                // Clear cache
                Cache::tags(['jobs', 'job-' . $job->id])->flush();

                Log::info('Job deleted successfully', [
                    'job_id' => $job->id,
                    'job_title' => $job->job_title
                ]);
            }

            return $deleted;
        });
    }

    /**
     * Get jobs by company.
     */
    public function getJobsByCompany(Company $company, int $perPage = 15): LengthAwarePaginator
    {
        return Job::with(['jobCategory', 'jobType', 'appliedJobs'])
                  ->byCompany($company->id)
                  ->latest()
                  ->paginate($perPage);
    }

    /**
     * Get job statistics.
     */
    public function getJobStatistics(): array
    {
        return Cache::remember('jobs.statistics', 3600, function () {
            return [
                'total_jobs' => Job::count(),
                'active_jobs' => Job::active()->count(),
                'featured_jobs' => Job::featured()->count(),
                'jobs_this_month' => Job::whereMonth('created_at', now()->month)->count(),
                'jobs_today' => Job::whereDate('created_at', today())->count(),
                'expired_jobs' => Job::where('job_expiry_date', '<', now())->count(),
                'draft_jobs' => Job::where('status', Job::STATUS_DRAFT)->count(),
            ];
        });
    }

    /**
     * Get popular jobs.
     */
    public function getPopularJobs(int $limit = 10): Collection
    {
        return Cache::tags(['jobs'])->remember('jobs.popular', 3600, function () use ($limit) {
            return Job::with(['company', 'jobCategory'])
                     ->popular()
                     ->active()
                     ->limit($limit)
                     ->get();
        });
    }

    /**
     * Get recent jobs.
     */
    public function getRecentJobs(int $limit = 10): Collection
    {
        return Cache::tags(['jobs'])->remember('jobs.recent', 1800, function () use ($limit) {
            return Job::with(['company', 'jobCategory', 'jobType'])
                     ->recent()
                     ->active()
                     ->latest()
                     ->limit($limit)
                     ->get();
        });
    }

    /**
     * Get similar jobs.
     */
    public function getSimilarJobs(Job $job, int $limit = 5): Collection
    {
        return Job::with(['company', 'jobCategory'])
                  ->where('id', '!=', $job->id)
                  ->where(function ($query) use ($job) {
                      $query->where('job_category_id', $job->job_category_id)
                            ->orWhere('job_type_id', $job->job_type_id)
                            ->orWhere('company_id', $job->company_id);
                  })
                  ->active()
                  ->limit($limit)
                  ->get();
    }

    /**
     * Mark job as featured.
     */
    public function markAsFeatured(Job $job, Carbon $expiryDate): bool
    {
        return DB::transaction(function () use ($job, $expiryDate) {
            $featuredRecord = $job->featured()->create([
                'featured_start_date' => now(),
                'featured_end_date' => $expiryDate,
                'is_active' => true,
            ]);

            Cache::tags(['jobs', 'featured', 'job-' . $job->id])->flush();

            Log::info('Job marked as featured', [
                'job_id' => $job->id,
                'expiry_date' => $expiryDate->toDateString()
            ]);

            return $featuredRecord !== null;
        });
    }

    /**
     * Expire old jobs.
     */
    public function expireOldJobs(): int
    {
        $expiredCount = Job::where('job_expiry_date', '<', now())
                          ->where('status', '!=', Job::STATUS_CLOSED)
                          ->update(['status' => Job::STATUS_CLOSED]);

        if ($expiredCount > 0) {
            Cache::tags(['jobs'])->flush();
            Log::info("Expired {$expiredCount} old jobs");
        }

        return $expiredCount;
    }

    /**
     * Get jobs expiring soon.
     */
    public function getJobsExpiringSoon(int $days = 7): Collection
    {
        return Job::with(['company'])
                  ->where('job_expiry_date', '<=', now()->addDays($days))
                  ->where('job_expiry_date', '>', now())
                  ->active()
                  ->get();
    }
} 