<?php

namespace Tests\Unit\Seeders;

use Tests\TestCase;
use Database\Seeders\JobShiftsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

class JobShiftsSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_job_shifts_seeder_runs_successfully()
    {
        $this->seed(JobShiftsSeeder::class);
        $count = DB::table('job_shifts')->count();
        $this->assertGreaterThan(0, $count);
    }

    public function test_job_shifts_seeder_data_integrity()
    {
        $this->seed(JobShiftsSeeder::class);
        $firstRecord = DB::table('job_shifts')->first();
        $this->assertNotNull($firstRecord);
    }
}