<?php

namespace App\HealthChecks;

use Spatie\Health\Checks\Check;
use Spatie\Health\Checks\Result;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use App\Models\Job;
use App\Models\JobApplication;
use Carbon\Carbon;

/**
 * Laravel Defibrillator Health Check
 * 
 * Integrates our custom defibrillator monitoring with Laravel Health package
 */
class JobPortalDefibrillatorCheck extends Check
{
    public function run(): Result
    {
        $result = Result::make();

        try {
            // Get cached health status from our defibrillator system
            $healthStatus = Cache::get('defibrillator:last_health_check');
            
            if (!$healthStatus) {
                return $result->failed('No defibrillator health data available');
            }

            $lastCheckTime = Carbon::parse($healthStatus['timestamp']);
            $minutesSinceCheck = $lastCheckTime->diffInMinutes(Carbon::now());

            // Check if health data is stale
            if ($minutesSinceCheck > 15) {
                return $result->failed("Health check data is stale ({$minutesSinceCheck} minutes old)");
            }

            // Check overall system status
            $systemStatus = $healthStatus['status'] ?? 'unknown';
            $issues = $healthStatus['issues'] ?? [];
            $warnings = $healthStatus['warnings'] ?? [];

            if ($systemStatus === 'critical' || !empty($issues)) {
                $message = 'System health critical';
                if (!empty($issues)) {
                    $message .= ': ' . implode(', ', array_slice($issues, 0, 2));
                }
                return $result->failed($message);
            }

            if (!empty($warnings)) {
                $message = 'System rhythm irregular';
                if (count($warnings) <= 2) {
                    $message .= ': ' . implode(', ', $warnings);
                } else {
                    $message .= ': ' . count($warnings) . ' warnings detected';
                }
                return $result->warning($message);
            }

            // Add system rhythm information
            $rhythm = $this->getSystemRhythm($healthStatus);
            $result->meta([
                'rhythm' => $rhythm,
                'last_check' => $lastCheckTime->format('Y-m-d H:i:s'),
                'checks_performed' => count($healthStatus['checks'] ?? []),
                'system_bpm' => $this->calculateSystemBPM($healthStatus),
            ]);

            return $result->ok("System heartbeat: {$rhythm}");

        } catch (\Exception $e) {
            return $result->failed("Defibrillator check failed: {$e->getMessage()}");
        }
    }

    /**
     * Get system rhythm status (inspired by defibrillator concept)
     */
    protected function getSystemRhythm(array $healthStatus): string
    {
        $status = $healthStatus['status'];
        $issues = count($healthStatus['issues'] ?? []);
        $warnings = count($healthStatus['warnings'] ?? []);

        if ($status === 'healthy' && $issues === 0 && $warnings === 0) {
            return 'normal sinus rhythm'; // Perfect health
        } elseif ($status === 'healthy' && $warnings > 0) {
            return 'sinus rhythm with irregularities'; // Minor issues
        } elseif ($status === 'critical') {
            return 'arrhythmia detected'; // System needs attention
        }

        return 'rhythm under evaluation';
    }

    /**
     * Calculate system BPM (requests per minute as heartbeat)
     */
    protected function calculateSystemBPM(array $healthStatus): int
    {
        if (isset($healthStatus['checks']['job_portal']['recent_applications'])) {
            // Use job applications as a proxy for system activity
            return min(max((int) $healthStatus['checks']['job_portal']['recent_applications'], 60), 120);
        }

        return 75; // Default healthy BPM
    }
}
