<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TagsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->command->info('🌱 Seeding tags...');
        
        // Clear existing data
        DB::table('tags')->truncate();
        
        $data = [
            ['id' => '1', 'name' => 'PHP', 'description' => null, 'created_at' => '2024-04-09 23:48:47', 'updated_at' => '2024-04-09 23:48:47', 'is_default' => '1'],
            ['id' => '2', 'name' => 'Laravel', 'description' => null, 'created_at' => '2024-04-09 23:48:48', 'updated_at' => '2024-04-09 23:48:48', 'is_default' => '1'],
            ['id' => '3', 'name' => 'HTML', 'description' => null, 'created_at' => '2024-04-09 23:48:48', 'updated_at' => '2024-04-09 23:48:48', 'is_default' => '1'],
            ['id' => '4', 'name' => 'CSS', 'description' => null, 'created_at' => '2024-04-09 23:48:48', 'updated_at' => '2024-04-09 23:48:48', 'is_default' => '1'],
            ['id' => '5', 'name' => 'Javascipt', 'description' => null, 'created_at' => '2024-04-09 23:48:48', 'updated_at' => '2024-04-09 23:48:48', 'is_default' => '1'],
            ['id' => '6', 'name' => 'Java', 'description' => null, 'created_at' => '2024-04-09 23:48:48', 'updated_at' => '2024-04-09 23:48:48', 'is_default' => '1'],
            ['id' => '7', 'name' => 'Python', 'description' => null, 'created_at' => '2024-04-09 23:48:48', 'updated_at' => '2024-04-09 23:48:48', 'is_default' => '1'],
            ['id' => '8', 'name' => 'Ruby', 'description' => null, 'created_at' => '2024-04-09 23:48:48', 'updated_at' => '2024-04-09 23:48:48', 'is_default' => '1'],
            ['id' => '9', 'name' => 'Android', 'description' => null, 'created_at' => '2024-04-09 23:48:48', 'updated_at' => '2024-04-09 23:48:48', 'is_default' => '1'],
        ];
        
        if (empty($data)) {
            $this->command->info('⚠️  No data to seed for tags');
            return;
        }
        
        // Insert data
        DB::table('tags')->insert($data);
        
        $this->command->info('✅ Seeded ' . count($data) . ' tags records');
    }
}