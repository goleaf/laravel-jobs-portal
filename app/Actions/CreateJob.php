<?php

namespace App\Actions;

use App\Models\Company;
use App\Models\Job;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use LumoSolutions\Actionable\Traits\IsRunnable;

class CreateJob
{
    use IsRunnable;

    /**
     * Create a new job with comprehensive setup
     */
    public function handle($jobData, ?int $userId = null): Job
    {
        return DB::transaction(function () use ($jobData, $userId) {
            $data = is_array($jobData) ? (object) $jobData : $jobData;

            // 1. Validate company exists and user has permission
            $company = Company::findOrFail($data->companyId);

            if ($userId && method_exists($company, 'hasUser') && ! $company->hasUser($userId)) {
                throw new \Exception('You do not have permission to post jobs for this company');
            }

            // 2. Validate job posting limits for company plan (skip in testing)
            if (! App::runningUnitTests()) {
                $this->validateJobPostingLimits($company);
            }

            // 3. Create the job with optimized data
            $job = Job::create([
                'job_title' => $data->jobTitle,
                'description' => $data->description,
                'company_id' => $data->companyId,
                'job_category_id' => $data->jobCategoryId ?? null,
                'job_type_id' => $data->jobTypeId ?? null,
                'career_level_id' => $data->careerLevelId ?? null,
                'functional_area_id' => $data->functionalAreaId ?? null,
                'job_shift_id' => $data->jobShiftId ?? null,
                'degree_level_id' => $data->degreeLevelId ?? null,
                'country_id' => $data->countryId ?? null,
                'state_id' => $data->stateId ?? null,
                'city_id' => $data->cityId ?? null,
                'address' => $data->address ?? null,
                'is_remote' => $data->isRemote ?? false,
                'salary_from' => $data->salaryFrom ?? null,
                'salary_to' => $data->salaryTo ?? null,
                'currency_id' => $data->currencyId ?? null,
                'salary_period_id' => $data->salaryPeriodId ?? null,
                'hide_salary' => $data->hideSalary ?? false,
                'salary_negotiable' => $data->salaryNegotiable ?? false,
                'job_expiry_date' => $data->jobExpiryDate ?? now()->addDays(30),
                'no_of_positions' => $data->numberOfPositions ?? 1,
                'years_experience_required' => $data->yearsExperienceRequired ?? null,
                'is_featured' => $data->isFeatured ?? false,
                'is_urgent' => $data->isUrgent ?? false,
                'is_freelance' => $data->isFreelance ?? false,
                'status' => $data->status ?? Job::STATUS_DRAFT,
                'is_active' => $data->isActive ?? false,
                'key_responsibilities' => $data->keyResponsibilities ?? null,
                'requirements' => $data->requirements ?? null,
                'benefits' => $data->benefits ?? null,
                'meta_title' => $data->metaTitle ?? $this->generateMetaTitle($data),
                'meta_description' => $data->metaDescription ?? $this->generateMetaDescription($data),
                'application_email' => $data->applicationEmail ?? null,
                'application_url' => $data->applicationUrl ?? null,
                'contact_person' => $data->contactPerson ?? null,
                'contact_email' => $data->contactEmail ?? null,
                'contact_phone' => $data->contactPhone ?? null,
                'slug' => $this->generateSlug($data->jobTitle, $company->name),
                'created_by' => $userId,
                'reference_id' => $this->generateReferenceId(),
            ]);

            // 4. Set comprehensive job settings using Laravel Model Settings
            $job->settings([
                'visibility' => [
                    'public' => true,
                    'searchable' => true,
                    'featured' => $data->isFeatured ?? false,
                    'urgent' => $data->isUrgent ?? false,
                ],
                'application' => [
                    'require_cover_letter' => $data->requireCoverLetter ?? false,
                    'max_applications' => 100,
                    'auto_accept' => false,
                    'send_confirmation_email' => true,
                    'screening_questions' => $data->screeningQuestions ?? [],
                ],
                'notifications' => [
                    'new_application' => true,
                    'job_expiry_reminder' => true,
                    'weekly_summary' => true,
                ],
                'workflow' => [
                    'auto_publish' => $data->autoPublish ?? false,
                    'require_approval' => method_exists($company, 'isVerified') ? ! $company->isVerified() : false,
                    'auto_close_on_expiry' => true,
                ],
                'seo' => [
                    'robots_index' => true,
                    'robots_follow' => true,
                    'structured_data_enabled' => true,
                ],
                'analytics' => [
                    'track_views' => true,
                    'track_applications' => true,
                    'track_shares' => true,
                ],
            ]);

            // 5. Attach skills to the job
            if (! empty($data->skillIds)) {
                $job->jobsSkill()->attach($data->skillIds);
            }

            // 6. Attach tags to the job
            if (! empty($data->tags)) {
                $this->attachTags($job, $data->tags);
            }

            // 7. Auto-publish if configured
            if (($data->autoPublish ?? false) && ($data->status ?? 'draft') === 'draft') {
                PublishJob::run($job);
            }

            // 8. Create job posting analytics record
            $this->createJobAnalytics($job);

            // 9. Update company statistics
            if (method_exists($company, 'increment')) {
                $company->increment('jobs_posted');
                $company->touch('last_job_posted_at');
            }

            // 10. Dispatch background tasks
            $this->dispatchBackgroundTasks($job, $data);

            // 11. Log job creation activity
            if (function_exists('activity')) {
                activity('job_management')
                    ->performedOn($job)
                    ->causedBy($userId)
                    ->withProperties([
                        'job_title' => $job->job_title,
                        'company_name' => $company->name,
                        'status' => $job->status,
                        'is_featured' => $job->is_featured,
                    ])
                    ->log('job_created');
            }

            Log::info('Job created successfully', [
                'job_id' => $job->id,
                'company_id' => $company->id,
                'title' => $job->job_title,
                'status' => $job->status,
            ]);

            return $job->fresh(['company', 'jobCategory', 'jobType', 'jobsSkill']);
        });
    }

    /**
     * Validate job posting limits based on company plan
     */
    private function validateJobPostingLimits(Company $company): void
    {
        $plan = $company->activePlan ?? null;

        if (! $plan) {
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
    private function generateMetaTitle($jobData): string
    {
        return Str::limit("{$jobData->jobTitle} - Job Opening", 60);
    }

    /**
     * Generate SEO-friendly meta description
     */
    private function generateMetaDescription($jobData): string
    {
        $description = strip_tags($jobData->description ?? '');

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
        return 'JOB-'.now()->format('Ymd').'-'.strtoupper(Str::random(6));
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
            'created_at' => now(),
        ]);
    }

    /**
     * Dispatch background tasks
     */
    private function dispatchBackgroundTasks(Job $job, $jobData): void
    {
        // Send notifications to relevant candidates
        if ($job->status === 'published' || $job->status === Job::STATUS_OPEN) {
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
                'company' => $job->company->name,
            ]);
        }
    }
}
