<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class FrontSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->command->info('🌱 Seeding front_settings...');
        
        // Clear existing data
        DB::table('front_settings')->truncate();
        
        $data = [
            ['id' => '1', 'key' => 'featured_jobs_price', 'value' => '0', 'created_at' => '2024-04-09 23:48:48', 'updated_at' => '2024-04-09 23:48:48'],
            ['id' => '2', 'key' => 'featured_jobs_days', 'value' => '10', 'created_at' => '2024-04-09 23:48:49', 'updated_at' => '2024-04-09 23:48:49'],
            ['id' => '3', 'key' => 'featured_jobs_quota', 'value' => '10', 'created_at' => '2024-04-09 23:48:49', 'updated_at' => '2024-04-09 23:48:49'],
            ['id' => '4', 'key' => 'featured_companies_price', 'value' => '0', 'created_at' => '2024-04-09 23:48:49', 'updated_at' => '2024-04-09 23:48:49'],
            ['id' => '5', 'key' => 'featured_companies_days', 'value' => '10', 'created_at' => '2024-04-09 23:48:49', 'updated_at' => '2024-04-09 23:48:49'],
            ['id' => '6', 'key' => 'featured_companies_quota', 'value' => '10', 'created_at' => '2024-04-09 23:48:49', 'updated_at' => '2024-04-09 23:48:49'],
            ['id' => '7', 'key' => 'featured_jobs_enable', 'value' => '0', 'created_at' => '2024-04-09 23:48:49', 'updated_at' => '2024-04-09 23:48:49'],
            ['id' => '8', 'key' => 'featured_companies_enable', 'value' => '0', 'created_at' => '2024-04-09 23:48:49', 'updated_at' => '2024-04-09 23:48:49'],
            ['id' => '9', 'key' => 'currency', 'value' => '64', 'created_at' => '2024-04-09 23:48:49', 'updated_at' => '2024-04-09 23:48:49'],
            ['id' => '10', 'key' => 'latest_jobs_enable', 'value' => '0', 'created_at' => '2024-04-09 23:48:49', 'updated_at' => '2024-04-09 23:48:49'],
        ];
        
        if (empty($data)) {
            $this->command->info('⚠️  No data to seed for front_settings');
            return;
        }
        
        // Insert data
        DB::table('front_settings')->insert($data);
        
        $this->command->info('✅ Seeded ' . count($data) . ' front_settings records');
    }
}