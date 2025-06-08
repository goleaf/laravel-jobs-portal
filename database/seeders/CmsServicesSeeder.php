<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CmsServicesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->command->info('🌱 Seeding cms_services...');
        
        // Clear existing data
        DB::table('cms_services')->truncate();
        
        $data = [
            ['id' => '1', 'key' => 'home_title', 'value' => 'Join us & Explore Thousands of Jobs', 'created_at' => '2024-04-09 23:48:50', 'updated_at' => '2024-04-09 23:48:50'],
            ['id' => '2', 'key' => 'home_description', 'value' => 'Find Jobs, Employment & Career Opportunities', 'created_at' => '2024-04-09 23:48:50', 'updated_at' => '2024-04-09 23:48:50'],
            ['id' => '3', 'key' => 'home_banner', 'value' => 'front_web/images/hero-img.png', 'created_at' => '2024-04-09 23:48:50', 'updated_at' => '2024-04-09 23:48:50'],
            ['id' => '4', 'key' => 'about_title_one', 'value' => 'Register', 'created_at' => '2024-04-09 23:48:50', 'updated_at' => '2024-04-09 23:48:50'],
            ['id' => '5', 'key' => 'about_description_one', 'value' => 'Start by creating an account on our awesome platform', 'created_at' => '2024-04-09 23:48:50', 'updated_at' => '2024-04-09 23:48:50'],
            ['id' => '6', 'key' => 'about_image_one', 'value' => 'front_web/images/register.png', 'created_at' => '2024-04-09 23:48:50', 'updated_at' => '2024-04-09 23:48:50'],
            ['id' => '7', 'key' => 'about_title_two', 'value' => 'Submit Resume', 'created_at' => '2024-04-09 23:48:50', 'updated_at' => '2024-04-09 23:48:50'],
            ['id' => '8', 'key' => 'about_description_two', 'value' => 'Fill out our forms and submit your resume right away', 'created_at' => '2024-04-09 23:48:50', 'updated_at' => '2024-04-09 23:48:50'],
            ['id' => '9', 'key' => 'about_image_two', 'value' => 'front_web/images/resume.png', 'created_at' => '2024-04-09 23:48:50', 'updated_at' => '2024-04-09 23:48:50'],
            ['id' => '10', 'key' => 'about_title_three', 'value' => 'Start Working', 'created_at' => '2024-04-09 23:48:50', 'updated_at' => '2024-04-09 23:48:50'],
        ];
        
        if (empty($data)) {
            $this->command->info('⚠️  No data to seed for cms_services');
            return;
        }
        
        // Insert data
        DB::table('cms_services')->insert($data);
        
        $this->command->info('✅ Seeded ' . count($data) . ' cms_services records');
    }
}