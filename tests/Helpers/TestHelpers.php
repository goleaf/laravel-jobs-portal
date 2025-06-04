<?php

namespace Tests\Helpers;

use App\Models\User;
use App\Models\Job;
use App\Models\Company;
use Illuminate\Support\Str;

class TestHelpers
{
    public static function createUserWithUniqueEmail(array $attributes = []): User
    {
        return User::factory()->create(array_merge([
            'email' => 'test_' . Str::random(8) . '@example.com',
            'password' => bcrypt('password'),
            'email_verified_at' => now(),
        ], $attributes));
    }

    public static function createJobWithUser(array $jobAttributes = [], ?User $user = null): Job
    {
        if (!$user) {
            $user = self::createUserWithUniqueEmail();
        }

        return Job::factory()->create(array_merge([
            'user_id' => $user->id,
            'title' => 'Test Job',
            'description' => 'Test job description',
        ], $jobAttributes));
    }

    public static function createCompanyWithUser(array $companyAttributes = [], ?User $user = null): Company
    {
        if (!$user) {
            $user = self::createUserWithUniqueEmail();
        }

        return Company::factory()->create(array_merge([
            'user_id' => $user->id,
            'name' => 'Test Company',
            'email' => 'company@example.com',
        ], $companyAttributes));
    }

    public static function createTestEnvironment(int $jobCount = 3): array
    {
        $user = self::createUserWithUniqueEmail();
        $company = self::createCompanyWithUser([], $user);
        
        $jobs = [];
        for ($i = 0; $i < $jobCount; $i++) {
            $jobs[] = self::createJobWithUser([
                'company_id' => $company->id,
                'title' => 'Test Job ' . ($i + 1),
            ], $user);
        }

        return [$user, $jobs, $company];
    }

    public static function getApiAuthHeaders(User $user): array
    {
        $token = $user->createToken('test-token')->plainTextToken;
        
        return [
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ];
    }
}