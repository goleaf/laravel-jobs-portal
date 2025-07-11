# Backend Services Documentation

## Overview

This document provides comprehensive documentation for all backend services, models, and business logic in the Laravel application. The backend is built with Laravel 12, PHP 8.2+, and follows SOLID principles with service-oriented architecture.

## Service Architecture

### Service Layer Structure

```
app/Services/
├── BaseService.php                    # Abstract base service
├── JobService.php                     # Job management
├── CompanyService.php                 # Company management
├── UserService.php                    # User management
├── EnhancedUserService.php            # Enhanced user features
├── JobSearchService.php               # Advanced job search
├── CacheService.php                   # Caching layer
├── RedisCacheService.php              # Redis-specific caching
├── SecurityService.php                # Security features
├── TwoFactorAuthService.php           # 2FA implementation
├── TranslationService.php             # Multi-language support
├── ImageService.php                   # Image processing
├── FileUploadService.php              # File upload handling
├── PerformanceAnalyticsService.php    # Analytics tracking
├── RateLimitingService.php            # Rate limiting
├── JobTypeService.php                 # Job type management
├── EnhancedCompanyService.php         # Enhanced company features
├── DeepRelationshipService.php        # Complex relationships
├── UniqueValueService.php             # Unique value generation
├── HabrViewsService.php              # View management
├── ImageOptimizationService.php       # Image optimization
├── DataScienceService.php            # Data analysis
├── SecurityComplianceService.php      # Security compliance
├── SEOService.php                     # SEO optimization
├── AuthorizationService.php           # Authorization logic
├── SettingsManager.php                # Settings management
├── UltimateCollectionOptimizer.php    # Collection optimization
├── CollectionForgetUtility.php        # Collection utilities
├── Context7IntegrationManager.php     # External integrations
└── Universal/
    ├── UniversalUniqueValueService.php
    ├── UniversalBusinessIntelligenceService.php
    └── UniversalAISecurityService.php
```

## Core Services

### BaseService (`app/Services/BaseService.php`)

**Abstract base class for all services.**

**Methods:**
- `protected function log(string $message, array $context = []): void` - Logging utility
- `protected function validate(array $data, array $rules): array` - Data validation
- `protected function handleException(\Exception $e): void` - Exception handling

**Usage:**
```php
class MyService extends BaseService
{
    public function processData(array $data): array
    {
        $validated = $this->validate($data, [
            'name' => 'required|string',
            'email' => 'required|email'
        ]);
        
        $this->log('Processing data', ['data' => $validated]);
        
        return $validated;
    }
}
```

### JobService (`app/Services/JobService.php`)

**Comprehensive job management service with caching and advanced features.**

#### Public Methods

**`getActiveJobs(int $perPage = 15): LengthAwarePaginator`**
Get active jobs with caching.

```php
$jobService = new JobService();
$activeJobs = $jobService->getActiveJobs(20);
```

**`getFeaturedJobs(int $limit = 10): Collection`**
Get featured jobs with caching.

```php
$featuredJobs = $jobService->getFeaturedJobs(5);
```

**`searchJobs(array $filters, int $perPage = 15): LengthAwarePaginator`**
Advanced job search with multiple filters.

```php
$filters = [
    'keyword' => 'PHP Developer',
    'category_id' => 2,
    'job_type_id' => 1,
    'country_id' => 1,
    'state_id' => 5,
    'city_id' => 10,
    'min_salary' => 50000,
    'max_salary' => 100000,
    'skills' => ['PHP', 'Laravel', 'MySQL'],
    'experience_level' => 'senior',
    'remote_work' => true
];

$searchResults = $jobService->searchJobs($filters, 20);
```

**`createJob(array $data): Job`**
Create a new job with skills and tags.

```php
$jobData = [
    'title' => 'Senior PHP Developer',
    'description' => 'We are looking for an experienced PHP developer...',
    'company_id' => 1,
    'job_category_id' => 2,
    'job_type_id' => 1,
    'location' => 'New York, NY',
    'salary_from' => 80000,
    'salary_to' => 120000,
    'experience' => '5+ years',
    'is_active' => true,
    'is_featured' => false,
    'skills' => [1, 2, 3],
    'tags' => [1, 2],
    'remote_work' => true,
    'benefits' => ['Health Insurance', '401k', 'Flexible Hours']
];

$newJob = $jobService->createJob($jobData);
```

