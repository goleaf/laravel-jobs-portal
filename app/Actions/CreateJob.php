<?php

namespace App\Actions;

use App\Dtos\JobData;
use App\Models\Job;
use App\Models\Company;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use LumoSolutions\Actionable\Concerns\IsRunnable;

class CreateJob
{
    use IsRunnable;

    /**
     * Create a new job with comprehensive setup
     */
    public function handle(JobData $jobData, int $userId = null): Job
    {
        return DB::transaction(function () use ($jobData, $userId) {
            // 1. Validate company exists and user has permission
            $company = Company::findOrFail($jobData->companyId);
            
            if ($userId && !$company->hasUser($userId)) {
                throw new \Exception('You do not have permission to post jobs for this company');
            }

            // 2. Validate job posting limits for company plan
            $this->validateJobPostingLimits($company);

            // 3. Create the job with optimized data
            $job = Job::create([
                'job_title' => $jobData->jobTitle,
                'description' => $jobData->description,
                'company_id' => $jobData->companyId,
                'job_category_id' => $jobData->jobCategoryId,
                'job_type_id' => $jobData->jobTypeId,
                'career_level_id' => $jobData->careerLevelId,
                'functional_area_id' => $jobData->functionalAreaId,
                'job_shift_id' => $jobData->jobShiftId,
                'degree_level_id' => $jobData->degreeLevelId,
                'country_id' => $jobData->countryId,
                'state_id' => $jobData->stateId,
                'city_id' => $jobData->cityId,
                'address' => $jobData->address,
                'is_remote' => $jobData->isRemote,
                'salary_from' => $jobData->salaryFrom,
                'salary_to' => $jobData->salaryTo,
                'currency_id' => $jobData->currencyId,
                'salary_period_id' => $jobData->salaryPeriodId,
                'hide_salary' => $jobData->hideSalary,
                'salary_negotiable' => $jobData->salaryNegotiable,
                'job_expiry_date' => $jobData->jobExpiryDate ?? now()->addDays(30),
                'no_of_positions' => $jobData->numberOfPositions,
                'years_experience_required' => $jobData->yearsExperienceRequired,
                'is_featured' => $jobData->isFeatured,
                'is_urgent' => $jobData->isUrgent,
                'is_freelance' => $jobData->isFreelance,
                'status' => $jobData->status,
                'is_active' => $jobData->isActive,
                'key_responsibilities' => $jobData->keyResponsibilities,
                'requirements' => $jobData->requirements,
                'benefits' => $jobData->benefits,
                'meta_title' => $jobData->metaTitle ?: $this->generateMetaTitle($jobData),
                'meta_description' => $jobData->metaDescription ?: $this->generateMetaDescription($jobData),
                'application_email' => $jobData->applicationEmail,
                'application_url' => $jobData->applicationUrl,
                'contact_person' => $jobData->contactPerson,
                'contact_email' => $jobData->contactEmail,
                'contact_phone' => $jobData->contactPhone,
                'slug' => $this->generateSlug($jobData->jobTitle, $company->name),
                'created_by' => $userId,
                'reference_id' => $this->generateReferenceId()
            ]);

            // 4. Set comprehensive job settings using Laravel Model Settings
            $job->settings([
                'visibility' => [
                    'public' => true,
                    'searchable' => true,
                    'featured' => $jobData->isFeatured,
                    'urgent' => $jobData->isUrgent
                ],
                'application' => [
                    'require_cover_letter' => $jobData->requireCoverLetter,
                    'max_applications' => 100,
                    'auto_accept' => false,
                    'send_confirmation_email' => true,
                    'screening_questions' => $jobData->screeningQuestions
                ],
                'notifications' => [
                    'new_application' => true,
                    'job_expiry_reminder' => true,
                    'weekly_summary' => true
                ],
                'workflow' => [
                    'auto_publish' => $jobData->autoPublish,
                    'require_approval' => !$company->isVerified(),
                    'auto_close_on_expiry' => true
                ],
                'seo' => [
                    'robots_index' => true,
                    'robots_follow' => true,
                    'structured_data_enabled' => true
                ],
                'analytics' => [
                    'track_views' => true,
                    'track_applications' => true,
                    'track_shares' => true
                ]
            ]);

            // 5. Attach skills to the job
            if (!empty($jobData->skillIds)) {
                $job->jobsSkill()->attach($jobData->skillIds);
            }

            // 6. Attach tags to the job
            if (!empty($jobData->tags)) {
                $this->attachTags($job, $jobData->tags);
            }

            // 7. Auto-publish if configured
            if ($jobData->autoPublish && $jobData->status === 'draft') {
                PublishJob::run($job);
            }

            // 8. Create job posting analytics record
            $this->createJobAnalytics($job);

            // 9. Update company statistics
            $company->increment('jobs_posted');
            $company->touch('last_job_posted_at');

            // 10. Dispatch background tasks
            $this->dispatchBackgroundTasks($job, $jobData);

            // 11. Log job creation activity
            activity('job_management')
                ->performedOn($job)
                ->causedBy($userId)
                ->withProperties([
                    'job_title' => $job->job_title,
                    'company_name' => $company->name,
                    'status' => $job->status,
                    'is_featured' => $job->is_featured
                ])
                ->log('job_created');

            Log::info('Job created successfully', [
                'job_id' => $job->id,
                'company_id' => $company->id,
                'title' => $job->job_title,
                'status' => $job->status
            ]);

            return $job->fresh(['company', 'jobCategory', 'jobType', 'jobsSkill']);
        });
    }

