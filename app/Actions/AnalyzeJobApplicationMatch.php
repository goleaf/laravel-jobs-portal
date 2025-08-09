<?php

namespace App\Actions;

use App\Models\JobApplication;
use Illuminate\Support\Facades\Log;
use LumoSolutions\Actionable\Traits\IsDispatchable;
use LumoSolutions\Actionable\Traits\IsRunnable;

class AnalyzeJobApplicationMatch
{
    use IsDispatchable;
    use IsRunnable;

    public function handle(JobApplication $application): void
    {
        $score = 0.7; // placeholder match score
        $analysis = [
            'score' => $score,
            'factors' => [
                'skills' => 0.4,
                'experience' => 0.2,
                'location' => 0.1,
            ],
            'generated_at' => now()->toISOString(),
        ];

        try {
            $application->settings()->set('analysis.match', $analysis);
        } catch (\Throwable $e) {
            Log::warning('Failed to store application match analysis', [
                'application_id' => $application->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
