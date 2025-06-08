<?php

namespace Tests\Unit\Seeders;

use Tests\TestCase;
use Database\Seeders\CareerLevelsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

class CareerLevelsSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_career_levels_seeder_runs_successfully()
    {
        $this->seed(CareerLevelsSeeder::class);
        $count = DB::table('career_levels')->count();
        $this->assertGreaterThan(0, $count);
    }

    public function test_career_levels_seeder_data_integrity()
    {
        $this->seed(CareerLevelsSeeder::class);
        $firstRecord = DB::table('career_levels')->first();
        $this->assertNotNull($firstRecord);
    }
}