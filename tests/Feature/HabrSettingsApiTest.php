<?php

namespace Tests\Feature;

use App\Models\Setting;
use App\Models\User;
use App\Services\SettingsManager;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Laravel\Sanctum\Sanctum;
use Illuminate\Support\Facades\Cache;

/**
 * Habr-Based Settings API Test Suite
 * 
 * Tests the comprehensive settings management system based on Habr community best practices
 */
class HabrSettingsApiTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected User $admin;
    protected SettingsManager $settingsManager;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create admin user
        $this->admin = User::factory()->create([
            'email' => 'admin@test.com',
            'name' => 'Test Admin',
        ]);
        
        // Initialize settings manager
        $this->settingsManager = app(SettingsManager::class);
        
        // Seed some test settings
        $this->seedTestSettings();
    }

    private function seedTestSettings(): void
    {
        Setting::create([
            'key' => 'test_public_setting',
            'value' => 'public_value',
            'type' => 'string',
            'group' => 'test',
            'description' => 'A public test setting',
            'is_public' => true,
            'default_value' => 'default_public',
            'created_by' => $this->admin->id,
            'updated_by' => $this->admin->id,
        ]);

        Setting::create([
            'key' => 'test_private_setting',
            'value' => 'private_value',
            'type' => 'string',
            'group' => 'test',
            'description' => 'A private test setting',
            'is_public' => false,
            'default_value' => 'default_private',
            'created_by' => $this->admin->id,
            'updated_by' => $this->admin->id,
        ]);

        Setting::create([
            'key' => 'test_boolean_setting',
            'value' => '1',
            'type' => 'boolean',
            'group' => 'test',
            'description' => 'A boolean test setting',
            'is_public' => true,
            'default_value' => '0',
            'created_by' => $this->admin->id,
            'updated_by' => $this->admin->id,
        ]);

        Setting::create([
            'key' => 'test_integer_setting',
            'value' => '42',
            'type' => 'integer',
            'group' => 'test',
            'description' => 'An integer test setting',
            'is_public' => false,
            'default_value' => '0',
            'created_by' => $this->admin->id,
            'updated_by' => $this->admin->id,
        ]);
    }

    /** @test */
    public function it_can_get_public_settings_without_authentication()
    {
        $response = $this->getJson('/api/settings/public');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'settings',
                    'count',
                ],
                'message'
            ])
            ->assertJson([
                'success' => true,
                'data' => [
                    'settings' => [
                        'test_public_setting' => 'public_value',
                        'test_boolean_setting' => true, // Should be cast to boolean
                    ]
                ]
            ]);

        // Should not include private settings
        $response->assertJsonMissing([
            'test_private_setting' => 'private_value'
        ]);
    }

    /** @test */
    public function it_can_get_public_settings_by_group()
    {
        $response = $this->getJson('/api/settings/public/test');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'group',
                    'settings',
                    'count',
                ],
                'message'
            ])
            ->assertJson([
                'success' => true,
                'data' => [
                    'group' => 'test',
                    'settings' => [
                        'test_public_setting' => 'public_value',
                        'test_boolean_setting' => true,
                    ]
                ]
            ]);
    }

    /** @test */
    public function it_requires_authentication_for_protected_endpoints()
    {
        $response = $this->getJson('/api/settings');
        $response->assertStatus(401);

        $response = $this->getJson('/api/settings/test_setting');
        $response->assertStatus(401);

        $response = $this->putJson('/api/settings/test_setting', ['value' => 'new_value']);
        $response->assertStatus(401);
    }

    /** @test */
    public function it_can_get_all_settings_when_authenticated()
    {
        Sanctum::actingAs($this->admin);

        $response = $this->getJson('/api/settings');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'settings',
                    'schema',
                ],
                'message'
            ]);

        // Should include both public and private settings
        $responseData = $response->json();
        $this->assertTrue($responseData['success']);
        $this->assertArrayHasKey('settings', $responseData['data']);
        $this->assertArrayHasKey('schema', $responseData['data']);
    }

    /** @test */
    public function it_can_filter_settings_by_group()
    {
        Sanctum::actingAs($this->admin);

        $response = $this->getJson('/api/settings?group=test');

        $response->assertStatus(200);
        $responseData = $response->json();
        
        $this->assertTrue($responseData['success']);
        $this->assertArrayHasKey('settings', $responseData['data']);
    }

    /** @test */
    public function it_can_search_settings()
    {
        Sanctum::actingAs($this->admin);

        $response = $this->getJson('/api/settings?search=boolean');

        $response->assertStatus(200);
        $responseData = $response->json();
        
        $this->assertTrue($responseData['success']);
        $this->assertArrayHasKey('settings', $responseData['data']);
    }

    /** @test */
    public function it_can_get_specific_setting_with_metadata()
    {
        Sanctum::actingAs($this->admin);

        $response = $this->getJson('/api/settings/test_public_setting');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'setting' => [
                        'key',
                        'value',
                        'type',
                        'group',
                        'description',
                        'is_public',
                        'default_value',
                        'created_at',
                        'updated_at',
                    ]
                ],
                'message'
            ])
            ->assertJson([
                'success' => true,
                'data' => [
                    'setting' => [
                        'key' => 'test_public_setting',
                        'value' => 'public_value',
                        'type' => 'string',
                        'group' => 'test',
                        'description' => 'A public test setting',
                        'is_public' => true,
                        'default_value' => 'default_public',
                    ]
                ]
            ]);
    }

    /** @test */
    public function it_returns_404_for_non_existent_setting()
    {
        Sanctum::actingAs($this->admin);

        $response = $this->getJson('/api/settings/non_existent_setting');

        $response->assertStatus(404)
            ->assertJson([
                'success' => false,
                'message' => 'Setting not found',
                'error' => 'SETTING_NOT_FOUND'
            ]);
    }

    /** @test */
    public function it_can_update_existing_setting()
    {
        Sanctum::actingAs($this->admin);

        $response = $this->putJson('/api/settings/test_public_setting', [
            'value' => 'updated_value',
            'description' => 'Updated description',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'setting'
                ],
                'message'
            ])
            ->assertJson([
                'success' => true,
                'message' => 'Setting updated successfully'
            ]);

        // Verify the setting was actually updated
        $setting = Setting::where('key', 'test_public_setting')->first();
        $this->assertEquals('updated_value', $setting->value);
        $this->assertEquals('Updated description', $setting->description);
        $this->assertEquals($this->admin->id, $setting->updated_by);
    }

    /** @test */
    public function it_can_create_new_setting_via_update()
    {
        Sanctum::actingAs($this->admin);

        $response = $this->putJson('/api/settings/new_test_setting', [
            'value' => 'new_value',
            'type' => 'string',
            'group' => 'test',
            'description' => 'A new test setting',
            'is_public' => false,
            'default_value' => 'default_new',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Setting updated successfully'
            ]);

        // Verify the setting was created
        $setting = Setting::where('key', 'new_test_setting')->first();
        $this->assertNotNull($setting);
        $this->assertEquals('new_value', $setting->value);
        $this->assertEquals('test', $setting->group);
        $this->assertEquals($this->admin->id, $setting->created_by);
    }

    /** @test */
    public function it_validates_setting_values()
    {
        Sanctum::actingAs($this->admin);

        // Test missing value
        $response = $this->putJson('/api/settings/test_setting', []);
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['value']);

        // Test invalid type
        $response = $this->putJson('/api/settings/test_setting', [
            'value' => 'test',
            'type' => 'invalid_type',
        ]);
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['type']);
    }

    /** @test */
    public function it_can_bulk_update_settings()
    {
        Sanctum::actingAs($this->admin);

        $response = $this->postJson('/api/settings/bulk-update', [
            'settings' => [
                'test_public_setting' => 'bulk_updated_public',
                'test_private_setting' => 'bulk_updated_private',
                'test_boolean_setting' => false,
            ]
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'updated_count',
                    'total_count',
                ],
                'message'
            ])
            ->assertJson([
                'success' => true,
                'data' => [
                    'updated_count' => 3,
                    'total_count' => 3,
                ]
            ]);

        // Verify settings were updated
        $this->assertEquals('bulk_updated_public', $this->settingsManager->get('test_public_setting'));
        $this->assertEquals('bulk_updated_private', $this->settingsManager->get('test_private_setting'));
        $this->assertFalse($this->settingsManager->get('test_boolean_setting'));
    }

    /** @test */
    public function it_can_delete_setting()
    {
        Sanctum::actingAs($this->admin);

        $response = $this->deleteJson('/api/settings/test_public_setting');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Setting deleted successfully'
            ]);

        // Verify setting was deleted
        $this->assertNull(Setting::where('key', 'test_public_setting')->first());
    }

    /** @test */
    public function it_can_export_settings()
    {
        Sanctum::actingAs($this->admin);

        $response = $this->getJson('/api/settings/export/all');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'settings',
                    'group',
                    'count',
                    'exported_at',
                ],
                'message'
            ]);

        $responseData = $response->json();
        $this->assertTrue($responseData['success']);
        $this->assertGreaterThan(0, $responseData['data']['count']);
        $this->assertIsArray($responseData['data']['settings']);
    }

    /** @test */
    public function it_can_export_settings_by_group()
    {
        Sanctum::actingAs($this->admin);

        $response = $this->getJson('/api/settings/export/all?group=test');

        $response->assertStatus(200);
        $responseData = $response->json();
        
        $this->assertTrue($responseData['success']);
        $this->assertEquals('test', $responseData['data']['group']);
        $this->assertIsArray($responseData['data']['settings']);
    }

    /** @test */
    public function it_can_import_settings()
    {
        Sanctum::actingAs($this->admin);

        $importData = [
            'settings' => [
                [
                    'key' => 'imported_setting_1',
                    'value' => 'imported_value_1',
                    'type' => 'string',
                    'group' => 'imported',
                    'description' => 'First imported setting',
                    'is_public' => true,
                    'default_value' => 'default_1',
                ],
                [
                    'key' => 'imported_setting_2',
                    'value' => 'imported_value_2',
                    'type' => 'string',
                    'group' => 'imported',
                    'description' => 'Second imported setting',
                    'is_public' => false,
                    'default_value' => 'default_2',
                ]
            ]
        ];

        $response = $this->postJson('/api/settings/import', $importData);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'imported_count',
                    'total_count',
                ],
                'message'
            ])
            ->assertJson([
                'success' => true,
                'data' => [
                    'imported_count' => 2,
                    'total_count' => 2,
                ]
            ]);

        // Verify settings were imported
        $this->assertNotNull(Setting::where('key', 'imported_setting_1')->first());
        $this->assertNotNull(Setting::where('key', 'imported_setting_2')->first());
    }

    /** @test */
    public function it_can_reset_setting_to_default()
    {
        Sanctum::actingAs($this->admin);

        // First update the setting to a different value
        $this->putJson('/api/settings/test_public_setting', [
            'value' => 'changed_value'
        ]);

        // Reset to default
        $response = $this->postJson('/api/settings/test_public_setting/reset-default');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Setting reset to default value successfully'
            ]);

        // Verify setting was reset
        $setting = Setting::where('key', 'test_public_setting')->first();
        $this->assertEquals('default_public', $setting->value);
    }

    /** @test */
    public function it_can_clear_settings_cache()
    {
        Sanctum::actingAs($this->admin);

        // Add something to cache first
        Cache::put('settings.test_key', 'test_value', 3600);
        $this->assertTrue(Cache::has('settings.test_key'));

        $response = $this->postJson('/api/settings/cache/clear');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Settings cache cleared successfully'
            ]);
    }

    /** @test */
    public function it_can_access_api_documentation()
    {
        $response = $this->getJson('/api/settings/docs');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'title',
                'version',
                'description',
                'endpoints',
                'parameters',
                'authentication',
                'rate_limiting',
                'examples',
                'habr_integration',
            ]);

        $responseData = $response->json();
        $this->assertEquals('Habr-Based Settings API Documentation', $responseData['title']);
        $this->assertArrayHasKey('habr_integration', $responseData);
    }

    /** @test */
    public function settings_manager_service_works_correctly()
    {
        // Test basic get/set functionality
        $this->settingsManager->set('test_service_setting', 'service_value', [
            'type' => 'string',
            'group' => 'service_test',
        ]);

        $this->assertEquals('service_value', $this->settingsManager->get('test_service_setting'));

        // Test type casting
        $this->settingsManager->set('test_boolean_service', '1', [
            'type' => 'boolean',
        ]);

        $this->assertTrue($this->settingsManager->get('test_boolean_service'));

        // Test default values
        $this->assertEquals('default_value', $this->settingsManager->get('non_existent_setting', 'default_value'));

        // Test group functionality
        $groupSettings = $this->settingsManager->getGroup('test');
        $this->assertIsArray($groupSettings);
        $this->assertArrayHasKey('test_public_setting', $groupSettings);

        // Test exists
        $this->assertTrue($this->settingsManager->exists('test_public_setting'));
        $this->assertFalse($this->settingsManager->exists('non_existent_setting'));
    }

    /** @test */
    public function setting_model_handles_type_casting_correctly()
    {
        $setting = Setting::create([
            'key' => 'type_test_string',
            'value' => 'test_string',
            'type' => 'string',
            'group' => 'test',
        ]);
        $this->assertEquals('test_string', $setting->value);

        $setting = Setting::create([
            'key' => 'type_test_boolean',
            'value' => '1',
            'type' => 'boolean',
            'group' => 'test',
        ]);
        $this->assertTrue($setting->value);

        $setting = Setting::create([
            'key' => 'type_test_integer',
            'value' => '123',
            'type' => 'integer',
            'group' => 'test',
        ]);
        $this->assertEquals(123, $setting->value);

        $setting = Setting::create([
            'key' => 'type_test_float',
            'value' => '123.45',
            'type' => 'float',
            'group' => 'test',
        ]);
        $this->assertEquals(123.45, $setting->value);

        $setting = Setting::create([
            'key' => 'type_test_array',
            'value' => json_encode(['a', 'b', 'c']),
            'type' => 'array',
            'group' => 'test',
        ]);
        $this->assertEquals(['a', 'b', 'c'], $setting->value);
    }
}
