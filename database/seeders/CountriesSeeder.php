<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CountriesSeeder extends Seeder
{
    public function run(): void
    {
        Schema::withoutForeignKeyConstraints(function () {
            // Create basic countries for testing
            $countries = [
                ['id' => 1, 'name' => 'United States', 'short_code' => 'US', 'phone_code' => '+1'],
                ['id' => 2, 'name' => 'Canada', 'short_code' => 'CA', 'phone_code' => '+1'],
                ['id' => 3, 'name' => 'United Kingdom', 'short_code' => 'GB', 'phone_code' => '+44'],
                ['id' => 4, 'name' => 'Australia', 'short_code' => 'AU', 'phone_code' => '+61'],
                ['id' => 5, 'name' => 'Germany', 'short_code' => 'DE', 'phone_code' => '+49'],
            ];

            foreach ($countries as $country) {
                DB::table('countries')->updateOrInsert(
                    ['id' => $country['id']],
                    array_merge($country, [
                        'created_at' => now(),
                        'updated_at' => now()
                    ])
                );
            }
        });
    }
} 