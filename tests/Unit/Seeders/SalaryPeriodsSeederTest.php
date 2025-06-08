<?php

namespace Tests\Unit\Seeders;

use Tests\TestCase;
use Database\Seeders\SalaryPeriodsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

class SalaryPeriodsSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_salary_periods_seeder_runs_successfully()
    {
        $this->seed(SalaryPeriodsSeeder::class);
        $count = DB::table('salary_periods')->count();
        $this->assertGreaterThan(0, $count);
    }

    public function test_salary_periods_seeder_data_integrity()
    {
        $this->seed(SalaryPeriodsSeeder::class);
        $firstRecord = DB::table('salary_periods')->first();
        $this->assertNotNull($firstRecord);
    }
}