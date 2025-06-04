<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * Example Redis-backed job for performance testing
 */
class ProcessRedisJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 60;
    public $tries = 3;

    private string $data;

    public function __construct(string $data)
    {
        $this->data = $data;
        $this->onQueue('default'); // Use Redis queue
    }

    public function handle(): void
    {
        Log::info('Processing Redis job', [
            'data' => $this->data,
            'attempts' => $this->attempts(),
            'queue' => 'redis'
        ]);

        // Simulate processing
        sleep(2);

        Log::info('Redis job completed successfully');
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('Redis job failed', [
            'data' => $this->data,
            'error' => $exception->getMessage()
        ]);
    }
}