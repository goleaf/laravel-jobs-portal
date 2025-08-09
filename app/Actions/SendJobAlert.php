<?php

namespace App\Actions;

use App\Models\Job;
use Illuminate\Support\Facades\Log;
use LumoSolutions\Actionable\Traits\IsDispatchable;
use LumoSolutions\Actionable\Traits\IsRunnable;

class SendJobAlert
{
    use IsDispatchable;
    use IsRunnable;

    public function handle(Job $job): void
    {
        // Placeholder: integrate with subscriptions to notify interested candidates
        Log::info('Job alert queued', [
            'job_id' => $job->id,
            'title' => $job->job_title,
        ]);
    }
}
