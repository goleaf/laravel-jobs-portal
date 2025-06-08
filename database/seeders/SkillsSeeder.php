<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SkillsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->command->info('🌱 Seeding skills...');
        
        // Clear existing data
        DB::table('skills')->truncate();
        
        $data = [
            ['id' => '1', 'name' => 'Computer Skill', 'description' => 'Computer operating skill', 'created_at' => '2024-04-09 23:48:48', 'updated_at' => '2024-04-09 23:48:48', 'is_default' => '1'],
            ['id' => '2', 'name' => 'Communication Skill', 'description' => 'Communication skill', 'created_at' => '2024-04-09 23:48:48', 'updated_at' => '2024-04-09 23:48:48', 'is_default' => '1'],
            ['id' => '3', 'name' => 'Customer service Skill', 'description' => 'Customer service skill', 'created_at' => '2024-04-09 23:48:48', 'updated_at' => '2024-04-09 23:48:48', 'is_default' => '1'],
            ['id' => '4', 'name' => 'Interpersonal Skill', 'description' => 'Interpersonal skill', 'created_at' => '2024-04-09 23:48:48', 'updated_at' => '2024-04-09 23:48:48', 'is_default' => '1'],
            ['id' => '5', 'name' => 'Leadership Skill', 'description' => 'Leadership skill', 'created_at' => '2024-04-09 23:48:48', 'updated_at' => '2024-04-09 23:48:48', 'is_default' => '1'],
            ['id' => '6', 'name' => 'Management Skill', 'description' => 'Management skill', 'created_at' => '2024-04-09 23:48:48', 'updated_at' => '2024-04-09 23:48:48', 'is_default' => '1'],
            ['id' => '7', 'name' => 'Problem-solving Skill', 'description' => 'Problem-solving skill', 'created_at' => '2024-04-09 23:48:48', 'updated_at' => '2024-04-09 23:48:48', 'is_default' => '1'],
            ['id' => '8', 'name' => 'Time management Skill', 'description' => 'Time management skill', 'created_at' => '2024-04-09 23:48:48', 'updated_at' => '2024-04-09 23:48:48', 'is_default' => '1'],
        ];
        
        if (empty($data)) {
            $this->command->info('⚠️  No data to seed for skills');
            return;
        }
        
        // Insert data
        DB::table('skills')->insert($data);
        
        $this->command->info('✅ Seeded ' . count($data) . ' skills records');
    }
}