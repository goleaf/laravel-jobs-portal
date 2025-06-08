<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->command->info('🌱 Seeding settings...');
        
        // Clear existing data
        DB::table('settings')->truncate();
        
        $data = [
            ['id' => '1', 'key' => 'application_name', 'value' => 'InfyOmLabs', 'created_at' => '2024-04-09 23:48:45', 'updated_at' => '2024-04-09 23:48:45'],
            ['id' => '2', 'key' => 'logo', 'value' => 'assets/img/infyom-logo.png', 'created_at' => '2024-04-09 23:48:45', 'updated_at' => '2024-04-09 23:48:45'],
            ['id' => '3', 'key' => 'favicon', 'value' => 'favicon.ico', 'created_at' => '2024-04-09 23:48:45', 'updated_at' => '2024-04-09 23:48:45'],
            ['id' => '4', 'key' => 'company_description', 'value' => 'Leading Laravel Development Company of India', 'created_at' => '2024-04-09 23:48:45', 'updated_at' => '2024-04-09 23:48:45'],
            ['id' => '5', 'key' => 'address', 'value' => '446, Tulsi Arcade, Nr. Sudama Chowk, Mota Varachha, Surat - 394101, Gujarat, India', 'created_at' => '2024-04-09 23:48:45', 'updated_at' => '2024-04-09 23:48:45'],
            ['id' => '6', 'key' => 'phone', 'value' => '70963 36561', 'created_at' => '2024-04-09 23:48:45', 'updated_at' => '2024-04-09 23:48:49'],
            ['id' => '7', 'key' => 'email', 'value' => 'contact@infyom.in', 'created_at' => '2024-04-09 23:48:45', 'updated_at' => '2024-04-09 23:48:45'],
            ['id' => '8', 'key' => 'facebook_url', 'value' => 'https://www.facebook.com/infyom/', 'created_at' => '2024-04-09 23:48:45', 'updated_at' => '2024-04-09 23:48:45'],
            ['id' => '9', 'key' => 'twitter_url', 'value' => 'https://twitter.com/infyom?lang=en', 'created_at' => '2024-04-09 23:48:45', 'updated_at' => '2024-04-09 23:48:45'],
            ['id' => '10', 'key' => 'google_plus_url', 'value' => 'https://infyom.com/', 'created_at' => '2024-04-09 23:48:46', 'updated_at' => '2024-04-09 23:48:46'],
        ];
        
        if (empty($data)) {
            $this->command->info('⚠️  No data to seed for settings');
            return;
        }
        
        // Insert data
        DB::table('settings')->insert($data);
        
        $this->command->info('✅ Seeded ' . count($data) . ' settings records');
    }
}