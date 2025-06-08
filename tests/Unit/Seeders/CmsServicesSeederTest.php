<?php

namespace Tests\Unit\Seeders;

use Tests\TestCase;
use Database\Seeders\CmsServicesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

class CmsServicesSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_cms_services_seeder_runs_successfully()
    {
        $this->seed(CmsServicesSeeder::class);
        $count = DB::table('cms_services')->count();
        $this->assertGreaterThan(0, $count);
    }

    public function test_cms_services_seeder_data_integrity()
    {
        $this->seed(CmsServicesSeeder::class);
        $firstRecord = DB::table('cms_services')->first();
        $this->assertNotNull($firstRecord);
    }
}