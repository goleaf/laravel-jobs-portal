<?php

namespace App\Actions;

use App\Models\Job;
use Illuminate\Support\Facades\Log;
use LumoSolutions\Actionable\Traits\IsRunnable;

class PublishJob
{
    use IsRunnable;

    public function handle(Job $job): Job
    {
        $job->forceFill([
            'status' => Job::STATUS_OPEN,
            'is_active' => true,
            'published_at' => now(),
        ])->save();

        try {
            GenerateJobStructuredData::dispatch($job);
            UpdateJobSearchIndex::dispatch($job);

            if ($job->settings('social.auto_post_linkedin', false)) {
                PostJobToLinkedIn::dispatch($job);
            }

            if ($job->settings('notifications.new_application', true)) {
                SendJobAlert::dispatch($job);
            }
        } catch (\Throwable $e) {
            Log::error('Post-publish tasks failed', [
                'job_id' => $job->id,
                'error' => $e->getMessage(),
            ]);
        }

        Log::info('Job published', [
            'job_id' => $job->id,
            'title' => $job->job_title,
        ]);

        return $job->fresh();
    }
}
