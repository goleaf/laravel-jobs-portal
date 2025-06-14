<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\RateLimitingService;

class RateLimitStats extends Command
{
    protected $signature = "rate-limit:stats";
    protected $description = "Display rate limiting statistics";

    public function handle(): int
    {
        $service = app(RateLimitingService::class);
        $stats = $service->getStats();
        
        $this->info("🚦 Rate Limiting Statistics");
        $this->line(str_repeat("=", 30));
        
        $this->table(
            ["Metric", "Value"],
            [
                ["Status", $stats["status"]],
                ["Cache Driver", $stats["cache_driver"]],
                ["Message", $stats["message"]],
            ]
        );

        return 0;
    }
}