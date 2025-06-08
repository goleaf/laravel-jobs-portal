<?php

namespace Tests\Unit\Seeders;

use Tests\TestCase;
use Database\Seeders\CitiesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

class CitiesSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_cities_seeder_runs_successfully()
    {
        $this->seed(CitiesSeeder::class);
        $count = DB::table('cities')->count();
        $this->assertGreaterThan(0, $count);
    }

    public function test_cities_seeder_data_integrity()
    {
        $this->seed(CitiesSeeder::class);
        $firstRecord = DB::table('cities')->first();
        $this->assertNotNull($firstRecord);
    }
}