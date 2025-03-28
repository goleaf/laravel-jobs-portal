<?php

namespace Tests\Helpers;

use App\Models\Candidate;
use App\Models\Company;
use App\Models\Job;
use App\Models\Todo;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class TestHelpers
{
    /**
     * Create a user with a guaranteed unique email
     *
     * @param array $attributes
     * @return User
     */
    public static function createUserWithUniqueEmail(array $attributes = []): User
    {
        $uniqueEmail = 'test_' . time() . '_' . uniqid() . '@example.com';
        
        return User::factory()->create(array_merge([
            'email' => $uniqueEmail,
            'is_active' => true,
        ], $attributes));
    }

    /**
     * Create test todos for a user
     *
     * @param int $count
     * @param array $userAttributes
     * @return array
     */
    public static function createTodosForUser(int $count = 3, array $userAttributes = []): array
    {
        $user = self::createUserWithUniqueEmail($userAttributes);
        
        $todos = Todo::factory()->count($count)->create([
            'user_id' => $user->id
        ]);
        
        return [$user, $todos];
    }

    /**
     * Setup complete test environment with one line
     *
     * @param array $userAttributes
     * @return array
     */
    public static function createTestEnvironment(array $userAttributes = []): array
    {
        $user = self::createUserWithUniqueEmail($userAttributes);
        
        // Create some todos
        $todos = Todo::factory()->count(3)->create([
            'user_id' => $user->id
        ]);
        
        return [$user, $todos];
    }

    /**
     * Get API authentication headers
     *
     * @param User $user
     * @return array
     */
    public static function getApiAuthHeaders(User $user): array
    {
        $token = $user->createToken('test-token')->plainTextToken;
        
        return [
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ];
    }

    /**
     * Create a candidate user with profile
     *
     * @param array $userAttributes
     * @param array $candidateAttributes
     * @return array
     */
    public static function createCandidateWithProfile(array $userAttributes = [], array $candidateAttributes = []): array
    {
        $user = self::createUserWithUniqueEmail(array_merge([
            'user_type' => User::CANDIDATE,
        ], $userAttributes));
        
        $candidate = Candidate::factory()->create(array_merge([
            'user_id' => $user->id,
        ], $candidateAttributes));
        
        return [$user, $candidate];
    }

    /**
     * Create an employer user with company
     *
     * @param array $userAttributes
     * @param array $companyAttributes
     * @return array
     */
    public static function createEmployerWithCompany(array $userAttributes = [], array $companyAttributes = []): array
    {
        $user = self::createUserWithUniqueEmail(array_merge([
            'user_type' => User::EMPLOYER,
        ], $userAttributes));
        
        $company = Company::factory()->create(array_merge([
            'user_id' => $user->id,
            'is_active' => true,
        ], $companyAttributes));
        
        return [$user, $company];
    }

    /**
     * Create jobs for a company
     *
     * @param Company $company
     * @param int $count
     * @param array $jobAttributes
     * @return Collection
     */
    public static function createJobsForCompany(Company $company, int $count = 3, array $jobAttributes = []): Collection
    {
        return Job::factory()->count($count)->create(array_merge([
            'company_id' => $company->id,
            'status' => Job::STATUS_OPEN,
            'is_suspended' => false,
        ], $jobAttributes));
    }
} 