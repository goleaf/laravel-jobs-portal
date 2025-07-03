<?php

namespace Tests\Feature\Api;

use App\Actions\SettingsManagement\GetModelSettings;
use App\Actions\SettingsManagement\UpdateModelSettings;
use App\Data\SettingsManagement\ModelSettingsData;
use App\Data\SettingsManagement\SettingsUpdateData;
use App\Models\Candidate;
use App\Models\Company;
use App\Models\Job;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Cache;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Settings Management API Test Suite
 *
 * Comprehensive testing for the Settings Management API including:
 * - Actionable actions testing
 * - DTO transformations
 * - API endpoints
 * - Caching functionality
 * - Validation rules
 * - Error handling
 */
class SettingsManagementApiTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    protected User $user;
    protected Candidate $candidate;
    protected Company $company;
    protected Job $job;

    protected function setUp(): void
    {
        parent::setUp();

        // Create test models with settings support
        $this->user = User::factory()->create();
        $this->candidate = Candidate::factory()->create();
        $this->company = Company::factory()->create();
        $this->job = Job::factory()->create();

        // Authenticate user
        Sanctum::actingAs($this->user);
    }

    /**
     * Test Phase 3A: Settings Management API Actions
     */

    /** @test */
    public function it_can_retrieve_model_settings_using_actionable_action()
    {
        // Set some test settings
        $this->user->settings()->set('profile.theme', 'dark');
        $this->user->settings()->set('notifications.email', true);

        // Test the Actionable action directly
        $settingsData = ModelSettingsData::forRetrieval(
            modelType: User::class,
            modelId: $this->user->id
        );

        $result = GetModelSettings::run($settingsData);

        $this->assertArrayHasKey('settings', $result);
        $this->assertArrayHasKey('model_type', $result);
        $this->assertArrayHasKey('model_id', $result);
        $this->assertEquals(User::class, $result['model_type']);
        $this->assertEquals($this->user->id, $result['model_id']);
        $this->assertEquals('dark', $result['settings']['profile']['theme']);
        $this->assertTrue($result['settings']['notifications']['email']);
    }

    /** @test */
    public function it_can_update_model_settings_using_actionable_action()
    {
        $newSettings = [
            'profile' => ['theme' => 'light'],
            'notifications' => ['email' => false, 'sms' => true],
        ];

        $updateData = SettingsUpdateData::fromChanges(
            modelType: User::class,
            modelId: $this->user->id,
            currentSettings: [],
            newSettings: $newSettings,
            userId: $this->user->id,
            updateReason: 'Test update'
        );

        $result = UpdateModelSettings::run($updateData);

        $this->assertTrue($result['success']);
        $this->assertEquals(User::class, $result['model_type']);
        $this->assertEquals($this->user->id, $result['model_id']);
        $this->assertIsArray($result['updated_keys']);
        $this->assertStringContainsString('Test update', $result['update_summary']);

        // Verify settings were actually updated
        $this->assertEquals('light', $this->user->settings()->get('profile.theme'));
        $this->assertFalse($this->user->settings()->get('notifications.email'));
        $this->assertTrue($this->user->settings()->get('notifications.sms'));
    }

    /** @test */
    public function it_properly_tracks_changes_in_settings_update_data()
    {
        $currentSettings = [
            'profile' => ['theme' => 'dark'],
            'notifications' => ['email' => true],
        ];

        $newSettings = [
            'profile' => ['theme' => 'light'],
            'notifications' => ['email' => true, 'sms' => true],
        ];

        $updateData = SettingsUpdateData::fromChanges(
            modelType: User::class,
            modelId: $this->user->id,
            currentSettings: $currentSettings,
            newSettings: $newSettings,
            userId: $this->user->id
        );

        // Should detect theme change and new sms setting
        $this->assertContains('profile.theme', $updateData->changedKeys);
        $this->assertContains('notifications.sms', $updateData->changedKeys);

        // Should not include unchanged email setting
        $this->assertNotContains('notifications.email', $updateData->changedKeys);
    }

    /**
     * Test Phase 3A: API Endpoints
     */

    /** @test */
    public function it_can_get_available_models_via_api()
    {
        $response = $this->getJson('/api/v1/settings/');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'supported_models',
                    'total_count',
                ],
                'message',
            ]);

        $data = $response->json('data');
        $this->assertArrayHasKey('user', $data['supported_models']);
        $this->assertArrayHasKey('candidate', $data['supported_models']);
        $this->assertArrayHasKey('company', $data['supported_models']);
        $this->assertArrayHasKey('job', $data['supported_models']);
    }

    /** @test */
    public function it_can_get_settings_schema_for_model_type()
    {
        $response = $this->getJson('/api/v1/settings/user/schema');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'model_type',
                    'model_name',
                    'supports_settings',
                    'settings_categories',
                ],
                'message',
            ]);

        $data = $response->json('data');
        $this->assertEquals('App\Models\User', $data['model_type']);
        $this->assertEquals('User', $data['model_name']);
        $this->assertTrue($data['supports_settings']);
    }

    /** @test */
    public function it_can_get_model_settings_via_api()
    {
        // Set test settings
        $this->user->settings()->set('profile.theme', 'dark');
        $this->user->settings()->set('notifications.email', true);

        $response = $this->getJson("/api/v1/settings/user/{$this->user->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'model_type',
                    'model_id',
                    'settings',
                    'from_cache',
                    'performance',
                ],
                'message',
            ]);

        $data = $response->json('data');
        $this->assertEquals('App\Models\User', $data['model_type']);
        $this->assertEquals($this->user->id, $data['model_id']);
        $this->assertEquals('dark', $data['settings']['profile']['theme']);
    }

    /** @test */
    public function it_can_get_specific_settings_keys_via_api()
    {
        // Set multiple settings
        $this->user->settings()->set('profile.theme', 'dark');
        $this->user->settings()->set('profile.language', 'en');
        $this->user->settings()->set('notifications.email', true);
        $this->user->settings()->set('notifications.sms', false);

        // Request only specific keys
        $response = $this->getJson("/api/v1/settings/user/{$this->user->id}?keys=profile.theme,notifications.email");

        $response->assertStatus(200);

        $settings = $response->json('data.settings');
        $this->assertArrayHasKey('profile', $settings);
        $this->assertArrayHasKey('notifications', $settings);
        $this->assertEquals('dark', $settings['profile']['theme']);
        $this->assertTrue($settings['notifications']['email']);

        // Should not include unrequested keys
        $this->assertArrayNotHasKey('language', $settings['profile'] ?? []);
        $this->assertArrayNotHasKey('sms', $settings['notifications'] ?? []);
    }

    /** @test */
    public function it_can_update_model_settings_via_api()
    {
        $newSettings = [
            'profile' => ['theme' => 'light'],
            'notifications' => ['email' => false],
        ];

        $response = $this->putJson("/api/v1/settings/user/{$this->user->id}", [
            'settings' => $newSettings,
            'update_strategy' => 'merge',
            'update_reason' => 'API test update',
            'validation_enabled' => true,
            'backup_enabled' => true,
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'model_type',
                    'model_id',
                    'updated_keys',
                    'update_summary',
                    'backup_id',
                ],
                'message',
            ]);

        // Verify settings were updated
        $this->assertEquals('light', $this->user->fresh()->settings()->get('profile.theme'));
        $this->assertFalse($this->user->fresh()->settings()->get('notifications.email'));
    }

    /** @test */
    public function it_can_perform_bulk_updates_via_api()
    {
        // Create additional users
        $user2 = User::factory()->create();
        $user3 = User::factory()->create();

        $bulkSettings = [
            'notifications' => ['system_alerts' => true],
            'profile' => ['auto_save' => true],
        ];

        $response = $this->postJson('/api/v1/settings/user/bulk', [
            'model_ids' => [$this->user->id, $user2->id, $user3->id],
            'settings' => $bulkSettings,
            'update_strategy' => 'merge',
            'update_reason' => 'Bulk API test',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'successful_updates',
                    'failed_updates',
                    'summary' => [
                        'total',
                        'successful',
                        'failed',
                        'success_rate',
                    ],
                ],
                'message',
            ]);

        $data = $response->json('data');
        $this->assertEquals(3, $data['summary']['total']);
        $this->assertEquals(3, $data['summary']['successful']);
        $this->assertEquals(0, $data['summary']['failed']);
        $this->assertEquals(100.0, $data['summary']['success_rate']);

        // Verify all users were updated
        $this->assertTrue($this->user->fresh()->settings()->get('notifications.system_alerts'));
        $this->assertTrue($user2->fresh()->settings()->get('profile.auto_save'));
        $this->assertTrue($user3->fresh()->settings()->get('notifications.system_alerts'));
    }

    /**
     * Test Phase 3A: Caching Functionality
     */

    /** @test */
    public function it_caches_settings_retrieval_properly()
    {
        // Set test settings
        $this->user->settings()->set('profile.theme', 'dark');

        // First request should cache the result
        $response1 = $this->getJson("/api/v1/settings/user/{$this->user->id}?cache_duration=300");
        $response1->assertStatus(200);
        $this->assertFalse($response1->json('data.from_cache'));

        // Second request should come from cache
        $response2 = $this->getJson("/api/v1/settings/user/{$this->user->id}?cache_duration=300");
        $response2->assertStatus(200);
        $this->assertTrue($response2->json('data.from_cache'));

        // Verify cache key was used
        $this->assertTrue($response2->json('data.performance.cache_hit'));
    }

    /** @test */
    public function it_clears_cache_after_settings_update()
    {
        // Set initial settings and cache them
        $this->user->settings()->set('profile.theme', 'dark');
        $this->getJson("/api/v1/settings/user/{$this->user->id}?cache_duration=300");

        // Update settings (should clear cache)
        $this->putJson("/api/v1/settings/user/{$this->user->id}", [
            'settings' => ['profile' => ['theme' => 'light']],
        ]);

        // Next get should not come from cache
        $response = $this->getJson("/api/v1/settings/user/{$this->user->id}?cache_duration=300");
        $this->assertFalse($response->json('data.from_cache'));
        $this->assertEquals('light', $response->json('data.settings.profile.theme'));
    }

    /**
     * Test Phase 3A: Validation and Error Handling
     */

    /** @test */
    public function it_validates_required_fields_in_update_request()
    {
        $response = $this->putJson("/api/v1/settings/user/{$this->user->id}", [
            // Missing required 'settings' field
            'update_strategy' => 'merge',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['settings']);
    }

    /** @test */
    public function it_validates_update_strategy_values()
    {
        $response = $this->putJson("/api/v1/settings/user/{$this->user->id}", [
            'settings' => ['profile' => ['theme' => 'dark']],
            'update_strategy' => 'invalid_strategy',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['update_strategy']);
    }

    /** @test */
    public function it_handles_non_existent_model_gracefully()
    {
        $response = $this->getJson('/api/v1/settings/user/999999');

        $response->assertStatus(500)
            ->assertJson([
                'success' => false,
                'message' => function ($message) {
                    return str_contains($message, 'not found');
                },
            ]);
    }

    /** @test */
    public function it_validates_bulk_update_model_ids_limit()
    {
        $tooManyIds = range(1, 101); // Exceeds the 100 limit

        $response = $this->postJson('/api/v1/settings/user/bulk', [
            'model_ids' => $tooManyIds,
            'settings' => ['profile' => ['theme' => 'dark']],
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['model_ids']);
    }

    /**
     * Test Phase 3A: Different Model Types
     */

    /** @test */
    public function it_works_with_different_model_types()
    {
        $models = [
            ['type' => 'candidate', 'model' => $this->candidate],
            ['type' => 'company', 'model' => $this->company],
            ['type' => 'job', 'model' => $this->job],
        ];

        foreach ($models as $modelData) {
            // Set test settings
            $modelData['model']->settings()->set('test.value', 'test_data');

            // Test retrieval
            $response = $this->getJson("/api/v1/settings/{$modelData['type']}/{$modelData['model']->id}");
            $response->assertStatus(200);

            $settings = $response->json('data.settings');
            $this->assertEquals('test_data', $settings['test']['value']);

            // Test update
            $updateResponse = $this->putJson("/api/v1/settings/{$modelData['type']}/{$modelData['model']->id}", [
                'settings' => ['test' => ['value' => 'updated_data']],
            ]);
            $updateResponse->assertStatus(200);

            // Verify update
            $this->assertEquals('updated_data', $modelData['model']->fresh()->settings()->get('test.value'));
        }
    }

    /**
     * Test Phase 3A: Public API Endpoints
     */

    /** @test */
    public function it_provides_public_api_information_without_authentication()
    {
        // Test without authentication
        $this->withoutMiddleware();

        $response = $this->getJson('/api/v1/public/settings/models');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'supported_models',
                    'api_version',
                    'documentation_url',
                ],
                'message',
            ]);

        $data = $response->json('data');
        $this->assertArrayHasKey('user', $data['supported_models']);
        $this->assertEquals('1.0', $data['api_version']);
    }

    /** @test */
    public function it_provides_public_schema_information()
    {
        $this->withoutMiddleware();

        $response = $this->getJson('/api/v1/public/settings/user/schema/public');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'model_type',
                    'schema' => [
                        'categories',
                        'description',
                    ],
                    'api_endpoints',
                ],
                'message',
            ]);

        $data = $response->json('data');
        $this->assertEquals('user', $data['model_type']);
        $this->assertIsArray($data['schema']['categories']);
    }

    /**
     * Test Phase 3A: API Documentation Endpoints
     */

    /** @test */
    public function it_provides_api_documentation()
    {
        $response = $this->getJson('/api/v1/settings/docs/');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'api_version',
                    'title',
                    'description',
                    'base_url',
                    'authentication',
                    'rate_limits',
                    'endpoints',
                    'supported_models',
                ],
                'message',
            ]);

        $data = $response->json('data');
        $this->assertEquals('1.0', $data['api_version']);
        $this->assertEquals('Settings Management API', $data['title']);
        $this->assertIsArray($data['supported_models']);
    }

    /** @test */
    public function it_provides_api_examples()
    {
        $response = $this->getJson('/api/v1/settings/docs/examples');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'get_settings',
                    'update_settings',
                    'bulk_update',
                ],
                'message',
            ]);

        $data = $response->json('data');
        $this->assertArrayHasKey('url', $data['get_settings']);
        $this->assertArrayHasKey('body', $data['update_settings']);
        $this->assertArrayHasKey('response', $data['bulk_update']);
    }

    /**
     * Helper Methods
     */
    protected function tearDown(): void
    {
        // Clear cache between tests
        Cache::flush();
        parent::tearDown();
    }
}
