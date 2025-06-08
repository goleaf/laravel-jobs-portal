<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class UsersSeeder extends Seeder
{
    public function run()
    {
        $this->command->info('🌱 Seeding users...');
        
        // Create admin user with ID 1 for foreign key references
        $data = [
            [
                'id' => 1,
                'first_name' => 'Admin',
                'last_name' => 'User',
                'name' => 'Admin User',
                'email' => 'admin@jobportal.com',
                'email_verified_at' => Carbon::now(),
                'password' => Hash::make('password'),
                'is_active' => 1,
                'is_verified' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'id' => 2,
                'first_name' => 'John',
                'last_name' => 'Doe',
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'email_verified_at' => Carbon::now(),
                'password' => Hash::make('password'),
                'is_active' => 1,
                'is_verified' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'id' => 3,
                'first_name' => 'Jane',
                'last_name' => 'Smith',
                'name' => 'Jane Smith',
                'email' => 'jane@example.com',
                'email_verified_at' => Carbon::now(),
                'password' => Hash::make('password'),
                'is_active' => 1,
                'is_verified' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]
        ];

        // Use foreign key constraints safely
        DB::table('users')->insert($data);
        
        $this->command->info('✅ Seeded ' . count($data) . ' users records');
    }
} 