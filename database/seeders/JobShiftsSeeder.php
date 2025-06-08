<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class JobShiftsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->command->info('🌱 Seeding job_shifts...');
        
        // Clear existing data
        DB::table('job_shifts')->truncate();
        
        $data = [
            ['id' => '1', 'shift' => 'First Shift', 'description' => 'First Shift', 'created_at' => '2024-04-09 23:48:48', 'updated_at' => '2024-04-09 23:48:48', 'is_default' => '1'],
            ['id' => '2', 'shift' => 'Second Shift', 'description' => 'Second Shift', 'created_at' => '2024-04-09 23:48:48', 'updated_at' => '2024-04-09 23:48:48', 'is_default' => '1'],
            ['id' => '3', 'shift' => 'Third Shift', 'description' => 'Third Shift', 'created_at' => '2024-04-09 23:48:48', 'updated_at' => '2024-04-09 23:48:48', 'is_default' => '1'],
            ['id' => '4', 'shift' => 'Fixed Shift', 'description' => 'Fixed Shift', 'created_at' => '2024-04-09 23:48:48', 'updated_at' => '2024-04-09 23:48:48', 'is_default' => '1'],
            ['id' => '5', 'shift' => 'Rotating Shift', 'description' => 'Rotating Shift', 'created_at' => '2024-04-09 23:48:48', 'updated_at' => '2024-04-09 23:48:48', 'is_default' => '1'],
            ['id' => '6', 'shift' => 'Split Shift', 'description' => 'Split Shift', 'created_at' => '2024-04-09 23:48:48', 'updated_at' => '2024-04-09 23:48:48', 'is_default' => '1'],
            ['id' => '7', 'shift' => 'On-call Shift', 'description' => 'On-call Shift', 'created_at' => '2024-04-09 23:48:48', 'updated_at' => '2024-04-09 23:48:48', 'is_default' => '1'],
            ['id' => '8', 'shift' => 'Weekday or weekend Shift', 'description' => 'Weekday or Weekend Shift', 'created_at' => '2024-04-09 23:48:48', 'updated_at' => '2024-04-09 23:48:48', 'is_default' => '1'],
        ];
        
        if (empty($data)) {
            $this->command->info('⚠️  No data to seed for job_shifts');
            return;
        }
        
        // Insert data
        DB::table('job_shifts')->insert($data);
        
        $this->command->info('✅ Seeded ' . count($data) . ' job_shifts records');
    }
}