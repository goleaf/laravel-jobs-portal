<?php

namespace Tests\Unit\Seeders;

use Tests\TestCase;
use Database\Seeders\NotificationSettingsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

class NotificationSettingsSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_notification_settings_seeder_runs_successfully()
    {
        $this->seed(NotificationSettingsSeeder::class);
        $count = DB::table('notification_settings')->count();
        $this->assertGreaterThan(0, $count);
    }

    public function test_notification_settings_seeder_data_integrity()
    {
        $this->seed(NotificationSettingsSeeder::class);
        $firstRecord = DB::table('notification_settings')->first();
        $this->assertNotNull($firstRecord);
    }
}