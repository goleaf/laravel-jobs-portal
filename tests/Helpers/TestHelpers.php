<?php

namespace Tests\Helpers;

use App\Models\User;
use App\Models\Job;
use App\Models\Company;
use App\Models\Candidate;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

class TestHelpers
{
    /**
     * Create a user with guaranteed unique email
     */
    public static function createUserWithUniqueEmail(array $attributes = []): User
    {
        $defaultAttributes = [
            "name" => "Test User",
            "email" => "test" . uniqid() . "@example.com",
            "password" => Hash::make("password"),
            "email_verified_at" => now(),
        ];

        return User::create(array_merge($defaultAttributes, $attributes));
    }

    /**
     * Create a job properly associated with a user
     */
    public static function createJobWithUser(array $jobAttributes = [], ?User $user = null): Job
    {
        if (!$user) {
            $user = self::createUserWithUniqueEmail();
        }

        $defaultJobAttributes = [
            "title" => "Test Job",
            "description" => "Test job description",
            "user_id" => $user->id,
            "company_id" => 1, // Assuming company exists
            "job_category_id" => 1,
            "job_type_id" => 1,
            "career_level_id" => 1,
            "functional_area_id" => 1,
            "salary_from" => 50000,
            "salary_to" => 80000,
            "salary_currency" => "USD",
            "salary_period_id" => 1,
            "country_id" => 1,
            "state_id" => 1,
            "city_id" => 1,
            "is_freelance" => false,
            "hide_salary" => false,
            "is_featured" => false,
            "status" => 1,
        ];

        return Job::create(array_merge($defaultJobAttributes, $jobAttributes));
    }

    /**
     * Create complete test environment
     */
    public static function createTestEnvironment(int $jobCount = 3): array
    {
        $user = self::createUserWithUniqueEmail([
            "name" => "Test Environment User",
        ]);

        $jobs = [];
        for ($i = 0; $i < $jobCount; $i++) {
            $jobs[] = self::createJobWithUser([
                "title" => "Test Job " . ($i + 1),
            ], $user);
        }

        return [$user, $jobs];
    }

    /**
     * Get API authentication headers
     */
    public static function getApiAuthHeaders(?User $user = null): array
    {
        if (!$user) {
            $user = self::createUserWithUniqueEmail();
        }

        // Create API token for user
        $token = $user->createToken("test-token")->plainTextToken;

        return [
            "Authorization" => "Bearer " . $token,
            "Accept" => "application/json",
            "Content-Type" => "application/json",
        ];
    }

    /**
     * Create basic required data for tests
     */
    public static function createBasicTestData(): void
    {
        // Create basic lookup data if not exists
        if (!\DB::table("job_categories")->exists()) {
            \DB::table("job_categories")->insert([
                "name" => "Technology",
                "created_at" => now(),
                "updated_at" => now(),
            ]);
        }

        if (!\DB::table("job_types")->exists()) {
            \DB::table("job_types")->insert([
                "name" => "Full Time",
                "created_at" => now(),
                "updated_at" => now(),
            ]);
        }

        if (!\DB::table("career_levels")->exists()) {
            \DB::table("career_levels")->insert([
                "level_name" => "Mid Level",
                "created_at" => now(),
                "updated_at" => now(),
            ]);
        }

        if (!\DB::table("functional_areas")->exists()) {
            \DB::table("functional_areas")->insert([
                "name" => "Software Development",
                "created_at" => now(),
                "updated_at" => now(),
            ]);
        }

        if (!\DB::table("salary_periods")->exists()) {
            \DB::table("salary_periods")->insert([
                "period" => "Monthly",
                "created_at" => now(),
                "updated_at" => now(),
            ]);
        }

        if (!\DB::table("countries")->exists()) {
            \DB::table("countries")->insert([
                "name" => "United States",
                "short_code" => "US",
                "created_at" => now(),
                "updated_at" => now(),
            ]);
        }

        if (!\DB::table("states")->exists()) {
            \DB::table("states")->insert([
                "name" => "California",
                "country_id" => 1,
                "created_at" => now(),
                "updated_at" => now(),
            ]);
        }

        if (!\DB::table("cities")->exists()) {
            \DB::table("cities")->insert([
                "name" => "San Francisco",
                "state_id" => 1,
                "created_at" => now(),
                "updated_at" => now(),
            ]);
        }

        if (!\DB::table("companies")->exists()) {
            \DB::table("companies")->insert([
                "name" => "Test Company",
                "email" => "test@company.com",
                "user_id" => 1,
                "created_at" => now(),
                "updated_at" => now(),
            ]);
        }
    }
}