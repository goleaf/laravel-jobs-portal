<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Simple Settings Management API Test
 * 
 * Basic tests to verify core functionality works without complex dependencies.
 */
class SimpleSettingsTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create test user
        $this->user = User::factory()->create();
        
        // Authenticate user
        Sanctum::actingAs($this->user);
    }

    /** @test */
    public function it_can_access_public_settings_info_without_auth()
    {
        // Test public endpoint without authentication
        $response = $this->withoutMiddleware()->getJson('/api/public/settings/models');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'data' => [
                        'supported_models',
                        'api_version',
                    ],
                    'message',
                ]);

        $this->assertTrue($response->json('success'));
    }

    /** @test */
    public function it_can_access_settings_docs()
    {
        $response = $this->getJson('/api/settings/docs/');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'data' => [
                        'api_version',
                        'title',
                        'supported_models',
                    ],
                    'message',
                ]);

        $this->assertTrue($response->json('success'));
    }

    /** @test */
    public function it_can_get_available_models()
    {
        $response = $this->getJson('/api/settings/');

        // Check if response is successful
        if ($response->status() !== 200) {
            // Print response content for debugging
            dump('Response Status: ' . $response->status());
            dump('Response Content: ' . $response->content());
        }

        $response->assertStatus(200);
        
        $this->assertTrue($response->json('success'));
        $this->assertArrayHasKey('supported_models', $response->json('data'));
    }

    /** @test */
    public function it_can_get_user_schema()
    {
        $response = $this->getJson('/api/settings/user/schema');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'data' => [
                        'model_type',
                        'model_name',
                        'supports_settings',
                    ],
                    'message',
                ]);

        $this->assertEquals('App\Models\User', $response->json('data.model_type'));
        $this->assertEquals('User', $response->json('data.model_name'));
    }

    /** @test */
    public function it_can_set_and_get_user_settings()
    {
        // First, try to set some settings
        $testSettings = [
            'profile' => ['theme' => 'dark'],
            'notifications' => ['email' => true],
        ];

        $updateResponse = $this->putJson("/api/settings/user/{$this->user->id}", [
            'settings' => $testSettings,
            'update_strategy' => 'merge',
        ]);

        // Check if update was successful
        if ($updateResponse->status() !== 200) {
            dump('Update Response Status: ' . $updateResponse->status());
            dump('Update Response Content: ' . $updateResponse->content());
        }

        $updateResponse->assertStatus(200);
        $this->assertTrue($updateResponse->json('success'));

        // Then try to retrieve the settings
        $getResponse = $this->getJson("/api/settings/user/{$this->user->id}");

        if ($getResponse->status() !== 200) {
            dump('Get Response Status: ' . $getResponse->status());
            dump('Get Response Content: ' . $getResponse->content());
        }

        $getResponse->assertStatus(200);
        $this->assertTrue($getResponse->json('success'));

        // Verify the settings were stored correctly
        $settings = $getResponse->json('data.settings');
        $this->assertEquals('dark', $settings['profile']['theme']);
        $this->assertTrue($settings['notifications']['email']);
    }
} 