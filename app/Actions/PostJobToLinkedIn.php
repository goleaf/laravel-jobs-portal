<?php

namespace App\Actions;

use App\Models\Job;
use Illuminate\Support\Facades\Log;
use LumoSolutions\Actionable\Traits\IsDispatchable;
use LumoSolutions\Actionable\Traits\IsRunnable;

class PostJobToLinkedIn
{
    use IsDispatchable;
    use IsRunnable;

    public function handle(Job $job): void
    {
        // Placeholder for LinkedIn integration
        Log::info('Job posted to LinkedIn (placeholder)', [
            'job_id' => $job->id,
        ]);
    }
}
