<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Country;
use App\Models\State;
use App\Models\City;
use App\Models\Industry;
use App\Models\CompanySize;
use App\Models\FunctionalArea;

class DebugSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🔍 Debug: Checking initial state...');
        $this->command->info('Industries: ' . Industry::count());
        $this->command->info('CompanySizes: ' . CompanySize::count());
        $this->command->info('FunctionalAreas: ' . FunctionalArea::count());
        
        $this->command->info('🔍 Debug: Creating 1 country...');
        $country = Country::factory(1)->create();
        
        $this->command->info('🔍 Debug: After country creation...');
        $this->command->info('Industries: ' . Industry::count());
        $this->command->info('CompanySizes: ' . CompanySize::count());
        $this->command->info('FunctionalAreas: ' . FunctionalArea::count());
        
        $this->command->info('🔍 Debug: Creating 1 state...');
        $state = State::factory(1)->create(['country_id' => $country->first()->id]);
        
        $this->command->info('🔍 Debug: After state creation...');
        $this->command->info('Industries: ' . Industry::count());
        $this->command->info('CompanySizes: ' . CompanySize::count());
        $this->command->info('FunctionalAreas: ' . FunctionalArea::count());
        
        $this->command->info('🔍 Debug: Creating 1 city...');
        $city = City::factory(1)->create(['state_id' => $state->first()->id]);
        
        $this->command->info('🔍 Debug: After city creation...');
        $this->command->info('Industries: ' . Industry::count());
        $this->command->info('CompanySizes: ' . CompanySize::count());
        $this->command->info('FunctionalAreas: ' . FunctionalArea::count());
        
        $this->command->info('✅ Debug complete!');
    }
} 