**`updateJob(Job $job, array $data): Job`**
Update an existing job.

```php
$updateData = [
    'title' => 'Updated Job Title',
    'is_active' => false,
    'salary_to' => 130000,
    'skills' => [1, 2, 3, 4]
];

$updatedJob = $jobService->updateJob($job, $updateData);
```

**`deleteJob(Job $job): bool`**
Soft delete a job.

```php
$deleted = $jobService->deleteJob($job);
```

**`getJobsByCompany(Company $company, int $perPage = 15): LengthAwarePaginator`**
Get jobs by specific company.

```php
$companyJobs = $jobService->getJobsByCompany($company, 10);
```

**`getJobStatistics(): array`**
Get comprehensive job statistics.

```php
$stats = $jobService->getJobStatistics();
// Returns: [
//     'total_jobs' => 1500,
//     'active_jobs' => 1200,
//     'featured_jobs' => 50,
//     'jobs_this_month' => 150,
//     'jobs_today' => 5,
//     'expired_jobs' => 100,
//     'draft_jobs' => 25
// ]
```

**`getPopularJobs(int $limit = 10): Collection`**
Get popular jobs based on views/applications.

```php
$popularJobs = $jobService->getPopularJobs(5);
```

**`getRecentJobs(int $limit = 10): Collection`**
Get recently posted jobs.

```php
$recentJobs = $jobService->getRecentJobs(5);
```

**`getSimilarJobs(Job $job, int $limit = 5): Collection`**
Get similar jobs based on category, type, or company.

```php
$similarJobs = $jobService->getSimilarJobs($job, 3);
```

**`markAsFeatured(Job $job, Carbon $expiryDate): bool`**
Mark job as featured with expiry date.

```php
$featured = $jobService->markAsFeatured($job, now()->addDays(30));
```

**`expireOldJobs(): int`**
Expire old jobs and return count.

```php
$expiredCount = $jobService->expireOldJobs();
```

**`getJobsExpiringSoon(int $days = 7): Collection`**
Get jobs expiring within specified days.

```php
$expiringJobs = $jobService->getJobsExpiringSoon(7);
```

### CompanyService (`app/Services/CompanyService.php`)

**Company management service with enhanced features.**

#### Public Methods

**`getActiveCompanies(int $perPage = 15): LengthAwarePaginator`**
Get active companies with pagination.

```php
$companies = $companyService->getActiveCompanies(20);
```

**`getFeaturedCompanies(int $limit = 10): Collection`**
Get featured companies.

```php
$featuredCompanies = $companyService->getFeaturedCompanies(5);
```

**`searchCompanies(array $filters, int $perPage = 15): LengthAwarePaginator`**
Search companies with filters.

```php
$filters = [
    'keyword' => 'Tech Company',
    'industry_id' => 1,
    'size_id' => 2,
    'location' => 'New York'
];

$searchResults = $companyService->searchCompanies($filters, 20);
```

**`createCompany(array $data): Company`**
Create a new company.

```php
$companyData = [
    'name' => 'Tech Solutions Inc.',
    'description' => 'Leading technology solutions provider...',
    'website' => 'https://techsolutions.com',
    'size_id' => 2,
    'industry_id' => 1,
    'is_active' => true,
    'is_featured' => false
];

$newCompany = $companyService->createCompany($companyData);
```

**`updateCompany(Company $company, array $data): Company`**
Update company information.

```php
$updateData = [
    'name' => 'Updated Company Name',
    'is_active' => false
];

$updatedCompany = $companyService->updateCompany($company, $updateData);
```

**`getCompanyStatistics(): array`**
Get company statistics.

```php
$stats = $companyService->getCompanyStatistics();
```

### UserService (`app/Services/UserService.php`)

**Basic user management service.**

#### Public Methods

**`getActiveUsers(int $perPage = 15): LengthAwarePaginator`**
Get active users.

