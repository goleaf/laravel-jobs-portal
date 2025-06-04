<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\CacheService;
use Illuminate\Support\Facades\DB;

class PerformanceReport extends Command
{
    protected $signature = 'app:performance-report';
    protected $description = 'Generate application performance report';
    
    public function handle()
    {
        $this->info('📊 Generating Performance Report...');
        
        // Cache statistics
        $cacheStats = CacheService::getStats();
        $this->table(['Metric', 'Value'], [
            ['Cache Keys', $cacheStats['total_keys'] ?? 'N/A'],
            ['Memory Usage', $cacheStats['memory_usage'] ?? 'N/A'],
            ['Cache Hits', $cacheStats['hit_rate'] ?? 'N/A'],
            ['Cache Misses', $cacheStats['miss_rate'] ?? 'N/A'],
        ]);
        
        // Database statistics
        $this->info("\n📊 Database Statistics:");
        try {
            $tables = ['users', 'jobs', 'companies', 'job_applications'];
            $dbStats = [];
            
            foreach ($tables as $table) {
                $count = DB::table($table)->count();
                $dbStats[] = [ucfirst($table), number_format($count)];
            }
            
            $this->table(['Table', 'Records'], $dbStats);
        } catch (\Exception $e) {
            $this->warn('Could not fetch database statistics');
        }
        
        $this->info('✅ Performance report complete!');
        
        return 0;
    }
}