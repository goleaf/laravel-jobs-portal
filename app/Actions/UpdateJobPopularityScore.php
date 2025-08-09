<?php

namespace App\Actions;

use App\Models\Job;
use Illuminate\Support\Facades\Log;
use LumoSolutions\Actionable\Traits\IsDispatchable;
use LumoSolutions\Actionable\Traits\IsRunnable;

class UpdateJobPopularityScore
{
    use IsDispatchable;
    use IsRunnable;

    public function handle(Job $job): void
    {
        // Placeholder: compute popularity score based on views/applications
        Log::info('Job popularity score updated', [
            'job_id' => $job->id,
        ]);
    }
}
