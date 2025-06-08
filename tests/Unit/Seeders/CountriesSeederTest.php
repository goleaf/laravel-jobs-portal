<?php

namespace Tests\Unit\Seeders;

use Tests\TestCase;
use Database\Seeders\CountriesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

class CountriesSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_countries_seeder_runs_successfully()
    {
        $this->seed(CountriesSeeder::class);
        $count = DB::table('countries')->count();
        $this->assertGreaterThan(0, $count);
    }

    public function test_countries_seeder_data_integrity()
    {
        $this->seed(CountriesSeeder::class);
        $firstRecord = DB::table('countries')->first();
        $this->assertNotNull($firstRecord);
    }
}