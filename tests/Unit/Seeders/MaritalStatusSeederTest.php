<?php

namespace Tests\Unit\Seeders;

use Tests\TestCase;
use Database\Seeders\MaritalStatusSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

class MaritalStatusSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_marital_status_seeder_runs_successfully()
    {
        $this->seed(MaritalStatusSeeder::class);
        $count = DB::table('marital_status')->count();
        $this->assertGreaterThan(0, $count);
    }

    public function test_marital_status_seeder_data_integrity()
    {
        $this->seed(MaritalStatusSeeder::class);
        $firstRecord = DB::table('marital_status')->first();
        $this->assertNotNull($firstRecord);
    }
}