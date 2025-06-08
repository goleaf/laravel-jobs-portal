<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->command->info('🌱 Seeding users...');
        
        // Clear existing data
        DB::table('users')->truncate();
        
        $data = [
            ['id' => '1', 'first_name' => 'Super', 'last_name' => 'Admin', 'email' => 'admin@infyjobs.com', 'phone' => '7878454512', 'email_verified_at' => '2024-04-09 23:48:44', 'password' => '$2y$10$snJd.U4iefPnkU1ABW3vMOi1v5fBX8se40EJAzl0cjVFlid7AfyuK', 'dob' => null, 'gender' => null, 'country_id' => null, 'state_id' => null, 'city_id' => null, 'is_active' => '1', 'is_verified' => '1', 'owner_id' => null, 'owner_type' => null, 'language' => 'en', 'profile_views' => '0', 'remember_token' => null, 'theme_mode' => '0', 'created_at' => '2024-04-09 23:48:44', 'updated_at' => '2024-04-09 23:48:44', 'facebook_url' => null, 'twitter_url' => null, 'linkedin_url' => null, 'google_plus_url' => null, 'pinterest_url' => null, 'is_default' => '1', 'stripe_id' => null, 'region_code' => null],
        ];
        
        if (empty($data)) {
            $this->command->info('⚠️  No data to seed for users');
            return;
        }
        
        // Insert data
        DB::table('users')->insert($data);
        
        $this->command->info('✅ Seeded ' . count($data) . ' users records');
    }
}