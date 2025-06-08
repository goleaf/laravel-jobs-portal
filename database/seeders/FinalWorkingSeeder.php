<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Models\Country;
use App\Models\State;
use App\Models\City;
use App\Models\User;
use App\Models\Company;
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
use App\Models\JobCategory;
use App\Models\Skill;
use App\Models\Job;
use App\Models\Candidate;
use App\Models\JobApplication;
use App\Models\Plan;

class FinalWorkingSeeder extends Seeder
{
    private array $seedingProgress = [];

    public function run(): void
    {
        $this->command->info('🚀 Starting Final Working Database Seeding...');
        
        // Disable foreign key checks for seeding
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        try {
            // Create storage directories
            $this->createStorageDirectories();
            
            // Phase 1: Core Data with Safe Limits
            $this->command->info('📍 Phase 1: Core System Data');
            $this->seedLocationData();
            $this->seedMasterDataSafely();
            $this->seedJobCategories();
            $this->seedSkills();
            
            // Phase 2: Users and Companies
            $this->command->info('👥 Phase 2: Users and Companies');
            $this->seedUsers();
            $this->seedCompanies();
            
            // Phase 3: Jobs and Applications
            $this->command->info('💼 Phase 3: Jobs and Applications');
            $this->seedJobs();
            $this->seedCandidates();
            $this->seedJobApplications();
            
            // Phase 4: Plans
            $this->command->info('💳 Phase 4: Subscription Plans');
            $this->seedPlans();
            
            // Generate final report
            $this->generateFinalReport();
            
        } catch (\Exception $e) {
            $this->command->error('❌ Seeding failed: ' . $e->getMessage());
            throw $e;
        } finally {
            // Re-enable foreign key checks
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        }
        
        $this->command->info('✅ Final Working Database Seeding Complete!');
    }

    private function createStorageDirectories(): void
    {
        $directories = [
            'public/companies/logos',
            'public/users/avatars',
            'public/candidates/resumes',
            'public/candidates/images',
            'public/sliders/headers',
            'public/blog/featured',
            'public/temp'
        ];

        foreach ($directories as $directory) {
            if (!Storage::exists($directory)) {
                Storage::makeDirectory($directory);
                $this->command->info("✅ Created directory: {$directory}");
            }
        }
    }

    private function seedLocationData(): void
    {
        $this->command->info('🌍 Seeding location data...');
        
        if (Country::count() == 0) {
            // Create 20 countries to avoid overwhelming the system
            $countries = Country::factory(20)->create();
            $this->seedingProgress['countries'] = $countries->count();
            
            // Create states for each country (2-5 per country)
            $states = collect();
            $countries->each(function ($country) use (&$states) {
                $stateCount = rand(2, 5);
                $countryStates = State::factory($stateCount)->create(['country_id' => $country->id]);
                $states = $states->concat($countryStates);
            });
            $this->seedingProgress['states'] = $states->count();
            
            // Create cities for each state (3-8 per state)
            $cities = collect();
            $states->each(function ($state) use (&$cities) {
                $cityCount = rand(3, 8);
                $stateCities = City::factory($cityCount)->create(['state_id' => $state->id]);
                $cities = $cities->concat($stateCities);
            });
            $this->seedingProgress['cities'] = $cities->count();
            
            $this->command->info("✅ Location data: {$countries->count()} countries, {$states->count()} states, {$cities->count()} cities");
        } else {
            $this->seedingProgress['countries'] = Country::count();
            $this->seedingProgress['states'] = State::count();
            $this->seedingProgress['cities'] = City::count();
            $this->command->info("✅ Location data already exists");
        }
    }

    private function seedMasterDataSafely(): void
    {
        $this->command->info('📋 Seeding master data with safe limits...');
        
        // Industry data - limit to 15 (well below 40 available)
        if (Industry::count() == 0) {
            Industry::factory(15)->create();
            $this->command->info("✅ Industries: 15");
        }
        
        // Company sizes - limit to 5
        if (CompanySize::count() == 0) {
            CompanySize::factory(5)->create();
            $this->command->info("✅ Company sizes: 5");
        }
        
        // Functional areas - limit to 12
        if (FunctionalArea::count() == 0) {
            FunctionalArea::factory(12)->create();
            $this->command->info("✅ Functional areas: 12");
        }
        
        // Career levels - limit to 6
        if (CareerLevel::count() == 0) {
            CareerLevel::factory(6)->create();
            $this->command->info("✅ Career levels: 6");
        }
        
        // Salary currencies - limit to 8
        if (SalaryCurrency::count() == 0) {
            SalaryCurrency::factory(8)->create();
            $this->command->info("✅ Salary currencies: 8");
        }
        
        // Salary periods - limit to 5
        if (SalaryPeriod::count() == 0) {
            SalaryPeriod::factory(5)->create();
            $this->command->info("✅ Salary periods: 5");
        }
        
        // Job types - limit to 6
        if (JobType::count() == 0) {
            JobType::factory(6)->create();
            $this->command->info("✅ Job types: 6");
        }
        
        // Job shifts - limit to 4
        if (JobShift::count() == 0) {
            JobShift::factory(4)->create();
            $this->command->info("✅ Job shifts: 4");
        }
        
        // Required degree levels - limit to 8
        if (RequiredDegreeLevel::count() == 0) {
            RequiredDegreeLevel::factory(8)->create();
            $this->command->info("✅ Degree levels: 8");
        }
        
        // Marital status - limit to 5
        if (MaritalStatus::count() == 0) {
            MaritalStatus::factory(5)->create();
            $this->command->info("✅ Marital statuses: 5");
        }
        
        // Languages - limit to 10
        if (Language::count() == 0) {
            Language::factory(10)->create();
            $this->command->info("✅ Languages: 10");
        }
        
        // Ownership types - limit to 6
        if (OwnerShipType::count() == 0) {
            OwnerShipType::factory(6)->create();
            $this->command->info("✅ Ownership types: 6");
        }
    }

