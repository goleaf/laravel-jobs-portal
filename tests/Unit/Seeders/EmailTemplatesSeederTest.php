<?php

namespace Tests\Unit\Seeders;

use Tests\TestCase;
use Database\Seeders\EmailTemplatesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

class EmailTemplatesSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_email_templates_seeder_runs_successfully()
    {
        $this->seed(EmailTemplatesSeeder::class);
        $count = DB::table('email_templates')->count();
        $this->assertGreaterThan(0, $count);
    }

    public function test_email_templates_seeder_data_integrity()
    {
        $this->seed(EmailTemplatesSeeder::class);
        $firstRecord = DB::table('email_templates')->first();
        $this->assertNotNull($firstRecord);
    }
}