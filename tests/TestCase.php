<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Disable telescope for testing
        config(['telescope.enabled' => false]);
        
        // Set up basic configuration for testing
        config([
            'app.name' => 'Job Portal Test',
            'app.env' => 'testing',
            'database.default' => 'sqlite',
            'database.connections.sqlite.database' => ':memory:',
        ]);
    }

    protected function createTestUser(array $attributes = []): \App\Models\User
    {
        return \App\Models\User::factory()->create(array_merge([
            'email' => 'test_' . uniqid() . '@example.com',
            'password' => bcrypt('password'),
            'email_verified_at' => now(),
        ], $attributes));
    }

    protected function createTestJob(array $attributes = []): \App\Models\Job
    {
        $user = $this->createTestUser();
        
        return \App\Models\Job::factory()->create(array_merge([
            'user_id' => $user->id,
            'title' => 'Test Job',
            'description' => 'Test job description',
            'expires_on' => now()->addDays(30),
        ], $attributes));
    }
}