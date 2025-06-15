<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use App\Models\Job;
use App\Models\JobApplication;
use App\Models\User;
use App\Models\Company;
use Carbon\Carbon;

/**
 * Laravel Defibrillator-inspired System Health Monitor
 * 
 * Ensures your Laravel Job Portal keeps a normal rhythm by monitoring:
 * - Queue workers health
 * - Database performance
 * - Mail system status
 * - Critical job portal functions
 * - Resource utilization
 */
class SystemHealthCheck extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'defibrillator:check 
                           {--threshold=5 : Maximum acceptable minutes for tasks}
                           {--alert : Send alerts for critical issues}
                           {--repair : Attempt automatic repairs}
                           {--detailed : Show detailed output}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Monitor job portal health and ensure normal system rhythm (Laravel Defibrillator-inspired)';

    /**
     * Health check results
     */
    protected array $healthChecks = [];
    protected array $criticalIssues = [];
    protected array $warnings = [];

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🫀 Laravel Defibrillator - Job Portal Health Check');
        $this->info('Ensuring your application maintains a normal rhythm...');
        $this->newLine();

        $threshold = (int) $this->option('threshold');
        $detailed = $this->option('detailed');

        // Core Health Checks
        $this->checkDatabaseHealth($detailed);
        $this->checkQueueHealth($threshold, $detailed);
        $this->checkJobPortalHealth($detailed);
        $this->checkSystemResources($detailed);
        $this->checkScheduledTasks($threshold, $detailed);
        $this->checkMailSystem($detailed);
        $this->checkCacheSystem($detailed);
        $this->checkStorageHealth($detailed);

        // Display Results
        $this->displayHealthSummary();

        // Handle Repairs
        if ($this->option('repair') && !empty($this->criticalIssues)) {
            $this->attemptRepairs();
        }

        // Handle Alerts
        if ($this->option('alert') && !empty($this->criticalIssues)) {
            $this->sendHealthAlerts();
        }

        return empty($this->criticalIssues) ? 0 : 1;
    }

    /**
     * Check database health and performance
     */
    protected function checkDatabaseHealth(bool $verbose): void
    {
        $this->info('🗄️  Checking Database Health...');

        try {
            $start = microtime(true);
            
            // Test connection
            DB::connection()->getPdo();
            $connectionTime = round((microtime(true) - $start) * 1000, 2);
            
            // Check critical tables
            $jobCount = Job::count();
            $applicationCount = JobApplication::count();
            $userCount = User::count();
            $companyCount = Company::count();

            // Check for failed jobs
            $failedJobs = DB::table('failed_jobs')->count();
            
            // Performance check
            $slowQueryTime = 1000; // 1 second threshold
            if ($connectionTime > $slowQueryTime) {
                $this->warnings[] = "Database connection slow: {$connectionTime}ms";
            }

            if ($failedJobs > 0) {
                $this->criticalIssues[] = "Found {$failedJobs} failed jobs in queue";
            }

            $this->healthChecks['database'] = [
                'status' => 'healthy',
                'connection_time' => $connectionTime . 'ms',
                'jobs' => $jobCount,
                'applications' => $applicationCount,
                'users' => $userCount,
                'companies' => $companyCount,
                'failed_jobs' => $failedJobs,
            ];

            if ($verbose) {
                $this->line("   ✅ Connection time: {$connectionTime}ms");
                $this->line("   📊 Jobs: {$jobCount}, Applications: {$applicationCount}");
                $this->line("   👥 Users: {$userCount}, Companies: {$companyCount}");
                if ($failedJobs > 0) {
                    $this->error("   ❌ Failed jobs: {$failedJobs}");
                }
            }

        } catch (\Exception $e) {
            $this->criticalIssues[] = 'Database connection failed: ' . $e->getMessage();
            $this->healthChecks['database'] = ['status' => 'critical', 'error' => $e->getMessage()];
            $this->error('   ❌ Database connection failed');
        }
    }

    /**
     * Check queue workers and job processing health
     */
    protected function checkQueueHealth(int $threshold, bool $verbose): void
    {
        $this->info('⚡ Checking Queue Health...');

        try {
            // Check pending jobs
            $pendingJobs = DB::table('jobs')->count();
            
            // Check if jobs are being processed (look for recent completions)
            $recentProcessed = DB::table('job_batches')
                ->where('finished_at', '>', Carbon::now()->subMinutes($threshold))
                ->count();

            // Check for stuck jobs (jobs that have been pending too long)
            $stuckJobs = DB::table('jobs')
                ->where('created_at', '<', Carbon::now()->subMinutes($threshold * 2))
                ->count();

            if ($stuckJobs > 0) {
                $this->criticalIssues[] = "Found {$stuckJobs} stuck jobs (older than " . ($threshold * 2) . " minutes)";
            }

            if ($pendingJobs > 100) {
                $this->warnings[] = "High queue backlog: {$pendingJobs} pending jobs";
            }

            $this->healthChecks['queue'] = [
                'status' => $stuckJobs > 0 ? 'critical' : 'healthy',
                'pending_jobs' => $pendingJobs,
                'recent_processed' => $recentProcessed,
                'stuck_jobs' => $stuckJobs,
            ];

            if ($verbose) {
                $this->line("   📦 Pending jobs: {$pendingJobs}");
                $this->line("   ✅ Recently processed: {$recentProcessed}");
                if ($stuckJobs > 0) {
                    $this->error("   ❌ Stuck jobs: {$stuckJobs}");
                }
            }

        } catch (\Exception $e) {
            $this->criticalIssues[] = 'Queue system check failed: ' . $e->getMessage();
            $this->healthChecks['queue'] = ['status' => 'critical', 'error' => $e->getMessage()];
        }
    }

    /**
     * Check job portal specific functionality
     */
    protected function checkJobPortalHealth(bool $verbose): void
    {
        $this->info('💼 Checking Job Portal Health...');

        try {
            // Check for recent job applications
            $recentApplications = JobApplication::where('created_at', '>', Carbon::now()->subHours(24))->count();
            
            // Check for active jobs
            $activeJobs = Job::where('is_active', true)->count();
            
            // Check for expired jobs not marked as inactive
            $expiredActiveJobs = Job::where('is_active', true)
                ->where('deadline', '<', Carbon::now())
                ->count();

            // Check for companies without jobs
            $inactiveCompanies = Company::whereDoesntHave('jobs', function($query) {
                $query->where('is_active', true);
            })->count();

            if ($expiredActiveJobs > 0) {
                $this->warnings[] = "{$expiredActiveJobs} expired jobs still marked as active";
            }

            $this->healthChecks['job_portal'] = [
                'status' => 'healthy',
                'recent_applications' => $recentApplications,
                'active_jobs' => $activeJobs,
                'expired_active_jobs' => $expiredActiveJobs,
                'inactive_companies' => $inactiveCompanies,
            ];

            if ($verbose) {
                $this->line("   📝 Recent applications (24h): {$recentApplications}");
                $this->line("   🎯 Active jobs: {$activeJobs}");
                if ($expiredActiveJobs > 0) {
                    $this->warn("   ⚠️  Expired active jobs: {$expiredActiveJobs}");
                }
            }

        } catch (\Exception $e) {
            $this->criticalIssues[] = 'Job portal health check failed: ' . $e->getMessage();
            $this->healthChecks['job_portal'] = ['status' => 'critical', 'error' => $e->getMessage()];
        }
    }

    /**
     * Check system resources
     */
    protected function checkSystemResources(bool $verbose): void
    {
        $this->info('💾 Checking System Resources...');

        try {
            $memoryUsage = memory_get_usage(true);
            $memoryLimit = ini_get('memory_limit');
            $memoryLimitBytes = $this->convertToBytes($memoryLimit);
            
            $memoryUsagePercent = ($memoryUsage / $memoryLimitBytes) * 100;

            if ($memoryUsagePercent > 80) {
                $this->warnings[] = "High memory usage: " . round($memoryUsagePercent, 2) . "%";
            }

            $this->healthChecks['resources'] = [
                'status' => $memoryUsagePercent > 90 ? 'critical' : 'healthy',
                'memory_usage' => $this->formatBytes($memoryUsage),
                'memory_limit' => $memoryLimit,
                'memory_usage_percent' => round($memoryUsagePercent, 2) . '%',
            ];

            if ($verbose) {
                $this->line("   🧠 Memory usage: " . $this->formatBytes($memoryUsage) . " / {$memoryLimit} (" . round($memoryUsagePercent, 2) . "%)");
            }

        } catch (\Exception $e) {
            $this->warnings[] = 'Resource check failed: ' . $e->getMessage();
        }
    }

    /**
     * Check scheduled tasks execution
     */
    protected function checkScheduledTasks(int $threshold, bool $verbose): void
    {
        $this->info('⏰ Checking Scheduled Tasks...');

        try {
            // Check if schedule:run has been executed recently
            $lastScheduleRun = Cache::get('schedule:last_run');
            
            if ($lastScheduleRun) {
                $lastRunTime = Carbon::parse($lastScheduleRun);
                $minutesSinceLastRun = $lastRunTime->diffInMinutes(Carbon::now());
                
                if ($minutesSinceLastRun > $threshold) {
                    $this->criticalIssues[] = "Scheduled tasks haven't run in {$minutesSinceLastRun} minutes";
                }
            } else {
                $this->warnings[] = 'No scheduled task execution recorded';
            }

            // Update last run cache
            Cache::put('schedule:last_run', Carbon::now(), 3600);

            $this->healthChecks['scheduled_tasks'] = [
                'status' => isset($minutesSinceLastRun) && $minutesSinceLastRun > $threshold ? 'critical' : 'healthy',
                'last_run' => $lastScheduleRun ?? 'Never',
                'minutes_since_last_run' => $minutesSinceLastRun ?? 'Unknown',
            ];

            if ($verbose && isset($minutesSinceLastRun)) {
                $this->line("   ⏱️  Last run: {$minutesSinceLastRun} minutes ago");
            }

        } catch (\Exception $e) {
            $this->warnings[] = 'Scheduled tasks check failed: ' . $e->getMessage();
        }
    }

    /**
     * Check mail system health
     */
    protected function checkMailSystem(bool $verbose): void
    {
        $this->info('📧 Checking Mail System...');

        try {
            $mailDriver = config('mail.default');
            $mailHost = config("mail.mailers.{$mailDriver}.host");
            
            // Check for recent email jobs
            $recentMailJobs = DB::table('jobs')
                ->where('payload', 'like', '%mail%')
                ->where('created_at', '>', Carbon::now()->subHours(1))
                ->count();

            $this->healthChecks['mail'] = [
                'status' => 'healthy',
                'driver' => $mailDriver,
                'host' => $mailHost,
                'recent_mail_jobs' => $recentMailJobs,
            ];

            if ($verbose) {
                $this->line("   📬 Mail driver: {$mailDriver}");
                $this->line("   📨 Recent mail jobs (1h): {$recentMailJobs}");
            }

        } catch (\Exception $e) {
            $this->warnings[] = 'Mail system check failed: ' . $e->getMessage();
        }
    }

    /**
     * Check cache system
     */
    protected function checkCacheSystem(bool $verbose): void
    {
        $this->info('🗂️  Checking Cache System...');

        try {
            $cacheDriver = config('cache.default');
            
            // Test cache write/read
            $testKey = 'defibrillator:health:test';
            $testValue = Carbon::now()->timestamp;
            
            Cache::put($testKey, $testValue, 60);
            $retrieved = Cache::get($testKey);
            
            $cacheWorking = ($retrieved == $testValue);
            
            if (!$cacheWorking) {
                $this->criticalIssues[] = 'Cache system not working properly';
            }

            Cache::forget($testKey);

            $this->healthChecks['cache'] = [
                'status' => $cacheWorking ? 'healthy' : 'critical',
                'driver' => $cacheDriver,
                'working' => $cacheWorking,
            ];

            if ($verbose) {
                $this->line("   💾 Cache driver: {$cacheDriver}");
                $this->line("   ✅ Cache working: " . ($cacheWorking ? 'Yes' : 'No'));
            }

        } catch (\Exception $e) {
            $this->criticalIssues[] = 'Cache system failed: ' . $e->getMessage();
        }
    }

    /**
     * Check storage health
     */
    protected function checkStorageHealth(bool $verbose): void
    {
        $this->info('💿 Checking Storage Health...');

        try {
            $disk = Storage::disk();
            
            // Test storage write/read
            $testFile = 'health-check-' . Carbon::now()->timestamp . '.txt';
            $testContent = 'Laravel Defibrillator Health Check';
            
            $disk->put($testFile, $testContent);
            $retrieved = $disk->get($testFile);
            $disk->delete($testFile);
            
            $storageWorking = ($retrieved === $testContent);

            if (!$storageWorking) {
                $this->criticalIssues[] = 'Storage system not working properly';
            }

            $this->healthChecks['storage'] = [
                'status' => $storageWorking ? 'healthy' : 'critical',
                'working' => $storageWorking,
            ];

            if ($verbose) {
                $this->line("   💾 Storage working: " . ($storageWorking ? 'Yes' : 'No'));
            }

        } catch (\Exception $e) {
            $this->criticalIssues[] = 'Storage system failed: ' . $e->getMessage();
        }
    }

    /**
     * Display health summary
     */
    protected function displayHealthSummary(): void
    {
        $this->newLine();
        $this->info('📊 Health Summary:');

        $healthyCount = 0;
        $criticalCount = 0;

        foreach ($this->healthChecks as $system => $data) {
            $status = $data['status'] ?? 'unknown';
            $icon = $status === 'healthy' ? '✅' : ($status === 'critical' ? '❌' : '⚠️');
            
            $this->line("   {$icon} " . ucfirst(str_replace('_', ' ', $system)) . ": {$status}");
            
            if ($status === 'healthy') $healthyCount++;
            if ($status === 'critical') $criticalCount++;
        }

        $this->newLine();
        
        if (empty($this->criticalIssues) && empty($this->warnings)) {
            $this->info('🫀 System Heartbeat: NORMAL RHYTHM ✅');
            $this->info('Your job portal is healthy and running smoothly!');
        } else {
            if (!empty($this->criticalIssues)) {
                $this->error('💔 CRITICAL ISSUES DETECTED:');
                foreach ($this->criticalIssues as $issue) {
                    $this->error("   • {$issue}");
                }
            }

            if (!empty($this->warnings)) {
                $this->warn('⚠️  WARNINGS:');
                foreach ($this->warnings as $warning) {
                    $this->warn("   • {$warning}");
                }
            }
        }

        // Store health status in cache for monitoring
        Cache::put('defibrillator:last_health_check', [
            'timestamp' => Carbon::now(),
            'status' => empty($this->criticalIssues) ? 'healthy' : 'critical',
            'checks' => $this->healthChecks,
            'issues' => $this->criticalIssues,
            'warnings' => $this->warnings,
        ], 3600);
    }

    /**
     * Attempt automatic repairs
     */
    protected function attemptRepairs(): void
    {
        $this->newLine();
        $this->info('🔧 Attempting Automatic Repairs...');

        // Clear failed jobs if requested
        if (in_array('Found failed jobs in queue', array_map(fn($issue) => substr($issue, 0, 25), $this->criticalIssues))) {
            DB::table('failed_jobs')->truncate();
            $this->info('   ✅ Cleared failed jobs table');
        }

        // Restart queue workers if stuck jobs detected
        if (!empty(array_filter($this->criticalIssues, fn($issue) => str_contains($issue, 'stuck jobs')))) {
            $this->info('   🔄 Restarting queue workers...');
            $this->call('queue:restart');
        }

        // Clean up expired jobs
        Job::where('is_active', true)
            ->where('deadline', '<', Carbon::now())
            ->update(['is_active' => false]);
        $this->info('   🧹 Cleaned up expired jobs');
    }

    /**
     * Send health alerts (placeholder for email/notification system)
     */
    protected function sendHealthAlerts(): void
    {
        $this->newLine();
        $this->warn('🚨 Health alerts would be sent to administrators');
        // Implement your notification system here
    }

    /**
     * Convert memory limit string to bytes
     */
    protected function convertToBytes(string $value): int
    {
        $value = trim($value);
        $last = strtolower($value[strlen($value) - 1]);
        $value = (int) $value;

        switch ($last) {
            case 'g': $value *= 1024;
            case 'm': $value *= 1024;
            case 'k': $value *= 1024;
        }

        return $value;
    }

    /**
     * Format bytes to human readable
     */
    protected function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }
}