```php
$users = $userService->getActiveUsers(20);
```

**`createUser(array $data): User`**
Create a new user.

```php
$userData = [
    'name' => 'John Doe',
    'email' => 'john@example.com',
    'password' => 'password123',
    'role' => 'candidate'
];

$newUser = $userService->createUser($userData);
```

**`updateUser(User $user, array $data): User`**
Update user information.

```php
$updateData = [
    'name' => 'Updated Name',
    'email' => 'updated@example.com'
];

$updatedUser = $userService->updateUser($user, $updateData);
```

**`getUserStatistics(): array`**
Get user statistics.

```php
$stats = $userService->getUserStatistics();
```

### EnhancedUserService (`app/Services/EnhancedUserService.php`)

**Enhanced user features with advanced functionality.**

#### Public Methods

**`getUserProfile(int $userId): array`**
Get detailed user profile.

```php
$profile = $enhancedUserService->getUserProfile(1);
```

**`updateUserProfile(int $userId, array $data): bool`**
Update user profile.

```php
$profileData = [
    'bio' => 'Experienced developer...',
    'skills' => ['PHP', 'Laravel', 'Vue.js'],
    'experience' => '5 years',
    'education' => 'Bachelor in Computer Science'
];

$updated = $enhancedUserService->updateUserProfile(1, $profileData);
```

**`getUserActivity(int $userId): Collection`**
Get user activity history.

```php
$activity = $enhancedUserService->getUserActivity(1);
```

### JobSearchService (`app/Services/JobSearchService.php`)

**Advanced job search with AI-powered features.**

#### Public Methods

**`advancedSearch(array $criteria): LengthAwarePaginator`**
Advanced job search with multiple criteria.

```php
$criteria = [
    'keywords' => ['PHP', 'Laravel', 'Vue.js'],
    'location' => 'New York',
    'salary_range' => [50000, 120000],
    'experience_level' => 'senior',
    'remote_work' => true,
    'company_size' => 'medium',
    'industry' => 'Technology',
    'job_type' => 'full-time',
    'skills_required' => ['PHP', 'MySQL'],
    'skills_preferred' => ['Redis', 'Docker']
];

$searchResults = $jobSearchService->advancedSearch($criteria);
```

**`getSearchSuggestions(string $query): array`**
Get search suggestions.

```php
$suggestions = $jobSearchService->getSearchSuggestions('PHP');
```

**`saveSearchCriteria(int $userId, array $criteria): bool`**
Save search criteria for user.

```php
$saved = $jobSearchService->saveSearchCriteria(1, $criteria);
```

### CacheService (`app/Services/CacheService.php`)

**Generic caching service with TTL support.**

#### Public Methods

**`remember(string $key, int $ttl, callable $callback)`**
Cache data with TTL.

```php
$data = $cacheService->remember('users:active', 3600, function () {
    return User::active()->get();
});
```

**`forget(string $key): bool`**
Remove cached data.

```php
$removed = $cacheService->forget('users:active');
```

**`flush(): bool`**
Clear all cache.

```php
$flushed = $cacheService->flush();
```

### RedisCacheService (`app/Services/RedisCacheService.php`)

**Redis-specific caching service.**

#### Public Methods

**`set(string $key, mixed $value, int $ttl = 3600): bool`**
Set Redis cache.

```php
$set = $redisCacheService->set('job:123', $jobData, 1800);
```

**`get(string $key): mixed`**
Get from Redis cache.

```php
$jobData = $redisCacheService->get('job:123');
```

**`delete(string $key): bool`**
Delete from Redis cache.

```php
$deleted = $redisCacheService->delete('job:123');
```

### SecurityService (`app/Services/SecurityService.php`)

**Security features and validation.**

#### Public Methods

**`validateInput(array $data): array`**
Validate and sanitize input data.

```php
$validated = $securityService->validateInput($userInput);
```

**`generateSecureToken(): string`**
Generate secure token.

```php
$token = $securityService->generateSecureToken();
```

**`validateToken(string $token): bool`**
Validate security token.

```php
$valid = $securityService->validateToken($token);
```

