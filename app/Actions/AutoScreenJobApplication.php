<?php

namespace App\Actions;

use App\Models\JobApplication;
use Illuminate\Support\Facades\Log;
use LumoSolutions\Actionable\Traits\IsDispatchable;
use LumoSolutions\Actionable\Traits\IsRunnable;

class AutoScreenJobApplication
{
    use IsDispatchable;
    use IsRunnable;

    public function handle(JobApplication $application): void
    {
        $result = [
            'passed' => true,
            'score' => 0.8,
            'screened_at' => now()->toISOString(),
        ];

        try {
            $application->settings()->set('screening.result', $result);
        } catch (\Throwable $e) {
            Log::warning('Failed to store screening result', [
                'application_id' => $application->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
