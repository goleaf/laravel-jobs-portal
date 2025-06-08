<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CitiesSeeder extends Seeder
{
    public function run(): void
    {
        Schema::withoutForeignKeyConstraints(function () {
            // Clear existing cities
            DB::table('cities')->truncate();
            
            // Cities data from infy-jobs.sql - mapped to our existing states
            $cities = [
                // Andaman and Nicobar Islands (state_id = 1)
                ['id' => 1, 'state_id' => 1, 'name' => 'Port Blair'],
                ['id' => 2, 'state_id' => 1, 'name' => 'Garacharma'],
                ['id' => 3, 'state_id' => 1, 'name' => 'Bombuflat'],
                ['id' => 4, 'state_id' => 1, 'name' => 'Rangat'],
                
                // California (state_id = 1 remapped for our US states)
                ['id' => 5, 'state_id' => 1, 'name' => 'Los Angeles'],
                ['id' => 6, 'state_id' => 1, 'name' => 'San Francisco'],
                ['id' => 7, 'state_id' => 1, 'name' => 'San Diego'],
                ['id' => 8, 'state_id' => 1, 'name' => 'Sacramento'],
                
                // Texas (state_id = 2)
                ['id' => 9, 'state_id' => 2, 'name' => 'Houston'],
                ['id' => 10, 'state_id' => 2, 'name' => 'Dallas'],
                ['id' => 11, 'state_id' => 2, 'name' => 'Austin'],
                ['id' => 12, 'state_id' => 2, 'name' => 'San Antonio'],
                
                // New York (state_id = 3)
                ['id' => 13, 'state_id' => 3, 'name' => 'New York City'],
                ['id' => 14, 'state_id' => 3, 'name' => 'Buffalo'],
                ['id' => 15, 'state_id' => 3, 'name' => 'Rochester'],
                ['id' => 16, 'state_id' => 3, 'name' => 'Syracuse'],
                
                // Florida (state_id = 4)
                ['id' => 17, 'state_id' => 4, 'name' => 'Miami'],
                ['id' => 18, 'state_id' => 4, 'name' => 'Orlando'],
                ['id' => 19, 'state_id' => 4, 'name' => 'Tampa'],
                ['id' => 20, 'state_id' => 4, 'name' => 'Jacksonville'],
                
                // Illinois (state_id = 5)
                ['id' => 21, 'state_id' => 5, 'name' => 'Chicago'],
                ['id' => 22, 'state_id' => 5, 'name' => 'Springfield'],
                ['id' => 23, 'state_id' => 5, 'name' => 'Rockford'],
                ['id' => 24, 'state_id' => 5, 'name' => 'Peoria'],
                
                // Washington (state_id = 6)
                ['id' => 25, 'state_id' => 6, 'name' => 'Seattle'],
                ['id' => 26, 'state_id' => 6, 'name' => 'Spokane'],
                ['id' => 27, 'state_id' => 6, 'name' => 'Tacoma'],
                ['id' => 28, 'state_id' => 6, 'name' => 'Vancouver'],
                
                // Add cities for all our state IDs to prevent foreign key errors
                ['id' => 29, 'state_id' => 50, 'name' => 'Honolulu'],
                ['id' => 30, 'state_id' => 51, 'name' => 'Anchorage'],
                ['id' => 31, 'state_id' => 52, 'name' => 'Billings'],
                ['id' => 32, 'state_id' => 53, 'name' => 'Cheyenne'],
                ['id' => 33, 'state_id' => 54, 'name' => 'Boise'],
                ['id' => 34, 'state_id' => 60, 'name' => 'Milwaukee'],
                ['id' => 35, 'state_id' => 80, 'name' => 'Portland'],
                ['id' => 36, 'state_id' => 81, 'name' => 'Burlington'],
                ['id' => 37, 'state_id' => 82, 'name' => 'Manchester'],
                ['id' => 38, 'state_id' => 83, 'name' => 'Hartford'],
                ['id' => 39, 'state_id' => 84, 'name' => 'Providence'],
                ['id' => 40, 'state_id' => 85, 'name' => 'Salt Lake City'],
                ['id' => 41, 'state_id' => 90, 'name' => 'Atlanta'],
                ['id' => 42, 'state_id' => 91, 'name' => 'Birmingham'],
                ['id' => 43, 'state_id' => 92, 'name' => 'Charleston'],
                ['id' => 44, 'state_id' => 93, 'name' => 'Charlotte'],
                ['id' => 45, 'state_id' => 94, 'name' => 'Nashville'],
                ['id' => 46, 'state_id' => 95, 'name' => 'Jackson'],
                ['id' => 47, 'state_id' => 96, 'name' => 'New Orleans'],
                ['id' => 48, 'state_id' => 97, 'name' => 'Little Rock'],
                ['id' => 49, 'state_id' => 98, 'name' => 'Kansas City'],
                ['id' => 50, 'state_id' => 99, 'name' => 'Des Moines'],
            ];

            foreach ($cities as $city) {
                DB::table('cities')->updateOrInsert(
                    ['id' => $city['id']],
                    array_merge($city, [
                        'created_at' => now(),
                        'updated_at' => now()
                    ])
                );
            }
        });
    }
} 