<?php

namespace Tests\Unit\Seeders;

use Tests\TestCase;
use Database\Seeders\FunctionalAreasSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

class FunctionalAreasSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_functional_areas_seeder_runs_successfully()
    {
        $this->seed(FunctionalAreasSeeder::class);
        $count = DB::table('functional_areas')->count();
        $this->assertGreaterThan(0, $count);
    }

    public function test_functional_areas_seeder_data_integrity()
    {
        $this->seed(FunctionalAreasSeeder::class);
        $firstRecord = DB::table('functional_areas')->first();
        $this->assertNotNull($firstRecord);
    }
}