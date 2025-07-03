<?php

namespace App\Services;

use App\Models\Candidate;
use App\Models\Job;
use App\Models\JobApplication;
use App\Repositories\CompanyRepository;
use App\Repositories\JobRepository;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Universal Job Service - Enhanced Implementation.
 *
 * Business logic layer for job operations following Laravel best practices:
 * - Clean separation of concerns
 * - Repository pattern integration
 * - Caching strategies
 * - Transaction management
 * - Event handling
 * - Performance optimization
 */
class UniversalJobService
{
    protected JobRepository $jobRepository;
    protected CompanyRepository $companyRepository;

    public function __construct(
        JobRepository $jobRepository,
        CompanyRepository $companyRepository
    ) {
        $this->jobRepository = $jobRepository;
        $this->companyRepository = $companyRepository;
    }

    /**
     * Get active jobs with advanced filtering.
     */
    public function getActiveJobs(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->jobRepository->getActiveJobs($filters, $perPage);
    }

    /**
     * Search jobs with keyword and filters.
     */
    public function searchJobs(string $keyword = '', array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        // Log search activity for analytics
        if (! empty($keyword)) {
            Log::info('Universal Job Service: Job search performed', [
                'keyword' => $keyword,
                'filters' => $filters,
                'user_id' => Auth::id(),
                'ip' => request()->ip(),
            ]);
        }

        return $this->jobRepository->searchJobs($keyword, $filters, $perPage);
    }

    /**
     * Get featured jobs for homepage.
     */
    public function getFeaturedJobs(int $limit = 6): Collection
    {
        return $this->jobRepository->getFeaturedJobs($limit);
    }

    /**
     * Get recent jobs.
     */
    public function getRecentJobs(int $days = 7, int $limit = 10): Collection
    {
        return $this->jobRepository->getRecentJobs($days, $limit);
    }

    /**
     * Get job details with view tracking.
     */
    public function getJobDetails(int $jobId, bool $trackView = true): ?Job
    {
        $job = $this->jobRepository->find($jobId);

        if ($job && $trackView) {
            $this->trackJobView($job);
        }

        return $job;
    }

    /**
     * Create new job posting.
     */
    public function createJob(array $jobData, int $companyId): Job
    {
        return DB::transaction(function () use ($jobData, $companyId) {
            // Validate company ownership
            $company = $this->companyRepository->find($companyId);
            if (! $company || $company->user_id !== Auth::id()) {
                throw new \Exception('Unauthorized to create job for this company');
            }

            // Prepare job data
            $jobData['company_id'] = $companyId;
            $jobData['user_id'] = Auth::id();
            $jobData['status'] = Job::STATUS_OPEN;
            $jobData['is_suspended'] = false;

            // Generate job slug if not provided
            if (empty($jobData['job_slug'])) {
                $jobData['job_slug'] = $this->generateJobSlug($jobData['job_title'], $companyId);
            }

            // Set default expiry date if not provided
            if (empty($jobData['job_expiry_date'])) {
                $jobData['job_expiry_date'] = now()->addDays(30)->toDateString();
            }

            $job = $this->jobRepository->create($jobData);

            // Handle job skills if provided
            if (! empty($jobData['skills'])) {
                $this->attachJobSkills($job, $jobData['skills']);
            }

            // Handle job tags if provided
            if (! empty($jobData['tags'])) {
                $this->attachJobTags($job, $jobData['tags']);
            }

            Log::info('Universal Job Service: Job created successfully', [
                'job_id' => $job->id,
                'company_id' => $companyId,
                'user_id' => Auth::id(),
                'job_title' => $job->job_title,
            ]);

            return $job;
        });
    }

