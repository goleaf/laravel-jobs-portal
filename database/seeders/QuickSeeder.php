<?php

namespace Database\Seeders;

use App\Models\Candidate;
use App\Models\CareerLevel;
use App\Models\City;
use App\Models\Company;
use App\Models\CompanySize;
use App\Models\Country;
use App\Models\FunctionalArea;
use App\Models\Industry;
use App\Models\Job;
use App\Models\JobApplication;
use App\Models\JobCategory;
use App\Models\JobShift;
use App\Models\JobType;
use App\Models\Language;
use App\Models\MaritalStatus;
use App\Models\OwnerShipType;
use App\Models\Plan;
use App\Models\RequiredDegreeLevel;
use App\Models\SalaryCurrency;
use App\Models\SalaryPeriod;
use App\Models\Skill;
use App\Models\State;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class QuickSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🚀 Starting Quick Database Seeding...');

        // Disable foreign key checks for seeding
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        try {
            // Phase 1: Essential Data Only
            $this->seedEssentialData();

            // Phase 2: Small User Base
            $this->seedSmallUserBase();

            // Phase 3: Basic Content
            $this->seedBasicContent();

            $this->command->info('✅ Quick Database Seeding Complete!');
            $this->generateQuickReport();
        } catch (\Exception $e) {
            $this->command->error('❌ Seeding failed: '.$e->getMessage());

            throw $e;
        } finally {
            // Re-enable foreign key checks
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        }
    }

    private function seedEssentialData(): void
    {
        $this->command->info('📍 Seeding essential data...');

        // Countries (5 only)
        if (0 == Country::count()) {
            $countries = ['United States', 'Canada', 'United Kingdom', 'Germany', 'Australia'];
            foreach ($countries as $name) {
                Country::create(['name' => $name]);
            }
        }

        // States (2 per country)
        if (0 == State::count()) {
            Country::all()->each(function ($country) {
                for ($i = 1; $i <= 2; ++$i) {
                    State::create([
                        'name' => $country->name." State {$i}",
                        'country_id' => $country->id,
                    ]);
                }
            });
        }

        // Cities (2 per state)
        if (0 == City::count()) {
            State::all()->each(function ($state) {
                for ($i = 1; $i <= 2; ++$i) {
                    City::create([
                        'name' => $state->name." City {$i}",
                        'state_id' => $state->id,
                    ]);
                }
            });
        }

        // Master data (minimal)
        if (0 == Industry::count()) {
            $industries = ['Technology', 'Healthcare', 'Finance', 'Education', 'Manufacturing'];
            foreach ($industries as $name) {
                Industry::create(['name' => $name, 'description' => "Industry: {$name}"]);
            }
        }

        if (0 == CompanySize::count()) {
            CompanySize::factory(3)->create();
        }

        if (0 == FunctionalArea::count()) {
            FunctionalArea::factory(5)->create();
        }

        if (0 == CareerLevel::count()) {
            CareerLevel::factory(4)->create();
        }

        if (0 == SalaryCurrency::count()) {
            SalaryCurrency::factory(3)->create();
        }

        if (0 == SalaryPeriod::count()) {
            SalaryPeriod::factory(3)->create();
        }

        if (0 == JobType::count()) {
            JobType::factory(3)->create();
        }

        if (0 == JobShift::count()) {
            JobShift::factory(3)->create();
        }

        if (0 == RequiredDegreeLevel::count()) {
            RequiredDegreeLevel::factory(4)->create();
        }

        if (0 == MaritalStatus::count()) {
            MaritalStatus::factory(3)->create();
        }

        if (0 == Language::count()) {
            Language::factory(5)->create();
        }

        if (0 == OwnerShipType::count()) {
            OwnerShipType::factory(3)->create();
        }

        // Job Categories
        if (0 == JobCategory::count()) {
            $categories = ['Software Development', 'Marketing', 'Sales', 'HR', 'Finance'];
            foreach ($categories as $name) {
                JobCategory::create([
                    'name' => $name,
                    'description' => "Category: {$name}",
                    'is_featured' => false,
                ]);
            }
        }

        // Skills
        if (0 == Skill::count()) {
            $skills = ['PHP', 'JavaScript', 'Python', 'Marketing', 'Sales', 'Communication', 'Leadership', 'Problem Solving'];
            foreach ($skills as $name) {
                Skill::create([
                    'name' => $name,
                    'description' => "Skill: {$name}",
                ]);
            }
        }

        $this->command->info('✅ Essential data seeded');
    }

    private function seedSmallUserBase(): void
    {
        $this->command->info('👥 Seeding small user base...');

        if (0 == User::count()) {
            // Create 1 admin
            User::factory(1)->create([
                'user_type' => 1,
                'email_verified_at' => now(),
            ]);

            // Create 5 employers
            User::factory(5)->create([
                'user_type' => 2,
                'email_verified_at' => now(),
            ]);

            // Create 10 candidates
            User::factory(10)->create([
                'user_type' => 3,
                'email_verified_at' => now(),
            ]);
        }

        // Companies (3 only)
        if (0 == Company::count()) {
            $employers = User::where('user_type', 2)->take(3)->get();
            $industries = Industry::all();
            $companySizes = CompanySize::all();
            $ownershipTypes = OwnerShipType::all();

            $employers->each(function ($employer) use ($industries, $companySizes, $ownershipTypes) {
                Company::factory()->create([
                    'user_id' => $employer->id,
                    'industry_id' => $industries->random()->id,
                    'ownership_type_id' => $ownershipTypes->random()->id,
                    'company_size_id' => $companySizes->random()->id,
                ]);
            });
        }

        // Candidates (5 only)
        if (0 == Candidate::count()) {
            $candidateUsers = User::where('user_type', 3)->take(5)->get();
            $skills = Skill::all();

            $candidateUsers->each(function ($user) use ($skills) {
                $candidate = Candidate::factory()->create([
                    'user_id' => $user->id,
                ]);

                // Attach 2-3 skills per candidate
                $candidateSkills = $skills->random(rand(2, 3));
                $user->candidateSkill()->attach($candidateSkills);
            });
        }

        $this->command->info('✅ Small user base seeded');
    }

    private function seedBasicContent(): void
    {
        $this->command->info('💼 Seeding basic content...');

        // Jobs (10 only)
        if (0 == Job::count()) {
            $companies = Company::all();
            $jobCategories = JobCategory::all();
            $skills = Skill::all();

            for ($i = 0; $i < 10; ++$i) {
                $job = Job::factory()->create([
                    'company_id' => $companies->random()->id,
                    'job_category_id' => $jobCategories->random()->id,
                    'is_featured' => rand(0, 100) < 30,
                    'status' => 1,
                ]);

                // Attach 2-3 skills per job
                $jobSkills = $skills->random(rand(2, 3));
                $job->jobsSkill()->attach($jobSkills);
            }
        }

        // Job Applications (15 only)
        if (0 == JobApplication::count()) {
            $candidates = Candidate::all();
            $jobs = Job::where('status', 1)->get();

            for ($i = 0; $i < 15; ++$i) {
                $candidate = $candidates->random();
                $job = $jobs->random();

                // Check for existing application
                $exists = JobApplication::where('candidate_id', $candidate->id)
                    ->where('job_id', $job->id)
                    ->exists()
                ;

                if (!$exists) {
                    JobApplication::factory()->create([
                        'candidate_id' => $candidate->id,
                        'job_id' => $job->id,
                        'status' => rand(0, 4),
                    ]);
                }
            }
        }

        // Plans (2 only)
        if (0 == Plan::count()) {
            Plan::factory(2)->create();
        }

        $this->command->info('✅ Basic content seeded');
    }

    private function generateQuickReport(): void
    {
        $this->command->info('');
        $this->command->info('📊 QUICK SEEDING REPORT');
        $this->command->info('==============================');
        $this->command->info('✅ Countries: '.Country::count());
        $this->command->info('✅ States: '.State::count());
        $this->command->info('✅ Cities: '.City::count());
        $this->command->info('✅ Users: '.User::count());
        $this->command->info('✅ Companies: '.Company::count());
        $this->command->info('✅ Jobs: '.Job::count());
        $this->command->info('✅ Candidates: '.Candidate::count());
        $this->command->info('✅ Job Applications: '.JobApplication::count());
        $this->command->info('✅ Skills: '.Skill::count());
        $this->command->info('✅ Job Categories: '.JobCategory::count());
        $this->command->info('✅ Plans: '.Plan::count());
        $this->command->info('==============================');
        $this->command->info('🎉 Quick seeding complete!');
        $this->command->info('🚀 Database is ready for testing!');
    }
}
