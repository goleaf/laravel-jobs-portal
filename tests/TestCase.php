<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Helpers\TestHelpers;
use Illuminate\Support\Facades\DB;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication, RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create basic test data
        TestHelpers::createBasicTestData();
        
        // Set up testing environment
        config(["app.env" => "testing"]);
        config(["cache.default" => "array"]);
        config(["session.driver" => "array"]);
        config(["queue.default" => "sync"]);

        // Context7 Pattern: Disable foreign key constraints for testing
        if (config('database.default') === 'sqlite') {
            DB::statement('PRAGMA foreign_keys=OFF');
        }
    }

    protected function tearDown(): void
    {
        // Context7 Pattern: Re-enable foreign key constraints after testing
        if (config('database.default') === 'sqlite') {
            DB::statement('PRAGMA foreign_keys=ON');
        }
        
        parent::tearDown();
    }
}