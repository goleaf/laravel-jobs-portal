<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Support\TestHelpers;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
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
        // Disable foreign key constraints during seeding
        DB::statement('PRAGMA foreign_keys=OFF');
        
        try {
            // Seed essential data in proper order to avoid foreign key issues
            $this->seed([
                // Master data first
                CreateDefaultIndustriesSeeder::class,
                CreateDefaultCareerLevelSeeder::class,
                CreateDefaultDegreeLevelSeeder::class,
                CreateDefaultFunctionalAreaSeeder::class,
                JobCategorySeeder::class,
                CreateDefaultJobTypeSeeder::class,
                CreateDefaultJobShiftSeeder::class,
                CreateDefaultSalaryPeriodSeeder::class,
                SalaryCurrencySeeder::class,
                DefaultCompanySizeSeeder::class,
                CreateDefaultOwnerShipTypeSeeder::class,
                
                // Geographic data
                CountriesSeeder::class,
                StatesSeeder::class,
                CitiesSeeder::class,
                
                // Users and related data
                UsersSeeder::class,
            ]);
            
            // Create basic test data
            $this->createBasicTestData();
            
        } finally {
            // Re-enable foreign key constraints
            DB::statement('PRAGMA foreign_keys=ON');
        }
    }

    /**
     * Setup method that can be called by tests needing foreign key data
     */
    protected function setUp(): void
    {
        parent::setUp();
        
        // Optimize memory usage
        ini_set('memory_limit', '2G');
        
        // Set up testing environment efficiently
        $this->setTestingConfig();

        // Enhanced Pattern: Configure database for testing
        $this->configureDatabaseForTesting();

        // Only seed essential data if test needs database
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
                // Configure SQLite for testing
                DB::statement('PRAGMA journal_mode=WAL');
                DB::statement('PRAGMA synchronous=NORMAL');
                DB::statement('PRAGMA temp_store=MEMORY');
                DB::statement('PRAGMA mmap_size=268435456'); // 256MB
                
                // Temporarily disable foreign key constraints during migrations
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

    private function createBasicTestData(): void
    {
        try {
            // Create basic candidates for testing
            DB::table('candidates')->insertOrIgnore([
                [
                    'id' => 1,
                    'user_id' => 2, // John Doe from UsersSeeder
                    'unique_id' => 'CAND-001',
                    'career_level_id' => 1,
                    'industry_id' => 1,
                    'functional_area_id' => 1,
                    'current_salary' => 50000,
                    'expected_salary' => 60000,
                    'salary_currency' => 'USD',
                    'immediate_available' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'id' => 2,
                    'user_id' => 3, // Jane Smith from UsersSeeder
                    'unique_id' => 'CAND-002',
                    'career_level_id' => 1,
                    'industry_id' => 1,
                    'functional_area_id' => 1,
                    'current_salary' => 45000,
                    'expected_salary' => 55000,
                    'salary_currency' => 'USD',
                    'immediate_available' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'id' => 3,
                    'user_id' => 1, // Admin user as candidate for testing
                    'unique_id' => 'CAND-003',
                    'career_level_id' => 1,
                    'industry_id' => 1,
                    'functional_area_id' => 1,
                    'current_salary' => 70000,
                    'expected_salary' => 80000,
                    'salary_currency' => 'USD',
                    'immediate_available' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ]);

            // Create basic media entry for resume testing
            if (Schema::hasTable('media')) {
                DB::table('media')->insertOrIgnore([
                    'id' => 1,
                    'model_type' => 'App\\Models\\Candidate',
                    'model_id' => 1,
                    'uuid' => '12345678-1234-1234-1234-123456789012',
                    'collection_name' => 'resumes',
                    'name' => 'test-resume',
                    'file_name' => 'test-resume.pdf',
                    'mime_type' => 'application/pdf',
                    'disk' => 'public',
                    'size' => 1024000,
                    'manipulations' => '[]',
                    'custom_properties' => '[]',
                    'generated_conversions' => '[]',
                    'responsive_images' => '[]',
                    'order_column' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // Create basic companies for testing
            if (Schema::hasTable('companies')) {
                DB::table('companies')->insertOrIgnore([
                    [
                        'id' => 1,
                        'user_id' => 1,
                        'name' => 'Test Company Inc',
                        'email' => 'test@company.com',
                        'location' => 'Test City',
                        'website' => 'https://testcompany.com',
                        'industry_id' => 1,
                        'company_size_id' => 1,
                        'established_in' => 2020,
                        'details' => 'Test company for unit testing',
                        'is_featured' => 0,
                        'slug' => 'test-company-inc',
                        'unique_id' => 'COMP-001',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ],
                ]);
            }

            // Create basic jobs for testing
            if (Schema::hasTable('jobs')) {
                DB::table('jobs')->insertOrIgnore([
                    [
                        'id' => 1,
                        'job_title' => 'Test Job Position',
                        'company_id' => 1,
                        'job_category_id' => 1,
                        'job_type_id' => 1,
                        'career_level_id' => 1,
                        'functional_area_id' => 1,
                        'description' => 'Test job description',
                        'salary_from' => 40000,
                        'salary_to' => 60000,
                        'status' => 1,
                        'is_featured' => 0,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ],
                ]);
            }

        } catch (\Exception $e) {
            // Log but don't fail tests if basic data creation fails
            if (app()->environment('testing')) {
                // Only log in testing environment
                error_log("TestCase: Failed to create basic test data - " . $e->getMessage());
            }
        }
    }

    /**
     * Helper method to create test user with role
     */
    protected function createTestUser(string $role = 'candidate'): \App\Models\User
    {
        $user = \App\Models\User::factory()->create();
        $user->assignRole($role);
        return $user;
    }

    /**
     * Helper method to create test candidate
     */
    protected function createTestCandidate(): \App\Models\Candidate
    {
        return \App\Models\Candidate::factory()->create();
    }

    /**
     * Helper method to create test company
     */
    protected function createTestCompany(): \App\Models\Company
    {
        return \App\Models\Company::factory()->create();
    }

    /**
     * Helper method to create test job
     */
    protected function createTestJob(): \App\Models\Job
    {
        return \App\Models\Job::factory()->create();
    }
}