<?php

namespace Database\Seeders;

use App\Models\CareerLevel;
use App\Models\CompanySize;
use App\Models\FunctionalArea;
use App\Models\Industry;
use App\Models\SalaryCurrency;
use App\Models\SalaryPeriod;
use Illuminate\Database\Seeder;

class SimpleSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🧪 Testing master data creation...');

        // Test each factory individually with small counts
        try {
            $this->command->info('Creating 3 industries...');
            Industry::factory(3)->create();
            $this->command->info('✅ Industries created: '.Industry::count());
        } catch (\Exception $e) {
            $this->command->error('❌ Industry creation failed: '.$e->getMessage());
        }

        try {
            $this->command->info('Creating 3 company sizes...');
            CompanySize::factory(3)->create();
            $this->command->info('✅ Company sizes created: '.CompanySize::count());
        } catch (\Exception $e) {
            $this->command->error('❌ Company size creation failed: '.$e->getMessage());
        }

        try {
            $this->command->info('Creating 3 functional areas...');
            FunctionalArea::factory(3)->create();
            $this->command->info('✅ Functional areas created: '.FunctionalArea::count());
        } catch (\Exception $e) {
            $this->command->error('❌ Functional area creation failed: '.$e->getMessage());
        }

        try {
            $this->command->info('Creating 3 career levels...');
            CareerLevel::factory(3)->create();
            $this->command->info('✅ Career levels created: '.CareerLevel::count());
        } catch (\Exception $e) {
            $this->command->error('❌ Career level creation failed: '.$e->getMessage());
        }

        try {
            $this->command->info('Creating 3 salary currencies...');
            SalaryCurrency::factory(3)->create();
            $this->command->info('✅ Salary currencies created: '.SalaryCurrency::count());
        } catch (\Exception $e) {
            $this->command->error('❌ Salary currency creation failed: '.$e->getMessage());
        }

        try {
            $this->command->info('Creating 3 salary periods...');
            SalaryPeriod::factory(3)->create();
            $this->command->info('✅ Salary periods created: '.SalaryPeriod::count());
        } catch (\Exception $e) {
            $this->command->error('❌ Salary period creation failed: '.$e->getMessage());
        }

        $this->command->info('✅ Simple seeder complete!');
    }
}