    private function seedJobCategories(): void
    {
        $this->command->info('📂 Seeding job categories...');
        
        if (JobCategory::count() == 0) {
            $categories = [
                ['name' => 'Software Development', 'description' => 'Jobs related to software development and programming'],
                ['name' => 'Data Science', 'description' => 'Data analysis, machine learning, and analytics roles'],
                ['name' => 'Marketing', 'description' => 'Digital marketing, content creation, and brand management'],
                ['name' => 'Sales', 'description' => 'Sales representatives, account managers, and business development'],
                ['name' => 'HR', 'description' => 'Human resources, recruitment, and people operations'],
                ['name' => 'Finance', 'description' => 'Financial analysis, accounting, and investment roles'],
                ['name' => 'Operations', 'description' => 'Operations management, logistics, and process improvement'],
                ['name' => 'Design', 'description' => 'UI/UX design, graphic design, and creative roles'],
                ['name' => 'Engineering', 'description' => 'Mechanical, electrical, and systems engineering'],
                ['name' => 'Healthcare', 'description' => 'Medical professionals, nursing, and healthcare administration'],
                ['name' => 'Education', 'description' => 'Teaching, training, and educational administration'],
                ['name' => 'Legal', 'description' => 'Legal counsel, compliance, and regulatory affairs'],
                ['name' => 'Consulting', 'description' => 'Management consulting, strategy, and advisory services'],
                ['name' => 'Project Management', 'description' => 'Project managers, program managers, and delivery leads'],
                ['name' => 'QA', 'description' => 'Quality assurance, testing, and quality control'],
            ];
            
            foreach ($categories as $categoryData) {
                JobCategory::create([
                    'name' => $categoryData['name'],
                    'description' => $categoryData['description'],
                    'is_featured' => false,
                ]);
            }
            
            $this->seedingProgress['job_categories'] = count($categories);
            $this->command->info("✅ Job categories: " . count($categories));
        }
    }

    private function seedSkills(): void
    {
        $this->command->info('🛠️ Seeding skills...');
        
        if (Skill::count() == 0) {
            $skillNames = [
                'PHP', 'JavaScript', 'Python', 'Java', 'Laravel', 'React', 'Vue.js',
                'MySQL', 'PostgreSQL', 'AWS', 'Docker', 'Git', 'Agile', 'Scrum',
                'Communication', 'Leadership', 'Problem Solving', 'Teamwork',
                'Angular', 'Node.js', 'Express.js', 'MongoDB', 'Redis', 'Kubernetes',
                'DevOps', 'CI/CD', 'HTML', 'CSS', 'SASS', 'TypeScript', 'GraphQL',
                'REST API', 'Microservices', 'SQL', 'NoSQL', 'Linux', 'Windows',
                'MacOS', 'Figma', 'Adobe Photoshop', 'Adobe Illustrator', 'Sketch',
                'Project Management', 'Time Management', 'Critical Thinking', 'Creativity',
                'Adaptability', 'Customer Service', 'Sales', 'Marketing'
            ];
            
            foreach ($skillNames as $skillName) {
                Skill::create([
                    'name' => $skillName,
                    'description' => "Skill in {$skillName}",
                ]);
            }
            
            $this->seedingProgress['skills'] = count($skillNames);
            $this->command->info("✅ Skills: " . count($skillNames));
        }
    }

    private function seedUsers(): void
    {
        $this->command->info('👥 Seeding users...');
        
        if (User::count() == 0) {
            // Create admin users
            $admins = User::factory(3)->create([
                'user_type' => 1,
                'email_verified_at' => now(),
            ]);
            
            // Create employer users
            $employers = User::factory(50)->create([
                'user_type' => 2,
                'email_verified_at' => now(),
            ]);
            
            // Create candidate users
            $candidates = User::factory(100)->create([
                'user_type' => 3,
                'email_verified_at' => now(),
            ]);
            
            $this->seedingProgress['users'] = $admins->count() + $employers->count() + $candidates->count();
            $this->command->info("✅ Users: {$admins->count()} admins, {$employers->count()} employers, {$candidates->count()} candidates");
        }
    }

