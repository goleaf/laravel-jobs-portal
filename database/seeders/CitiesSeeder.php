<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CitiesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->command->info('🌱 Seeding cities...');
        
        // Clear existing data
        DB::table('cities')->truncate();
        
        $data = [
            ['id' => '1', 'state_id' => '1', 'name' => 'Bombuflat', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['id' => '2', 'state_id' => '1', 'name' => 'Garacharma', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['id' => '3', 'state_id' => '1', 'name' => 'Port Blair', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['id' => '4', 'state_id' => '1', 'name' => 'Rangat', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['id' => '5', 'state_id' => '2', 'name' => 'Addanki', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['id' => '6', 'state_id' => '2', 'name' => 'Adivivaram', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['id' => '7', 'state_id' => '2', 'name' => 'Adoni', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['id' => '8', 'state_id' => '2', 'name' => 'Aganampudi', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['id' => '9', 'state_id' => '2', 'name' => 'Ajjaram', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['id' => '10', 'state_id' => '2', 'name' => 'Akividu', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
        ];
        
        if (empty($data)) {
            $this->command->info('⚠️  No data to seed for cities');
            return;
        }
        
        // Insert data
        DB::table('cities')->insert($data);
        
        $this->command->info('✅ Seeded ' . count($data) . ' cities records');
    }
}