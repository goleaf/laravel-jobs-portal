<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class WorkingDatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run()
    {
        $this->command->info('🚀 Starting working database seeding...');

        // Use Enhanced pattern: Disable foreign key constraints during seeding
        Schema::withoutForeignKeyConstraints(function () {
            $this->seedInOrder();
        });

        $this->command->info('✅ Working database seeding completed successfully!');
    }

    private function seedInOrder()
    {
        // Phase 1: Create essential system data
        $this->command->info('🔧 Phase 1: Essential system data');

        // Use existing seeders that are available
        $availableSeeders = [
            // Core system configuration
            DefaultLanguageSeeder::class,
            DefaultEnvSettingSeeder::class,
            CreateFrontSettingSeeder::class,
            SettingsTableSeeder::class,
            EmailTemplateSeeder::class,

            // Geographic data (for foreign key dependencies)
            CountriesSeeder::class,
            StatesSeeder::class,
            CitiesSeeder::class,

            // Job portal master data
            SkillsSeeder::class,
            CreateDefaultIndustriesSeeder::class,
            BasicJobsSeeder::class,
            CreateDefaultCareerLevelSeeder::class,
            CreateDefaultDegreeLevelSeeder::class,
            CreateDefaultFunctionalAreaSeeder::class,
            CreateDefaultJobTypeSeeder::class,
            CreateDefaultJobShiftSeeder::class,
            CreateDefaultSalaryPeriodSeeder::class,
            SalaryCurrencySeeder::class,
            DefaultCompanySizeSeeder::class,
            CreateDefaultOwnerShipTypeSeeder::class,
            MaritalStatusTableSeeder::class,
            SkillTableSeeder::class,
            CreateDefaultTagSeeder::class,
            JobCategorySeeder::class,

            // User management
            DefaultRoleSeeder::class,
            UsersSeeder::class, // Our fixed seeder with admin user

            // Content
            CreateDefaultPostCategorySeeder::class,
            CreateDefaultPostSeeder::class, // Should work now with user ID 1

            // Additional setup
            CreateNotificationSettingSeeder::class,
            DefaultTrialPlanSeeder::class,
        ];

        foreach ($availableSeeders as $seeder) {
            try {
                $this->call($seeder);
            } catch (\Exception $e) {
                $this->command->error("Failed to run {$seeder}: ".$e->getMessage());
                $this->command->info('Continuing with next seeder...');
            }
        }
    }
}
