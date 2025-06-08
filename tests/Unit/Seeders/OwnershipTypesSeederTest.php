<?php

namespace Tests\Unit\Seeders;

use Tests\TestCase;
use Database\Seeders\OwnershipTypesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

class OwnershipTypesSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_ownership_types_seeder_runs_successfully()
    {
        $this->seed(OwnershipTypesSeeder::class);
        $count = DB::table('ownership_types')->count();
        $this->assertGreaterThan(0, $count);
    }

    public function test_ownership_types_seeder_data_integrity()
    {
        $this->seed(OwnershipTypesSeeder::class);
        $firstRecord = DB::table('ownership_types')->first();
        $this->assertNotNull($firstRecord);
    }
}