<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Job;
use Illuminate\Foundation\Testing\RefreshDatabase;

class JobManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_create_job(): void
    {
        $user = $this->createTestUser();

        $response = $this->actingAs($user)->post('/jobs', [
            'title' => 'Software Developer',
            'description' => 'We are looking for a skilled developer',
            'expires_on' => now()->addDays(30)->format('Y-m-d'),
        ]);

        $response->assertStatus(302); // Redirect after creation
        $this->assertDatabaseHas('jobs', [
            'title' => 'Software Developer',
            'user_id' => $user->id,
        ]);
    }

    public function test_guest_cannot_create_job(): void
    {
        $response = $this->post('/jobs', [
            'title' => 'Software Developer',
            'description' => 'We are looking for a skilled developer',
        ]);

        $response->assertRedirect('/login');
    }

    public function test_user_can_view_their_jobs(): void
    {
        $user = $this->createTestUser();
        $job = $this->createTestJob(['user_id' => $user->id]);

        $response = $this->actingAs($user)->get('/jobs');

        $response->assertStatus(200);
        $response->assertSee($job->title);
    }

    public function test_user_can_update_their_job(): void
    {
        $user = $this->createTestUser();
        $job = $this->createTestJob(['user_id' => $user->id]);

        $response = $this->actingAs($user)->put("/jobs/{$job->id}", [
            'title' => 'Updated Job Title',
            'description' => $job->description,
            'expires_on' => $job->expires_on->format('Y-m-d'),
        ]);

        $response->assertStatus(302);
        $this->assertDatabaseHas('jobs', [
            'id' => $job->id,
            'title' => 'Updated Job Title',
        ]);
    }

    public function test_user_can_delete_their_job(): void
    {
        $user = $this->createTestUser();
        $job = $this->createTestJob(['user_id' => $user->id]);

        $response = $this->actingAs($user)->delete("/jobs/{$job->id}");

        $response->assertStatus(302);
        $this->assertDatabaseMissing('jobs', ['id' => $job->id]);
    }
}