<?php

namespace App\Actions;

use App\Models\Job;
use Illuminate\Support\Facades\Log;
use LumoSolutions\Actionable\Traits\IsDispatchable;
use LumoSolutions\Actionable\Traits\IsRunnable;

class UpdateJobSearchIndex
{
    use IsDispatchable;
    use IsRunnable;

    public function handle(Job $job): void
    {
        // Placeholder for search indexing integration (e.g., Scout/Algolia/Elasticsearch)
        Log::info('Job search index updated', [
            'job_id' => $job->id,
        ]);
    }
}
