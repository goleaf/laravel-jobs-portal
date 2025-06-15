<?php

namespace App\Console\Commands;

use App\Services\CacheService;
use Illuminate\Console\Command;

class WarmupCache extends Command
{
    protected $signature = 'cache:warmup';
    protected $description = 'Warm up application caches';

    public function handle()
    {
        $this->info('🔥 Warming up application caches...');

        CacheService::warmUp();

        $this->info('✅ Cache warmup complete!');

        return 0;
    }
}
