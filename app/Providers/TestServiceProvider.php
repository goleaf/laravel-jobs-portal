<?php

namespace App\Providers;

use Illuminate\Support\Facades\ParallelTesting;
use Illuminate\Support\ServiceProvider;

class TestServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        ParallelTesting::setUpProcess(function (int $token) {
            // Setup process-specific resources
        });

        ParallelTesting::setUpTestDatabase(function (string $database, int $token) {
            // Setup test database
        });

        ParallelTesting::tearDownProcess(function (int $token) {
            // Cleanup process resources
        });
    }
}