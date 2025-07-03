<?php

namespace Tests\Feature;

use App\Models\Setting;
use App\Models\User;
use App\Repositories\SettingRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class SettingTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    protected $adminUser;

    protected function setUp(): void
    {
        parent::setUp();

        // Create admin user for testing
        $this->adminUser = User::factory()->create([
            'user_type' => User::ADMIN,
        ]);

        // Create some settings
        Setting::create(['key' => 'app_name', 'value' => 'Job Portal']);
        Setting::create(['key' => 'company_url', 'value' => 'https://example.com']);
        Setting::create(['key' => 'region_code', 'value' => 'US']);
        Setting::create(['key' => 'phone', 'value' => '1234567890']);
    }

    /** @test */
    public function admin_can_view_general_settings()
    {
        $response = $this->actingAs($this->adminUser)
            ->get('/settings');

        $response->assertStatus(200);
        $response->assertViewIs('settings.general');
        $response->assertViewHas(['setting', 'sectionName', 'envSetting']);
    }

    /** @test */
    public function admin_can_view_specific_settings_section()
    {
        $sections = ['general', 'env_setting', 'social_settings'];

        foreach ($sections as $section) {
            $response = $this->actingAs($this->adminUser)
                ->get('/settings?section='.$section);

            $response->assertStatus(200);
            $response->assertViewIs('settings.'.$section);
            $response->assertViewHas('sectionName', $section);
        }
    }

    /** @test */
    public function admin_can_update_general_settings()
    {
        $settingData = [
            'app_name' => 'Updated Job Portal',
            'company_url' => 'https://updated-example.com',
            'region_code' => 'UK',
            'phone' => '9876543210',
            'sectionName' => 'general',
        ];

        $response = $this->actingAs($this->adminUser)
            ->post('/settings', $settingData);

        $response->assertStatus(302); // Redirects back
        $response->assertSessionHas('flash_notification');

        // Check database for updated settings
        $this->assertEquals('Updated Job Portal', Setting::where('key', 'app_name')->first()->value);
        $this->assertEquals('https://updated-example.com', Setting::where('key', 'company_url')->first()->value);
    }

    /** @test */
    public function non_admin_cannot_access_settings()
    {
        $employerUser = User::factory()->create(['user_type' => User::EMPLOYER]);

        $response = $this->actingAs($employerUser)
            ->get('/settings');

        // Assuming proper middleware restricting access
        $response->assertStatus(403); // Or 401/404 depending on implementation
    }

    /** @test */
    public function admin_can_update_env_settings()
    {
        // Mock the repository to prevent actual .env file changes during tests
        $this->mock(SettingRepository::class, function ($mock) {
            $mock->shouldReceive('updateSetting')->once()->andReturn(true);
            $mock->shouldReceive('getEnvData')->andReturn([]);
        });

        $envData = [
            'app_name' => 'Job Portal',
            'app_url' => 'http://test-url.com',
            'sectionName' => 'env_setting',
        ];

        $response = $this->actingAs($this->adminUser)
            ->post('/settings', $envData);

        $response->assertStatus(302); // Redirects back
        $response->assertSessionHas('flash_notification');
    }
}
