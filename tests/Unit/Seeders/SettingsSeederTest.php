<?php

namespace Tests\Unit\Seeders;

use Tests\TestCase;
use Database\Seeders\SettingsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

class SettingsSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_settings_seeder_runs_successfully()
    {
        $this->seed(SettingsSeeder::class);
        $count = DB::table('settings')->count();
        $this->assertGreaterThan(0, $count);
    }

    public function test_settings_seeder_data_integrity()
    {
        $this->seed(SettingsSeeder::class);
        $firstRecord = DB::table('settings')->first();
        $this->assertNotNull($firstRecord);
    }
}