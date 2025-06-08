<?php

namespace Tests\Unit\Seeders;

use Tests\TestCase;
use Database\Seeders\PostCategoriesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

class PostCategoriesSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_post_categories_seeder_runs_successfully()
    {
        $this->seed(PostCategoriesSeeder::class);
        $count = DB::table('post_categories')->count();
        $this->assertGreaterThan(0, $count);
    }

    public function test_post_categories_seeder_data_integrity()
    {
        $this->seed(PostCategoriesSeeder::class);
        $firstRecord = DB::table('post_categories')->first();
        $this->assertNotNull($firstRecord);
    }
}