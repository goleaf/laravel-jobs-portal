<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class StatesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->command->info('🌱 Seeding states...');
        
        // Clear existing data
        DB::table('states')->truncate();
        
        $data = [
            ['id' => '1', 'country_id' => '1', 'name' => 'Andaman and Nicobar Islands', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['id' => '2', 'country_id' => '1', 'name' => 'Andhra Pradesh', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['id' => '3', 'country_id' => '1', 'name' => 'Arunachal Pradesh', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['id' => '4', 'country_id' => '1', 'name' => 'Assam', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['id' => '5', 'country_id' => '1', 'name' => 'Bihar', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['id' => '6', 'country_id' => '1', 'name' => 'Chandigarh', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['id' => '7', 'country_id' => '1', 'name' => 'Chhattisgarh', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['id' => '8', 'country_id' => '1', 'name' => 'Dadra and Nagar Haveli', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['id' => '9', 'country_id' => '1', 'name' => 'Daman and Diu', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['id' => '10', 'country_id' => '1', 'name' => 'Delhi', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
        ];
        
        if (empty($data)) {
            $this->command->info('⚠️  No data to seed for states');
            return;
        }
        
        // Insert data
        DB::table('states')->insert($data);
        
        $this->command->info('✅ Seeded ' . count($data) . ' states records');
    }
}