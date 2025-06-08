<?php

namespace Tests\Unit\Seeders;

use Tests\TestCase;
use Database\Seeders\UsersSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

class UsersSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_users_seeder_runs_successfully()
    {
        $this->seed(UsersSeeder::class);
        $count = DB::table('users')->count();
        $this->assertGreaterThan(0, $count);
    }

    public function test_users_seeder_data_integrity()
    {
        $this->seed(UsersSeeder::class);
        $firstRecord = DB::table('users')->first();
        $this->assertNotNull($firstRecord);
    }
}