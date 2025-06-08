<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class IndustriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->command->info('🌱 Seeding industries...');
        
        // Clear existing data
        DB::table('industries')->truncate();
        
        $data = [
            ['id' => '1', 'name' => 'Manufacturing', 'description' => 'Manufacturing is the production of products for use or sale using labor and machines', 'created_at' => '2024-04-09 23:48:46', 'updated_at' => '2024-04-09 23:48:46', 'is_default' => '1'],
            ['id' => '2', 'name' => 'Advertising', 'description' => 'Advertising is a marketing communication that employs an openly sponsored, non-personal message to promote or sell a product, service or idea.', 'created_at' => '2024-04-09 23:48:46', 'updated_at' => '2024-04-09 23:48:46', 'is_default' => '1'],
            ['id' => '3', 'name' => 'Technology', 'description' => 'Technology is the sum of techniques, skills, methods, and processes used in the production of goods or services or in the accomplishment of objectives', 'created_at' => '2024-04-09 23:48:46', 'updated_at' => '2024-04-09 23:48:46', 'is_default' => '1'],
            ['id' => '4', 'name' => 'Marketing', 'description' => 'Marketing is the study and management of exchange relationships.', 'created_at' => '2024-04-09 23:48:46', 'updated_at' => '2024-04-09 23:48:46', 'is_default' => '1'],
            ['id' => '5', 'name' => 'Sales', 'description' => 'Sales are activities related to selling or the number of goods or services sold in a given targeted time period.', 'created_at' => '2024-04-09 23:48:46', 'updated_at' => '2024-04-09 23:48:46', 'is_default' => '1'],
        ];
        
        if (empty($data)) {
            $this->command->info('⚠️  No data to seed for industries');
            return;
        }
        
        // Insert data
        DB::table('industries')->insert($data);
        
        $this->command->info('✅ Seeded ' . count($data) . ' industries records');
    }
}