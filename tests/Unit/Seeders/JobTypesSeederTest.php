<?php

namespace Tests\Unit\Seeders;

use Tests\TestCase;
use Database\Seeders\JobTypesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

class JobTypesSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_job_types_seeder_runs_successfully()
    {
        $this->seed(JobTypesSeeder::class);
        $count = DB::table('job_types')->count();
        $this->assertGreaterThan(0, $count);
    }

    public function test_job_types_seeder_data_integrity()
    {
        $this->seed(JobTypesSeeder::class);
        $firstRecord = DB::table('job_types')->first();
        $this->assertNotNull($firstRecord);
    }
}