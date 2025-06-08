<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SalaryPeriodsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->command->info('🌱 Seeding salary_periods...');
        
        // Clear existing data
        DB::table('salary_periods')->truncate();
        
        $data = [
            ['id' => '1', 'period' => 'Weekly Pay Period', 'description' => 'Weekly Pay Period', 'created_at' => '2024-04-09 23:48:48', 'updated_at' => '2024-04-09 23:48:48', 'is_default' => '1'],
            ['id' => '2', 'period' => 'Every Other Week Pay Period', 'description' => 'Every Other Week Pay Period', 'created_at' => '2024-04-09 23:48:48', 'updated_at' => '2024-04-09 23:48:48', 'is_default' => '1'],
            ['id' => '3', 'period' => 'Semi Monthly Pay Period', 'description' => 'Semi Monthly Pay Period', 'created_at' => '2024-04-09 23:48:48', 'updated_at' => '2024-04-09 23:48:48', 'is_default' => '1'],
            ['id' => '4', 'period' => 'Monthly Pay Period', 'description' => 'Monthly Pay Period', 'created_at' => '2024-04-09 23:48:48', 'updated_at' => '2024-04-09 23:48:48', 'is_default' => '1'],
        ];
        
        if (empty($data)) {
            $this->command->info('⚠️  No data to seed for salary_periods');
            return;
        }
        
        // Insert data
        DB::table('salary_periods')->insert($data);
        
        $this->command->info('✅ Seeded ' . count($data) . ' salary_periods records');
    }
}