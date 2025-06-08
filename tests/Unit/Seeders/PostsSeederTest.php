<?php

namespace Tests\Unit\Seeders;

use Tests\TestCase;
use Database\Seeders\PostsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

class PostsSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_posts_seeder_runs_successfully()
    {
        $this->seed(PostsSeeder::class);
        $count = DB::table('posts')->count();
        $this->assertGreaterThan(0, $count);
    }

    public function test_posts_seeder_data_integrity()
    {
        $this->seed(PostsSeeder::class);
        $firstRecord = DB::table('posts')->first();
        $this->assertNotNull($firstRecord);
    }
}