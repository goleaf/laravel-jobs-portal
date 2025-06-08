<?php

namespace Tests\Unit\Seeders;

use Tests\TestCase;
use Database\Seeders\StatesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

class StatesSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_states_seeder_runs_successfully()
    {
        $this->seed(StatesSeeder::class);
        $count = DB::table('states')->count();
        $this->assertGreaterThan(0, $count);
    }

    public function test_states_seeder_data_integrity()
    {
        $this->seed(StatesSeeder::class);
        $firstRecord = DB::table('states')->first();
        $this->assertNotNull($firstRecord);
    }
}