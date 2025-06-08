<?php

namespace Tests\Unit\Seeders;

use Tests\TestCase;
use Database\Seeders\RolesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

class RolesSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_roles_seeder_runs_successfully()
    {
        $this->seed(RolesSeeder::class);
        $count = DB::table('roles')->count();
        $this->assertGreaterThan(0, $count);
    }

    public function test_roles_seeder_data_integrity()
    {
        $this->seed(RolesSeeder::class);
        $firstRecord = DB::table('roles')->first();
        $this->assertNotNull($firstRecord);
    }
}