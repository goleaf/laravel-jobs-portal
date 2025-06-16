<?php

namespace Tests\Feature;

use App\Views\JobTemplateModel;
use App\Views\CompanyTemplateModel;
use App\Views\JobListTemplateModel;
use App\Services\HabrViewsService;
use App\Models\Job;
use App\Models\Company;
use App\Models\User;
use App\Models\JobCategory;
use App\Models\JobType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Collection;
use Tests\TestCase;
use Carbon\Carbon;

/**
 * Habr Views Integration Test Suite
 * 
 * Tests the complete integration of PHP Views package with Laravel Job Portal
 * Based on Habr article patterns for model-oriented templating
 */
class HabrViewsIntegrationTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected HabrViewsService $habrViews;
    protected User $user;
    protected Company $company;
    protected Job $job;
    protected JobCategory $category;
    protected JobType $jobType;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Initialize Habr Views Service
        $this->habrViews = new HabrViewsService();
        
        // Create test data
        $this->createTestData();
    }

    private function createTestData(): void
    {
        // Create user
        $this->user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'user_type' => 'employer',
        ]);

        // Create job category
        $this->category = JobCategory::create([
            'name' => 'Software Development',
            'slug' => 'software-development',
            'description' => 'Software development jobs',
            'is_active' => true,
        ]);

        // Create job type
        $this->jobType = JobType::create([
            'name' => 'Full-time',
            'slug' => 'full-time',
            'description' => 'Full-time positions',
            'is_active' => true,
        ]);

        // Create company
        $this->company = Company::factory()->create([
            'name' => 'Test Company Inc.',
            'slug' => 'test-company-inc',
            'description' => 'A test company for demonstration purposes',
            'website' => 'https://testcompany.com',
            'email' => 'hr@testcompany.com',
            'phone' => '+1234567890',
            'location' => 'San Francisco, CA',
            'is_active' => true,
            'is_verified' => true,
            'user_id' => $this->user->id,
        ]);

        // Create job
        $this->job = Job::factory()->create([
            'title' => 'Senior PHP Developer',
            'slug' => 'senior-php-developer',
            'description' => 'We are looking for an experienced PHP developer to join our team.',
            'requirements' => 'PHP, Laravel, MySQL, 5+ years experience',
            'key_responsibilities' => 'Develop web applications, maintain existing code, mentor junior developers',
            'salary_from' => 80000,
            'salary_to' => 120000,
            'salary_currency' => 'USD',
            'salary_period' => 'year',
            'location' => 'San Francisco, CA',
            'deadline' => Carbon::now()->addMonth(),
            'is_active' => true,
            'is_featured' => true,
            'status' => 'active',
            'experience_years' => 5,
            'employment_type' => 'full-time',
            'work_type' => 'hybrid',
            'company_id' => $this->company->id,
            'category_id' => $this->category->id,
            'job_type_id' => $this->jobType->id,
        ]);
    }

    /** @test */
    public function it_creates_job_template_model_from_job_entity()
    {
        $jobModel = JobTemplateModel::fromJob($this->job);

        $this->assertInstanceOf(JobTemplateModel::class, $jobModel);
        $this->assertEquals('Senior PHP Developer', $jobModel->title);
        $this->assertEquals('We are looking for an experienced PHP developer to join our team.', $jobModel->description);
        $this->assertEquals(80000, $jobModel->salaryFrom);
        $this->assertEquals(120000, $jobModel->salaryTo);
        $this->assertEquals('USD', $jobModel->salaryCurrency);
        $this->assertEquals('year', $jobModel->salaryPeriod);
        $this->assertEquals('San Francisco, CA', $jobModel->location);
        $this->assertTrue($jobModel->isFeatured);
        $this->assertTrue($jobModel->isActive);
        $this->assertEquals('active', $jobModel->status);
        $this->assertEquals(5, $jobModel->experienceYears);
        $this->assertEquals('full-time', $jobModel->employmentType);
        $this->assertEquals('hybrid', $jobModel->workType);
    }

    /** @test */
    public function it_creates_company_template_model_from_company_entity()
    {
        $companyModel = CompanyTemplateModel::fromCompany($this->company);

        $this->assertInstanceOf(CompanyTemplateModel::class, $companyModel);
        $this->assertEquals('Test Company Inc.', $companyModel->name);
        $this->assertEquals('test-company-inc', $companyModel->slug);
        $this->assertEquals('A test company for demonstration purposes', $companyModel->description);
        $this->assertEquals('https://testcompany.com', $companyModel->website);
        $this->assertEquals('hr@testcompany.com', $companyModel->email);
        $this->assertEquals('+1234567890', $companyModel->phone);
        $this->assertEquals('San Francisco, CA', $companyModel->location);
        $this->assertTrue($companyModel->isActive);
        $this->assertTrue($companyModel->isVerified);
    }

    /** @test */
    public function job_template_model_provides_helper_methods()
    {
        $jobModel = JobTemplateModel::fromJob($this->job);

        // Test salary range formatting
        $this->assertEquals('$80,000.00 USD - $120,000.00 USD per year', $jobModel->salaryRange());

        // Test experience level
        $this->assertEquals('Senior (5+ years)', $jobModel->experienceLevel());

        // Test work type icon
        $this->assertEquals('🔄', $jobModel->workTypeIcon());

        // Test employment type badge
        $this->assertStringContains('bg-blue-100 text-blue-800', $jobModel->employmentTypeBadge());

        // Test urgency level
        $this->assertContains($jobModel->getUrgencyLevel(), ['normal', 'moderate', 'urgent', 'critical', 'expired']);

        // Test URLs
        $this->assertStringContains('jobs.show', $jobModel->url());
        $this->assertStringContains('jobs.apply', $jobModel->applyUrl());

        // Test structured data
        $structuredData = $jobModel->structuredData();
        $this->assertEquals('https://schema.org', $structuredData['@context']);
        $this->assertEquals('JobPosting', $structuredData['@type']);
        $this->assertEquals('Senior PHP Developer', $structuredData['title']);
    }

    /** @test */
    public function company_template_model_provides_helper_methods()
    {
        $companyModel = CompanyTemplateModel::fromCompany($this->company);

        // Test website URL formatting
        $this->assertEquals('https://testcompany.com', $companyModel->websiteUrl());

        // Test URLs
        $this->assertStringContains('companies.show', $companyModel->url());
        $this->assertStringContains('companies.jobs', $companyModel->jobsUrl());

        // Test contact info
        $contactInfo = $companyModel->contactInfo();
        $this->assertArrayHasKey('email', $contactInfo);
        $this->assertArrayHasKey('phone', $contactInfo);
        $this->assertArrayHasKey('website', $contactInfo);

        // Test verification badge
        $this->assertStringContains('Verified', $companyModel->verificationBadge());

        // Test statistics summary
        $stats = $companyModel->statisticsSummary();
        $this->assertArrayHasKey('active_jobs', $stats);
        $this->assertArrayHasKey('total_jobs', $stats);
        $this->assertArrayHasKey('applications', $stats);
        $this->assertArrayHasKey('employees', $stats);

        // Test structured data
        $structuredData = $companyModel->structuredData();
        $this->assertEquals('https://schema.org', $structuredData['@context']);
        $this->assertEquals('Organization', $structuredData['@type']);
        $this->assertEquals('Test Company Inc.', $structuredData['name']);
    }

    /** @test */
    public function habr_views_service_initializes_correctly()
    {
        $this->assertInstanceOf(HabrViewsService::class, $this->habrViews);
        $this->assertStringContains('habr-templates', $this->habrViews->getTemplatesPath());
        $this->assertStringContains('habr-cache', $this->habrViews->getCacheDirectory());
    }

    /** @test */
    public function habr_views_service_can_create_models()
    {
        $jobModel = $this->habrViews->createModel(JobTemplateModel::class);
        $this->assertInstanceOf(JobTemplateModel::class, $jobModel);

        $companyModel = $this->habrViews->createModel(CompanyTemplateModel::class);
        $this->assertInstanceOf(CompanyTemplateModel::class, $companyModel);
    }

    /** @test */
    public function habr_views_service_provides_cache_management()
    {
        // Get cache info
        $cacheInfo = $this->habrViews->getCacheInfo();
        $this->assertArrayHasKey('file_count', $cacheInfo);
        $this->assertArrayHasKey('total_size', $cacheInfo);
        $this->assertArrayHasKey('total_size_human', $cacheInfo);
        $this->assertArrayHasKey('cache_directory', $cacheInfo);

        // Clear cache
        $this->assertTrue($this->habrViews->clearCache());
    }

    /** @test */
    public function habr_views_service_provides_performance_benchmarking()
    {
        $performanceStats = $this->habrViews->getPerformanceStats();
        
        $this->assertArrayHasKey('job_rendering', $performanceStats);
        $this->assertArrayHasKey('cache_info', $performanceStats);
        $this->assertArrayHasKey('performance_summary', $performanceStats);

        $jobRendering = $performanceStats['job_rendering'];
        $this->assertArrayHasKey('iterations', $jobRendering);
        $this->assertArrayHasKey('total_time', $jobRendering);
        $this->assertArrayHasKey('average_time', $jobRendering);
        $this->assertArrayHasKey('renders_per_second', $jobRendering);

        $performanceSummary = $performanceStats['performance_summary'];
        $this->assertArrayHasKey('average_render_time_ms', $performanceSummary);
        $this->assertArrayHasKey('renders_per_second', $performanceSummary);
        $this->assertArrayHasKey('cache_status', $performanceSummary);
        
        // Verify performance is reasonable (faster than 100ms per render)
        $this->assertLessThan(100, $performanceSummary['average_render_time_ms']);
    }

    /** @test */
    public function job_list_template_model_handles_collections()
    {
        // Create additional jobs for testing
        $jobs = Job::factory()->count(3)->create([
            'company_id' => $this->company->id,
            'category_id' => $this->category->id,
            'job_type_id' => $this->jobType->id,
        ]);

        $allJobs = collect([$this->job])->merge($jobs);

        $listModel = new JobListTemplateModel();
        $listModel->title = 'Test Jobs';
        $listModel->description = 'A list of test jobs';
        $listModel->jobs = $allJobs->map(function ($job) {
            return JobTemplateModel::fromJob($job);
        })->toArray();
        $listModel->totalCount = $allJobs->count();
        $listModel->showPagination = true;
        $listModel->currentPage = 1;
        $listModel->perPage = 10;

        // Test basic properties
        $this->assertEquals('Test Jobs', $listModel->title);
        $this->assertEquals(4, $listModel->totalCount);
        $this->assertEquals(4, count($listModel->jobs));

        // Test statistics
        $stats = $listModel->statistics();
        $this->assertEquals(4, $stats['total']);
        $this->assertEquals(4, $stats['showing']);

        // Test pagination
        $pagination = $listModel->pagination();
        $this->assertEquals(1, $pagination['current_page']);
        $this->assertEquals(10, $pagination['per_page']);
        $this->assertEquals(4, $pagination['total']);
        $this->assertEquals(1, $pagination['total_pages']);

        // Test view type class
        $this->assertStringContains('space-y-4', $listModel->viewTypeClass());
        
        $listModel->viewType = 'grid';
        $this->assertStringContains('grid', $listModel->viewTypeClass());
    }

    /** @test */
    public function base_template_model_provides_utility_methods()
    {
        $jobModel = JobTemplateModel::fromJob($this->job);

        // Test date formatting
        $this->assertIsString($jobModel->formatDate($this->job->created_at));
        $this->assertIsString($jobModel->humanDate($this->job->created_at));

        // Test currency formatting
        $this->assertEquals('80,000.00 USD', $jobModel->formatCurrency(80000, 'USD'));

        // Test text truncation
        $longText = str_repeat('This is a long text. ', 20);
        $truncated = $jobModel->truncate($longText, 100);
        $this->assertLessThanOrEqual(103, strlen($truncated)); // 100 + '...'

        // Test number formatting
        $this->assertEquals('1,000', $jobModel->formatNumber(1000));

        // Test status badge
        $this->assertStringContains('bg-', $jobModel->statusBadge('active'));

        // Test SEO meta
        $seoMeta = $jobModel->seoMeta('Test Title', 'Test Description', ['php', 'laravel']);
        $this->assertEquals('Test Title', $seoMeta['title']);
        $this->assertEquals('Test Description', $seoMeta['description']);
        $this->assertEquals('php, laravel', $seoMeta['keywords']);

        // Test breadcrumb
        $breadcrumb = $jobModel->breadcrumb([
            ['title' => 'Jobs', 'url' => '/jobs'],
            ['title' => 'Senior PHP Developer', 'url' => '/jobs/senior-php-developer'],
        ]);
        $this->assertCount(3, $breadcrumb); // Home + 2 custom items
        $this->assertEquals('Home', $breadcrumb[0]['title']);
    }

    /** @test */
    public function template_models_handle_edge_cases()
    {
        // Test with minimal job data
        $minimalJob = Job::factory()->create([
            'title' => 'Basic Job',
            'description' => null,
            'salary_from' => null,
            'salary_to' => null,
            'company_id' => $this->company->id,
        ]);

        $jobModel = JobTemplateModel::fromJob($minimalJob);
        $this->assertEquals('Basic Job', $jobModel->title);
        $this->assertEquals('Salary negotiable', $jobModel->salaryRange());

        // Test with minimal company data
        $minimalCompany = Company::factory()->create([
            'name' => 'Basic Company',
            'description' => null,
            'website' => null,
            'user_id' => $this->user->id,
        ]);

        $companyModel = CompanyTemplateModel::fromCompany($minimalCompany);
        $this->assertEquals('Basic Company', $companyModel->name);
        $this->assertNull($companyModel->websiteUrl());
        $this->assertEquals('Size not specified', $companyModel->sizeDescription());
    }

    /** @test */
    public function template_models_generate_valid_structured_data()
    {
        $jobModel = JobTemplateModel::fromJob($this->job);
        $jobStructuredData = $jobModel->structuredData();

        // Validate JSON-LD structure
        $this->assertIsArray($jobStructuredData);
        $this->assertEquals('https://schema.org', $jobStructuredData['@context']);
        $this->assertEquals('JobPosting', $jobStructuredData['@type']);

        $companyModel = CompanyTemplateModel::fromCompany($this->company);
        $companyStructuredData = $companyModel->structuredData();

        // Validate Organization structure
        $this->assertIsArray($companyStructuredData);
        $this->assertEquals('https://schema.org', $companyStructuredData['@context']);
        $this->assertEquals('Organization', $companyStructuredData['@type']);
    }

    /** @test */
    public function habr_views_integration_achieves_performance_targets()
    {
        // Benchmark rendering performance
        $startTime = microtime(true);
        $iterations = 100;

        for ($i = 0; $i < $iterations; $i++) {
            $jobModel = JobTemplateModel::fromJob($this->job);
            // Simulate some template operations
            $jobModel->salaryRange();
            $jobModel->experienceLevel();
            $jobModel->structuredData();
        }

        $endTime = microtime(true);
        $averageTime = ($endTime - $startTime) / $iterations;

        // Performance targets based on Habr article claims
        $this->assertLessThan(0.01, $averageTime, 'Model creation should be under 10ms');

        // Test memory efficiency
        $startMemory = memory_get_usage();
        
        for ($i = 0; $i < 50; $i++) {
            $jobModel = JobTemplateModel::fromJob($this->job);
            unset($jobModel);
        }
        
        $endMemory = memory_get_usage();
        $memoryPerModel = ($endMemory - $startMemory) / 50;

        $this->assertLessThan(10000, $memoryPerModel, 'Memory usage should be under 10KB per model');
    }

    /** @test */
    public function habr_views_provides_comprehensive_feature_set()
    {
        // Test all major features mentioned in Habr article
        
        // 1. Model-oriented approach
        $jobModel = JobTemplateModel::fromJob($this->job);
        $this->assertInstanceOf(JobTemplateModel::class, $jobModel);
        
        // 2. Typed properties
        $this->assertIsString($jobModel->title);
        $this->assertIsBool($jobModel->isFeatured);
        $this->assertIsInt($jobModel->experienceYears);
        
        // 3. Encapsulated logic in methods
        $this->assertIsString($jobModel->salaryRange());
        $this->assertIsString($jobModel->experienceLevel());
        $this->assertIsArray($jobModel->structuredData());
        
        // 4. Namespace support (covered by service initialization)
        $this->assertStringContains('App\\Views', get_class($jobModel));
        
        // 5. Performance optimization (covered by benchmark test)
        $performanceStats = $this->habrViews->getPerformanceStats();
        $this->assertArrayHasKey('performance_summary', $performanceStats);
        
        // 6. Flexibility (demonstrated by different model types)
        $companyModel = CompanyTemplateModel::fromCompany($this->company);
        $this->assertInstanceOf(CompanyTemplateModel::class, $companyModel);
        
        $this->addToAssertionCount(1); // Comprehensive feature test passed
    }
} 