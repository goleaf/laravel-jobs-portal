<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class JobTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->command->info('🌱 Seeding job_types...');
        
        // Clear existing data
        DB::table('job_types')->truncate();
        
        $data = [
            ['id' => '1', 'name' => 'Architecture and Engineering', 'description' => 'Architecture and Engineering', 'created_at' => '2024-04-09 23:48:46', 'updated_at' => '2024-04-09 23:48:46', 'is_default' => '1'],
            ['id' => '2', 'name' => 'Arts, Design, Entertainment, Sports, and Media', 'description' => 'Arts, Design, Entertainment, Sports, and Media', 'created_at' => '2024-04-09 23:48:47', 'updated_at' => '2024-04-09 23:48:47', 'is_default' => '1'],
            ['id' => '3', 'name' => 'Building and Grounds Cleaning and Maintenance', 'description' => 'Building and Grounds Cleaning and Maintenance', 'created_at' => '2024-04-09 23:48:47', 'updated_at' => '2024-04-09 23:48:47', 'is_default' => '1'],
            ['id' => '4', 'name' => 'Business and Financial Operations', 'description' => 'Business and Financial Operations', 'created_at' => '2024-04-09 23:48:47', 'updated_at' => '2024-04-09 23:48:47', 'is_default' => '1'],
            ['id' => '5', 'name' => 'Community and Social Services', 'description' => 'Community and Social Services', 'created_at' => '2024-04-09 23:48:47', 'updated_at' => '2024-04-09 23:48:47', 'is_default' => '1'],
            ['id' => '6', 'name' => 'Computer and Mathematical', 'description' => 'Computer and Mathematical', 'created_at' => '2024-04-09 23:48:47', 'updated_at' => '2024-04-09 23:48:47', 'is_default' => '1'],
            ['id' => '7', 'name' => 'Construction and Extraction', 'description' => 'Construction and Extraction', 'created_at' => '2024-04-09 23:48:47', 'updated_at' => '2024-04-09 23:48:47', 'is_default' => '1'],
            ['id' => '8', 'name' => 'Education, Training, and Library', 'description' => 'Education, Training, and Library', 'created_at' => '2024-04-09 23:48:47', 'updated_at' => '2024-04-09 23:48:47', 'is_default' => '1'],
        ];
        
        if (empty($data)) {
            $this->command->info('⚠️  No data to seed for job_types');
            return;
        }
        
        // Insert data
        DB::table('job_types')->insert($data);
        
        $this->command->info('✅ Seeded ' . count($data) . ' job_types records');
    }
}