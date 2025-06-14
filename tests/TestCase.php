<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Support\TestHelpers;
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
        
        // Create basic candidates and media entry for testing
        $this->createBasicCandidates();
        $this->createBasicMediaEntry();
    }

    /**
     * Setup method that can be called by tests needing foreign key data
     */
    protected function setUp(): void
    {
        parent::setUp();
        
        // Optimize memory usage
        ini_set('memory_limit', '2G');
        
        // Create basic test data
        TestHelpers::createBasicTestData();
        
        // Set up testing environment efficiently
        $this->setTestingConfig();

        // Enhanced Pattern: Disable foreign key constraints for testing
        $this->configureDatabaseForTesting();

        // Only seed if the test uses RefreshDatabase and needs foreign key data
        if (in_array(RefreshDatabase::class, class_uses_recursive($this))) {
            $this->seedEssentialData();
        }
    }

    protected function tearDown(): void
    {
        // Enhanced Pattern: Re-enable foreign key constraints after testing
        $this->restoreDatabaseConstraints();
        
        // Force garbage collection
        if (function_exists('gc_collect_cycles')) {
            gc_collect_cycles();
        }
        
        parent::tearDown();
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
                // Force disable foreign key constraints for testing
                DB::statement('PRAGMA foreign_keys=OFF');
                // Verify it's disabled
                $result = DB::select('PRAGMA foreign_keys');
                if (!empty($result) && $result[0]->foreign_keys == 1) {
                    // If still enabled, try again
                    DB::statement('PRAGMA foreign_keys=OFF');
                }
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

    private function createBasicCandidates(): void
    {
        try {
            // Create basic candidates for testing
            DB::table('candidates')->insert([
                [
                    'id' => 1,
                    'user_id' => 2, // John Doe from UsersSeeder
                    'career_level_id' => 1,
                    'industry_id' => 1,
                    'functional_area_id' => 1,
                    'current_salary' => 50000,
                    'expected_salary' => 60000,
                    'salary_currency_id' => 1,
                    'salary_period_id' => 1,
                    'country_id' => 1,
                    'state_id' => 1,
                    'city_id' => 1,
                    'is_active' => 1,
                    'is_verified' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'id' => 2,
                    'user_id' => 3, // Jane Smith from UsersSeeder
                    'career_level_id' => 1,
                    'industry_id' => 1,
                    'functional_area_id' => 1,
                    'current_salary' => 45000,
                    'expected_salary' => 55000,
                    'salary_currency_id' => 1,
                    'salary_period_id' => 1,
                    'country_id' => 1,
                    'state_id' => 1,
                    'city_id' => 1,
                    'is_active' => 1,
                    'is_verified' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'id' => 3,
                    'user_id' => 1, // Admin user as candidate for testing
                    'career_level_id' => 1,
                    'industry_id' => 1,
                    'functional_area_id' => 1,
                    'current_salary' => 70000,
                    'expected_salary' => 80000,
                    'salary_currency_id' => 1,
                    'salary_period_id' => 1,
                    'country_id' => 1,
                    'state_id' => 1,
                    'city_id' => 1,
                    'is_active' => 1,
                    'is_verified' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ]);
        } catch (\Exception $e) {
            // Ignore if candidates table doesn't exist or entries already exist
        }
    }

    private function createBasicMediaEntry(): void
    {
        try {
            // Create a basic media entry for resume testing
            DB::table('media')->insert([
                'id' => 1,
                'model_type' => 'App\\Models\\Candidate',
                'model_id' => 1,
                'uuid' => '12345678-1234-1234-1234-123456789012',
                'collection_name' => 'resumes',
                'name' => 'test-resume',
                'file_name' => 'test-resume.pdf',
                'mime_type' => 'application/pdf',
                'disk' => 'public',
                'conversions_disk' => 'public',
                'size' => 1024,
                'manipulations' => '[]',
                'custom_properties' => '[]',
                'generated_conversions' => '[]',
                'responsive_images' => '[]',
                'order_column' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } catch (\Exception $e) {
            // Ignore if media table doesn't exist or entry already exists
        }
    }
}