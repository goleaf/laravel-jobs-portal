<?php

namespace Tests\Unit\Seeders;

use Tests\TestCase;
use Database\Seeders\FrontSettingsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

class FrontSettingsSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_front_settings_seeder_runs_successfully()
    {
        $this->seed(FrontSettingsSeeder::class);
        $count = DB::table('front_settings')->count();
        $this->assertGreaterThan(0, $count);
    }

    public function test_front_settings_seeder_data_integrity()
    {
        $this->seed(FrontSettingsSeeder::class);
        $firstRecord = DB::table('front_settings')->first();
        $this->assertNotNull($firstRecord);
    }
}