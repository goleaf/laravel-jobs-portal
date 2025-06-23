<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class RefactoredDatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run()
    {
        $this->command->info('🚀 Starting refactored database seeding with Enhanced patterns...');

        // Use Enhanced pattern: Disable foreign key constraints during seeding
        Schema::withoutForeignKeyConstraints(function () {
            $this->seedInOrder();
        });

        $this->command->info('✅ Refactored database seeding completed successfully!');
    }

    private function seedInOrder()
    {
        // Phase 1: Base reference data (no foreign keys)
        $this->command->info('🔧 Phase 1: Base reference data');
        $this->call([
            CountriesSeeder::class,
            StatesSeeder::class,
            CitiesSeeder::class,
            SalaryCurrenciesSeeder::class,
            LanguagesSeeder::class,
            MaritalStatusSeeder::class,
            CompanySizesSeeder::class,
            OwnershipTypesSeeder::class,
        ]);

        // Phase 2: Job and system configuration
        $this->command->info('🔧 Phase 2: Job and system configuration');
        $this->call([
            IndustriesSeeder::class,
            FunctionalAreasSeeder::class,
            CareerLevelsSeeder::class,
            RequiredDegreeLevelsSeeder::class,
            JobTypesSeeder::class,
            JobShiftsSeeder::class,
            SalaryPeriodsSeeder::class,
            JobCategoriesSeeder::class,
            SkillsSeeder::class,
            TagsSeeder::class,
        ]);

        // Phase 3: System configuration and templates
        $this->command->info('🔧 Phase 3: System configuration');
        $this->call([
            SettingsSeeder::class,
            EnvSettingsSeeder::class,
            FrontSettingsSeeder::class,
            EmailTemplatesSeeder::class,
            CmsServicesSeeder::class,
            NotificationSettingsSeeder::class,
            PlansSeeder::class,
        ]);

        // Phase 4: Users and roles (required for content with foreign keys)
        $this->command->info('🔧 Phase 4: Users and authentication');
        $this->call([
            RolesSeeder::class,
            UsersSeeder::class, // This creates user with ID 1
        ]);

        // Phase 5: Content with foreign key dependencies
        $this->command->info('🔧 Phase 5: Content with dependencies');
        $this->call([
            PostCategoriesSeeder::class,
            PostsSeeder::class, // Now safe - user with ID 1 exists
        ]);
    }
}
