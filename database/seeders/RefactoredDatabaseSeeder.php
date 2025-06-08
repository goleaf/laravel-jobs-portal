<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RefactoredDatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->command->info('🚀 Starting refactored database seeding...');
        
        // Disable foreign key checks (MySQL/MariaDB only)
        if (DB::getDriverName() === 'mysql') {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        }
        
        $this->call(CareerLevelsSeeder::class);
        $this->call(CountriesSeeder::class);
        $this->call(StatesSeeder::class);
        $this->call(CitiesSeeder::class);
        $this->call(CmsServicesSeeder::class);
        $this->call(CompanySizesSeeder::class);
        $this->call(EmailTemplatesSeeder::class);
        $this->call(EnvSettingsSeeder::class);
        $this->call(FrontSettingsSeeder::class);
        $this->call(FunctionalAreasSeeder::class);
        $this->call(IndustriesSeeder::class);
        $this->call(JobCategoriesSeeder::class);
        $this->call(JobShiftsSeeder::class);
        $this->call(JobTypesSeeder::class);
        $this->call(LanguagesSeeder::class);
        $this->call(MaritalStatusSeeder::class);
        $this->call(NotificationSettingsSeeder::class);
        $this->call(OwnershipTypesSeeder::class);
        $this->call(PlansSeeder::class);
        $this->call(PostsSeeder::class);
        $this->call(PostCategoriesSeeder::class);
        $this->call(RequiredDegreeLevelsSeeder::class);
        $this->call(RolesSeeder::class);
        $this->call(SalaryCurrenciesSeeder::class);
        $this->call(SalaryPeriodsSeeder::class);
        $this->call(SettingsSeeder::class);
        $this->call(SkillsSeeder::class);
        $this->call(TagsSeeder::class);
        $this->call(UsersSeeder::class);
        
        // Re-enable foreign key checks (MySQL/MariaDB only)
        if (DB::getDriverName() === 'mysql') {
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        }
        
        $this->command->info('✅ Refactored database seeding completed!');
    }
}