### TwoFactorAuthService (`app/Services/TwoFactorAuthService.php`)

**Two-factor authentication implementation.**

#### Public Methods

**`generateSecret(): string`**
Generate 2FA secret.

```php
$secret = $twoFactorAuthService->generateSecret();
```

**`verifyCode(string $secret, string $code): bool`**
Verify 2FA code.

```php
$valid = $twoFactorAuthService->verifyCode($secret, $code);
```

**`enable2FA(int $userId): bool`**
Enable 2FA for user.

```php
$enabled = $twoFactorAuthService->enable2FA(1);
```

### TranslationService (`app/Services/TranslationService.php`)

**Multi-language support service.**

#### Public Methods

**`getTranslations(string $locale): array`**
Get translations for locale.

```php
$translations = $translationService->getTranslations('en');
```

**`updateTranslation(string $locale, string $key, string $value): bool`**
Update translation.

```php
$updated = $translationService->updateTranslation('en', 'welcome', 'Welcome!');
```

**`getAvailableLocales(): array`**
Get available locales.

```php
$locales = $translationService->getAvailableLocales();
```

### ImageService (`app/Services/ImageService.php`)

**Image processing and optimization.**

#### Public Methods

**`uploadImage(UploadedFile $file, string $path): string`**
Upload and process image.

```php
$imagePath = $imageService->uploadImage($request->file('image'), 'avatars');
```

**`resizeImage(string $path, int $width, int $height): bool`**
Resize image.

```php
$resized = $imageService->resizeImage($imagePath, 300, 300);
```

**`deleteImage(string $path): bool`**
Delete image.

```php
$deleted = $imageService->deleteImage($imagePath);
```

### FileUploadService (`app/Services/FileUploadService.php`)

**File upload handling service.**

#### Public Methods

**`uploadFile(UploadedFile $file, string $directory): string`**
Upload file to storage.

```php
$filePath = $fileUploadService->uploadFile($request->file('document'), 'resumes');
```

**`deleteFile(string $path): bool`**
Delete file from storage.

```php
$deleted = $fileUploadService->deleteFile($filePath);
```

**`getFileUrl(string $path): string`**
Get public URL for file.

```php
$url = $fileUploadService->getFileUrl($filePath);
```

### PerformanceAnalyticsService (`app/Services/PerformanceAnalyticsService.php`)

**Performance tracking and analytics.**

#### Public Methods

**`trackPageView(string $url, array $data = []): void`**
Track page view.

```php
$analyticsService->trackPageView('/jobs', ['user_id' => 1]);
```

**`trackEvent(string $event, array $data = []): void`**
Track custom event.

```php
$analyticsService->trackEvent('job_application', [
    'job_id' => 123,
    'user_id' => 1,
    'company_id' => 5
]);
```

**`getAnalytics(string $startDate, string $endDate): array`**
Get analytics data.

```php
$analytics = $analyticsService->getAnalytics('2024-01-01', '2024-01-31');
```

### RateLimitingService (`app/Services/RateLimitingService.php`)

**Rate limiting implementation.**

#### Public Methods

**`checkRateLimit(string $key, int $maxAttempts, int $decayMinutes): bool`**
Check rate limit.

```php
$allowed = $rateLimitingService->checkRateLimit('api:user:1', 100, 60);
```

**`incrementAttempts(string $key): int`**
Increment attempt counter.

```php
$attempts = $rateLimitingService->incrementAttempts('login:ip:192.168.1.1');
```

**`clearAttempts(string $key): void`**
Clear attempt counter.

```php
$rateLimitingService->clearAttempts('login:ip:192.168.1.1');
```

## Advanced Services

### JobTypeService (`app/Services/JobTypeService.php`)

**Job type management with advanced features.**

### EnhancedCompanyService (`app/Services/EnhancedCompanyService.php`)

**Enhanced company features with analytics.**

### DeepRelationshipService (`app/Services/DeepRelationshipService.php`)

**Complex relationship management.**

### UniqueValueService (`app/Services/UniqueValueService.php`)

**Unique value generation and validation.**

### HabrViewsService (`app/Services/HabrViewsService.php`)

