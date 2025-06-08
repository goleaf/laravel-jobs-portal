<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Industry;
use App\Models\CompanySize;
use App\Models\FunctionalArea;
use App\Models\CareerLevel;
use App\Models\SalaryCurrency;
use App\Models\SalaryPeriod;
use App\Models\JobType;
use App\Models\JobShift;
use App\Models\RequiredDegreeLevel;
use App\Models\MaritalStatus;
use App\Models\Language;
use App\Models\OwnerShipType;

class TestFactoriesSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🧪 Testing individual factories...');

        try {
            $this->command->info('Testing Industry factory...');
            Industry::factory(5)->create();
            $this->command->info('✅ Industry factory: OK');
        } catch (\Exception $e) {
            $this->command->error('❌ Industry factory failed: ' . $e->getMessage());
        }

        try {
            $this->command->info('Testing CompanySize factory...');
            CompanySize::factory(5)->create();
            $this->command->info('✅ CompanySize factory: OK');
        } catch (\Exception $e) {
            $this->command->error('❌ CompanySize factory failed: ' . $e->getMessage());
        }

        try {
            $this->command->info('Testing FunctionalArea factory...');
            FunctionalArea::factory(5)->create();
            $this->command->info('✅ FunctionalArea factory: OK');
        } catch (\Exception $e) {
            $this->command->error('❌ FunctionalArea factory failed: ' . $e->getMessage());
        }

        try {
            $this->command->info('Testing CareerLevel factory...');
            CareerLevel::factory(5)->create();
            $this->command->info('✅ CareerLevel factory: OK');
        } catch (\Exception $e) {
            $this->command->error('❌ CareerLevel factory failed: ' . $e->getMessage());
        }

        try {
            $this->command->info('Testing SalaryCurrency factory...');
            SalaryCurrency::factory(5)->create();
            $this->command->info('✅ SalaryCurrency factory: OK');
        } catch (\Exception $e) {
            $this->command->error('❌ SalaryCurrency factory failed: ' . $e->getMessage());
        }

        try {
            $this->command->info('Testing SalaryPeriod factory...');
            SalaryPeriod::factory(5)->create();
            $this->command->info('✅ SalaryPeriod factory: OK');
        } catch (\Exception $e) {
            $this->command->error('❌ SalaryPeriod factory failed: ' . $e->getMessage());
        }

        try {
            $this->command->info('Testing JobType factory...');
            JobType::factory(5)->create();
            $this->command->info('✅ JobType factory: OK');
        } catch (\Exception $e) {
            $this->command->error('❌ JobType factory failed: ' . $e->getMessage());
        }

        try {
            $this->command->info('Testing JobShift factory...');
            JobShift::factory(5)->create();
            $this->command->info('✅ JobShift factory: OK');
        } catch (\Exception $e) {
            $this->command->error('❌ JobShift factory failed: ' . $e->getMessage());
        }

        try {
            $this->command->info('Testing RequiredDegreeLevel factory...');
            RequiredDegreeLevel::factory(5)->create();
            $this->command->info('✅ RequiredDegreeLevel factory: OK');
        } catch (\Exception $e) {
            $this->command->error('❌ RequiredDegreeLevel factory failed: ' . $e->getMessage());
        }

        try {
            $this->command->info('Testing MaritalStatus factory...');
            MaritalStatus::factory(5)->create();
            $this->command->info('✅ MaritalStatus factory: OK');
        } catch (\Exception $e) {
            $this->command->error('❌ MaritalStatus factory failed: ' . $e->getMessage());
        }

        try {
            $this->command->info('Testing Language factory...');
            Language::factory(5)->create();
            $this->command->info('✅ Language factory: OK');
        } catch (\Exception $e) {
            $this->command->error('❌ Language factory failed: ' . $e->getMessage());
        }

        try {
            $this->command->info('Testing OwnerShipType factory...');
            OwnerShipType::factory(5)->create();
            $this->command->info('✅ OwnerShipType factory: OK');
        } catch (\Exception $e) {
            $this->command->error('❌ OwnerShipType factory failed: ' . $e->getMessage());
        }

        $this->command->info('🏁 Factory testing complete!');
    }
} 