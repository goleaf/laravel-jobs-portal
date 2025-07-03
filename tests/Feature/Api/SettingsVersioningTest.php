<?php

namespace Tests\Feature\Api;

use App\Actions\SettingsManagement\CreateSettingsVersion;
use App\Models\SettingsVersion;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SettingsVersioningTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->actingAs($this->user);
    }

    /** @test */
    public function it_creates_version_when_settings_are_updated()
    {
        // Initial settings update
        $response = $this->putJson("/api/settings/user/{$this->user->id}", [
            'settings' => [
                'profile' => ['theme' => 'dark', 'language' => 'en'],
            ],
            'update_strategy' => 'merge',
        ]);

        $response->assertStatus(200);

        // Check if version was created
        $this->assertDatabaseHas('settings_versions', [
            'model_type' => User::class,
            'model_id' => $this->user->id,
            'version_number' => 1,
            'change_type' => 'update',
            'is_active' => true,
        ]);
    }

    /** @test */
    public function it_can_get_version_history()
    {
        // Create some versions
        $this->putJson("/api/settings/user/{$this->user->id}", [
            'settings' => ['profile' => ['theme' => 'dark']],
        ]);

        $this->putJson("/api/settings/user/{$this->user->id}", [
            'settings' => ['profile' => ['theme' => 'light']],
        ]);

        // Get version history
        $response = $this->getJson("/api/settings/user/{$this->user->id}/history");

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'data' => [
                'model_type',
                'model_id',
                'versions' => [
                    '*' => [
                        'version_id',
                        'version_number',
                        'change_type',
                        'created_at',
                        'is_latest',
                    ],
                ],
                'total_versions',
            ],
        ]);

        $this->assertTrue($response->json('success'));
        $this->assertEquals(2, $response->json('data.total_versions'));
    }

    /** @test */
    public function it_can_get_specific_version_details()
    {
        // Create a version
        $this->putJson("/api/settings/user/{$this->user->id}", [
            'settings' => ['profile' => ['theme' => 'dark', 'notifications' => true]],
        ]);

        $version = SettingsVersion::forModel(User::class, $this->user->id)->first();

        // Get version details
        $response = $this->getJson("/api/settings/user/{$this->user->id}/version/{$version->version_id}");

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'data' => [
                'version' => [
                    'version_id',
                    'version_number',
                    'settings_data',
                    'change_summary',
                    'integrity_valid',
                ],
                'navigation',
            ],
        ]);

        $this->assertTrue($response->json('success'));
        $this->assertTrue($response->json('data.version.integrity_valid'));
    }

    /** @test */
    public function it_can_rollback_to_previous_version()
    {
        // Create initial version
        $this->putJson("/api/settings/user/{$this->user->id}", [
            'settings' => ['profile' => ['theme' => 'dark']],
        ]);

        $firstVersion = SettingsVersion::forModel(User::class, $this->user->id)->first();

        // Update settings again
        $this->putJson("/api/settings/user/{$this->user->id}", [
            'settings' => ['profile' => ['theme' => 'light']],
        ]);

        // Rollback to first version
        $response = $this->postJson("/api/settings/user/{$this->user->id}/rollback/{$firstVersion->version_id}", [
            'reason' => 'Testing rollback functionality',
            'confirm' => true,
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'data' => [
                'rollback_version' => [
                    'version_id',
                    'version_number',
                    'target_version',
                    'reason',
                ],
                'applied_settings',
            ],
        ]);

        $this->assertTrue($response->json('success'));

        // Verify rollback created a new version
        $this->assertDatabaseHas('settings_versions', [
            'model_type' => User::class,
            'model_id' => $this->user->id,
            'change_type' => 'rollback',
        ]);
    }

    /** @test */
    public function it_can_compare_two_versions()
    {
        // Create first version
        $this->putJson("/api/settings/user/{$this->user->id}", [
            'settings' => ['profile' => ['theme' => 'dark', 'language' => 'en']],
        ]);

        $version1 = SettingsVersion::forModel(User::class, $this->user->id)->latest('created_at')->first();

        // Create second version with changes
        $this->putJson("/api/settings/user/{$this->user->id}", [
            'settings' => ['profile' => ['theme' => 'light', 'language' => 'es']],
        ]);

        $version2 = SettingsVersion::forModel(User::class, $this->user->id)->latest('created_at')->first();

        // Compare versions
        $response = $this->postJson("/api/settings/user/{$this->user->id}/compare", [
            'version_1' => $version1->version_id,
            'version_2' => $version2->version_id,
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'data' => [
                'comparison' => [
                    'version_from',
                    'version_to',
                    'changes',
                    'changes_count',
                ],
                'version_1_info',
                'version_2_info',
            ],
        ]);

        $this->assertTrue($response->json('success'));
        $this->assertGreaterThan(0, $response->json('data.comparison.changes_count'));
    }

    /** @test */
    public function it_validates_version_integrity_with_checksums()
    {
        // Create a version using the action directly
        $version = CreateSettingsVersion::run(
            modelType: User::class,
            modelId: $this->user->id,
            newSettings: ['profile' => ['theme' => 'dark']],
            changeType: 'update',
            userId: $this->user->id
        );

        // Verify checksum is generated
        $this->assertNotNull($version->checksum);
        $this->assertTrue($version->verifyChecksum());

        // Test integrity validation in API
        $response = $this->getJson("/api/settings/user/{$this->user->id}/version/{$version->version_id}");

        $response->assertStatus(200);
        $this->assertTrue($response->json('data.version.integrity_valid'));
    }

    /** @test */
    public function it_tracks_change_statistics_and_metadata()
    {
        $version = CreateSettingsVersion::run(
            modelType: User::class,
            modelId: $this->user->id,
            newSettings: [
                'profile' => ['theme' => 'dark', 'language' => 'en'],
                'notifications' => ['email' => true, 'sms' => false],
            ],
            previousSettings: [
                'profile' => ['theme' => 'light', 'language' => 'en'],
                'notifications' => ['email' => false, 'sms' => false],
            ],
            changeType: 'update',
            userId: $this->user->id,
            changeReason: 'User preference update'
        );

        // Verify metadata
        $this->assertNotNull($version->change_summary);
        $this->assertIsArray($version->changed_keys);
        $this->assertGreaterThan(0, $version->size_bytes);
        $this->assertNotNull($version->ip_address);
        $this->assertEquals('User preference update', $version->change_reason);

        // Verify change summary structure
        $summary = $version->change_summary;
        $this->assertArrayHasKey('type', $summary);
        $this->assertArrayHasKey('changes_count', $summary);
        $this->assertEquals('update', $summary['type']);
    }

    /** @test */
    public function it_handles_version_navigation()
    {
        // Create multiple versions
        $versions = [];
        for ($i = 1; $i <= 3; $i++) {
            $response = $this->putJson("/api/settings/user/{$this->user->id}", [
                'settings' => ['profile' => ['theme' => "theme_{$i}"]],
            ]);
            $response->assertStatus(200);
        }

        $allVersions = SettingsVersion::forModel(User::class, $this->user->id)
            ->orderBy('version_number')
            ->get();

        // Test navigation for middle version
        $middleVersion = $allVersions[1]; // Version 2
        $response = $this->getJson("/api/settings/user/{$this->user->id}/version/{$middleVersion->version_id}");

        $response->assertStatus(200);

        $navigation = $response->json('data.navigation');
        $this->assertNotNull($navigation['previous_version']); // Version 1
        $this->assertNotNull($navigation['next_version']);     // Version 3
    }

    /** @test */
    public function it_prevents_rollback_to_corrupted_versions()
    {
        // Create a version
        $version = CreateSettingsVersion::run(
            modelType: User::class,
            modelId: $this->user->id,
            newSettings: ['profile' => ['theme' => 'dark']],
            changeType: 'update',
            userId: $this->user->id
        );

        // Manually corrupt the checksum (simulate data corruption)
        $version->update(['checksum' => 'invalid_checksum']);

        // Try to rollback to corrupted version
        $response = $this->postJson("/api/settings/user/{$this->user->id}/rollback/{$version->version_id}", [
            'reason' => 'Testing corruption protection',
            'confirm' => true,
        ]);

        $response->assertStatus(400);
        $response->assertJson([
            'success' => false,
            'error' => 'VERSION_INTEGRITY_ERROR',
        ]);
    }
}
