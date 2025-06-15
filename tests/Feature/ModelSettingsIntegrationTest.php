<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Company;
use App\Models\UserSettings;

/**
 * ModelSettingsIntegrationTest - Comprehensive testing for Laravel Model Settings
 * 
 * This test suite verifies the full integration of the glorand/laravel-model-settings
 * package with all its features and functionality.
 */
class ModelSettingsIntegrationTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $user;
    protected $company;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create test user and company
        $this->user = User::factory()->create();
        $this->company = Company::factory()->create();
    }

    /** @test */
    public function it_can_check_if_settings_exist_and_are_empty()
    {
        // Initially settings should not exist and be empty
        $this->assertFalse($this->user->settings()->exist());
        $this->assertTrue($this->user->settings()->empty());
        
        // After setting a value, settings should exist and not be empty
        $this->user->settings()->set('test.key', 'test_value');
        $this->assertTrue($this->user->settings()->exist());
        $this->assertFalse($this->user->settings()->empty());
    }

    /** @test */
    public function it_can_get_all_settings_with_defaults()
    {
        $allSettings = $this->user->settings()->all();
        
        // Should return default settings even if none are set
        $this->assertIsArray($allSettings);
        $this->assertArrayHasKey('profile', $allSettings);
        $this->assertArrayHasKey('job_preferences', $allSettings);
        $this->assertArrayHasKey('privacy', $allSettings);
    }

    /** @test */
    public function it_can_get_specific_setting_with_default()
    {
        // Test getting setting with default value
        $theme = $this->user->settings()->get('profile.theme', 'dark');
        $this->assertEquals('light', $theme); // Should return default from model
        
        // Test getting non-existent setting with custom default
        $customSetting = $this->user->settings()->get('non.existent', 'custom_default');
        $this->assertEquals('custom_default', $customSetting);
    }

    /** @test */
    public function it_can_set_and_update_settings()
    {
        // Set a single setting
        $this->user->settings()->set('profile.theme', 'dark');
        $this->assertEquals('dark', $this->user->settings()->get('profile.theme'));
        
        // Update the same setting
        $this->user->settings()->update('profile.theme', 'auto');
        $this->assertEquals('auto', $this->user->settings()->get('profile.theme'));
        
        // Set multiple settings
        $this->user->settings()->setMultiple([
            'profile.language' => 'es',
            'job_preferences.remote_work' => true,
        ]);
        
        $this->assertEquals('es', $this->user->settings()->get('profile.language'));
        $this->assertTrue($this->user->settings()->get('job_preferences.remote_work'));
    }

    /** @test */
    public function it_can_apply_multiple_settings_at_once()
    {
        $newSettings = [
            'profile' => [
                'theme' => 'dark',
                'language' => 'fr',
                'timezone' => 'Europe/Paris',
            ],
            'job_preferences' => [
                'job_alerts' => false,
                'remote_work' => true,
            ]
        ];
        
        $this->user->settings()->apply($newSettings);
        
        $this->assertEquals('dark', $this->user->settings()->get('profile.theme'));
        $this->assertEquals('fr', $this->user->settings()->get('profile.language'));
        $this->assertEquals('Europe/Paris', $this->user->settings()->get('profile.timezone'));
        $this->assertFalse($this->user->settings()->get('job_preferences.job_alerts'));
        $this->assertTrue($this->user->settings()->get('job_preferences.remote_work'));
    }

    /** @test */
    public function it_can_check_if_setting_exists()
    {
        $this->assertFalse($this->user->settings()->has('custom.setting'));
        
        $this->user->settings()->set('custom.setting', 'value');
        $this->assertTrue($this->user->settings()->has('custom.setting'));
    }

    /** @test */
    public function it_can_delete_specific_settings()
    {
        // Set some settings
        $this->user->settings()->setMultiple([
            'test.setting1' => 'value1',
            'test.setting2' => 'value2',
            'test.setting3' => 'value3',
        ]);
        
        // Delete single setting
        $this->user->settings()->delete('test.setting1');
        $this->assertFalse($this->user->settings()->has('test.setting1'));
        $this->assertTrue($this->user->settings()->has('test.setting2'));
        
        // Delete multiple settings
        $this->user->settings()->deleteMultiple(['test.setting2', 'test.setting3']);
        $this->assertFalse($this->user->settings()->has('test.setting2'));
        $this->assertFalse($this->user->settings()->has('test.setting3'));
    }

    /** @test */
    public function it_can_clear_all_settings()
    {
        // Set some custom settings
        $this->user->settings()->setMultiple([
            'custom.setting1' => 'value1',
            'custom.setting2' => 'value2',
        ]);
        
        $this->assertTrue($this->user->settings()->has('custom.setting1'));
        
        // Clear all settings
        $this->user->settings()->clear();
        
        // Custom settings should be gone
        $this->assertFalse($this->user->settings()->has('custom.setting1'));
        $this->assertFalse($this->user->settings()->has('custom.setting2'));
        
        // But defaults should still be available
        $allSettings = $this->user->settings()->all();
        $this->assertArrayHasKey('profile', $allSettings);
    }

    /** @test */
    public function it_can_get_multiple_settings_at_once()
    {
        $this->user->settings()->setMultiple([
            'profile.theme' => 'dark',
            'profile.language' => 'es',
            'job_preferences.remote_work' => true,
        ]);
        
        $multipleSettings = $this->user->settings()->getMultiple([
            'profile.theme',
            'profile.language',
            'job_preferences.remote_work',
            'non.existent.setting'
        ], 'default_value');
        
        $this->assertEquals('dark', $multipleSettings['profile.theme']);
        $this->assertEquals('es', $multipleSettings['profile.language']);
        $this->assertTrue($multipleSettings['job_preferences.remote_work']);
        $this->assertEquals('default_value', $multipleSettings['non.existent.setting']);
    }

    /** @test */
    public function it_validates_settings_according_to_rules()
    {
        $this->expectException(\Illuminate\Validation\ValidationException::class);
        
        // Try to set invalid theme value
        $this->user->settings()->set('profile.theme', 'invalid_theme');
    }

    /** @test */
    public function it_works_with_company_settings()
    {
        // Test company settings functionality
        $this->assertFalse($this->company->settings()->exist());
        
        $companySettings = [
            'branding' => [
                'primary_color' => '#ff0000',
                'brand_voice' => 'casual',
            ],
            'recruitment' => [
                'auto_publish_jobs' => true,
                'application_deadline_days' => 45,
            ]
        ];
        
        $this->company->settings()->apply($companySettings);
        
        $this->assertEquals('#ff0000', $this->company->settings()->get('branding.primary_color'));
        $this->assertEquals('casual', $this->company->settings()->get('branding.brand_voice'));
        $this->assertTrue($this->company->settings()->get('recruitment.auto_publish_jobs'));
        $this->assertEquals(45, $this->company->settings()->get('recruitment.application_deadline_days'));
    }

    /** @test */
    public function it_can_test_api_endpoints()
    {
        // Test getting user settings via API
        $response = $this->getJson("/api/model-settings/users/{$this->user->id}");
        
        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'data' => [
                        'user_id',
                        'settings',
                        'has_settings',
                        'is_empty'
                    ]
                ]);

        // Test updating user settings via API
        $newSettings = [
            'settings' => [
                'profile' => [
                    'theme' => 'dark',
                    'language' => 'es'
                ]
            ]
        ];

        $response = $this->putJson("/api/model-settings/users/{$this->user->id}", $newSettings);
        
        $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'message' => 'User settings updated successfully'
                ]);

        // Verify the settings were actually updated
        $this->assertEquals('dark', $this->user->fresh()->settings()->get('profile.theme'));
        $this->assertEquals('es', $this->user->fresh()->settings()->get('profile.language'));
    }

    /** @test */
    public function it_can_test_specific_setting_api_endpoints()
    {
        // Test getting specific setting
        $response = $this->getJson("/api/model-settings/users/{$this->user->id}/profile.theme");
        
        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'data' => [
                        'user_id',
                        'key',
                        'value',
                        'has_setting'
                    ]
                ]);

        // Test setting specific value
        $response = $this->putJson("/api/model-settings/users/{$this->user->id}/profile.theme", [
            'value' => 'dark'
        ]);
        
        $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'message' => 'Setting updated successfully'
                ]);

        // Test deleting specific setting
        $response = $this->deleteJson("/api/model-settings/users/{$this->user->id}/profile.theme");
        
        $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'message' => 'Setting deleted successfully'
                ]);
    }

    /** @test */
    public function it_can_test_demonstration_endpoint()
    {
        $response = $this->getJson('/api/model-settings/demo');
        
        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'message',
                    'data' => [
                        'user_id',
                        'demonstration',
                        'package_info' => [
                            'name',
                            'version',
                            'features'
                        ]
                    ]
                ]);
    }

    /** @test */
    public function it_can_test_schema_endpoint()
    {
        $response = $this->getJson('/api/model-settings/schema');
        
        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'data' => [
                        'user_settings' => [
                            'default_settings',
                            'validation_rules'
                        ],
                        'package_config'
                    ]
                ]);
    }

    /** @test */
    public function it_can_work_with_user_settings_model()
    {
        $userSettings = UserSettings::create([
            'user_id' => $this->user->id,
        ]);

        // Test helper methods
        $this->assertEquals('light', $userSettings->getTheme());
        $this->assertEquals('en', $userSettings->getLanguage());
        $this->assertTrue($userSettings->hasJobAlertsEnabled());
        $this->assertTrue($userSettings->isProfileSearchable());

        // Test updating settings
        $newSettings = [
            'profile' => [
                'theme' => 'dark',
                'language' => 'fr'
            ],
            'job_preferences' => [
                'job_alerts' => false
            ]
        ];

        $this->assertTrue($userSettings->updateSettings($newSettings));
        
        $this->assertEquals('dark', $userSettings->getTheme());
        $this->assertEquals('fr', $userSettings->getLanguage());
        $this->assertFalse($userSettings->hasJobAlertsEnabled());

        // Test reset to defaults
        $this->assertTrue($userSettings->resetToDefaults());
        $this->assertEquals('light', $userSettings->getTheme());
        $this->assertEquals('en', $userSettings->getLanguage());
        $this->assertTrue($userSettings->hasJobAlertsEnabled());
    }
}
