<?php

namespace Tests\Unit\Seeders;

use Tests\TestCase;
use Database\Seeders\JobCategoriesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

class JobCategoriesSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_job_categories_seeder_runs_successfully()
    {
        $this->seed(JobCategoriesSeeder::class);
        $count = DB::table('job_categories')->count();
        $this->assertGreaterThan(0, $count);
    }

    public function test_job_categories_seeder_data_integrity()
    {
        $this->seed(JobCategoriesSeeder::class);
        $firstRecord = DB::table('job_categories')->first();
        $this->assertNotNull($firstRecord);
    }
}