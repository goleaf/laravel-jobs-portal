<?php

namespace Tests\Unit\Seeders;

use Tests\TestCase;
use Database\Seeders\SkillsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

class SkillsSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_skills_seeder_runs_successfully()
    {
        $this->seed(SkillsSeeder::class);
        $count = DB::table('skills')->count();
        $this->assertGreaterThan(0, $count);
    }

    public function test_skills_seeder_data_integrity()
    {
        $this->seed(SkillsSeeder::class);
        $firstRecord = DB::table('skills')->first();
        $this->assertNotNull($firstRecord);
    }
}