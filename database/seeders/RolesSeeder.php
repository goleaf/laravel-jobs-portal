<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->command->info('🌱 Seeding roles...');
        
        // Clear existing data
        DB::table('roles')->truncate();
        
        $data = [
            ['id' => '1', 'name' => 'Admin', 'guard_name' => 'web', 'created_at' => '2024-04-09 23:48:44', 'updated_at' => '2024-04-09 23:48:44'],
            ['id' => '2', 'name' => 'Employer', 'guard_name' => 'web', 'created_at' => '2024-04-09 23:48:44', 'updated_at' => '2024-04-09 23:48:44'],
            ['id' => '3', 'name' => 'Candidate', 'guard_name' => 'web', 'created_at' => '2024-04-09 23:48:44', 'updated_at' => '2024-04-09 23:48:44'],
        ];
        
        if (empty($data)) {
            $this->command->info('⚠️  No data to seed for roles');
            return;
        }
        
        // Insert data
        DB::table('roles')->insert($data);
        
        $this->command->info('✅ Seeded ' . count($data) . ' roles records');
    }
}