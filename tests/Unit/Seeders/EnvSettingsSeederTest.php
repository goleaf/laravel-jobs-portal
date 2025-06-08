<?php

namespace Tests\Unit\Seeders;

use Tests\TestCase;
use Database\Seeders\EnvSettingsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

class EnvSettingsSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_env_settings_seeder_runs_successfully()
    {
        $this->seed(EnvSettingsSeeder::class);
        $count = DB::table('env_settings')->count();
        $this->assertGreaterThan(0, $count);
    }

    public function test_env_settings_seeder_data_integrity()
    {
        $this->seed(EnvSettingsSeeder::class);
        $firstRecord = DB::table('env_settings')->first();
        $this->assertNotNull($firstRecord);
    }
}