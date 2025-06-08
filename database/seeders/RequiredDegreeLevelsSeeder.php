<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RequiredDegreeLevelsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->command->info('🌱 Seeding required_degree_levels...');
        
        // Clear existing data
        DB::table('required_degree_levels')->truncate();
        
        $data = [
            ['id' => '1', 'name' => 'Associate of Arts (A.A.'],
            ['id' => '2', 'name' => 'Associate of Science (A.S.'],
            ['id' => '3', 'name' => 'Associate of Applied Science (AAS'],
            ['id' => '4', 'name' => 'Bachelor of Arts (B.A.'],
            ['id' => '5', 'name' => 'Bachelor of Science (B.S.'],
            ['id' => '6', 'name' => 'Bachelor of Fine Arts (BFA'],
            ['id' => '7', 'name' => 'Bachelor of Applied Science (BAS'],
            ['id' => '8', 'name' => 'Master of Arts (M.A.'],
            ['id' => '9', 'name' => 'Master of Science (M.S.'],
            ['id' => '10', 'name' => 'Master of Business Administration (MBA'],
        ];
        
        if (empty($data)) {
            $this->command->info('⚠️  No data to seed for required_degree_levels');
            return;
        }
        
        // Insert data
        DB::table('required_degree_levels')->insert($data);
        
        $this->command->info('✅ Seeded ' . count($data) . ' required_degree_levels records');
    }
}