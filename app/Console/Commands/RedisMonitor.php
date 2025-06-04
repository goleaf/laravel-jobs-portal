<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Redis;
use App\Services\RedisCacheService;

/**
 * Redis monitoring and statistics command
 */
class RedisMonitor extends Command
{
    protected $signature = 'redis:monitor {--interval=5 : Monitoring interval in seconds}';
    protected $description = 'Monitor Redis performance and statistics';

    private RedisCacheService $cacheService;

    public function __construct(RedisCacheService $cacheService)
    {
        parent::__construct();
        $this->cacheService = $cacheService;
    }

    public function handle(): int
    {
        $interval = (int) $this->option('interval');
        
        $this->info("🔍 Starting Redis monitoring (interval: {$interval}s)");
        $this->info("Press Ctrl+C to stop");
        $this->newLine();

        while (true) {
            $this->displayStats();
            sleep($interval);
            $this->newLine();
        }

        return 0;
    }

    private function displayStats(): void
    {
        $timestamp = now()->format('Y-m-d H:i:s');
        $this->info("📊 Redis Stats - {$timestamp}");
        $this->line(str_repeat('=', 50));

        $stats = $this->cacheService->getRedisStats();
        
        if ($stats['status'] === 'connected') {
            $this->table(
                ['Metric', 'Value'],
                [
                    ['Status', '🟢 Connected'],
                    ['Memory Used', $stats['memory_used']],
                    ['Total Keys', $stats['keys_total']],
                    ['Cache Hits', $stats['hits']],
                    ['Cache Misses', $stats['misses']],
                    ['Hit Ratio', $stats['hit_ratio']],
                ]
            );
        } else {
            $this->error("❌ Redis Status: {$stats['status']}");
            if (isset($stats['message'])) {
                $this->error("Error: {$stats['message']}");
            }
        }
    }
}