**View management and analytics.**

### ImageOptimizationService (`app/Services/ImageOptimizationService.php`)

**Advanced image optimization.**

### DataScienceService (`app/Services/DataScienceService.php`)

**Data analysis and machine learning features.**

### SecurityComplianceService (`app/Services/SecurityComplianceService.php`)

**Security compliance and auditing.**

### SEOService (`app/Services/SEOService.php`)

**SEO optimization and meta tag management.**

### AuthorizationService (`app/Services/AuthorizationService.php`)

**Advanced authorization logic.**

### SettingsManager (`app/Services/SettingsManager.php`)

**Application settings management.**

### UltimateCollectionOptimizer (`app/Services/UltimateCollectionOptimizer.php`)

**Collection optimization and performance.**

### CollectionForgetUtility (`app/Services/CollectionForgetUtility.php`)

**Collection utilities and helpers.**

### Context7IntegrationManager (`app/Services/Context7IntegrationManager.php`)

**External system integrations.**

## Universal Services

### UniversalUniqueValueService (`app/Services/Universal/UniversalUniqueValueService.php`)

**Universal unique value generation.**

### UniversalBusinessIntelligenceService (`app/Services/Universal/UniversalBusinessIntelligenceService.php`)

**Business intelligence and analytics.**

### UniversalAISecurityService (`app/Services/Universal/UniversalAISecurityService.php`)

**AI-powered security features.**

## Service Usage Patterns

### Dependency Injection

```php
class JobController extends Controller
{
    protected $jobService;
    protected $companyService;

    public function __construct(JobService $jobService, CompanyService $companyService)
    {
        $this->jobService = $jobService;
        $this->companyService = $companyService;
    }

    public function index(Request $request)
    {
        $filters = $request->only(['keyword', 'category_id', 'location']);
        $jobs = $this->jobService->searchJobs($filters, 20);

        return response()->json([
            'success' => true,
            'data' => $jobs
        ]);
    }
}
```

### Service Chaining

```php
class ComplexJobService
{
    protected $jobService;
    protected $companyService;
    protected $cacheService;

    public function __construct(
        JobService $jobService,
        CompanyService $companyService,
        CacheService $cacheService
    ) {
        $this->jobService = $jobService;
        $this->companyService = $companyService;
        $this->cacheService = $cacheService;
    }

    public function getJobsWithCompanyInfo(array $filters): array
    {
        return $this->cacheService->remember('jobs:with:company', 3600, function () use ($filters) {
            $jobs = $this->jobService->searchJobs($filters, 20);
            
            return $jobs->map(function ($job) {
                $job->company_info = $this->companyService->getCompanyDetails($job->company_id);
                return $job;
            });
        });
    }
}
```

### Error Handling

```php
class SafeJobService extends BaseService
{
    public function createJobSafely(array $data): ?Job
    {
        try {
            $validated = $this->validate($data, [
                'title' => 'required|string|max:255',
                'company_id' => 'required|exists:companies,id',
                'salary_from' => 'nullable|numeric|min:0'
            ]);

            return $this->jobService->createJob($validated);
        } catch (ValidationException $e) {
            $this->log('Validation failed for job creation', ['errors' => $e->errors()]);
            throw $e;
        } catch (\Exception $e) {
            $this->log('Job creation failed', ['error' => $e->getMessage()]);
            $this->handleException($e);
            return null;
        }
    }
}
```

## Best Practices

1. **Single Responsibility:** Each service should have a single, well-defined purpose
2. **Dependency Injection:** Use Laravel's DI container for service dependencies
3. **Error Handling:** Implement proper error handling and logging
4. **Caching:** Use caching for expensive operations
5. **Validation:** Always validate input data
6. **Documentation:** Document complex methods and business logic
7. **Testing:** Write comprehensive tests for all services
8. **Performance:** Optimize for performance with lazy loading and caching
9. **Security:** Implement proper security measures
10. **Maintainability:** Keep services modular and maintainable

This documentation provides a comprehensive guide for working with backend services in the Laravel application. Follow these patterns and best practices to maintain consistency and quality across the codebase.