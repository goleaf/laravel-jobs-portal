<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SalaryCurrencySeeder extends Seeder
{
    public function run(): void
    {
        Schema::withoutForeignKeyConstraints(function () {
            // Create basic salary currencies
            $currencies = [
                ['id' => 1, 'currency_name' => 'US Dollar', 'currency_code' => 'USD', 'currency_icon' => '$'],
                ['id' => 2, 'currency_name' => 'Euro', 'currency_code' => 'EUR', 'currency_icon' => '€'],
                ['id' => 3, 'currency_name' => 'British Pound', 'currency_code' => 'GBP', 'currency_icon' => '£'],
            ];

            foreach ($currencies as $currency) {
                DB::table('salary_currencies')->updateOrInsert(
                    ['id' => $currency['id']],
                    array_merge($currency, [
                        'created_at' => now(),
                        'updated_at' => now()
                    ])
                );
            }
        });
    }
} 