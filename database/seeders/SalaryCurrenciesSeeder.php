<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SalaryCurrenciesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->command->info('🌱 Seeding salary_currencies...');
        
        // Clear existing data
        DB::table('salary_currencies')->truncate();
        
        $data = [
            ['id' => '1', 'currency_name' => 'AED United Arab Emirates Dirham', 'created_at' => '2024-04-09 23:48:43', 'updated_at' => '2024-04-09 23:48:43', 'is_default' => '1', 'currency_icon' => 'د.إ', 'currency_code' => 'AED'],
            ['id' => '2', 'currency_name' => 'AFN Afghanistan Afghani', 'created_at' => '2024-04-09 23:48:43', 'updated_at' => '2024-04-09 23:48:43', 'is_default' => '1', 'currency_icon' => '؋', 'currency_code' => 'AFN'],
            ['id' => '3', 'currency_name' => 'ALL Albania Lek', 'created_at' => '2024-04-09 23:48:43', 'updated_at' => '2024-04-09 23:48:43', 'is_default' => '1', 'currency_icon' => 'Lek', 'currency_code' => 'ALL'],
            ['id' => '4', 'currency_name' => 'AMD Armenian Dram', 'created_at' => '2024-04-09 23:48:43', 'updated_at' => '2024-04-09 23:48:43', 'is_default' => '1', 'currency_icon' => '֏', 'currency_code' => 'AMD'],
            ['id' => '5', 'currency_name' => 'ANG Netherlands Antilles Guilder', 'created_at' => '2024-04-09 23:48:43', 'updated_at' => '2024-04-09 23:48:43', 'is_default' => '1', 'currency_icon' => 'ƒ', 'currency_code' => 'ANG'],
            ['id' => '6', 'currency_name' => 'AOA Angola kwanza', 'created_at' => '2024-04-09 23:48:43', 'updated_at' => '2024-04-09 23:48:43', 'is_default' => '1', 'currency_icon' => 'Kz', 'currency_code' => 'AOA'],
            ['id' => '7', 'currency_name' => 'ARS Argentina Peso', 'created_at' => '2024-04-09 23:48:43', 'updated_at' => '2024-04-09 23:48:43', 'is_default' => '1', 'currency_icon' => '$', 'currency_code' => 'ARS'],
            ['id' => '8', 'currency_name' => 'AWG Aruba Guilder', 'created_at' => '2024-04-09 23:48:43', 'updated_at' => '2024-04-09 23:48:43', 'is_default' => '1', 'currency_icon' => 'ƒ', 'currency_code' => 'AWG'],
            ['id' => '9', 'currency_name' => 'AZN Azerbaijan Manat', 'created_at' => '2024-04-09 23:48:43', 'updated_at' => '2024-04-09 23:48:43', 'is_default' => '1', 'currency_icon' => '₼', 'currency_code' => 'AZN'],
            ['id' => '10', 'currency_name' => 'BAM Bosnia and Herzegovina Convertible Marka', 'created_at' => '2024-04-09 23:48:43', 'updated_at' => '2024-04-09 23:48:43', 'is_default' => '1', 'currency_icon' => 'KM', 'currency_code' => 'BAM'],
        ];
        
        if (empty($data)) {
            $this->command->info('⚠️  No data to seed for salary_currencies');
            return;
        }
        
        // Insert data
        DB::table('salary_currencies')->insert($data);
        
        $this->command->info('✅ Seeded ' . count($data) . ' salary_currencies records');
    }
}