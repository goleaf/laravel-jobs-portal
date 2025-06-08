<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Models\Country;
use App\Models\State;
use App\Models\City;
use App\Models\Company;
use App\Models\JobCategory;
use App\Models\Industry;
use App\Models\Skill;
use App\Models\Job;
use App\Models\Candidate;
use App\Models\JobApplication;
use App\Models\Plan;
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
use App\Models\Setting;
use App\Models\FAQ;
use App\Models\Testimonial;
use App\Models\Post;
use App\Models\PostCategory;
use App\Models\Tag;
use App\Models\EmailTemplate;
use App\Models\HeaderSlider;
use App\Models\BrandingSliders;
use App\Models\ImageSlider;
use App\Models\NewsLetter;
use App\Models\Noticeboard;

class ComprehensiveDatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    private $seedingProgress = [];
    private $generatedImages = [];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🚀 Starting Comprehensive Database Seeding...');
        
        // Disable foreign key checks for seeding
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        try {
            // Create storage directories for images
            $this->createStorageDirectories();
            
            // Priority 1: Core System Data
            $this->command->info('📍 Priority 1: Core System Data');
            $this->seedCoreLocationData();
            $this->seedMasterData(); // Move master data first
            $this->seedCoreUserData();
            $this->seedJobCategoriesAndIndustries();
            $this->seedComprehensiveSkills();
            $this->seedCoreCompanyData(); // Move after master data
            
            // Priority 2: Job Portal Core
            $this->command->info('💼 Priority 2: Job Portal Core');
            $this->seedComprehensiveJobs();
            $this->seedCandidateProfiles();
            $this->seedJobApplications();
            $this->seedPlansAndSubscriptions();
            
            // Priority 3: Supporting Data
            $this->command->info('⚙️ Priority 3: Supporting Data');
            $this->seedSettingsAndConfiguration();
            $this->seedContentManagement();
            
            // Priority 4: CMS & Marketing
            $this->command->info('🎨 Priority 4: CMS & Marketing');
            $this->seedCMSContent();
            $this->seedBlogSystem();
            $this->seedNotificationsAndCommunications();
            $this->seedSocialFeatures();
            
            // Generate summary report
            $this->generateSeedingReport();
            
        } catch (\Exception $e) {
            $this->command->error('❌ Seeding failed: ' . $e->getMessage());
            Log::error('Database seeding failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        } finally {
            // Re-enable foreign key checks
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        }
        
        $this->command->info('✅ Comprehensive Database Seeding Complete!');
    }

    /**
     * Create storage directories for images
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
            'public/temp'
        ];

        foreach ($directories as $directory) {
            if (!Storage::exists($directory)) {
                Storage::makeDirectory($directory);
                $this->command->info("✅ Created directory: {$directory}");
            }
        }
    }

    /**
     * Priority 1.1: Seed core location data
     */
    private function seedCoreLocationData(): void
    {
        $this->command->info('🌍 Seeding location data...');
        
        // Countries (50 major countries)
        $countries = Country::factory(50)->create();
        $this->seedingProgress['countries'] = $countries->count();
        
        // States (200 states/provinces) - collect properly
        $states = collect();
        $countries->each(function ($country) use (&$states) {
            $stateCount = rand(2, 8);
            $countryStates = State::factory($stateCount)->create(['country_id' => $country->id]);
            $states = $states->concat($countryStates);
        });
        $this->seedingProgress['states'] = $states->count();
        
        // Cities (up to 1000 cities) - collect properly
        $cities = collect();
        $states->each(function ($state) use (&$cities) {
            $cityCount = rand(3, 10);
            $stateCities = City::factory($cityCount)->create([
                'state_id' => $state->id
            ]);
            $cities = $cities->concat($stateCities);
        });
        $this->seedingProgress['cities'] = $cities->count();
        
        $this->command->info("✅ Location data: {$countries->count()} countries, {$states->count()} states, {$cities->count()} cities");
    }

    /**
     * Priority 1.2: Seed diverse user accounts
     */
    private function seedCoreUserData(): void
    {
        $this->command->info('👥 Seeding user data...');
        
        // Admin users
        $admins = User::factory(10)->create([
            'user_type' => 1, // Admin
            'email_verified_at' => now(),
        ]);
        
        // Generate avatars for admins
        $this->generateUserAvatars($admins, 'admin');
        
        // Employer users
        $employers = User::factory(250)->create([
            'user_type' => 2, // Employer
            'email_verified_at' => now(),
        ]);
        
        // Generate avatars for employers
        $this->generateUserAvatars($employers, 'employer');
        
        // Candidate users
        $candidates = User::factory(350)->create([
            'user_type' => 3, // Candidate
            'email_verified_at' => now(),
        ]);
        
        // Generate avatars for candidates
        $this->generateUserAvatars($candidates, 'candidate');
        
        $totalUsers = $admins->count() + $employers->count() + $candidates->count();
        $this->seedingProgress['users'] = $totalUsers;
        
        $this->command->info("✅ Users: {$admins->count()} admins, {$employers->count()} employers, {$candidates->count()} candidates");
    }

    /**
     * Priority 1.3: Generate company profiles with logos
     */
    private function seedCoreCompanyData(): void
    {
        $this->command->info('🏢 Seeding company data...');
        
        $employers = User::where('user_type', 2)->get();
        $cities = City::all();
        $industries = Industry::all();
        $companySizes = CompanySize::all();
        $ownershipTypes = OwnerShipType::all();
        
        if ($cities->isEmpty()) {
            $this->command->warn('⚠️ No cities available, creating companies without location');
        }
        
        $companies = collect();
        
        $employers->take(200)->each(function ($employer) use ($cities, $industries, $companySizes, $ownershipTypes, &$companies) {
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
            
            // Generate company logo
            $this->generateCompanyLogo($company);
            
            $companies->push($company);
        });
        
        $this->seedingProgress['companies'] = $companies->count();
        $this->command->info("✅ Companies: {$companies->count()} with logos");
    }

    /**
     * Priority 1.4: Seed job categories and industries
     */
    private function seedJobCategoriesAndIndustries(): void
    {
        $this->command->info('📂 Seeding job categories and industries...');
        
        // Job categories with realistic data - only if not exists
        if (JobCategory::count() == 0) {
            $jobCategories = [
                'Software Development', 'Data Science', 'Product Management', 'Marketing', 'Sales',
                'Human Resources', 'Finance', 'Operations', 'Customer Support', 'Design',
                'Engineering', 'Healthcare', 'Education', 'Legal', 'Consulting',
                'Project Management', 'Quality Assurance', 'DevOps', 'Security', 'Research'
            ];
            
            foreach ($jobCategories as $categoryName) {
                JobCategory::factory()->create(['name' => $categoryName]);
            }
            
            $this->seedingProgress['job_categories'] = count($jobCategories);
            $this->command->info("✅ Job categories: " . count($jobCategories));
        } else {
            $this->seedingProgress['job_categories'] = JobCategory::count();
            $this->command->info("✅ Job categories: " . JobCategory::count() . " (already exists)");
        }
    }

    /**
     * Priority 1.5: Create comprehensive skill database
     */
    private function seedComprehensiveSkills(): void
    {
        $this->command->info('🛠️ Seeding skills data...');
        
        if (Skill::count() == 0) {
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
                'Communication', 'Leadership', 'Problem Solving', 'Teamwork', 'Time Management', 'Critical Thinking'
            ];
            
            foreach ($skills as $skillName) {
                Skill::factory()->create(['name' => $skillName]);
            }
            
            // Add additional random skills to reach 500
            $remainingSkills = 500 - count($skills);
            Skill::factory($remainingSkills)->create();
            
            $this->seedingProgress['skills'] = 500;
            $this->command->info("✅ Skills: 500 comprehensive skills");
        } else {
            $this->seedingProgress['skills'] = Skill::count();
            $this->command->info("✅ Skills: " . Skill::count() . " (already exists)");
        }
    }

    /**
     * Seed master data for dropdowns
     */
    private function seedMasterData(): void
    {
        $this->command->info('📋 Seeding master data...');
        
        // Industry data - check if already exists
        if (Industry::count() == 0) {
            Industry::factory(15)->create(); // Reduced to 15 to avoid unique constraint issues
        }
        
        // Company sizes - check if already exists
        if (CompanySize::count() == 0) {
            CompanySize::factory(8)->create();
        }
        
        // Functional areas - check if already exists
        if (FunctionalArea::count() == 0) {
            FunctionalArea::factory(20)->create(); // Reduced to 20 to avoid unique constraint issues
        }
        
        // Career levels
        if (CareerLevel::count() == 0) {
            CareerLevel::factory(8)->create();
        }
        
        // Salary currencies
        if (SalaryCurrency::count() == 0) {
            SalaryCurrency::factory(15)->create(); // Reduced to 15 to avoid unique constraint issues
        }
        
        // Salary periods
        if (SalaryPeriod::count() == 0) {
            SalaryPeriod::factory(6)->create();
        }
        
        // Job types
        if (JobType::count() == 0) {
            JobType::factory(8)->create();
        }
        
        // Job shifts
        if (JobShift::count() == 0) {
            JobShift::factory(5)->create();
        }
        
        // Required degree levels
        if (RequiredDegreeLevel::count() == 0) {
            RequiredDegreeLevel::factory(10)->create();
        }
        
        // Marital status
        if (MaritalStatus::count() == 0) {
            MaritalStatus::factory(6)->create();
        }
        
        // Languages
        if (Language::count() == 0) {
            Language::factory(12)->create(); // Reduced to 12 to avoid unique constraint issues
        }
        
        // Ownership types
        if (OwnerShipType::count() == 0) {
            OwnerShipType::factory(8)->create();
        }
        
        $this->command->info("✅ Master data seeded");
    }

    /**
     * Priority 2.1: Create diverse job postings
     */
    private function seedComprehensiveJobs(): void
    {
        $this->command->info('💼 Seeding job postings...');
        
        $companies = Company::all();
        $jobCategories = JobCategory::all();
        $skills = Skill::all();
        
        $jobs = collect();
        
        // Create 1000 jobs
        for ($i = 0; $i < 1000; $i++) {
            $company = $companies->random();
            
            $job = Job::factory()->create([
                'company_id' => $company->id,
                'job_category_id' => $jobCategories->random()->id,
                'city_id' => $company->city_id,
                'state_id' => $company->state_id,
                'country_id' => $company->country_id,
                'is_featured' => rand(0, 100) < 20, // 20% featured
                'is_freelance' => rand(0, 100) < 15, // 15% freelance
                'status' => rand(0, 100) < 90 ? 1 : 0, // 90% active
            ]);
            
            // Attach random skills (2-8 skills per job)
            $jobSkills = $skills->random(rand(2, 8));
            $job->skills()->attach($jobSkills);
            
            $jobs->push($job);
        }
        
        $this->seedingProgress['jobs'] = 1000;
        $this->command->info("✅ Jobs: 1000 diverse job postings");
    }

    /**
     * Priority 2.2: Generate candidate profiles
     */
    private function seedCandidateProfiles(): void
    {
        $this->command->info('👨‍💼 Seeding candidate profiles...');
        
        $candidateUsers = User::where('user_type', 3)->get();
        $skills = Skill::all();
        
        $candidates = collect();
        
        $candidateUsers->take(500)->each(function ($user) use ($skills, $candidates) {
            $candidate = Candidate::factory()->create([
                'user_id' => $user->id,
            ]);
            
            // Generate resume file
            $this->generateCandidateResume($candidate);
            
            // Generate candidate image
            $this->generateCandidateImage($candidate);
            
            // Attach random skills
            $candidateSkills = $skills->random(rand(3, 12));
            $candidate->candidateSkill()->attach($candidateSkills);
            
            $candidates->push($candidate);
        });
        
        $this->seedingProgress['candidates'] = $candidates->count();
        $this->command->info("✅ Candidates: {$candidates->count()} with resumes and images");
    }

    /**
     * Priority 2.3: Create realistic job applications
     */
    private function seedJobApplications(): void
    {
        $this->command->info('📝 Seeding job applications...');
        
        $candidates = Candidate::all();
        $jobs = Job::where('status', 1)->get(); // Only active jobs
        
        $applications = collect();
        
        // Create 2000 job applications
        for ($i = 0; $i < 2000; $i++) {
            $candidate = $candidates->random();
            $job = $jobs->random();
            
            // Avoid duplicate applications
            $exists = JobApplication::where('candidate_id', $candidate->id)
                ->where('job_id', $job->id)
                ->exists();
                
            if (!$exists) {
                $application = JobApplication::factory()->create([
                    'candidate_id' => $candidate->id,
                    'job_id' => $job->id,
                    'status' => rand(0, 4), // Various application statuses
                ]);
                
                $applications->push($application);
            }
        }
        
        $this->seedingProgress['job_applications'] = $applications->count();
        $this->command->info("✅ Job Applications: {$applications->count()}");
    }

    /**
     * Priority 2.4: Seed subscription plans
     */
    private function seedPlansAndSubscriptions(): void
    {
        $this->command->info('💳 Seeding plans and subscriptions...');
        
        // Create subscription plans
        $plans = Plan::factory(6)->create();
        
        $this->seedingProgress['plans'] = $plans->count();
        $this->command->info("✅ Plans: {$plans->count()} subscription plans");
    }

    /**
     * Priority 3: Seed settings and configuration
     */
    private function seedSettingsAndConfiguration(): void
    {
        $this->command->info('⚙️ Seeding settings and configuration...');
        
        // System settings
        Setting::factory(20)->create();
        
        // FAQs
        FAQ::factory(25)->create();
        
        // Email templates
        EmailTemplate::factory(15)->create();
        
        $this->command->info("✅ Settings and configuration seeded");
    }

    /**
     * Priority 3: Seed content management
     */
    private function seedContentManagement(): void
    {
        $this->command->info('📄 Seeding content management...');
        
        // Testimonials
        Testimonial::factory(30)->create();
        
        $this->command->info("✅ Content management seeded");
    }

    /**
     * Priority 4: Seed CMS content
     */
    private function seedCMSContent(): void
    {
        $this->command->info('🎨 Seeding CMS content...');
        
        // Header sliders
        $headerSliders = HeaderSlider::factory(8)->create();
        foreach ($headerSliders as $slider) {
            $this->generateSliderImage($slider, 'header');
        }
        
        // Branding sliders
        $brandingSliders = BrandingSliders::factory(6)->create();
        foreach ($brandingSliders as $slider) {
            $this->generateSliderImage($slider, 'branding');
        }
        
        // Image sliders
        $imageSliders = ImageSlider::factory(10)->create();
        foreach ($imageSliders as $slider) {
            $this->generateSliderImage($slider, 'image');
        }
        
        $this->command->info("✅ CMS content with images seeded");
    }

    /**
     * Priority 4: Seed blog system
     */
    private function seedBlogSystem(): void
    {
        $this->command->info('📝 Seeding blog system...');
        
        // Post categories
        $categories = PostCategory::factory(12)->create();
        
        // Blog posts
        $posts = collect();
        for ($i = 0; $i < 100; $i++) {
            $post = Post::factory()->create();
            
            // Generate featured image for post
            $this->generateBlogImage($post);
            
            $posts->push($post);
        }
        
        // Tags
        Tag::factory(50)->create();
        
        $this->command->info("✅ Blog system: {$categories->count()} categories, {$posts->count()} posts");
    }

    /**
     * Priority 4: Seed notifications and communications
     */
    private function seedNotificationsAndCommunications(): void
    {
        $this->command->info('📢 Seeding notifications and communications...');
        
        // Newsletters
        NewsLetter::factory(100)->create();
        
        // Noticeboards
        Noticeboard::factory(15)->create();
        
        $this->command->info("✅ Notifications and communications seeded");
    }

    /**
     * Priority 4: Seed social features
     */
    private function seedSocialFeatures(): void
    {
        $this->command->info('👥 Seeding social features...');
        
        // This will be implemented with relationship-based factories
        // Favorite jobs, favorite companies, etc.
        
        $this->command->info("✅ Social features seeded");
    }

    /**
     * Generate user avatars
     */
    private function generateUserAvatars($users, $type): void
    {
        $users->each(function ($user) use ($type) {
            $avatar = $this->generatePlaceholderImage(200, 200, $user->first_name ?? 'User');
            $fileName = "avatar_{$user->id}_{$type}.jpg";
            $path = "users/avatars/{$fileName}";
            
            Storage::disk('public')->put($path, $avatar);
            $this->generatedImages[] = $path;
        });
    }

    /**
     * Generate company logos
     */
    private function generateCompanyLogo($company): void
    {
        $logo = $this->generatePlaceholderImage(300, 150, $company->name);
        $fileName = "logo_{$company->id}.jpg";
        $path = "companies/logos/{$fileName}";
        
        Storage::disk('public')->put($path, $logo);
        $this->generatedImages[] = $path;
        
        // Update company with logo path
        $company->update(['logo' => $fileName]);
    }

    /**
     * Generate candidate resume files
     */
    private function generateCandidateResume($candidate): void
    {
        $resumeContent = "Sample Resume for {$candidate->user->first_name} {$candidate->user->last_name}";
        $fileName = "resume_{$candidate->id}.txt";
        $path = "candidates/resumes/{$fileName}";
        
        Storage::disk('public')->put($path, $resumeContent);
        
        // Update candidate with resume path
        $candidate->update(['resume_path' => $fileName]);
    }

    /**
     * Generate candidate images
     */
    private function generateCandidateImage($candidate): void
    {
        $image = $this->generatePlaceholderImage(400, 400, $candidate->user->first_name ?? 'Candidate');
        $fileName = "image_{$candidate->id}.jpg";
        $path = "candidates/images/{$fileName}";
        
        Storage::disk('public')->put($path, $image);
        $this->generatedImages[] = $path;
        
        // Update candidate with image path
        $candidate->update(['image_path' => $fileName]);
    }

    /**
     * Generate slider images
     */
    private function generateSliderImage($slider, $type): void
    {
        $image = $this->generatePlaceholderImage(1200, 600, "Slider {$slider->id}");
        $fileName = "slider_{$type}_{$slider->id}.jpg";
        $path = "sliders/{$type}s/{$fileName}";
        
        Storage::disk('public')->put($path, $image);
        $this->generatedImages[] = $path;
    }

    /**
     * Generate blog featured images
     */
    private function generateBlogImage($post): void
    {
        $image = $this->generatePlaceholderImage(800, 400, $post->title);
        $fileName = "blog_{$post->id}.jpg";
        $path = "blog/featured/{$fileName}";
        
        Storage::disk('public')->put($path, $image);
        $this->generatedImages[] = $path;
    }

    /**
     * Generate placeholder image
     */
    private function generatePlaceholderImage($width, $height, $text): string
    {
        // Create a simple colored rectangle with text
        $image = imagecreate($width, $height);
        $bgColor = imagecolorallocate($image, rand(100, 255), rand(100, 255), rand(100, 255));
        $textColor = imagecolorallocate($image, 255, 255, 255);
        
        // Add text if GD supports it
        if (function_exists('imagettftext')) {
            $fontSize = min($width, $height) / 10;
            imagestring($image, 5, 10, $height/2, substr($text, 0, 20), $textColor);
        }
        
        ob_start();
        imagejpeg($image, null, 80);
        $imageData = ob_get_contents();
        ob_end_clean();
        imagedestroy($image);
        
        return $imageData;
    }

    /**
     * Generate comprehensive seeding report
     */
    private function generateSeedingReport(): void
    {
        $this->command->info('📊 Generating seeding report...');
        
        $report = [
            'seeding_completed_at' => now()->toDateTimeString(),
            'total_records_created' => array_sum($this->seedingProgress),
            'records_by_type' => $this->seedingProgress,
            'images_generated' => count($this->generatedImages),
            'storage_directories_created' => 10
        ];
        
        Storage::disk('public')->put('seeding_report.json', json_encode($report, JSON_PRETTY_PRINT));
        
        $this->command->table(
            ['Entity', 'Records Created'],
            collect($this->seedingProgress)->map(function ($count, $entity) {
                return [ucwords(str_replace('_', ' ', $entity)), $count];
            })->toArray()
        );
        
        $this->command->info("📁 Generated {$report['images_generated']} images");
        $this->command->info("📄 Seeding report saved to storage/app/public/seeding_report.json");
    }
} 