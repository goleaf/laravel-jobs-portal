<?php

namespace Tests\Unit\Seeders;

use Tests\TestCase;
use Database\Seeders\LanguagesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

class LanguagesSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_languages_seeder_runs_successfully()
    {
        $this->seed(LanguagesSeeder::class);
        $count = DB::table('languages')->count();
        $this->assertGreaterThan(0, $count);
    }

    public function test_languages_seeder_data_integrity()
    {
        $this->seed(LanguagesSeeder::class);
        $firstRecord = DB::table('languages')->first();
        $this->assertNotNull($firstRecord);
    }
}