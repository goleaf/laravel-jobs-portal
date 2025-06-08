<?php

namespace Tests\Unit\Seeders;

use Tests\TestCase;
use Database\Seeders\RequiredDegreeLevelsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

class RequiredDegreeLevelsSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_required_degree_levels_seeder_runs_successfully()
    {
        $this->seed(RequiredDegreeLevelsSeeder::class);
        $count = DB::table('required_degree_levels')->count();
        $this->assertGreaterThan(0, $count);
    }

    public function test_required_degree_levels_seeder_data_integrity()
    {
        $this->seed(RequiredDegreeLevelsSeeder::class);
        $firstRecord = DB::table('required_degree_levels')->first();
        $this->assertNotNull($firstRecord);
    }
}