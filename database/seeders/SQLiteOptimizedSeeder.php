<?php

namespace Database\Seeders;

use App\Models\Application;
use App\Models\Candidate;
use App\Models\CandidateEducation;
use App\Models\CandidateExperience;
// Import all models
use App\Models\CareerLevel;
use App\Models\City;
use App\Models\Company;
use App\Models\CompanySize;
use App\Models\Country;
use App\Models\FrontSetting;
use App\Models\FunctionalArea;
use App\Models\Industry;
use App\Models\Job;
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
use App\Models\Setting;
use App\Models\Skill;
use App\Models\State;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class SQLiteOptimizedSeeder extends Seeder
{
    private array $seedingProgress = [];
    private array $generatedData = [];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🚀 Starting SQLite Optimized Seeding...');

        // SQLite foreign key handling
        DB::statement('PRAGMA foreign_keys = OFF;');

        try {
            // Create storage directories
            $this->createStorageDirectories();

            // Phase 1: Core Foundation
            $this->command->info('📍 Phase 1: Core Foundation');
            $this->seedLocationData();
            $this->seedSystemSettings();

            // Phase 2: Master Data
            $this->command->info('📋 Phase 2: Master Data');
            $this->seedMasterData();

            // Phase 3: Users and Companies
            $this->command->info('👥 Phase 3: Users and Companies');
            $this->seedUsers();
            $this->seedCompanies();

            // Phase 4: Job Portal Core
            $this->command->info('💼 Phase 4: Job Portal Core');
            $this->seedJobCategories();
            $this->seedSkills();
            $this->seedJobs();
            $this->seedCandidates();
            $this->seedJobApplications();

            // Phase 5: Financial System
            $this->command->info('💳 Phase 5: Financial System');
            $this->seedPlans();

            // Generate report
            $this->generateSeedingReport();
        } catch (\Exception $e) {
            $this->command->error('❌ Seeding failed: '.$e->getMessage());
            Log::error('SQLite seeding failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            throw $e;
        } finally {
            // Re-enable foreign key checks
            DB::statement('PRAGMA foreign_keys = ON;');
        }

        $this->command->info('✅ SQLite Optimized Seeding Complete!');
    }

    /**
     * Create storage directories.
     */
    private function createStorageDirectories(): void
    {
        $directories = [
            'public/companies/logos',
            'public/users/avatars',
            'public/candidates/resumes',
            'public/candidates/images',
            'public/temp',
        ];

        foreach ($directories as $directory) {
            if (!Storage::exists($directory)) {
                Storage::makeDirectory($directory);
                $this->command->info("✅ Created directory: {$directory}");
            }
        }
    }

    /**
     * Seed location data.
     */
    private function seedLocationData(): void
    {
        $this->command->info('🌍 Seeding location data...');

        if (0 == Country::count()) {
            // Create 20 countries for SQLite optimization
            $countries = Country::factory(20)->create();
            $this->seedingProgress['countries'] = $countries->count();

            $states = collect();
            $countries->each(function ($country) use (&$states) {
                $stateCount = rand(2, 4);
                $countryStates = State::factory($stateCount)->create(['country_id' => $country->id]);
                $states = $states->concat($countryStates);
            });
            $this->seedingProgress['states'] = $states->count();
            $this->generatedData['states'] = $states;

            $cities = collect();
            $states->each(function ($state) use (&$cities) {
                $cityCount = rand(3, 6);
                $stateCities = City::factory($cityCount)->create(['state_id' => $state->id]);
                $cities = $cities->concat($stateCities);
            });
            $this->seedingProgress['cities'] = $cities->count();
            $this->generatedData['cities'] = $cities;

            $this->command->info("✅ Location data: {$countries->count()} countries, {$states->count()} states, {$cities->count()} cities");
        } else {
            $this->seedingProgress['countries'] = Country::count();
            $this->seedingProgress['states'] = State::count();
            $this->seedingProgress['cities'] = City::count();
            $this->generatedData['states'] = State::all();
            $this->generatedData['cities'] = City::with('state.country')->get();
            $this->command->info('✅ Location data already exists');
        }
    }

    /**
     * Seed system settings.
     */
    private function seedSystemSettings(): void
    {
        $this->command->info('⚙️ Seeding system settings...');

        if (0 == Setting::count()) {
            $settings = Setting::factory(15)->create();
            $this->seedingProgress['settings'] = $settings->count();
        } else {
            $this->seedingProgress['settings'] = Setting::count();
        }

        if (0 == FrontSetting::count()) {
            $frontSettings = FrontSetting::factory(10)->create();
            $this->seedingProgress['front_settings'] = $frontSettings->count();
        } else {
            $this->seedingProgress['front_settings'] = FrontSetting::count();
        }

        $this->command->info('✅ System settings seeded');
    }

    /**
     * Seed master data tables.
     */
    private function seedMasterData(): void
    {
        $this->command->info('📋 Seeding master data...');

        // Optimized counts for SQLite
        $masterDataConfig = [
            'industries' => 12,
            'company_sizes' => 6,
            'ownership_types' => 6,
            'functional_areas' => 10,
            'career_levels' => 6,
            'salary_currencies' => 10,
            'salary_periods' => 5,
            'job_types' => 6,
            'job_shifts' => 4,
            'required_degree_levels' => 8,
            'marital_status' => 5,
            'languages' => 10,
        ];

        foreach ($masterDataConfig as $table => $count) {
            $modelClass = $this->getModelClass($table);
            if ($modelClass && 0 == $modelClass::count()) {
                $modelClass::factory($count)->create();
                $this->seedingProgress[$table] = $count;
                $this->command->info("✅ {$table}: {$count}");
            } else {
                $this->seedingProgress[$table] = $modelClass ? $modelClass::count() : 0;
            }
        }

        $this->command->info('✅ Master data seeded');
    }

    /**
     * Get model class from table name.
     */
    private function getModelClass(string $table): ?string
    {
        $mapping = [
            'industries' => Industry::class,
            'company_sizes' => CompanySize::class,
            'ownership_types' => OwnerShipType::class,
            'functional_areas' => FunctionalArea::class,
            'career_levels' => CareerLevel::class,
            'salary_currencies' => SalaryCurrency::class,
            'salary_periods' => SalaryPeriod::class,
            'job_types' => JobType::class,
            'job_shifts' => JobShift::class,
            'required_degree_levels' => RequiredDegreeLevel::class,
            'marital_status' => MaritalStatus::class,
            'languages' => Language::class,
        ];

        return $mapping[$table] ?? null;
    }

    /**
     * Seed users.
     */
    private function seedUsers(): void
    {
        $this->command->info('👥 Seeding users...');

        if (0 == User::count()) {
            // Optimized counts for SQLite
            $admins = User::factory(3)->create([
                'user_type' => 'admin',
                'email_verified_at' => now(),
            ]);

            $employers = User::factory(50)->create([
                'user_type' => 'employer',
                'email_verified_at' => now(),
            ]);

            $candidates = User::factory(100)->create([
                'user_type' => 'candidate',
                'email_verified_at' => now(),
            ]);

            $totalUsers = $admins->count() + $employers->count() + $candidates->count();
            $this->seedingProgress['users'] = $totalUsers;
            $this->generatedData['users'] = User::all();

            $this->command->info("✅ Users: {$admins->count()} admins, {$employers->count()} employers, {$candidates->count()} candidates");
        } else {
            $this->seedingProgress['users'] = User::count();
            $this->generatedData['users'] = User::all();
            $this->command->info('✅ Users already exist');
        }
    }

    /**
     * Seed companies.
     */
    private function seedCompanies(): void
    {
        $this->command->info('🏢 Seeding companies...');

        if (0 == Company::count()) {
            $employers = User::where('user_type', 'employer')->get();
            $cities = $this->generatedData['cities'] ?? City::with('state.country')->get();
            $industries = Industry::all();
            $companySizes = CompanySize::all();
            $ownershipTypes = OwnerShipType::all();

            $companies = collect();

            $employers->take(40)->each(function ($employer) use ($cities, $industries, $companySizes, $ownershipTypes, &$companies) {
                $city = $cities->isNotEmpty() ? $cities->random() : null;

                $company = Company::factory()->create([
                    'user_id' => $employer->id,
                    'industry_id' => $industries->isNotEmpty() ? $industries->random()->id : null,
                    'ownership_type_id' => $ownershipTypes->isNotEmpty() ? $ownershipTypes->random()->id : null,
                    'company_size_id' => $companySizes->isNotEmpty() ? $companySizes->random()->id : null,
                ]);

                $companies->push($company);
            });

            $this->seedingProgress['companies'] = $companies->count();
            $this->generatedData['companies'] = $companies;
            $this->command->info("✅ Companies: {$companies->count()}");
        } else {
            $this->seedingProgress['companies'] = Company::count();
            $this->generatedData['companies'] = Company::all();
            $this->command->info('✅ Companies already exist');
        }
    }

    /**
     * Seed job categories.
     */
    private function seedJobCategories(): void
    {
        $this->command->info('📂 Seeding job categories...');

        if (0 == JobCategory::count()) {
            $categories = [
                'Software Development', 'Data Science', 'Marketing', 'Sales', 'HR',
                'Finance', 'Operations', 'Design', 'Engineering', 'Healthcare',
                'Education', 'Legal', 'Consulting', 'Project Management', 'QA',
            ];

            foreach ($categories as $categoryName) {
                JobCategory::factory()->create(['name' => $categoryName]);
            }

            $this->seedingProgress['job_categories'] = count($categories);
            $this->command->info('✅ Job categories: '.count($categories));
        } else {
            $this->seedingProgress['job_categories'] = JobCategory::count();
            $this->command->info('✅ Job categories already exist');
        }
    }

    /**
     * Seed skills.
     */
    private function seedSkills(): void
    {
        $this->command->info('🛠️ Seeding skills...');

        if (0 == Skill::count()) {
            $skills = [
                'PHP', 'JavaScript', 'Python', 'Java', 'Laravel', 'React', 'Vue.js',
                'MySQL', 'PostgreSQL', 'SQLite', 'AWS', 'Docker', 'Git', 'Agile',
                'Communication', 'Leadership', 'Problem Solving', 'Teamwork',
                'Angular', 'Node.js', 'MongoDB', 'DevOps', 'HTML', 'CSS',
            ];

            foreach ($skills as $skillName) {
                Skill::factory()->create(['name' => $skillName]);
            }

            // Add additional random skills
            Skill::factory(25)->create();

            $this->seedingProgress['skills'] = count($skills) + 25;
            $this->command->info('✅ Skills: '.(count($skills) + 25));
        } else {
            $this->seedingProgress['skills'] = Skill::count();
            $this->command->info('✅ Skills already exist');
        }
    }

    /**
     * Seed jobs.
     */
    private function seedJobs(): void
    {
        $this->command->info('💼 Seeding jobs...');

        if (0 == Job::count()) {
            $companies = $this->generatedData['companies'] ?? Company::all();
            $jobCategories = JobCategory::all();
            $skills = Skill::all();

            $jobs = collect();

            // Create 200 jobs for SQLite optimization
            for ($i = 0; $i < 200; ++$i) {
                $company = $companies->random();

                $job = Job::factory()->create([
                    'company_id' => $company->id,
                    'job_category_id' => $jobCategories->random()->id,
                    'state_id' => $company->state_id,
                    'country_id' => $company->country_id,
                    'is_featured' => rand(0, 100) < 20,
                    'status' => rand(0, 100) < 90 ? 1 : 0,
                ]);

                // Attach skills
                if ($skills->isNotEmpty()) {
                    $jobSkills = $skills->random(rand(2, min(5, $skills->count())));
                    $job->jobsSkill()->attach($jobSkills);
                }

                $jobs->push($job);
            }

            $this->seedingProgress['jobs'] = 200;
            $this->generatedData['jobs'] = $jobs;
            $this->command->info('✅ Jobs: 200');
        } else {
            $this->seedingProgress['jobs'] = Job::count();
            $this->generatedData['jobs'] = Job::all();
            $this->command->info('✅ Jobs already exist');
        }
    }

    /**
     * Seed candidates.
     */
    private function seedCandidates(): void
    {
        $this->command->info('👨‍💼 Seeding candidates...');

        if (0 == Candidate::count()) {
            $candidateUsers = User::where('user_type', 'candidate')->get();
            $skills = Skill::all();

            $candidates = collect();

            $candidateUsers->take(80)->each(function ($user) use ($skills, &$candidates) {
                $candidate = Candidate::factory()->create([
                    'user_id' => $user->id,
                ]);

                // Attach skills
                if ($skills->isNotEmpty()) {
                    $candidateSkills = $skills->random(rand(3, min(8, $skills->count())));
                    $user->candidateSkill()->attach($candidateSkills);
                }

                // Add education (1-2 per candidate)
                CandidateEducation::factory(rand(1, 2))->create([
                    'candidate_id' => $candidate->id,
                ]);

                // Add experience (0-3 per candidate)
                CandidateExperience::factory(rand(0, 3))->create([
                    'candidate_id' => $candidate->id,
                ]);

                $candidates->push($candidate);
            });

            $this->seedingProgress['candidates'] = $candidates->count();
            $this->seedingProgress['candidate_educations'] = CandidateEducation::count();
            $this->seedingProgress['candidate_experiences'] = CandidateExperience::count();
            $this->generatedData['candidates'] = $candidates;

            $this->command->info("✅ Candidates: {$candidates->count()} with education and experience");
        } else {
            $this->seedingProgress['candidates'] = Candidate::count();
            $this->seedingProgress['candidate_educations'] = CandidateEducation::count();
            $this->seedingProgress['candidate_experiences'] = CandidateExperience::count();
            $this->generatedData['candidates'] = Candidate::all();
            $this->command->info('✅ Candidates already exist');
        }
    }

    /**
     * Seed job applications.
     */
    private function seedJobApplications(): void
    {
        $this->command->info('📝 Seeding job applications...');

        if (0 == Application::count()) {
            $candidates = $this->generatedData['candidates'] ?? Candidate::all();
            $jobs = $this->generatedData['jobs'] ?? Job::where('status', 1)->get();

            $applications = collect();

            // Create 300 applications for SQLite optimization
            for ($i = 0; $i < 300; ++$i) {
                $candidate = $candidates->random();
                $job = $jobs->random();

                // Avoid duplicates
                $exists = Application::where('candidate_id', $candidate->id)
                    ->where('job_id', $job->id)
                    ->exists()
                ;

                if (!$exists) {
                    $application = Application::create([
                        'candidate_id' => $candidate->id,
                        'job_id' => $job->id,
                        'resume_id' => 1,
                        'expected_salary' => rand(40000, 120000),
                        'notes' => fake()->optional(0.7)->paragraph(),
                        'status' => rand(0, 4),
                    ]);

                    $applications->push($application);
                }
            }

            $this->seedingProgress['job_applications'] = $applications->count();
            $this->command->info("✅ Job Applications: {$applications->count()}");
        } else {
            $this->seedingProgress['job_applications'] = Application::count();
            $this->command->info('✅ Job applications already exist');
        }
    }

    /**
     * Seed plans.
     */
    private function seedPlans(): void
    {
        $this->command->info('💳 Seeding subscription plans...');

        if (0 == Plan::count()) {
            $plans = Plan::factory(3)->create();
            $this->seedingProgress['plans'] = $plans->count();
            $this->command->info("✅ Plans: {$plans->count()}");
        } else {
            $this->seedingProgress['plans'] = Plan::count();
            $this->command->info('✅ Plans already exist');
        }
    }

    /**
     * Generate seeding report.
     */
    private function generateSeedingReport(): void
    {
        $this->command->info('');
        $this->command->info('📊 SQLITE SEEDING REPORT');
        $this->command->info('='.str_repeat('=', 40));

        $total = 0;
        foreach ($this->seedingProgress as $model => $count) {
            $this->command->info("✅ {$model}: {$count}");
            $total += $count;
        }

        $this->command->info('='.str_repeat('=', 40));
        $this->command->info("🎉 Total Records: {$total}");
        $this->command->info('🚀 SQLite database is ready!');
    }
}
