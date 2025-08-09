<?php

namespace App\Actions;

use App\Models\Job;
use Illuminate\Support\Facades\Log;
use LumoSolutions\Actionable\Traits\IsDispatchable;
use LumoSolutions\Actionable\Traits\IsRunnable;

class GenerateJobStructuredData
{
    use IsDispatchable;
    use IsRunnable;

    public function handle(Job $job): void
    {
        $jsonLd = [
            '@context' => 'https://schema.org',
            '@type' => 'JobPosting',
            'title' => $job->job_title,
            'description' => strip_tags((string) $job->description),
            'datePosted' => optional($job->created_at)->toIso8601String(),
            'validThrough' => $job->expires_at?->toIso8601String() ?? null,
            'employmentType' => $job->jobType->name ?? null,
            'hiringOrganization' => [
                '@type' => 'Organization',
                'name' => $job->company->name ?? null,
            ],
            'jobLocation' => [
                '@type' => 'Place',
                'address' => [
                    '@type' => 'PostalAddress',
                    'addressLocality' => $job->city->name ?? $job->city ?? null,
                    'addressRegion' => $job->state->name ?? $job->state ?? null,
                    'addressCountry' => $job->country->name ?? $job->country ?? null,
                ],
            ],
        ];

        try {
            $job->settings()->set('seo.structured_data', $jsonLd);
        } catch (\Throwable $e) {
            Log::warning('Failed to store structured data', [
                'job_id' => $job->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
