<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Helpers\TestHelpers;
use Illuminate\Support\Facades\DB;
use Database\Seeders\UsersSeeder;
use Database\Seeders\CountriesSeeder;
use Database\Seeders\StatesSeeder;
use Database\Seeders\CitiesSeeder;
use Database\Seeders\BasicJobsSeeder;
use Database\Seeders\SkillsSeeder;
use Database\Seeders\CreateDefaultIndustriesSeeder;
use Database\Seeders\CreateDefaultCareerLevelSeeder;
use Database\Seeders\CreateDefaultDegreeLevelSeeder;
use Database\Seeders\CreateDefaultFunctionalAreaSeeder;
use Database\Seeders\CreateDefaultJobTypeSeeder;
use Database\Seeders\CreateDefaultJobShiftSeeder;
use Database\Seeders\CreateDefaultSalaryPeriodSeeder;
use Database\Seeders\SalaryCurrencySeeder;
use Database\Seeders\DefaultCompanySizeSeeder;
use Database\Seeders\CreateDefaultOwnerShipTypeSeeder;
use Database\Seeders\JobCategorySeeder;
use Database\Seeders\CreateDefaultPostCategorySeeder;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication, RefreshDatabase;

    /**
     * Setup essential data for tests that require foreign key dependencies
     */
    protected function seedEssentialData(): void
    {
        // Seed essential data in dependency order
        $this->seed([
            // Geographic data
            CountriesSeeder::class,
            StatesSeeder::class,
            CitiesSeeder::class,
            
            // Master data
            SkillsSeeder::class,
            CreateDefaultIndustriesSeeder::class,
            CreateDefaultCareerLevelSeeder::class,
            CreateDefaultDegreeLevelSeeder::class,
            CreateDefaultFunctionalAreaSeeder::class,
            CreateDefaultJobTypeSeeder::class,
            CreateDefaultJobShiftSeeder::class,
            CreateDefaultSalaryPeriodSeeder::class,
            SalaryCurrencySeeder::class,
            DefaultCompanySizeSeeder::class,
            CreateDefaultOwnerShipTypeSeeder::class,
            JobCategorySeeder::class,
            CreateDefaultPostCategorySeeder::class,
            
            // Users and related data
            UsersSeeder::class,
            BasicJobsSeeder::class,
        ]);
    }

    /**
     * Setup method that can be called by tests needing foreign key data
     */
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

        // Only seed if the test uses RefreshDatabase and needs foreign key data
        if (in_array(RefreshDatabase::class, class_uses_recursive($this))) {
            $this->seedEssentialData();
        }
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