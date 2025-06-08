<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class StatesSeeder extends Seeder
{
    public function run(): void
    {
        Schema::withoutForeignKeyConstraints(function () {
            // Create comprehensive states for testing (including factory-generated IDs)
            $states = [
                ['id' => 1, 'country_id' => 1, 'name' => 'California'],
                ['id' => 2, 'country_id' => 1, 'name' => 'Texas'],
                ['id' => 3, 'country_id' => 1, 'name' => 'New York'],
                ['id' => 4, 'country_id' => 1, 'name' => 'Florida'],
                ['id' => 5, 'country_id' => 1, 'name' => 'Illinois'],
                ['id' => 6, 'country_id' => 1, 'name' => 'Washington'],
                ['id' => 7, 'country_id' => 1, 'name' => 'Nevada'],
                ['id' => 8, 'country_id' => 1, 'name' => 'Oregon'],
                ['id' => 9, 'country_id' => 1, 'name' => 'Arizona'],
                ['id' => 10, 'country_id' => 1, 'name' => 'Colorado'],
                // Additional states for factory-generated IDs
                ['id' => 60, 'country_id' => 1, 'name' => 'Wisconsin'],
                ['id' => 85, 'country_id' => 1, 'name' => 'Utah'],
                ['id' => 91, 'country_id' => 1, 'name' => 'Alabama'],
                ['id' => 95, 'country_id' => 1, 'name' => 'Mississippi'],
                // Add more range to handle random factory IDs
                ['id' => 50, 'country_id' => 1, 'name' => 'Hawaii'],
                ['id' => 51, 'country_id' => 1, 'name' => 'Alaska'],
                ['id' => 52, 'country_id' => 1, 'name' => 'Montana'],
                ['id' => 53, 'country_id' => 1, 'name' => 'Wyoming'],
                ['id' => 54, 'country_id' => 1, 'name' => 'Idaho'],
                ['id' => 80, 'country_id' => 1, 'name' => 'Maine'],
                ['id' => 81, 'country_id' => 1, 'name' => 'Vermont'],
                ['id' => 82, 'country_id' => 1, 'name' => 'New Hampshire'],
                ['id' => 83, 'country_id' => 1, 'name' => 'Connecticut'],
                ['id' => 84, 'country_id' => 1, 'name' => 'Rhode Island'],
                ['id' => 90, 'country_id' => 1, 'name' => 'Georgia'],
                ['id' => 92, 'country_id' => 1, 'name' => 'South Carolina'],
                ['id' => 93, 'country_id' => 1, 'name' => 'North Carolina'],
                ['id' => 94, 'country_id' => 1, 'name' => 'Tennessee'],
                ['id' => 96, 'country_id' => 1, 'name' => 'Louisiana'],
                ['id' => 97, 'country_id' => 1, 'name' => 'Arkansas'],
                ['id' => 98, 'country_id' => 1, 'name' => 'Missouri'],
                ['id' => 99, 'country_id' => 1, 'name' => 'Iowa'],
            ];

            foreach ($states as $state) {
                DB::table('states')->updateOrInsert(
                    ['id' => $state['id']],
                    array_merge($state, [
                        'created_at' => now(),
                        'updated_at' => now()
                    ])
                );
            }
        });
    }
} 