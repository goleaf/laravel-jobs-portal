<?php

namespace Tests\Unit\Seeders;

use Tests\TestCase;
use Database\Seeders\IndustriesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

class IndustriesSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_industries_seeder_runs_successfully()
    {
        $this->seed(IndustriesSeeder::class);
        $count = DB::table('industries')->count();
        $this->assertGreaterThan(0, $count);
    }

    public function test_industries_seeder_data_integrity()
    {
        $this->seed(IndustriesSeeder::class);
        $firstRecord = DB::table('industries')->first();
        $this->assertNotNull($firstRecord);
    }
}