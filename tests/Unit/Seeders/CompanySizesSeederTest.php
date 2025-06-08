<?php

namespace Tests\Unit\Seeders;

use Tests\TestCase;
use Database\Seeders\CompanySizesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

class CompanySizesSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_company_sizes_seeder_runs_successfully()
    {
        $this->seed(CompanySizesSeeder::class);
        $count = DB::table('company_sizes')->count();
        $this->assertGreaterThan(0, $count);
    }

    public function test_company_sizes_seeder_data_integrity()
    {
        $this->seed(CompanySizesSeeder::class);
        $firstRecord = DB::table('company_sizes')->first();
        $this->assertNotNull($firstRecord);
    }
}