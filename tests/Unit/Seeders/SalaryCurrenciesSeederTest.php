<?php

namespace Tests\Unit\Seeders;

use Tests\TestCase;
use Database\Seeders\SalaryCurrenciesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

class SalaryCurrenciesSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_salary_currencies_seeder_runs_successfully()
    {
        $this->seed(SalaryCurrenciesSeeder::class);
        $count = DB::table('salary_currencies')->count();
        $this->assertGreaterThan(0, $count);
    }

    public function test_salary_currencies_seeder_data_integrity()
    {
        $this->seed(SalaryCurrenciesSeeder::class);
        $firstRecord = DB::table('salary_currencies')->first();
        $this->assertNotNull($firstRecord);
    }
}