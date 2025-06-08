<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class OwnershipTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->command->info('🌱 Seeding ownership_types...');
        
        // Clear existing data
        DB::table('ownership_types')->truncate();
        
        $data = [
            ['id' => '1', 'name' => 'Sole Proprietorship', 'description' => 'The sole proprietorship is the simplest business form under which one can operate a business.', 'created_at' => '2024-04-09 23:48:46', 'updated_at' => '2024-04-09 23:48:46', 'is_default' => '1'],
            ['id' => '2', 'name' => 'Public', 'description' => 'A company whose shares are traded freely on a stock exchange.', 'created_at' => '2024-04-09 23:48:46', 'updated_at' => '2024-04-09 23:48:46', 'is_default' => '1'],
            ['id' => '3', 'name' => 'Private', 'description' => 'A company whose shares may not be offered to the public for sale and which operates under legal requirements less strict than those for a public company.', 'created_at' => '2024-04-09 23:48:46', 'updated_at' => '2024-04-09 23:48:46', 'is_default' => '1'],
            ['id' => '4', 'name' => 'Government', 'description' => 'A government company is a company in which 51% or more of the paid-up capital is held by the Government or State Government.', 'created_at' => '2024-04-09 23:48:46', 'updated_at' => '2024-04-09 23:48:46', 'is_default' => '1'],
            ['id' => '5', 'name' => 'NGO', 'description' => 'A non-profit organization that operates independently of any government, typically one whose purpose is to address a social or political issue.', 'created_at' => '2024-04-09 23:48:46', 'updated_at' => '2024-04-09 23:48:46', 'is_default' => '1'],
        ];
        
        if (empty($data)) {
            $this->command->info('⚠️  No data to seed for ownership_types');
            return;
        }
        
        // Insert data
        DB::table('ownership_types')->insert($data);
        
        $this->command->info('✅ Seeded ' . count($data) . ' ownership_types records');
    }
}