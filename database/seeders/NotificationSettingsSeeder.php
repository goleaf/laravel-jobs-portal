<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class NotificationSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->command->info('🌱 Seeding notification_settings...');
        
        // Clear existing data
        DB::table('notification_settings')->truncate();
        
        $data = [
            ['id' => '1', 'key' => 'JOB_APPLICATION_SUBMITTED', 'type' => 'employer', 'value' => '1', 'created_at' => '2024-04-09 23:48:49', 'updated_at' => '2024-04-09 23:48:49'],
            ['id' => '2', 'key' => 'MARK_JOB_FEATURED', 'type' => 'employer', 'value' => '1', 'created_at' => '2024-04-09 23:48:49', 'updated_at' => '2024-04-09 23:48:49'],
            ['id' => '3', 'key' => 'MARK_COMPANY_FEATURED', 'type' => 'employer', 'value' => '1', 'created_at' => '2024-04-09 23:48:49', 'updated_at' => '2024-04-09 23:48:49'],
            ['id' => '4', 'key' => 'CANDIDATE_SELECTED_FOR_JOB', 'type' => 'candidate', 'value' => '1', 'created_at' => '2024-04-09 23:48:49', 'updated_at' => '2024-04-09 23:48:49'],
            ['id' => '5', 'key' => 'CANDIDATE_REJECTED_FOR_JOB', 'type' => 'candidate', 'value' => '1', 'created_at' => '2024-04-09 23:48:49', 'updated_at' => '2024-04-09 23:48:49'],
            ['id' => '6', 'key' => 'CANDIDATE_SHORTLISTED_FOR_JOB', 'type' => 'candidate', 'value' => '1', 'created_at' => '2024-04-09 23:48:49', 'updated_at' => '2024-04-09 23:48:49'],
            ['id' => '7', 'key' => 'NEW_EMPLOYER_REGISTERED', 'type' => 'admin', 'value' => '1', 'created_at' => '2024-04-09 23:48:49', 'updated_at' => '2024-04-09 23:48:50'],
            ['id' => '8', 'key' => 'NEW_CANDIDATE_REGISTERED', 'type' => 'admin', 'value' => '1', 'created_at' => '2024-04-09 23:48:49', 'updated_at' => '2024-04-09 23:48:50'],
            ['id' => '9', 'key' => 'EMPLOYER_PURCHASE_PLAN', 'type' => 'admin', 'value' => '1', 'created_at' => '2024-04-09 23:48:49', 'updated_at' => '2024-04-09 23:48:50'],
            ['id' => '10', 'key' => 'FOLLOW_COMPANY', 'type' => 'employer', 'value' => '1', 'created_at' => '2024-04-09 23:48:49', 'updated_at' => '2024-04-09 23:48:49'],
        ];
        
        if (empty($data)) {
            $this->command->info('⚠️  No data to seed for notification_settings');
            return;
        }
        
        // Insert data
        DB::table('notification_settings')->insert($data);
        
        $this->command->info('✅ Seeded ' . count($data) . ' notification_settings records');
    }
}