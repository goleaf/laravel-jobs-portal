<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PlansSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->command->info('🌱 Seeding plans...');
        
        // Clear existing data
        DB::table('plans')->truncate();
        
        $data = [
            ['id' => '1', 'name' => 'Trial Plan', 'stripe_plan_id' => null, 'allowed_jobs' => '1', 'amount' => '0', 'salary_currency_id' => '1', 'is_trial_plan' => '1', 'created_at' => '2024-04-09 23:48:43', 'updated_at' => '2024-04-09 23:48:43', 'deleted_at' => null],
        ];
        
        if (empty($data)) {
            $this->command->info('⚠️  No data to seed for plans');
            return;
        }
        
        // Insert data
        DB::table('plans')->insert($data);
        
        $this->command->info('✅ Seeded ' . count($data) . ' plans records');
    }
}