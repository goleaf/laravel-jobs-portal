<?php

namespace Database\Seeders;

use App\Models\BrandingSliders;
use App\Models\Candidate;
use App\Models\CandidateEducation;
use App\Models\CandidateExperience;
// Import all models
use App\Models\CareerLevel;
use App\Models\City;
use App\Models\CmsServices;
use App\Models\Company;
use App\Models\CompanySize;
use App\Models\Country;
use App\Models\EmailJob;
use App\Models\EmailTemplate;
use App\Models\EnvSetting;
use App\Models\FAQ;
use App\Models\FavouriteCompany;
use App\Models\FavouriteJob;
use App\Models\FeaturedRecord;
use App\Models\File;
use App\Models\FrontSetting;
use App\Models\FunctionalArea;
use App\Models\HeaderSlider;
use App\Models\ImageSlider;
use App\Models\Industry;
use App\Models\Inquiry;
use App\Models\Job;
use App\Models\JobApplication;
use App\Models\JobApplicationSchedule;
use App\Models\JobCategory;
use App\Models\JobShift;
use App\Models\JobStage;
use App\Models\JobType;
use App\Models\Language;
use App\Models\MaritalStatus;
use App\Models\NewsLetter;
use App\Models\Noticeboard;
use App\Models\Notification;
use App\Models\NotificationSetting;
use App\Models\OwnerShipType;
use App\Models\Plan;
use App\Models\Post;
use App\Models\PostCategory;
use App\Models\PostComment;
use App\Models\ReportedJob;
use App\Models\ReportedToCompany;
use App\Models\RequiredDegreeLevel;
use App\Models\SalaryCurrency;
use App\Models\SalaryPeriod;
use App\Models\Setting;
use App\Models\Skill;
use App\Models\SocialAccount;
use App\Models\State;
use App\Models\Subscription;
use App\Models\Tag;
use App\Models\Testimonial;
use App\Models\Todo;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class ComprehensiveAllTablesSeeder extends Seeder
{
    private array $seedingProgress = [];
    private array $generatedData = [];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🚀 Starting Comprehensive All Tables Seeding...');

        // Disable foreign key checks for seeding (SQLite-compatible)
        if ('sqlite' === config('database.default')) {
            DB::statement('PRAGMA foreign_keys = OFF;');
        } else {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        }

        try {
            // Create storage directories
            $this->createStorageDirectories();

            // Phase 1: Core System Foundation
            $this->command->info('📍 Phase 1: Core System Foundation');
            $this->seedLocationData();
            $this->seedSystemSettings();
            $this->seedPermissionsAndRoles();

            // Phase 2: Master Data Tables
            $this->command->info('📋 Phase 2: Master Data Tables');
            $this->seedMasterData();

            // Phase 3: User Management
            $this->command->info('👥 Phase 3: User Management');
            $this->seedUsers();
            $this->seedCompanies();

            // Phase 4: Job Portal Core
            $this->command->info('💼 Phase 4: Job Portal Core');
            $this->seedJobCategories();
            $this->seedSkills();
            $this->seedJobs();
            $this->seedCandidates();
            $this->seedJobApplications();

            // Phase 5: Content Management System
            $this->command->info('📝 Phase 5: Content Management System');
            $this->seedContentTables();

            // Phase 6: Communication & Notifications
            $this->command->info('📧 Phase 6: Communication & Notifications');
            $this->seedCommunicationTables();

            // Phase 7: Media & Sliders
            $this->command->info('🎨 Phase 7: Media & Sliders');
            $this->seedMediaTables();

            // Phase 8: Social Features
            $this->command->info('🤝 Phase 8: Social Features');
            $this->seedSocialFeatures();

            // Phase 9: Financial System
            $this->command->info('💳 Phase 9: Financial System');
            $this->seedFinancialTables();

            // Phase 10: System Utilities
            $this->command->info('⚙️ Phase 10: System Utilities');
            $this->seedSystemUtilities();

            // Generate comprehensive report
            $this->generateComprehensiveReport();
        } catch (\Exception $e) {
            $this->command->error('❌ Seeding failed: '.$e->getMessage());
            Log::error('Database seeding failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            throw $e;
        } finally {
            // Re-enable foreign key checks
            if ('sqlite' === config('database.default')) {
                DB::statement('PRAGMA foreign_keys = ON;');
            } else {
                DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            }
        }

        $this->command->info('✅ Comprehensive All Tables Seeding Complete!');
    }

    /**
     * Create storage directories for media files.
     */
    private function createStorageDirectories(): void
    {
        $directories = [
            'public/companies/logos',
            'public/users/avatars',
            'public/candidates/resumes',
            'public/candidates/images',
            'public/sliders/headers',
            'public/sliders/branding',
            'public/sliders/images',
            'public/blog/featured',
            'public/industries/icons',
            'public/temp',
            'public/files',
            'public/media',
        ];

        foreach ($directories as $directory) {
            if (!Storage::exists($directory)) {
                Storage::makeDirectory($directory);
                $this->command->info("✅ Created directory: {$directory}");
            }
        }
    }

    /**
     * Phase 1: Seed core location data.
     */
    private function seedLocationData(): void
    {
        $this->command->info('🌍 Seeding location data...');

        if (0 == Country::count()) {
            // Create 30 countries
            $countries = Country::factory(30)->create();
            $this->seedingProgress['countries'] = $countries->count();

            // Create states for each country (2-6 per country)
            $states = collect();
            $countries->each(function ($country) use (&$states) {
                $stateCount = rand(2, 6);
                $countryStates = State::factory($stateCount)->create(['country_id' => $country->id]);
                $states = $states->concat($countryStates);
            });
            $this->seedingProgress['states'] = $states->count();
            $this->generatedData['states'] = $states;

            // Create cities for each state (3-10 per state)
            $cities = collect();
            $states->each(function ($state) use (&$cities) {
                $cityCount = rand(3, 10);
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
            $this->generatedData['cities'] = City::all();
            $this->command->info('✅ Location data already exists');
        }
    }

    /**
     * Seed system settings tables.
     */
    private function seedSystemSettings(): void
    {
        $this->command->info('⚙️ Seeding system settings...');

        // Settings table
        if (0 == Setting::count()) {
            $settings = Setting::factory(20)->create();
            $this->seedingProgress['settings'] = $settings->count();
        } else {
            $this->seedingProgress['settings'] = Setting::count();
        }

        // Front settings
        if (0 == FrontSetting::count()) {
            $frontSettings = FrontSetting::factory(15)->create();
            $this->seedingProgress['front_settings'] = $frontSettings->count();
        } else {
            $this->seedingProgress['front_settings'] = FrontSetting::count();
        }

        // Environment settings
        if (0 == EnvSetting::count()) {
            $envSettings = EnvSetting::factory(10)->create();
            $this->seedingProgress['env_settings'] = $envSettings->count();
        } else {
            $this->seedingProgress['env_settings'] = EnvSetting::count();
        }

        $this->command->info('✅ System settings seeded');
    }

    /**
     * Seed permissions and roles using factories.
     */
    private function seedPermissionsAndRoles(): void
    {
        $this->command->info('🔐 Seeding permissions and roles...');

        // Check if the model exists
        if (class_exists(Permission::class)) {
            $permissionClass = Permission::class;
            $roleClass = Role::class;

            if (0 == $permissionClass::count()) {
                // Create basic permissions
                $permissions = [
                    'view users', 'create users', 'edit users', 'delete users',
                    'view companies', 'create companies', 'edit companies', 'delete companies',
                    'view jobs', 'create jobs', 'edit jobs', 'delete jobs',
                    'view candidates', 'edit candidates', 'delete candidates',
                    'view applications', 'manage applications',
                    'admin access', 'employer access', 'candidate access',
                ];

                foreach ($permissions as $permission) {
                    $permissionClass::create(['name' => $permission]);
                }
                $this->seedingProgress['permissions'] = count($permissions);
            } else {
                $this->seedingProgress['permissions'] = $permissionClass::count();
            }

            if (0 == $roleClass::count()) {
                // Create basic roles
                $roles = ['admin', 'employer', 'candidate', 'super-admin'];
                foreach ($roles as $role) {
                    $roleClass::create(['name' => $role]);
                }
                $this->seedingProgress['roles'] = count($roles);
            } else {
                $this->seedingProgress['roles'] = $roleClass::count();
            }
        } else {
            $this->command->warn('⚠️ Spatie Permission package not found, skipping permissions and roles');
            $this->seedingProgress['permissions'] = 0;
            $this->seedingProgress['roles'] = 0;
        }

        $this->command->info('✅ Permissions and roles seeded');
    }

    /**
     * Phase 2: Seed all master data tables.
     */
    private function seedMasterData(): void
    {
        $this->command->info('📋 Seeding master data tables...');

        // Industries
        if (0 == Industry::count()) {
            $industries = Industry::factory(20)->create();
            $this->seedingProgress['industries'] = $industries->count();
        } else {
            $this->seedingProgress['industries'] = Industry::count();
        }

        // Company sizes
        if (0 == CompanySize::count()) {
            $companySizes = CompanySize::factory(8)->create();
            $this->seedingProgress['company_sizes'] = $companySizes->count();
        } else {
            $this->seedingProgress['company_sizes'] = CompanySize::count();
        }

        // Ownership types
        if (0 == OwnerShipType::count()) {
            $ownershipTypes = OwnerShipType::factory(8)->create();
            $this->seedingProgress['ownership_types'] = $ownershipTypes->count();
        } else {
            $this->seedingProgress['ownership_types'] = OwnerShipType::count();
        }

        // Functional areas
        if (0 == FunctionalArea::count()) {
            $functionalAreas = FunctionalArea::factory(15)->create();
            $this->seedingProgress['functional_areas'] = $functionalAreas->count();
        } else {
            $this->seedingProgress['functional_areas'] = FunctionalArea::count();
        }

        // Career levels
        if (0 == CareerLevel::count()) {
            $careerLevels = CareerLevel::factory(8)->create();
            $this->seedingProgress['career_levels'] = $careerLevels->count();
        } else {
            $this->seedingProgress['career_levels'] = CareerLevel::count();
        }

        // Salary currencies
        if (0 == SalaryCurrency::count()) {
            $salaryCurrencies = SalaryCurrency::factory(15)->create();
            $this->seedingProgress['salary_currencies'] = $salaryCurrencies->count();
        } else {
            $this->seedingProgress['salary_currencies'] = SalaryCurrency::count();
        }

        // Salary periods
        if (0 == SalaryPeriod::count()) {
            $salaryPeriods = SalaryPeriod::factory(6)->create();
            $this->seedingProgress['salary_periods'] = $salaryPeriods->count();
        } else {
            $this->seedingProgress['salary_periods'] = SalaryPeriod::count();
        }

        // Job types
        if (0 == JobType::count()) {
            $jobTypes = JobType::factory(8)->create();
            $this->seedingProgress['job_types'] = $jobTypes->count();
        } else {
            $this->seedingProgress['job_types'] = JobType::count();
        }

        // Job shifts
        if (0 == JobShift::count()) {
            $jobShifts = JobShift::factory(5)->create();
            $this->seedingProgress['job_shifts'] = $jobShifts->count();
        } else {
            $this->seedingProgress['job_shifts'] = JobShift::count();
        }

        // Required degree levels
        if (0 == RequiredDegreeLevel::count()) {
            $degreeLevels = RequiredDegreeLevel::factory(10)->create();
            $this->seedingProgress['required_degree_levels'] = $degreeLevels->count();
        } else {
            $this->seedingProgress['required_degree_levels'] = RequiredDegreeLevel::count();
        }

        // Marital statuses
        if (0 == MaritalStatus::count()) {
            $maritalStatuses = MaritalStatus::factory(6)->create();
            $this->seedingProgress['marital_status'] = $maritalStatuses->count();
        } else {
            $this->seedingProgress['marital_status'] = MaritalStatus::count();
        }

        // Languages
        if (0 == Language::count()) {
            $languages = Language::factory(15)->create();
            $this->seedingProgress['languages'] = $languages->count();
        } else {
            $this->seedingProgress['languages'] = Language::count();
        }

        // Job stages
        if (0 == JobStage::count()) {
            $jobStages = JobStage::factory(6)->create();
            $this->seedingProgress['job_stages'] = $jobStages->count();
        } else {
            $this->seedingProgress['job_stages'] = JobStage::count();
        }

        $this->command->info('✅ Master data tables seeded');
    }

    /**
     * Phase 3: Seed users.
     */
    private function seedUsers(): void
    {
        $this->command->info('👥 Seeding users...');

        if (0 == User::count()) {
            // Create admin users
            $admins = User::factory(5)->create([
                'user_type' => 1, // Admin
                'email_verified_at' => now(),
            ]);

            // Create employer users
            $employers = User::factory(100)->create([
                'user_type' => 2, // Employer
                'email_verified_at' => now(),
            ]);

            // Create candidate users
            $candidates = User::factory(200)->create([
                'user_type' => 3, // Candidate
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
            $employers = User::where('user_type', 2)->get();
            $cities = $this->generatedData['cities'] ?? City::all();
            $industries = Industry::all();
            $companySizes = CompanySize::all();
            $ownershipTypes = OwnerShipType::all();

            $companies = collect();

            $employers->take(80)->each(function ($employer) use ($cities, $industries, $companySizes, $ownershipTypes, &$companies) {
                $city = $cities->isNotEmpty() ? $cities->random() : null;

                $company = Company::factory()->create([
                    'user_id' => $employer->id,
                    'industry_id' => $industries->isNotEmpty() ? $industries->random()->id : null,
                    'ownership_type_id' => $ownershipTypes->isNotEmpty() ? $ownershipTypes->random()->id : null,
                    'company_size_id' => $companySizes->isNotEmpty() ? $companySizes->random()->id : null,
                    'city_id' => $city?->id,
                    'state_id' => $city?->state_id,
                    'country_id' => $city?->state?->country_id,
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
     * Phase 4: Seed job categories.
     */
    private function seedJobCategories(): void
    {
        $this->command->info('📂 Seeding job categories...');

        if (0 == JobCategory::count()) {
            $categories = [
                'Software Development', 'Data Science', 'Product Management', 'Marketing', 'Sales',
                'Human Resources', 'Finance', 'Operations', 'Customer Support', 'Design',
                'Engineering', 'Healthcare', 'Education', 'Legal', 'Consulting',
                'Project Management', 'Quality Assurance', 'DevOps', 'Security', 'Research',
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
                // Programming Languages
                'PHP', 'JavaScript', 'Python', 'Java', 'C#', 'Ruby', 'Go', 'Rust', 'Swift', 'Kotlin',
                // Web Technologies
                'Laravel', 'React', 'Vue.js', 'Angular', 'Node.js', 'Express.js', 'Django', 'Flask',
                // Databases
                'MySQL', 'PostgreSQL', 'MongoDB', 'Redis', 'Oracle', 'SQL Server', 'SQLite',
                // Cloud & DevOps
                'AWS', 'Azure', 'Google Cloud', 'Docker', 'Kubernetes', 'Jenkins', 'Git', 'CI/CD',
                // Design & UX
                'Adobe Photoshop', 'Adobe Illustrator', 'Figma', 'Sketch', 'UI/UX Design', 'Prototyping',
                // Project Management
                'Agile', 'Scrum', 'Kanban', 'JIRA', 'Trello', 'Project Planning', 'Risk Management',
                // Soft Skills
                'Communication', 'Leadership', 'Problem Solving', 'Teamwork', 'Time Management', 'Critical Thinking',
            ];

            foreach ($skills as $skillName) {
                Skill::factory()->create(['name' => $skillName]);
            }

            // Add additional random skills
            $additionalSkills = Skill::factory(50)->create();

            $this->seedingProgress['skills'] = count($skills) + 50;
            $this->command->info('✅ Skills: '.(count($skills) + 50));
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
            $cities = $this->generatedData['cities'] ?? City::all();

            $jobs = collect();

            // Create 500 jobs
            for ($i = 0; $i < 500; ++$i) {
                $company = $companies->random();
                $city = $cities->isNotEmpty() ? $cities->random() : null;

                $job = Job::factory()->create([
                    'company_id' => $company->id,
                    'job_category_id' => $jobCategories->random()->id,
                    'city_id' => $city?->id ?? $company->city_id,
                    'state_id' => $city?->state_id ?? $company->state_id,
                    'country_id' => $city?->state?->country_id ?? $company->country_id,
                    'is_featured' => rand(0, 100) < 20, // 20% featured
                    'status' => rand(0, 100) < 90 ? 1 : 0, // 90% active
                ]);

                // Attach random skills (2-8 skills per job)
                if ($skills->isNotEmpty()) {
                    $jobSkills = $skills->random(rand(2, min(8, $skills->count())));
                    $job->jobsSkill()->attach($jobSkills);
                }

                $jobs->push($job);
            }

            $this->seedingProgress['jobs'] = 500;
            $this->generatedData['jobs'] = $jobs;
            $this->command->info('✅ Jobs: 500');
        } else {
            $this->seedingProgress['jobs'] = Job::count();
            $this->generatedData['jobs'] = Job::all();
            $this->command->info('✅ Jobs already exist');
        }
    }

    /**
     * Seed candidates and related data.
     */
    private function seedCandidates(): void
    {
        $this->command->info('👨‍💼 Seeding candidates...');

        if (0 == Candidate::count()) {
            $candidateUsers = User::where('user_type', 3)->get();
            $skills = Skill::all();
            $languages = Language::all();

            $candidates = collect();

            $candidateUsers->take(150)->each(function ($user) use ($skills, $languages, &$candidates) {
                $candidate = Candidate::factory()->create([
                    'user_id' => $user->id,
                ]);

                // Attach random skills
                if ($skills->isNotEmpty()) {
                    $candidateSkills = $skills->random(rand(3, min(12, $skills->count())));
                    $user->candidateSkill()->attach($candidateSkills);
                }

                // Attach languages
                if ($languages->isNotEmpty()) {
                    $candidateLanguages = $languages->random(rand(1, min(4, $languages->count())));
                    $user->candidateLanguage()->attach($candidateLanguages);
                }

                // Create education records (1-3 per candidate)
                $educationCount = rand(1, 3);
                for ($i = 0; $i < $educationCount; ++$i) {
                    CandidateEducation::factory()->create([
                        'candidate_id' => $candidate->id,
                    ]);
                }

                // Create experience records (0-4 per candidate)
                $experienceCount = rand(0, 4);
                for ($i = 0; $i < $experienceCount; ++$i) {
                    CandidateExperience::factory()->create([
                        'candidate_id' => $candidate->id,
                    ]);
                }

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
     * Seed job applications and schedules.
     */
    private function seedJobApplications(): void
    {
        $this->command->info('📝 Seeding job applications...');

        if (0 == JobApplication::count()) {
            $candidates = $this->generatedData['candidates'] ?? Candidate::all();
            $jobs = $this->generatedData['jobs'] ?? Job::where('status', 1)->get();
            $jobStages = JobStage::all();

            $applications = collect();

            // Create 800 job applications
            for ($i = 0; $i < 800; ++$i) {
                $candidate = $candidates->random();
                $job = $jobs->random();

                // Avoid duplicate applications
                $exists = JobApplication::where('candidate_id', $candidate->id)
                    ->where('job_id', $job->id)
                    ->exists()
                ;

                if (!$exists) {
                    $application = JobApplication::factory()->create([
                        'candidate_id' => $candidate->id,
                        'job_id' => $job->id,
                        'job_stage_id' => $jobStages->isNotEmpty() ? $jobStages->random()->id : null,
                        'status' => rand(0, 4), // Various application statuses
                    ]);

                    // 30% chance of having a schedule
                    if (rand(0, 100) < 30) {
                        JobApplicationSchedule::factory()->create([
                            'job_application_id' => $application->id,
                        ]);
                    }

                    $applications->push($application);
                }
            }

            $this->seedingProgress['job_applications'] = $applications->count();
            $this->seedingProgress['job_application_schedules'] = JobApplicationSchedule::count();

            $this->command->info("✅ Job Applications: {$applications->count()} with schedules");
        } else {
            $this->seedingProgress['job_applications'] = JobApplication::count();
            $this->seedingProgress['job_application_schedules'] = JobApplicationSchedule::count();
            $this->command->info('✅ Job applications already exist');
        }
    }

    /**
     * Phase 5: Seed content management tables.
     */
    private function seedContentTables(): void
    {
        $this->command->info('📝 Seeding content management tables...');

        // Post categories
        if (0 == PostCategory::count()) {
            $postCategories = PostCategory::factory(10)->create();
            $this->seedingProgress['post_categories'] = $postCategories->count();
        } else {
            $this->seedingProgress['post_categories'] = PostCategory::count();
        }

        // Posts
        if (0 == Post::count()) {
            $posts = Post::factory(50)->create();
            $this->seedingProgress['posts'] = $posts->count();

            // Post comments
            $comments = collect();
            $posts->each(function ($post) use (&$comments) {
                $commentCount = rand(0, 5);
                for ($i = 0; $i < $commentCount; ++$i) {
                    $comment = PostComment::factory()->create(['post_id' => $post->id]);
                    $comments->push($comment);
                }
            });
            $this->seedingProgress['post_comments'] = $comments->count();
        } else {
            $this->seedingProgress['posts'] = Post::count();
            $this->seedingProgress['post_comments'] = PostComment::count();
        }

        // Tags
        if (0 == Tag::count()) {
            $tags = Tag::factory(30)->create();
            $this->seedingProgress['tags'] = $tags->count();
        } else {
            $this->seedingProgress['tags'] = Tag::count();
        }

        // Testimonials
        if (0 == Testimonial::count()) {
            $testimonials = Testimonial::factory(20)->create();
            $this->seedingProgress['testimonials'] = $testimonials->count();
        } else {
            $this->seedingProgress['testimonials'] = Testimonial::count();
        }

        // FAQs
        if (0 == FAQ::count()) {
            $faqs = FAQ::factory(25)->create();
            $this->seedingProgress['faqs'] = $faqs->count();
        } else {
            $this->seedingProgress['faqs'] = FAQ::count();
        }

        // CMS Services
        if (0 == CmsServices::count()) {
            $cmsServices = CmsServices::factory(15)->create();
            $this->seedingProgress['cms_services'] = $cmsServices->count();
        } else {
            $this->seedingProgress['cms_services'] = CmsServices::count();
        }

        $this->command->info('✅ Content management tables seeded');
    }

    /**
     * Phase 6: Seed communication and notification tables.
     */
    private function seedCommunicationTables(): void
    {
        $this->command->info('📧 Seeding communication tables...');

        // Email templates
        if (0 == EmailTemplate::count()) {
            $emailTemplates = EmailTemplate::factory(15)->create();
            $this->seedingProgress['email_templates'] = $emailTemplates->count();
        } else {
            $this->seedingProgress['email_templates'] = EmailTemplate::count();
        }

        // Email jobs
        if (0 == EmailJob::count()) {
            $emailJobs = EmailJob::factory(30)->create();
            $this->seedingProgress['email_jobs'] = $emailJobs->count();
        } else {
            $this->seedingProgress['email_jobs'] = EmailJob::count();
        }

        // Notifications
        if (0 == Notification::count()) {
            $notifications = Notification::factory(100)->create();
            $this->seedingProgress['notifications'] = $notifications->count();
        } else {
            $this->seedingProgress['notifications'] = Notification::count();
        }

        // Notification settings
        if (0 == NotificationSetting::count()) {
            $notificationSettings = NotificationSetting::factory(20)->create();
            $this->seedingProgress['notification_settings'] = $notificationSettings->count();
        } else {
            $this->seedingProgress['notification_settings'] = NotificationSetting::count();
        }

        // News letters
        if (0 == NewsLetter::count()) {
            $newsLetters = NewsLetter::factory(50)->create();
            $this->seedingProgress['news_letters'] = $newsLetters->count();
        } else {
            $this->seedingProgress['news_letters'] = NewsLetter::count();
        }

        // Inquiries
        if (0 == Inquiry::count()) {
            $inquiries = Inquiry::factory(40)->create();
            $this->seedingProgress['inquiries'] = $inquiries->count();
        } else {
            $this->seedingProgress['inquiries'] = Inquiry::count();
        }

        $this->command->info('✅ Communication tables seeded');
    }

    /**
     * Phase 7: Seed media and slider tables.
     */
    private function seedMediaTables(): void
    {
        $this->command->info('🎨 Seeding media tables...');

        // Image sliders
        if (0 == ImageSlider::count()) {
            $imageSliders = ImageSlider::factory(10)->create();
            $this->seedingProgress['image_sliders'] = $imageSliders->count();
        } else {
            $this->seedingProgress['image_sliders'] = ImageSlider::count();
        }

        // Header sliders
        if (0 == HeaderSlider::count()) {
            $headerSliders = HeaderSlider::factory(8)->create();
            $this->seedingProgress['header_sliders'] = $headerSliders->count();
        } else {
            $this->seedingProgress['header_sliders'] = HeaderSlider::count();
        }

        // Branding sliders
        if (0 == BrandingSliders::count()) {
            $brandingSliders = BrandingSliders::factory(6)->create();
            $this->seedingProgress['branding_sliders'] = $brandingSliders->count();
        } else {
            $this->seedingProgress['branding_sliders'] = BrandingSliders::count();
        }

        // Files
        if (0 == File::count()) {
            $files = File::factory(50)->create();
            $this->seedingProgress['files'] = $files->count();
        } else {
            $this->seedingProgress['files'] = File::count();
        }

        $this->command->info('✅ Media tables seeded');
    }

    /**
     * Phase 8: Seed social features.
     */
    private function seedSocialFeatures(): void
    {
        $this->command->info('🤝 Seeding social features...');

        $users = $this->generatedData['users'] ?? User::all();
        $companies = $this->generatedData['companies'] ?? Company::all();
        $jobs = $this->generatedData['jobs'] ?? Job::all();
        $candidates = $this->generatedData['candidates'] ?? Candidate::all();

        // Social accounts
        if (0 == SocialAccount::count()) {
            $socialAccounts = collect();
            $users->take(50)->each(function ($user) use (&$socialAccounts) {
                if (rand(0, 100) < 30) { // 30% chance
                    $account = SocialAccount::factory()->create(['user_id' => $user->id]);
                    $socialAccounts->push($account);
                }
            });
            $this->seedingProgress['social_accounts'] = $socialAccounts->count();
        } else {
            $this->seedingProgress['social_accounts'] = SocialAccount::count();
        }

        // Favourite companies
        if (0 == FavouriteCompany::count()) {
            $favouriteCompanies = collect();
            for ($i = 0; $i < 100; ++$i) {
                $user = $users->random();
                $company = $companies->random();

                $exists = FavouriteCompany::where('user_id', $user->id)
                    ->where('company_id', $company->id)
                    ->exists()
                ;

                if (!$exists) {
                    $favourite = FavouriteCompany::factory()->create([
                        'user_id' => $user->id,
                        'company_id' => $company->id,
                    ]);
                    $favouriteCompanies->push($favourite);
                }
            }
            $this->seedingProgress['favourite_companies'] = $favouriteCompanies->count();
        } else {
            $this->seedingProgress['favourite_companies'] = FavouriteCompany::count();
        }

        // Favourite jobs
        if (0 == FavouriteJob::count()) {
            $favouriteJobs = collect();
            for ($i = 0; $i < 150; ++$i) {
                $user = $users->random();
                $job = $jobs->random();

                $exists = FavouriteJob::where('user_id', $user->id)
                    ->where('job_id', $job->id)
                    ->exists()
                ;

                if (!$exists) {
                    $favourite = FavouriteJob::factory()->create([
                        'user_id' => $user->id,
                        'job_id' => $job->id,
                    ]);
                    $favouriteJobs->push($favourite);
                }
            }
            $this->seedingProgress['favourite_jobs'] = $favouriteJobs->count();
        } else {
            $this->seedingProgress['favourite_jobs'] = FavouriteJob::count();
        }

        // Reported jobs
        if (0 == ReportedJob::count()) {
            $reportedJobs = ReportedJob::factory(20)->create();
            $this->seedingProgress['reported_jobs'] = $reportedJobs->count();
        } else {
            $this->seedingProgress['reported_jobs'] = ReportedJob::count();
        }

        // Reported companies
        if (0 == ReportedToCompany::count()) {
            $reportedCompanies = ReportedToCompany::factory(15)->create();
            $this->seedingProgress['reported_to_companies'] = $reportedCompanies->count();
        } else {
            $this->seedingProgress['reported_to_companies'] = ReportedToCompany::count();
        }

        // Featured records
        if (0 == FeaturedRecord::count()) {
            $featuredRecords = FeaturedRecord::factory(30)->create();
            $this->seedingProgress['featured_records'] = $featuredRecords->count();
        } else {
            $this->seedingProgress['featured_records'] = FeaturedRecord::count();
        }

        $this->command->info('✅ Social features seeded');
    }

    /**
     * Phase 9: Seed financial system.
     */
    private function seedFinancialTables(): void
    {
        $this->command->info('💳 Seeding financial system...');

        // Plans
        if (0 == Plan::count()) {
            $plans = Plan::factory(5)->create();
            $this->seedingProgress['plans'] = $plans->count();
            $this->generatedData['plans'] = $plans;
        } else {
            $this->seedingProgress['plans'] = Plan::count();
            $this->generatedData['plans'] = Plan::all();
        }

        // Subscriptions
        if (0 == Subscription::count()) {
            $users = $this->generatedData['users'] ?? User::where('user_type', 2)->get(); // Employers
            $plans = $this->generatedData['plans'] ?? Plan::all();

            $subscriptions = collect();
            $users->take(30)->each(function ($user) use ($plans, &$subscriptions) {
                if (rand(0, 100) < 60) { // 60% chance of having subscription
                    $subscription = Subscription::factory()->create([
                        'user_id' => $user->id,
                        'plan_id' => $plans->random()->id,
                    ]);
                    $subscriptions->push($subscription);
                }
            });

            $this->seedingProgress['subscriptions'] = $subscriptions->count();
            $this->generatedData['subscriptions'] = $subscriptions;
        } else {
            $this->seedingProgress['subscriptions'] = Subscription::count();
            $this->generatedData['subscriptions'] = Subscription::all();
        }

        // Transactions
        if (0 == Transaction::count()) {
            $subscriptions = $this->generatedData['subscriptions'] ?? Subscription::all();

            $transactions = collect();
            $subscriptions->each(function ($subscription) use (&$transactions) {
                $transactionCount = rand(1, 3);
                for ($i = 0; $i < $transactionCount; ++$i) {
                    $transaction = Transaction::factory()->create([
                        'user_id' => $subscription->user_id,
                        'subscription_id' => $subscription->id,
                    ]);
                    $transactions->push($transaction);
                }
            });

            $this->seedingProgress['transactions'] = $transactions->count();
        } else {
            $this->seedingProgress['transactions'] = Transaction::count();
        }

        $this->command->info('✅ Financial system seeded');
    }

    /**
     * Phase 10: Seed system utilities.
     */
    private function seedSystemUtilities(): void
    {
        $this->command->info('⚙️ Seeding system utilities...');

        // Noticeboards
        if (0 == Noticeboard::count()) {
            $noticeboards = Noticeboard::factory(15)->create();
            $this->seedingProgress['noticeboards'] = $noticeboards->count();
        } else {
            $this->seedingProgress['noticeboards'] = Noticeboard::count();
        }

        // Todos
        if (0 == Todo::count()) {
            $todos = Todo::factory(25)->create();
            $this->seedingProgress['todos'] = $todos->count();
        } else {
            $this->seedingProgress['todos'] = Todo::count();
        }

        $this->command->info('✅ System utilities seeded');
    }

    /**
     * Generate comprehensive seeding report.
     */
    private function generateComprehensiveReport(): void
    {
        $this->command->info('');
        $this->command->info('📊 COMPREHENSIVE SEEDING REPORT');
        $this->command->info('='.str_repeat('=', 50));

        $categories = [
            'CORE SYSTEM' => [
                'countries', 'states', 'cities', 'users', 'settings',
                'front_settings', 'env_settings', 'permissions', 'roles',
            ],
            'MASTER DATA' => [
                'industries', 'company_sizes', 'ownership_types', 'functional_areas',
                'career_levels', 'salary_currencies', 'salary_periods', 'job_types',
                'job_shifts', 'required_degree_levels', 'marital_status', 'languages', 'job_stages',
            ],
            'JOB PORTAL' => [
                'companies', 'job_categories', 'skills', 'jobs', 'candidates',
                'candidate_educations', 'candidate_experiences', 'job_applications', 'job_application_schedules',
            ],
            'CONTENT MANAGEMENT' => [
                'post_categories', 'posts', 'post_comments', 'tags', 'testimonials', 'faqs', 'cms_services',
            ],
            'COMMUNICATION' => [
                'email_templates', 'email_jobs', 'notifications', 'notification_settings', 'news_letters', 'inquiries',
            ],
            'MEDIA' => [
                'image_sliders', 'header_sliders', 'branding_sliders', 'files',
            ],
            'SOCIAL FEATURES' => [
                'social_accounts', 'favourite_companies', 'favourite_jobs', 'reported_jobs',
                'reported_to_companies', 'featured_records',
            ],
            'FINANCIAL' => [
                'plans', 'subscriptions', 'transactions',
            ],
            'UTILITIES' => [
                'noticeboards', 'todos',
            ],
        ];

        $grandTotal = 0;

        foreach ($categories as $categoryName => $tables) {
            $this->command->info('');
            $this->command->info("📂 {$categoryName}:");
            $categoryTotal = 0;

            foreach ($tables as $table) {
                $count = $this->seedingProgress[$table] ?? 0;
                $this->command->info("  ✅ {$table}: {$count}");
                $categoryTotal += $count;
                $grandTotal += $count;
            }

            $this->command->info("  📊 Category Total: {$categoryTotal}");
        }

        $this->command->info('');
        $this->command->info('='.str_repeat('=', 50));
        $this->command->info("🎉 GRAND TOTAL RECORDS: {$grandTotal}");
        $this->command->info('='.str_repeat('=', 50));
        $this->command->info('');
        $this->command->info('🚀 NEXT STEPS:');
        $this->command->info('1. Run: php artisan storage:link');
        $this->command->info('2. Clear caches: php artisan cache:clear');
        $this->command->info('3. Run: php artisan config:clear');
        $this->command->info('4. Access your application and explore the seeded data');
        $this->command->info('');
        $this->command->info('✅ All tables have been successfully seeded!');
    }
}