    /**
     * Update existing job.
     */
    public function updateJob(int $jobId, array $jobData): bool
    {
        return DB::transaction(function () use ($jobId, $jobData) {
            $job = $this->jobRepository->findOrFail($jobId);

            // Validate ownership
            if ($job->user_id !== Auth::id()) {
                throw new \Exception('Unauthorized to update this job');
            }

            // Update job slug if title changed
            if (! empty($jobData['job_title']) && $jobData['job_title'] !== $job->job_title) {
                $jobData['job_slug'] = $this->generateJobSlug($jobData['job_title'], $job->company_id);
            }

            $updated = $this->jobRepository->update($jobId, $jobData);

            // Handle skills update
            if (isset($jobData['skills'])) {
                $this->syncJobSkills($job, $jobData['skills']);
            }

            // Handle tags update
            if (isset($jobData['tags'])) {
                $this->syncJobTags($job, $jobData['tags']);
            }

            Log::info('Universal Job Service: Job updated successfully', [
                'job_id' => $jobId,
                'user_id' => Auth::id(),
                'updated_fields' => array_keys($jobData),
            ]);

            return $updated;
        });
    }

    /**
     * Delete job posting.
     */
    public function deleteJob(int $jobId): bool
    {
        return DB::transaction(function () use ($jobId) {
            $job = $this->jobRepository->findOrFail($jobId);

            // Validate ownership
            if ($job->user_id !== Auth::id()) {
                throw new \Exception('Unauthorized to delete this job');
            }

            // Check if job has applications
            if ($job->jobApplications()->count() > 0) {
                // Soft delete by changing status instead of hard delete
                $deleted = $this->jobRepository->update($jobId, [
                    'status' => Job::STATUS_CLOSED,
                    'is_suspended' => true,
                ]);
            } else {
                $deleted = $this->jobRepository->delete($jobId);
            }

            Log::info('Universal Job Service: Job deleted successfully', [
                'job_id' => $jobId,
                'user_id' => Auth::id(),
                'job_title' => $job->job_title,
            ]);

            return $deleted;
        });
    }

    /**
     * Apply for job.
     */
    public function applyForJob(int $jobId, array $applicationData): JobApplication
    {
        return DB::transaction(function () use ($jobId, $applicationData) {
            $job = $this->jobRepository->findOrFail($jobId);

            // Validate job is still open
            if ($job->status !== Job::STATUS_OPEN || $job->is_suspended) {
                throw new \Exception('This job is no longer accepting applications');
            }

            // Check if already applied
            $existingApplication = JobApplication::where('job_id', $jobId)
                ->where('candidate_id', Auth::id())
                ->first();

            if ($existingApplication) {
                throw new \Exception('You have already applied for this job');
            }

            // Create application
            $applicationData['job_id'] = $jobId;
            $applicationData['candidate_id'] = Auth::id();
            $applicationData['status'] = JobApplication::STATUS_PENDING;

            $application = JobApplication::create($applicationData);

            // Update job application count cache
            Cache::forget("job_applications_count_{$jobId}");

            Log::info('Universal Job Service: Job application submitted', [
                'job_id' => $jobId,
                'candidate_id' => Auth::id(),
                'application_id' => $application->id,
            ]);

            return $application;
        });
    }

    /**
     * Get jobs by company.
     */
    public function getJobsByCompany(int $companyId, array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->jobRepository->getJobsByCompany($companyId, $filters, $perPage);
    }

    /**
     * Get job statistics for dashboard.
     */
    public function getJobStatistics(): array
    {
        return $this->jobRepository->getJobStatistics();
    }

    /**
     * Mark jobs as expired.
     */
    public function markExpiredJobs(): int
    {
        $expiredCount = 0;

        $this->jobRepository->processInChunks(100, function ($jobs) use (&$expiredCount) {
            foreach ($jobs as $job) {
                if ($job->job_expiry_date < now()->toDateString() && $job->status === Job::STATUS_OPEN) {
                    $this->jobRepository->markAsExpired($job->id);
                    $expiredCount++;
                }
            }
        });

        if ($expiredCount > 0) {
            Log::info('Universal Job Service: Expired jobs marked', [
                'count' => $expiredCount,
                'date' => now()->toDateString(),
            ]);
        }

        return $expiredCount;
    }

