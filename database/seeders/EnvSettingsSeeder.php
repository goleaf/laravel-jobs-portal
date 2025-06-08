<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class EnvSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->command->info('🌱 Seeding env_settings...');
        
        // Clear existing data
        DB::table('env_settings')->truncate();
        
        $data = [
            ['id' => '1', 'key' => 'facebook_app_id', 'value' => '', 'created_at' => '2024-04-09 23:48:51', 'updated_at' => '2024-04-09 23:48:51'],
            ['id' => '2', 'key' => 'facebook_app_secret', 'value' => '', 'created_at' => '2024-04-09 23:48:51', 'updated_at' => '2024-04-09 23:48:51'],
            ['id' => '3', 'key' => 'facebook_redirect', 'value' => '', 'created_at' => '2024-04-09 23:48:51', 'updated_at' => '2024-04-09 23:48:51'],
            ['id' => '4', 'key' => 'pusher_app_id', 'value' => '', 'created_at' => '2024-04-09 23:48:51', 'updated_at' => '2024-04-09 23:48:51'],
            ['id' => '5', 'key' => 'pusher_app_key', 'value' => '', 'created_at' => '2024-04-09 23:48:51', 'updated_at' => '2024-04-09 23:48:51'],
            ['id' => '6', 'key' => 'pusher_app_secret', 'value' => '', 'created_at' => '2024-04-09 23:48:51', 'updated_at' => '2024-04-09 23:48:51'],
            ['id' => '7', 'key' => 'pusher_app_cluster', 'value' => '', 'created_at' => '2024-04-09 23:48:51', 'updated_at' => '2024-04-09 23:48:51'],
            ['id' => '8', 'key' => 'stripe_key', 'value' => '', 'created_at' => '2024-04-09 23:48:51', 'updated_at' => '2024-04-09 23:48:51'],
            ['id' => '9', 'key' => 'stripe_secret', 'value' => '', 'created_at' => '2024-04-09 23:48:51', 'updated_at' => '2024-04-09 23:48:51'],
            ['id' => '10', 'key' => 'stripe_webhook_key', 'value' => '', 'created_at' => '2024-04-09 23:48:51', 'updated_at' => '2024-04-09 23:48:51'],
        ];
        
        if (empty($data)) {
            $this->command->info('⚠️  No data to seed for env_settings');
            return;
        }
        
        // Insert data
        DB::table('env_settings')->insert($data);
        
        $this->command->info('✅ Seeded ' . count($data) . ' env_settings records');
    }
}