    /**
     * Validate job posting limits based on company plan
     */
    private function validateJobPostingLimits(Company $company): void
    {
        $plan = $company->activePlan;
        
        if (!$plan) {
            throw new \Exception('Company must have an active plan to post jobs');
        }

        $currentActiveJobs = $company->jobs()->active()->count();
        
        if ($currentActiveJobs >= $plan->job_limit) {
            throw new \Exception("Job posting limit reached. Current plan allows {$plan->job_limit} active jobs.");
        }
    }

    /**
     * Generate SEO-friendly meta title
     */
    private function generateMetaTitle(JobData $jobData): string
    {
        return Str::limit("{$jobData->jobTitle} - Job Opening", 60);
    }

    /**
     * Generate SEO-friendly meta description
     */
    private function generateMetaDescription(JobData $jobData): string
    {
        $description = strip_tags($jobData->description);
        return Str::limit("Apply for {$jobData->jobTitle}. {$description}", 160);
    }

    /**
     * Generate unique job slug
     */
    private function generateSlug(string $jobTitle, string $companyName): string
    {
        $baseSlug = Str::slug("{$jobTitle} at {$companyName}");
        $slug = $baseSlug;
        $counter = 1;

        while (Job::where('slug', $slug)->exists()) {
            $slug = "{$baseSlug}-{$counter}";
            $counter++;
        }

        return $slug;
    }

    /**
     * Generate unique reference ID
     */
    private function generateReferenceId(): string
    {
        return 'JOB-' . now()->format('Ymd') . '-' . strtoupper(Str::random(6));
    }

    /**
     * Attach tags to job
     */
    private function attachTags(Job $job, array $tags): void
    {
        $tagIds = [];
        
        foreach ($tags as $tagName) {
            $tag = \App\Models\Tag::firstOrCreate(['name' => $tagName]);
            $tagIds[] = $tag->id;
        }
        
        $job->jobsTag()->attach($tagIds);
    }

    /**
     * Create job analytics record
     */
    private function createJobAnalytics(Job $job): void
    {
        \App\Models\JobAnalytics::create([
            'job_id' => $job->id,
            'views_count' => 0,
            'applications_count' => 0,
            'shares_count' => 0,
            'conversion_rate' => 0,
            'created_at' => now()
        ]);
    }

    /**
     * Dispatch background tasks
     */
    private function dispatchBackgroundTasks(Job $job, JobData $jobData): void
    {
        // Send notifications to relevant candidates
        if ($job->status === 'published') {
            SendJobAlert::dispatch($job);
        }

        // Generate structured data for SEO
        GenerateJobStructuredData::dispatch($job);

        // Update search index
        UpdateJobSearchIndex::dispatch($job);

        // Generate social media posts if enabled
        if ($job->settings('social.auto_post_linkedin', false)) {
            PostJobToLinkedIn::dispatch($job);
        }

        // Send notification to admin for featured jobs
        if ($job->is_featured) {
            AdminNotification::dispatch('featured_job_created', [
                'job_id' => $job->id,
                'job_title' => $job->job_title,
                'company' => $job->company->name
            ]);
        }
    }
}
