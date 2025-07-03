<?php

namespace Tests\Feature;

use App\Models\Candidate;
use App\Models\Company;
use App\Models\Job;
use App\Models\JobCategory;
use App\Models\JobType;
use App\Models\User;
use App\Models\UserSettings;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

/**
 * ModelSettingsIntegrationTest - Comprehensive testing for Laravel Model Settings
 *
 * This test suite verifies the full integration of the glorand/laravel-model-settings
 * package with all its features and functionality.
 */
class ModelSettingsIntegrationTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    protected $user;
    protected $company;
    protected $job;
    protected $candidate;
    protected $jobCategory;
    protected $jobType;

    protected function setUp(): void
    {
        parent::setUp();

        // Create test data
        $this->user = User::factory()->create();
        $this->company = Company::factory()->create();
        $this->job = Job::factory()->create();
        $this->candidate = Candidate::factory()->create();
        $this->jobCategory = JobCategory::factory()->create();
        $this->jobType = JobType::factory()->create();
    }

    /** @test */
    public function it_can_check_if_settings_exist_and_are_empty()
    {
        // With defaultSettings configured, settings will exist but be "empty" of custom values
        $this->assertTrue($this->user->settings()->exist()); // Default settings exist
        $this->assertFalse($this->user->settings()->empty()); // Default settings are not empty

        // Check that we can detect if custom settings have been set
        $this->assertFalse($this->user->settings()->has('test.key'));

        // After setting a custom value, settings should still exist and not be empty
        $this->user->settings()->set('test.key', 'test_value');
        $this->assertTrue($this->user->settings()->exist());
        $this->assertFalse($this->user->settings()->empty());
        $this->assertTrue($this->user->settings()->has('test.key'));
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
            ],
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
            'non.existent.setting',
        ], 'default_value');

        // The getMultiple method returns nested arrays, not flat dot notation keys
        $this->assertEquals('dark', $multipleSettings['profile']['theme']);
        $this->assertEquals('es', $multipleSettings['profile']['language']);
        $this->assertTrue($multipleSettings['job_preferences']['remote_work']);

        // Test that we can also get individual settings the traditional way
        $this->assertEquals('dark', $this->user->settings()->get('profile.theme'));
        $this->assertEquals('es', $this->user->settings()->get('profile.language'));
        $this->assertTrue($this->user->settings()->get('job_preferences.remote_work'));
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
        // Test company settings functionality - with defaultSettings configured, settings will exist
        $this->assertTrue($this->company->settings()->exist()); // Default settings exist

        $companySettings = [
            'branding' => [
                'primary_color' => '#ff0000',
                'brand_voice' => 'casual',
            ],
            'recruitment' => [
                'auto_publish_jobs' => true,
                'application_deadline_days' => 45,
            ],
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
                    'is_empty',
                ],
            ]);

        // Test updating user settings via API
        $newSettings = [
            'settings' => [
                'profile' => [
                    'theme' => 'dark',
                    'language' => 'es',
                ],
            ],
        ];

        $response = $this->putJson("/api/model-settings/users/{$this->user->id}", $newSettings);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Settings updated successfully',
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
                    'has_setting',
                ],
            ]);

        // Test setting specific value
        $response = $this->putJson("/api/model-settings/users/{$this->user->id}/profile.theme", [
            'value' => 'dark',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Setting updated successfully',
            ]);

        // Test deleting specific setting
        $response = $this->deleteJson("/api/model-settings/users/{$this->user->id}/profile.theme");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Setting deleted successfully',
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
                        'features',
                    ],
                ],
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
                        'validation_rules',
                    ],
                    'package_config',
                ],
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
                'language' => 'fr',
            ],
            'job_preferences' => [
                'job_alerts' => false,
            ],
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

    /** @test */
    public function it_can_list_all_supported_models()
    {
        $response = $this->getJson('/api/model-settings/models');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'supported_models',
                'total_models',
            ])
            ->assertJson([
                'success' => true,
            ]);

        $models = $response->json('supported_models');
        $this->assertArrayHasKey('users', $models);
        $this->assertArrayHasKey('companies', $models);
        $this->assertArrayHasKey('jobs', $models);
        $this->assertArrayHasKey('candidates', $models);
        $this->assertArrayHasKey('job-categories', $models);
        $this->assertArrayHasKey('job-types', $models);
    }

    /** @test */
    public function it_can_get_user_settings()
    {
        $response = $this->getJson("/api/model-settings/users/{$this->user->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'model',
                'id',
                'settings',
                'default_settings',
            ])
            ->assertJson([
                'success' => true,
                'model' => 'users',
                'id' => $this->user->id,
            ]);
    }

    /** @test */
    public function it_can_update_user_settings()
    {
        $newSettings = [
            'settings' => [
                'profile' => [
                    'theme' => 'dark',
                    'language' => 'es',
                ],
            ],
        ];

        $response = $this->putJson("/api/model-settings/users/{$this->user->id}", $newSettings);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Settings updated successfully',
            ]);
    }

    /** @test */
    public function it_can_get_job_settings()
    {
        $response = $this->getJson("/api/model-settings/jobs/{$this->job->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'model',
                'id',
                'settings',
                'default_settings',
            ]);

        $settings = $response->json('settings');
        $this->assertArrayHasKey('visibility', $settings);
        $this->assertArrayHasKey('application', $settings);
        $this->assertArrayHasKey('notifications', $settings);
        $this->assertArrayHasKey('display', $settings);
        $this->assertArrayHasKey('seo', $settings);
        $this->assertArrayHasKey('social', $settings);
        $this->assertArrayHasKey('analytics', $settings);
        $this->assertArrayHasKey('workflow', $settings);
        $this->assertArrayHasKey('premium', $settings);
    }

    /** @test */
    public function it_can_update_job_settings()
    {
        $newSettings = [
            'settings' => [
                'visibility' => [
                    'featured' => true,
                    'urgent' => true,
                ],
                'application' => [
                    'require_cover_letter' => true,
                    'max_applications' => 50,
                ],
            ],
        ];

        $response = $this->putJson("/api/model-settings/jobs/{$this->job->id}", $newSettings);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);
    }

    /** @test */
    public function it_can_get_candidate_settings()
    {
        $response = $this->getJson("/api/model-settings/candidates/{$this->candidate->id}");

        $response->assertStatus(200);

        $settings = $response->json('settings');
        $this->assertArrayHasKey('profile', $settings);
        $this->assertArrayHasKey('privacy', $settings);
        $this->assertArrayHasKey('job_preferences', $settings);
        $this->assertArrayHasKey('notifications', $settings);
        $this->assertArrayHasKey('dashboard', $settings);
        $this->assertArrayHasKey('search', $settings);
        $this->assertArrayHasKey('career', $settings);
        $this->assertArrayHasKey('social', $settings);
    }

    /** @test */
    public function it_can_update_candidate_settings()
    {
        $newSettings = [
            'settings' => [
                'profile' => [
                    'visibility' => 'recruiters_only',
                    'show_salary_expectations' => false,
                ],
                'privacy' => [
                    'allow_recruiter_contact' => false,
                    'hide_from_current_employer' => true,
                ],
            ],
        ];

        $response = $this->putJson("/api/model-settings/candidates/{$this->candidate->id}", $newSettings);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);
    }

    /** @test */
    public function it_can_get_specific_setting()
    {
        $response = $this->getJson("/api/model-settings/candidates/{$this->candidate->id}/profile.visibility");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'model',
                'id',
                'key',
                'value',
            ]);
    }

    /** @test */
    public function it_can_set_specific_setting()
    {
        $response = $this->putJson("/api/model-settings/candidates/{$this->candidate->id}/dashboard.default_view", [
            'value' => 'applications',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'key' => 'dashboard.default_view',
                'value' => 'applications',
            ]);
    }

    /** @test */
    public function it_validates_setting_values()
    {
        $response = $this->putJson("/api/model-settings/candidates/{$this->candidate->id}/dashboard.default_view", [
            'value' => 'invalid_view',
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'message' => 'Validation failed',
            ]);
    }

    /** @test */
    public function it_can_delete_specific_setting()
    {
        // First set a setting
        $this->putJson("/api/model-settings/candidates/{$this->candidate->id}/career.career_goals", [
            'value' => 'Become a senior developer',
        ]);

        // Then delete it
        $response = $this->deleteJson("/api/model-settings/candidates/{$this->candidate->id}/career.career_goals");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Setting deleted successfully',
            ]);
    }

    /** @test */
    public function it_can_clear_all_model_settings()
    {
        $response = $this->deleteJson("/api/model-settings/candidates/{$this->candidate->id}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'All settings cleared successfully',
            ]);
    }

    /** @test */
    public function it_can_get_model_schema()
    {
        $response = $this->getJson('/api/model-settings/jobs/schema');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'model',
                'default_settings',
                'validation_rules',
                'supported_operations',
            ]);
    }

    /** @test */
    public function it_can_get_job_category_settings()
    {
        $response = $this->getJson("/api/model-settings/job-categories/{$this->jobCategory->id}");

        $response->assertStatus(200);

        $settings = $response->json('settings');
        $this->assertArrayHasKey('display', $settings);
        $this->assertArrayHasKey('filtering', $settings);
        $this->assertArrayHasKey('seo', $settings);
        $this->assertArrayHasKey('content', $settings);
        $this->assertArrayHasKey('notifications', $settings);
        $this->assertArrayHasKey('analytics', $settings);
        $this->assertArrayHasKey('features', $settings);
        $this->assertArrayHasKey('moderation', $settings);
    }

    /** @test */
    public function it_can_get_job_type_settings()
    {
        $response = $this->getJson("/api/model-settings/job-types/{$this->jobType->id}");

        $response->assertStatus(200);

        $settings = $response->json('settings');
        $this->assertArrayHasKey('display', $settings);
        $this->assertArrayHasKey('filtering', $settings);
        $this->assertArrayHasKey('features', $settings);
        $this->assertArrayHasKey('analytics', $settings);
    }

    /** @test */
    public function it_handles_non_existent_model()
    {
        $response = $this->getJson('/api/model-settings/invalid-model/1');

        $response->assertStatus(500)
            ->assertJson([
                'success' => false,
            ]);
    }

    /** @test */
    public function it_handles_non_existent_record()
    {
        $response = $this->getJson('/api/model-settings/users/99999');

        $response->assertStatus(500)
            ->assertJson([
                'success' => false,
            ]);
    }

    /** @test */
    public function it_can_run_comprehensive_demo()
    {
        $response = $this->getJson('/api/model-settings/demo/comprehensive');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'models_demonstrated',
                'total_models',
                'features_demonstrated',
            ])
            ->assertJson([
                'success' => true,
            ]);

        $models = $response->json('models_demonstrated');
        $this->assertNotEmpty($models);
    }

    /** @test */
    public function it_preserves_default_settings_structure()
    {
        $user = User::factory()->create();

        // Get settings without any modifications
        $response = $this->getJson("/api/model-settings/users/{$user->id}");

        $settings = $response->json('settings');
        $defaultSettings = $response->json('default_settings');

        // Settings should match default settings initially
        $this->assertEquals($defaultSettings, $settings);
    }

    /** @test */
    public function it_maintains_settings_after_model_updates()
    {
        // Set custom settings
        $this->putJson("/api/model-settings/jobs/{$this->job->id}", [
            'settings' => [
                'visibility' => [
                    'featured' => true,
                ],
            ],
        ]);

        // Update the job model
        $this->job->update(['job_title' => 'Updated Job Title']);

        // Check settings are preserved
        $response = $this->getJson("/api/model-settings/jobs/{$this->job->id}");
        $settings = $response->json('settings');

        $this->assertTrue($settings['visibility']['featured']);
    }
}
