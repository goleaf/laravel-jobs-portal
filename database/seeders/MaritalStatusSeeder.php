<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MaritalStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->command->info('🌱 Seeding marital_status...');
        
        // Clear existing data
        DB::table('marital_status')->truncate();
        
        $data = [
            ['id' => '1', 'marital_status' => 'Married', 'description' => 'Married', 'created_at' => '2024-04-09 23:48:46', 'updated_at' => '2024-04-09 23:48:46', 'is_default' => '1'],
            ['id' => '2', 'marital_status' => 'Widowed', 'description' => 'Widowed', 'created_at' => '2024-04-09 23:48:46', 'updated_at' => '2024-04-09 23:48:46', 'is_default' => '1'],
            ['id' => '3', 'marital_status' => 'Separated', 'description' => 'Separated', 'created_at' => '2024-04-09 23:48:46', 'updated_at' => '2024-04-09 23:48:46', 'is_default' => '1'],
            ['id' => '4', 'marital_status' => 'Divorced', 'description' => 'Divorced', 'created_at' => '2024-04-09 23:48:46', 'updated_at' => '2024-04-09 23:48:46', 'is_default' => '1'],
            ['id' => '5', 'marital_status' => 'Single', 'description' => 'Single', 'created_at' => '2024-04-09 23:48:46', 'updated_at' => '2024-04-09 23:48:46', 'is_default' => '1'],
        ];
        
        if (empty($data)) {
            $this->command->info('⚠️  No data to seed for marital_status');
            return;
        }
        
        // Insert data
        DB::table('marital_status')->insert($data);
        
        $this->command->info('✅ Seeded ' . count($data) . ' marital_status records');
    }
}