    private function seedCompanies(): void
    {
        $this->command->info('🏢 Seeding companies...');
        
        if (Company::count() == 0) {
            $employers = User::where('user_type', 2)->get();
            $industries = Industry::all();
            $companySizes = CompanySize::all();
            $ownershipTypes = OwnerShipType::all();
            
            $companies = collect();
            
            $employers->take(40)->each(function ($employer) use ($industries, $companySizes, $ownershipTypes, &$companies) {
                $company = Company::factory()->create([
                    'user_id' => $employer->id,
                    'industry_id' => $industries->isNotEmpty() ? $industries->random()->id : null,
                    'ownership_type_id' => $ownershipTypes->isNotEmpty() ? $ownershipTypes->random()->id : null,
                    'company_size_id' => $companySizes->isNotEmpty() ? $companySizes->random()->id : null,
                ]);
                
                $companies->push($company);
            });
            
            $this->seedingProgress['companies'] = $companies->count();
            $this->command->info("✅ Companies: {$companies->count()}");
        }
    }

    private function seedJobs(): void
    {
        $this->command->info('💼 Seeding jobs...');
        
        if (Job::count() == 0) {
            $companies = Company::all();
            $jobCategories = JobCategory::all();
            $skills = Skill::all();
            
            $jobs = collect();
            
            // Create 100 jobs
            for ($i = 0; $i < 100; $i++) {
                $company = $companies->random();
                
                $job = Job::factory()->create([
                    'company_id' => $company->id,
                    'job_category_id' => $jobCategories->random()->id,
                    'is_featured' => rand(0, 100) < 20,
                    'status' => rand(0, 100) < 90 ? 1 : 0,
                ]);
                
                // Attach 2-5 skills per job
                $jobSkills = $skills->random(rand(2, 5));
                $job->jobsSkill()->attach($jobSkills);
                
                $jobs->push($job);
            }
            
            $this->seedingProgress['jobs'] = 100;
            $this->command->info("✅ Jobs: 100");
        }
    }

    private function seedCandidates(): void
    {
        $this->command->info('👨‍💼 Seeding candidates...');
        
        if (Candidate::count() == 0) {
            $candidateUsers = User::where('user_type', 3)->get();
            $skills = Skill::all();
            
            $candidates = collect();
            
            $candidateUsers->take(80)->each(function ($user) use ($skills, &$candidates) {
                $candidate = Candidate::factory()->create([
                    'user_id' => $user->id,
                ]);
                
                // Attach 3-8 skills per candidate
                $candidateSkills = $skills->random(rand(3, 8));
                $user->candidateSkill()->attach($candidateSkills);
                
                $candidates->push($candidate);
            });
            
            $this->seedingProgress['candidates'] = $candidates->count();
            $this->command->info("✅ Candidates: {$candidates->count()}");
        }
    }

    private function seedJobApplications(): void
    {
        $this->command->info('📝 Seeding job applications...');
        
        if (JobApplication::count() == 0) {
            $candidates = Candidate::all();
            $jobs = Job::where('status', 1)->get();
            
            $applications = collect();
            
            // Create 200 applications
            for ($i = 0; $i < 200; $i++) {
                $candidate = $candidates->random();
                $job = $jobs->random();
                
                // Check for existing application
                $exists = JobApplication::where('candidate_id', $candidate->id)
                    ->where('job_id', $job->id)
                    ->exists();
                
                if (!$exists) {
                    $application = JobApplication::factory()->create([
                        'candidate_id' => $candidate->id,
                        'job_id' => $job->id,
                        'status' => rand(0, 4),
                    ]);
                    
                    $applications->push($application);
                }
            }
            
            $this->seedingProgress['job_applications'] = $applications->count();
            $this->command->info("✅ Job Applications: {$applications->count()}");
        }
    }

    private function seedPlans(): void
    {
        $this->command->info('💳 Seeding subscription plans...');
        
        if (Plan::count() == 0) {
            $plans = Plan::factory(3)->create();
            
            $this->seedingProgress['plans'] = $plans->count();
            $this->command->info("✅ Plans: {$plans->count()}");
        }
    }

    private function generateFinalReport(): void
    {
        $this->command->info('');
        $this->command->info('📊 FINAL SEEDING REPORT');
        $this->command->info('==============================');
        
        $total = 0;
        foreach ($this->seedingProgress as $model => $count) {
            $this->command->info("✅ {$model}: {$count}");
            $total += $count;
        }
        
        $this->command->info('==============================');
        $this->command->info("🎉 Total Records Created: {$total}");
        $this->command->info('🚀 Database is ready for use!');
        $this->command->info('');
        $this->command->info('Next steps:');
        $this->command->info('1. Run: php artisan storage:link');
        $this->command->info('2. Visit your application to see the seeded data');
        $this->command->info('3. Login with any seeded user account');
    }
} 