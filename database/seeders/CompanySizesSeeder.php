<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CompanySizesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->command->info('🌱 Seeding company_sizes...');
        
        // Clear existing data
        DB::table('company_sizes')->truncate();
        
        $data = [
            ['id' => '1', 'size' => '5-10', 'created_at' => '2024-04-09 23:48:46', 'updated_at' => '2024-04-09 23:48:46', 'is_default' => '1'],
            ['id' => '2', 'size' => '11-20', 'created_at' => '2024-04-09 23:48:46', 'updated_at' => '2024-04-09 23:48:46', 'is_default' => '1'],
            ['id' => '3', 'size' => '21-50', 'created_at' => '2024-04-09 23:48:46', 'updated_at' => '2024-04-09 23:48:46', 'is_default' => '1'],
            ['id' => '4', 'size' => '51-100', 'created_at' => '2024-04-09 23:48:46', 'updated_at' => '2024-04-09 23:48:46', 'is_default' => '1'],
        ];
        
        if (empty($data)) {
            $this->command->info('⚠️  No data to seed for company_sizes');
            return;
        }
        
        // Insert data
        DB::table('company_sizes')->insert($data);
        
        $this->command->info('✅ Seeded ' . count($data) . ' company_sizes records');
    }
}