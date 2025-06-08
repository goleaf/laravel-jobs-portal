<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class FunctionalAreasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->command->info('🌱 Seeding functional_areas...');
        
        // Clear existing data
        DB::table('functional_areas')->truncate();
        
        $data = [
            ['id' => '1', 'name' => 'Human Resource', 'created_at' => '2024-04-09 23:48:48', 'updated_at' => '2024-04-09 23:48:48', 'is_default' => '1'],
            ['id' => '2', 'name' => 'Marketing/Promotion', 'created_at' => '2024-04-09 23:48:48', 'updated_at' => '2024-04-09 23:48:48', 'is_default' => '1'],
            ['id' => '3', 'name' => 'Customer Service Support', 'created_at' => '2024-04-09 23:48:48', 'updated_at' => '2024-04-09 23:48:48', 'is_default' => '1'],
            ['id' => '4', 'name' => 'Sales', 'created_at' => '2024-04-09 23:48:48', 'updated_at' => '2024-04-09 23:48:48', 'is_default' => '1'],
            ['id' => '5', 'name' => 'Accounting and Finance', 'created_at' => '2024-04-09 23:48:48', 'updated_at' => '2024-04-09 23:48:48', 'is_default' => '1'],
            ['id' => '6', 'name' => 'Distribution', 'created_at' => '2024-04-09 23:48:48', 'updated_at' => '2024-04-09 23:48:48', 'is_default' => '1'],
            ['id' => '7', 'name' => 'Research and Development', 'created_at' => '2024-04-09 23:48:48', 'updated_at' => '2024-04-09 23:48:48', 'is_default' => '1'],
            ['id' => '8', 'name' => 'Administrative/Management', 'created_at' => '2024-04-09 23:48:48', 'updated_at' => '2024-04-09 23:48:48', 'is_default' => '1'],
            ['id' => '9', 'name' => 'Production', 'created_at' => '2024-04-09 23:48:48', 'updated_at' => '2024-04-09 23:48:48', 'is_default' => '1'],
            ['id' => '10', 'name' => 'Operations', 'created_at' => '2024-04-09 23:48:48', 'updated_at' => '2024-04-09 23:48:48', 'is_default' => '1'],
        ];
        
        if (empty($data)) {
            $this->command->info('⚠️  No data to seed for functional_areas');
            return;
        }
        
        // Insert data
        DB::table('functional_areas')->insert($data);
        
        $this->command->info('✅ Seeded ' . count($data) . ' functional_areas records');
    }
}