    /**
     * Toggle job featured status.
     */
    public function toggleFeaturedStatus(int $jobId, bool $featured = true, ?Carbon $featuredUntil = null): bool
    {
        $job = $this->jobRepository->findOrFail($jobId);

        // Validate ownership
        if ($job->user_id !== Auth::id()) {
            throw new \Exception('Unauthorized to modify this job');
        }

        $updated = $this->jobRepository->toggleFeatured($jobId, $featured, $featuredUntil);

        Log::info('Universal Job Service: Job featured status updated', [
            'job_id' => $jobId,
            'featured' => $featured,
            'featured_until' => $featuredUntil?->toDateString(),
            'user_id' => Auth::id(),
        ]);

        return $updated;
    }

    /**
     * Get jobs for sitemap.
     */
    public function getJobsForSitemap(): Collection
    {
        return $this->jobRepository->getJobsForSitemap();
    }

    /**
     * Get recommended jobs for candidate.
     */
    public function getRecommendedJobs(int $candidateId, int $limit = 10): Collection
    {
        $cacheKey = "recommended_jobs_{$candidateId}_{$limit}";

        return Cache::remember($cacheKey, now()->addHours(6), function () use ($candidateId, $limit) {
            // Get candidate skills and preferences
            $candidate = Candidate::with(['candidateSkills', 'jobCategory'])->find($candidateId);

            if (! $candidate) {
                return collect();
            }

            $query = $this->jobRepository->newQuery()
                ->with(['company', 'jobCategory', 'jobType'])
                ->where('status', Job::STATUS_OPEN)
                ->where('is_suspended', false)
                ->where('job_expiry_date', '>=', now()->toDateString());

            // Match by job category
            if ($candidate->job_category_id) {
                $query->where('job_category_id', $candidate->job_category_id);
            }

            // Match by skills
            if ($candidate->candidateSkills->isNotEmpty()) {
                $skillIds = $candidate->candidateSkills->pluck('skill_id')->toArray();
                $query->whereHas('jobSkills', function ($skillQuery) use ($skillIds) {
                    $skillQuery->whereIn('skill_id', $skillIds);
                });
            }

            return $query->latest('created_at')->limit($limit)->get();
        });
    }

    /**
     * Track job view for analytics.
     */
    protected function trackJobView(Job $job): void
    {
        $cacheKey = "job_view_{$job->id}_".(Auth::id() ?? request()->ip());

        // Only track once per user/IP per day
        if (! Cache::has($cacheKey)) {
            // Increment view count
            DB::table('jobs')
                ->where('id', $job->id)
                ->increment('views_count');

            // Cache to prevent duplicate tracking
            Cache::put($cacheKey, true, now()->addDay());

            Log::debug('Universal Job Service: Job view tracked', [
                'job_id' => $job->id,
                'user_id' => Auth::id(),
                'ip' => request()->ip(),
            ]);
        }
    }

    /**
     * Generate unique job slug.
     */
    protected function generateJobSlug(string $title, int $companyId): string
    {
        $baseSlug = \Str::slug($title);
        $slug = $baseSlug;
        $counter = 1;

        while ($this->jobRepository->exists(['job_slug' => $slug, 'company_id' => $companyId])) {
            $slug = $baseSlug.'-'.$counter;
            $counter++;
        }

        return $slug;
    }

    /**
     * Attach skills to job.
     */
    protected function attachJobSkills(Job $job, array $skillIds): void
    {
        if (! empty($skillIds)) {
            $job->jobSkills()->attach($skillIds);
        }
    }

    /**
     * Sync job skills.
     */
    protected function syncJobSkills(Job $job, array $skillIds): void
    {
        $job->jobSkills()->sync($skillIds);
    }

    /**
     * Attach tags to job.
     */
    protected function attachJobTags(Job $job, array $tagIds): void
    {
        if (! empty($tagIds)) {
            $job->jobTags()->attach($tagIds);
        }
    }

    /**
     * Sync job tags.
     */
    protected function syncJobTags(Job $job, array $tagIds): void
    {
        $job->jobTags()->sync($tagIds);
    }
}
