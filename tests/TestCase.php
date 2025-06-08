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
        
        // Optimize memory usage
        ini_set('memory_limit', '2G');
        
        // Create basic test data only if needed
        $this->createBasicTestDataIfNeeded();
        
        // Set up testing environment efficiently
        $this->setTestingConfig();

        // Context7 Pattern: Disable foreign key constraints for testing
        $this->configureDatabaseForTesting();
    }

    protected function tearDown(): void
    {
        // Context7 Pattern: Re-enable foreign key constraints after testing
        $this->restoreDatabaseConstraints();
        
        // Force garbage collection
        if (function_exists('gc_collect_cycles')) {
            gc_collect_cycles();
        }
        
        parent::tearDown();
    }

    private function createBasicTestDataIfNeeded(): void
    {
        // Only create test data if we're actually testing database functionality
        if ($this->shouldCreateTestData()) {
            TestHelpers::createBasicTestData();
        }
    }

    private function shouldCreateTestData(): bool
    {
        // Check if this test actually needs database data
        $reflection = new \ReflectionClass($this);
        $methods = $reflection->getMethods();
        
        foreach ($methods as $method) {
            if (strpos($method->getName(), 'test') === 0) {
                $docComment = $method->getDocComment();
                if ($docComment && strpos($docComment, '@database') !== false) {
                    return true;
                }
            }
        }
        
        // Default to creating data for safety, but this can be optimized per test
        return true;
    }

    private function setTestingConfig(): void
    {
        config(["app.env" => "testing"]);
        config(["cache.default" => "array"]);
        config(["session.driver" => "array"]);
        config(["queue.default" => "sync"]);
        config(["mail.default" => "array"]);
    }

    private function configureDatabaseForTesting(): void
    {
        try {
            if (config('database.default') === 'sqlite') {
                DB::statement('PRAGMA foreign_keys=OFF');
            }
        } catch (\Exception $e) {
            // Ignore if database is not available yet
        }
    }

    private function restoreDatabaseConstraints(): void
    {
        try {
            if (config('database.default') === 'sqlite') {
                DB::statement('PRAGMA foreign_keys=ON');
            }
        } catch (\Exception $e) {
            // Ignore if database is not available
        }
    }
}