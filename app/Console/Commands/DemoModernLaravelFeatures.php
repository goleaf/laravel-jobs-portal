<?php

namespace App\Console\Commands;

use App\Helpers\ValidationHelper;
use App\Models\CustomMedia;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

/**
 * Demonstration of Modern Laravel Techniques Integration
 *
 * Showcases Laravel 12.16+ features integrated from:
 * - Arr::hasAll() validation (Laravel 12.16)
 * - Collection groupBy and filtering techniques
 * - Enhanced validation patterns
 * - Modern factory patterns
 */
class DemoModernLaravelFeatures extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'demo:modern-laravel {--feature=all : Specific feature to demo}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Demonstrate modern Laravel techniques integrated into the job portal';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $feature = $this->option('feature');

        $this->info('🚀 Modern Laravel Techniques Demo - Job Portal Application');
        $this->info('Showcasing Laravel 12.16+ features and best practices');
        $this->newLine();

        match ($feature) {
            'validation' => $this->demoArrHasAllValidation(),
            'collections' => $this->demoCollectionTechniques(),
            'factories' => $this->demoEnhancedFactories(),
            'helpers' => $this->demoValidationHelpers(),
            'all' => $this->demoAllFeatures(),
            default => $this->demoAllFeatures(),
        };

        return Command::SUCCESS;
    }

    /**
     * Demo Arr::hasAll() validation from Laravel 12.16
     */
    private function demoArrHasAllValidation(): void
    {
        $this->info('📋 Laravel 12.16 Arr::hasAll() Validation Demo');
        $this->line('Source: https://ashallendesign.co.uk/blog/arr-has-all-php-array');
        $this->newLine();

        // Demo 1: Basic validation
        $userData = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'location' => 'New York',
        ];

        $requiredFields = ['name', 'email'];
        $hasAllRequired = Arr::hasAll($userData, $requiredFields);

        $this->line('✅ User data validation:');
        $this->line('   Required fields: '.implode(', ', $requiredFields));
        $this->line('   Has all required: '.($hasAllRequired ? 'YES' : 'NO'));

        // Demo 2: Nested validation with dot notation
        $jobApplication = [
            'personal' => [
                'first_name' => 'Jane',
                'last_name' => 'Smith',
                'email' => 'jane@example.com',
            ],
            'experience' => [
                'years' => 5,
                'level' => 'senior',
            ],
        ];

        $nestedRequired = ['personal.first_name', 'personal.email', 'experience.years'];
        $hasNestedRequired = Arr::hasAll($jobApplication, $nestedRequired);

        $this->line('✅ Nested validation with dot notation:');
        $this->line('   Required nested fields: '.implode(', ', $nestedRequired));
        $this->line('   Has all nested required: '.($hasNestedRequired ? 'YES' : 'NO'));

        // Demo 3: Model validation
        $mediaData = [
            'name' => 'profile-image',
            'file_name' => 'profile.jpg',
            'mime_type' => 'image/jpeg',
            'collection_name' => 'avatars',
            'size' => 2048,
        ];

        $isValidMedia = CustomMedia::validateRequiredFields($mediaData);
        $this->line('✅ CustomMedia model validation:');
        $this->line('   Media data valid: '.($isValidMedia ? 'YES' : 'NO'));

        $this->newLine();
    }

    /**
     * Demo Collection techniques for grouping and filtering
     */
    private function demoCollectionTechniques(): void
    {
        $this->info('📊 Collection GroupBy and Filtering Demo');
        $this->line('Inspired by: Laravel collection groupBy and having techniques');
        $this->newLine();

        // Sample job data
        $jobs = collect([
            ['title' => 'Laravel Developer', 'category' => 'Development', 'salary' => 75000, 'experience' => 'Senior'],
            ['title' => 'React Developer', 'category' => 'Development', 'salary' => 65000, 'experience' => 'Mid'],
            ['title' => 'UI Designer', 'category' => 'Design', 'salary' => 55000, 'experience' => 'Junior'],
            ['title' => 'Senior Designer', 'category' => 'Design', 'salary' => 70000, 'experience' => 'Senior'],
            ['title' => 'DevOps Engineer', 'category' => 'Operations', 'salary' => 80000, 'experience' => 'Senior'],
        ]);

        // Group by category
        $jobsByCategory = $jobs->groupBy('category');
        $this->line('✅ Jobs grouped by category:');
        foreach ($jobsByCategory as $category => $categoryJobs) {
            $this->line("   {$category}: {$categoryJobs->count()} jobs");
        }

        // Filter high-paying jobs and group by experience
        $highPayingJobs = $jobs->filter(fn ($job) => $job['salary'] > 60000)
            ->groupBy('experience');

        $this->line('✅ High-paying jobs (>60k) by experience:');
        foreach ($highPayingJobs as $level => $levelJobs) {
            $avgSalary = $levelJobs->avg('salary');
            $this->line("   {$level}: {$levelJobs->count()} jobs, avg salary: $".number_format($avgSalary));
        }

        // Demo Collection::wrap() for consistent handling
        $singleJob = ['title' => 'PHP Developer', 'salary' => 70000];
        $wrappedJob = Collection::wrap($singleJob);
        $this->line('✅ Collection::wrap() demo:');
        $this->line("   Wrapped single job into collection: {$wrappedJob->count()} item(s)");

        $this->newLine();
    }

    /**
     * Demo enhanced factory patterns
     */
    private function demoEnhancedFactories(): void
    {
        $this->info('🏭 Enhanced Factory Patterns Demo');
        $this->newLine();

        try {
            // Demo factory with validation
            $this->line('✅ Creating CustomMedia with enhanced validation:');
            $media = CustomMedia::factory()->withValidation()->make();
            $this->line("   Created media: {$media->name} ({$media->mime_type})");

            // Demo specific factory states
            $this->line('✅ Creating image-specific media:');
            $imageMedia = CustomMedia::factory()->image()->make();
            $this->line("   Created image: {$imageMedia->file_name} ({$imageMedia->mime_type})");

            // Demo collection factory
            $this->line('✅ Creating media for specific collection:');
            $avatarMedia = CustomMedia::factory()->forCollection('avatars')->make();
            $this->line("   Created avatar media: {$avatarMedia->name} in '{$avatarMedia->collection_name}' collection");

        } catch (\Exception $e) {
            $this->error('Factory demo error: '.$e->getMessage());
        }

        $this->newLine();
    }

    /**
     * Demo ValidationHelper class
     */
    private function demoValidationHelpers(): void
    {
        $this->info('🛡️ ValidationHelper Class Demo');
        $this->newLine();

        // Demo job application validation
        $jobAppData = [
            'user_id' => 1,
            'job_id' => 1,
            'resume' => 'resume.pdf',
            'cover_letter' => 'Dear hiring manager...',
        ];

        $isValidJobApp = ValidationHelper::validateJobApplicationData($jobAppData);
        $this->line('✅ Job application validation:');
        $this->line('   Application data valid: '.($isValidJobApp ? 'YES' : 'NO'));

        // Demo nested profile validation
        $profileData = [
            'personal' => [
                'first_name' => 'John',
                'last_name' => 'Doe',
                'email' => 'john@example.com',
            ],
            'contact' => [
                'phone' => '+1234567890',
                'address' => '123 Main St',
            ],
        ];

        $isValidProfile = ValidationHelper::validateUserProfileData($profileData);
        $this->line('✅ User profile validation:');
        $this->line('   Profile data valid: '.($isValidProfile ? 'YES' : 'NO'));

        // Demo search filter validation
        $searchFilters = [
            'keyword' => 'Laravel',
            'location' => 'Remote',
            'job_category_id' => 1,
            'salary_min' => 50000,
        ];

        $areValidFilters = ValidationHelper::validateSearchFilters($searchFilters);
        $this->line('✅ Search filters validation:');
        $this->line('   Search filters valid: '.($areValidFilters ? 'YES' : 'NO'));

        $this->newLine();
    }

    /**
     * Demo all features
     */
    private function demoAllFeatures(): void
    {
        $this->demoArrHasAllValidation();
        $this->demoCollectionTechniques();
        $this->demoEnhancedFactories();
        $this->demoValidationHelpers();

        $this->info('🎉 Modern Laravel Integration Complete!');
        $this->line('Successfully demonstrated:');
        $this->line('• Laravel 12.16 Arr::hasAll() validation');
        $this->line('• Collection groupBy and filtering');
        $this->line('• Enhanced factory patterns');
        $this->line('• Comprehensive validation helpers');
        $this->line('• Nested data validation with dot notation');
        $this->line('• Modern Laravel best practices');
        $this->newLine();

        $this->info('📚 Resources integrated:');
        $this->line('• Ash Allen Design - Arr::hasAll() validation');
        $this->line('• Laravel News - Collection techniques');
        $this->line('• Modern Laravel patterns and best practices');
    }
}
