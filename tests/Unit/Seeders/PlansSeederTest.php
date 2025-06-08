<?php

namespace Tests\Unit\Seeders;

use Tests\TestCase;
use Database\Seeders\PlansSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

class PlansSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_plans_seeder_runs_successfully()
    {
        $this->seed(PlansSeeder::class);
        $count = DB::table('plans')->count();
        $this->assertGreaterThan(0, $count);
    }

    public function test_plans_seeder_data_integrity()
    {
        $this->seed(PlansSeeder::class);
        $firstRecord = DB::table('plans')->first();
        $this->assertNotNull($firstRecord);
    }
}