<?php

namespace Tests\Unit\Seeders;

use Tests\TestCase;
use Database\Seeders\TagsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

class TagsSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_tags_seeder_runs_successfully()
    {
        $this->seed(TagsSeeder::class);
        $count = DB::table('tags')->count();
        $this->assertGreaterThan(0, $count);
    }

    public function test_tags_seeder_data_integrity()
    {
        $this->seed(TagsSeeder::class);
        $firstRecord = DB::table('tags')->first();
        $this->assertNotNull($firstRecord);
    }
}