<?php

namespace App\Actions;

use App\Dtos\JobApplicationData;
use App\Models\Candidate;
use App\Models\Job;
use App\Models\JobApplication;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use LumoSolutions\Actionable\Concerns\IsDispatchable;
use LumoSolutions\Actionable\Concerns\IsRunnable;

class ProcessJobApplication
{
    use IsDispatchable;
    use IsRunnable;

    /**
     * Process a job application with comprehensive business logic
     */
    public function handle(JobApplicationData $applicationData): JobApplication
    {
        return DB::transaction(function () use ($applicationData) {
            // 1. Validate job and candidate exist and are active
            $job = Job::findOrFail($applicationData->jobId);
            $candidate = Candidate::findOrFail($applicationData->candidateId);

            if (! $job->isActive() || $job->isExpired()) {
                throw new \Exception('Job is no longer accepting applications');
            }

            // 2. Check for duplicate applications
            $existingApplication = JobApplication::where('job_id', $applicationData->jobId)
                ->where('candidate_id', $applicationData->candidateId)
                ->first();

            if ($existingApplication && ! $existingApplication->trashed()) {
                throw new \Exception('You have already applied for this job');
            }

            // 3. Check if job has reached application limit
            $maxApplications = $job->settings('application.max_applications', 100);
            $currentApplications = $job->appliedJobs()->count();

            if ($currentApplications >= $maxApplications) {
                throw new \Exception('This job has reached its application limit');
            }

            // 4. Create the job application
            $application = JobApplication::create([
                'job_id' => $applicationData->jobId,
                'candidate_id' => $applicationData->candidateId,
                'status' => JobApplication::STATUS_PENDING,
                'cover_letter' => $applicationData->coverLetter,
                'resume_path' => $applicationData->resumePath,
                'expected_salary' => $applicationData->expectedSalary,
                'available_start_date' => $applicationData->availableStartDate,
                'screening_answers' => $applicationData->screeningAnswers,
                'notes' => $applicationData->notes,
                'applied_at' => now(),
                'application_source' => $applicationData->applicationSource ?? 'website',
                'metadata' => $applicationData->metadata,
            ]);

            // 5. Set application-specific settings
            $application->settings([
                'privacy.share_with_employer.contact_details' => $applicationData->shareContactDetails,
                'privacy.share_with_employer.expected_salary' => $applicationData->shareExpectedSalary,
                'workflow.auto_acknowledge' => $job->settings('application.send_confirmation_email', true),
                'tracking.application_source' => $applicationData->applicationSource ?? 'website',
            ]);

            // 6. Update job statistics
            $job->increment('application_count');
            $job->touch('last_application_at');

            // 7. Update candidate statistics
            $candidate->increment('applications_count');
            $candidate->touch('last_application_at');

            // 8. Log the application activity
            activity('job_application')
                ->performedOn($application)
                ->causedBy($candidate)
                ->withProperties([
                    'job_title' => $job->job_title,
                    'company_name' => $job->company->name,
                    'application_source' => $applicationData->applicationSource,
                ])
                ->log('applied_for_job');

            // 9. Dispatch notification actions (background processing)
            if ($job->settings('notifications.new_application', true)) {
                SendJobApplicationNotification::dispatch($application, 'employer');
            }

            if ($application->settings('workflow.auto_acknowledge', true)) {
                SendJobApplicationNotification::dispatch($application, 'candidate');
            }

            // 10. Dispatch skill matching and recommendations (background)
            AnalyzeJobApplicationMatch::dispatch($application);
            UpdateCandidateRecommendations::dispatch($candidate);

            // 11. Auto-screening if enabled
            if ($job->settings('workflow.screening_questions_enabled', false) &&
                ! empty($applicationData->screeningAnswers)) {
                AutoScreenJobApplication::dispatch($application);
            }

            // 12. Update job search rankings
            UpdateJobPopularityScore::dispatch($job);

            Log::info('Job application processed successfully', [
                'application_id' => $application->id,
                'job_id' => $job->id,
                'candidate_id' => $candidate->id,
                'source' => $applicationData->applicationSource,
            ]);

            return $application;
        });
    }

    /**
     * Handle failed job application processing
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('Job application processing failed', [
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString(),
        ]);

        // Send failure notification to admin
        AdminNotification::dispatch('job_application_processing_failed', [
            'error' => $exception->getMessage(),
            'timestamp' => now(),
        ]);
    }
}
