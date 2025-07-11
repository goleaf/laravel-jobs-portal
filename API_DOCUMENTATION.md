# Comprehensive API Documentation

## Table of Contents

1. [Overview](#overview)
2. [Authentication](#authentication)
3. [API Endpoints](#api-endpoints)
4. [Services](#services)
5. [Frontend Components](#frontend-components)
6. [Models](#models)
7. [Error Handling](#error-handling)
8. [Rate Limiting](#rate-limiting)
9. [Examples](#examples)

## Overview

This documentation covers all public APIs, functions, and components in the Laravel-based job portal system. The system consists of:

- **Backend**: Laravel PHP application with RESTful APIs
- **Frontend**: Vue.js 3 SPA with TypeScript
- **Database**: MySQL with Eloquent ORM
- **Authentication**: Laravel Sanctum for SPA authentication
- **Caching**: Redis for performance optimization

## Authentication

### Authentication Flow

The system uses Laravel Sanctum for SPA authentication with Bearer tokens.

#### Login
```http
POST /api/v1/auth/login
Content-Type: application/json

{
    "email": "user@example.com",
    "password": "password123"
}
```

**Response:**
```json
{
    "success": true,
    "message": "Login successful",
    "data": {
        "user": {
            "id": 1,
            "name": "John Doe",
            "email": "user@example.com",
            "email_verified_at": "2024-01-01T00:00:00.000000Z",
            "is_admin": false,
            "role": "candidate",
            "created_at": "2024-01-01T00:00:00.000000Z"
        },
        "token": "1|abc123...",
        "token_type": "Bearer"
    }
}
```

#### Register
```http
POST /api/v1/auth/register
Content-Type: application/json

{
    "name": "John Doe",
    "email": "user@example.com",
    "password": "password123",
    "password_confirmation": "password123",
    "role": "candidate"
}
```

#### Get Current User
```http
GET /api/v1/auth/user
Authorization: Bearer {token}
```

#### Logout
```http
POST /api/v1/auth/logout
Authorization: Bearer {token}
```

#### Logout All Devices
```http
POST /api/v1/auth/logout-all
Authorization: Bearer {token}
```

## API Endpoints

### Authentication Controllers

#### AuthController (`app/Http/Controllers/Api/V1/AuthController.php`)

**Public Methods:**
- `login(Request $request): JsonResponse` - User login
- `register(Request $request): JsonResponse` - User registration
- `user(Request $request): JsonResponse` - Get current user
- `logout(Request $request): JsonResponse` - Logout user
- `logoutAll(Request $request): JsonResponse` - Logout from all devices
- `refresh(Request $request): JsonResponse` - Refresh token
- `checkRole(Request $request, string $role): JsonResponse` - Check user role

### Job Management

#### JobApiController (`app/Http/Controllers/Api/V1/JobApiController.php`)

**Endpoints:**
```http
GET    /api/v1/jobs              # List jobs with pagination and filters
POST   /api/v1/jobs              # Create new job
GET    /api/v1/jobs/{id}         # Get specific job
PUT    /api/v1/jobs/{id}         # Update job
DELETE /api/v1/jobs/{id}         # Delete job
```

**Query Parameters for Job Listing:**
- `search` - Search in title, description, or company name
- `status` - Filter by status (active/inactive)
- `sort` - Sort field (default: created_at)
- `order` - Sort order (asc/desc)
- `per_page` - Items per page (max: 100)

**Example Job Creation:**
```http
POST /api/v1/jobs
Content-Type: application/json

{
    "title": "Senior PHP Developer",
    "description": "We are looking for an experienced PHP developer...",
    "company_id": 1,
    "category_id": 2,
    "type_id": 1,
    "location": "New York, NY",
    "salary_from": 80000,
    "salary_to": 120000,
    "experience": "5+ years",
    "is_active": true,
    "is_featured": false
}
```

### Company Management

#### CompanyApiController (`app/Http/Controllers/Api/V1/CompanyApiController.php`)

**Endpoints:**
```http
GET    /api/v1/companies         # List companies
POST   /api/v1/companies         # Create company
GET    /api/v1/companies/{id}    # Get company details
PUT    /api/v1/companies/{id}    # Update company
DELETE /api/v1/companies/{id}    # Delete company
```

### Candidate Management

#### CandidateApiController (`app/Http/Controllers/Api/V1/CandidateApiController.php`)

**Endpoints:**
```http
GET    /api/v1/candidates        # List candidates
POST   /api/v1/candidates        # Create candidate profile
GET    /api/v1/candidates/{id}   # Get candidate details
PUT    /api/v1/candidates/{id}   # Update candidate
DELETE /api/v1/candidates/{id}   # Delete candidate
```

### Application Management

#### ApplicationApiController (`app/Http/Controllers/Api/V1/ApplicationApiController.php`)

**Endpoints:**
```http
GET    /api/v1/applications      # List job applications
POST   /api/v1/applications      # Submit job application
GET    /api/v1/applications/{id} # Get application details
PUT    /api/v1/applications/{id} # Update application status
DELETE /api/v1/applications/{id} # Withdraw application
```

### Master Data Controllers

#### CompanySizeApiController
```http
GET /api/v1/company-sizes       # List company sizes
```

#### SalaryCurrencyApiController
```http
GET /api/v1/salary-currencies   # List salary currencies
```

#### SalaryPeriodApiController
```http
GET /api/v1/salary-periods      # List salary periods
```

#### LocationApiController
```http
GET /api/v1/locations           # List locations
```

### Content Management

#### HeaderSliderApiController
```http
GET /api/v1/header-sliders      # List header sliders
```

#### ImageSliderApiController
```http
GET /api/v1/image-sliders       # List image sliders
```

#### BrandingSliderApiController
```http
GET /api/v1/branding-sliders    # List branding sliders
```

### File Management

#### FileUploadApiController
```http
POST /api/v1/file-upload        # Upload files
```

#### FilePreviewApiController
```http
GET /api/v1/file-preview/{id}   # Preview uploaded files
```

### Dashboard & Analytics

#### DashboardController
```http
GET /api/v1/dashboard/stats     # Get dashboard statistics
```

### Payment Integration

#### PaymentApiController
```http
POST /api/v1/payment/process    # Process payments
GET /api/v1/payment/methods     # Get payment methods
```

### Real-time Features

#### RealTimeApiController
```http
GET /api/v1/realtime/notifications  # Get real-time notifications
```

### Health & Monitoring

#### Health Check
```http
GET /api/v1/health              # System health check
```

## Services

### JobService (`app/Services/JobService.php`)

**Public Methods:**

#### `getActiveJobs(int $perPage = 15): LengthAwarePaginator`
Get active jobs with caching.

```php
$jobService = new JobService();
$activeJobs = $jobService->getActiveJobs(20);
```

#### `getFeaturedJobs(int $limit = 10): Collection`
Get featured jobs with caching.

```php
$featuredJobs = $jobService->getFeaturedJobs(5);
```

#### `searchJobs(array $filters, int $perPage = 15): LengthAwarePaginator`
Search jobs with advanced filters.

```php
$filters = [
    'keyword' => 'PHP Developer',
    'category_id' => 2,
    'job_type_id' => 1,
    'country_id' => 1,
    'min_salary' => 50000,
    'max_salary' => 100000,
    'skills' => ['PHP', 'Laravel', 'MySQL']
];

$searchResults = $jobService->searchJobs($filters, 20);
```

#### `createJob(array $data): Job`
Create a new job with skills and tags.

```php
$jobData = [
    'title' => 'Senior Developer',
    'description' => 'Job description...',
    'company_id' => 1,
    'skills' => [1, 2, 3],
    'tags' => [1, 2]
];

$newJob = $jobService->createJob($jobData);
```

#### `updateJob(Job $job, array $data): Job`
Update an existing job.

```php
$updateData = [
    'title' => 'Updated Job Title',
    'is_active' => false
];

$updatedJob = $jobService->updateJob($job, $updateData);
```

#### `deleteJob(Job $job): bool`
Soft delete a job.

```php
$deleted = $jobService->deleteJob($job);
```

#### `getJobsByCompany(Company $company, int $perPage = 15): LengthAwarePaginator`
Get jobs by specific company.

```php
$companyJobs = $jobService->getJobsByCompany($company, 10);
```

#### `getJobStatistics(): array`
Get comprehensive job statistics.

```php
$stats = $jobService->getJobStatistics();
// Returns: total_jobs, active_jobs, featured_jobs, etc.
```

#### `getPopularJobs(int $limit = 10): Collection`
Get popular jobs based on views/applications.

```php
$popularJobs = $jobService->getPopularJobs(5);
```

#### `getRecentJobs(int $limit = 10): Collection`
Get recently posted jobs.

```php
$recentJobs = $jobService->getRecentJobs(5);
```

#### `getSimilarJobs(Job $job, int $limit = 5): Collection`
Get similar jobs based on category, type, or company.

```php
$similarJobs = $jobService->getSimilarJobs($job, 3);
```

### CompanyService (`app/Services/CompanyService.php`)

**Public Methods:**

#### `getActiveCompanies(int $perPage = 15): LengthAwarePaginator`
Get active companies with pagination.

#### `getFeaturedCompanies(int $limit = 10): Collection`
Get featured companies.

#### `searchCompanies(array $filters, int $perPage = 15): LengthAwarePaginator`
Search companies with filters.

#### `createCompany(array $data): Company`
Create a new company.

#### `updateCompany(Company $company, array $data): Company`
Update company information.

#### `getCompanyStatistics(): array`
Get company statistics.

### UserService (`app/Services/UserService.php`)

**Public Methods:**

#### `getActiveUsers(int $perPage = 15): LengthAwarePaginator`
Get active users.

#### `createUser(array $data): User`
Create a new user.

#### `updateUser(User $user, array $data): User`
Update user information.

#### `getUserStatistics(): array`
Get user statistics.

### EnhancedUserService (`app/Services/EnhancedUserService.php`)

**Public Methods:**

#### `getUserProfile(int $userId): array`
Get detailed user profile.

#### `updateUserProfile(int $userId, array $data): bool`
Update user profile.

#### `getUserActivity(int $userId): Collection`
Get user activity history.

### JobSearchService (`app/Services/JobSearchService.php`)

**Public Methods:**

#### `advancedSearch(array $criteria): LengthAwarePaginator`
Advanced job search with multiple criteria.

#### `getSearchSuggestions(string $query): array`
Get search suggestions.

#### `saveSearchCriteria(int $userId, array $criteria): bool`
Save search criteria for user.

### CacheService (`app/Services/CacheService.php`)

**Public Methods:**

#### `remember(string $key, int $ttl, callable $callback)`
Cache data with TTL.

#### `forget(string $key): bool`
Remove cached data.

#### `flush(): bool`
Clear all cache.

### RedisCacheService (`app/Services/RedisCacheService.php`)

**Public Methods:**

#### `set(string $key, mixed $value, int $ttl = 3600): bool`
Set Redis cache.

#### `get(string $key): mixed`
Get from Redis cache.

#### `delete(string $key): bool`
Delete from Redis cache.

### SecurityService (`app/Services/SecurityService.php`)

**Public Methods:**

#### `validateInput(array $data): array`
Validate and sanitize input data.

#### `generateSecureToken(): string`
Generate secure token.

#### `validateToken(string $token): bool`
Validate security token.

### TwoFactorAuthService (`app/Services/TwoFactorAuthService.php`)

**Public Methods:**

#### `generateSecret(): string`
Generate 2FA secret.

#### `verifyCode(string $secret, string $code): bool`
Verify 2FA code.

#### `enable2FA(int $userId): bool`
Enable 2FA for user.

### TranslationService (`app/Services/TranslationService.php`)

**Public Methods:**

#### `getTranslations(string $locale): array`
Get translations for locale.

#### `updateTranslation(string $locale, string $key, string $value): bool`
Update translation.

#### `getAvailableLocales(): array`
Get available locales.

### ImageService (`app/Services/ImageService.php`)

**Public Methods:**

#### `uploadImage(UploadedFile $file, string $path): string`
Upload and process image.

#### `resizeImage(string $path, int $width, int $height): bool`
Resize image.

#### `deleteImage(string $path): bool`
Delete image.

### FileUploadService (`app/Services/FileUploadService.php`)

**Public Methods:**

#### `uploadFile(UploadedFile $file, string $directory): string`
Upload file to storage.

#### `deleteFile(string $path): bool`
Delete file from storage.

#### `getFileUrl(string $path): string`
Get public URL for file.

### PerformanceAnalyticsService (`app/Services/PerformanceAnalyticsService.php`)

**Public Methods:**

#### `trackPageView(string $url, array $data = []): void`
Track page view.

#### `trackEvent(string $event, array $data = []): void`
Track custom event.

#### `getAnalytics(string $startDate, string $endDate): array`
Get analytics data.

### RateLimitingService (`app/Services/RateLimitingService.php`)

**Public Methods:**

#### `checkRateLimit(string $key, int $maxAttempts, int $decayMinutes): bool`
Check rate limit.

#### `incrementAttempts(string $key): int`
Increment attempt counter.

#### `clearAttempts(string $key): void`
Clear attempt counter.

## Frontend Components

### Vue.js Components

#### TheWelcome.vue (`frontend/src/components/TheWelcome.vue`)

**Props:** None
**Events:** None
**Slots:** None

**Usage:**
```vue
<template>
  <TheWelcome />
</template>

<script setup>
import TheWelcome from '@/components/TheWelcome.vue'
</script>
```

**Features:**
- Displays welcome information
- Links to documentation
- Tooling information
- Ecosystem details
- Community links
- Support information

#### WelcomeItem.vue (`frontend/src/components/WelcomeItem.vue`)

**Props:**
- `heading` (string) - The heading text
- `icon` (component) - The icon component

**Slots:**
- `icon` - Icon slot
- `heading` - Heading slot
- `default` - Content slot

**Usage:**
```vue
<template>
  <WelcomeItem>
    <template #icon>
      <DocumentationIcon />
    </template>
    <template #heading>Documentation</template>
    <p>Welcome to our documentation...</p>
  </WelcomeItem>
</template>

<script setup>
import WelcomeItem from '@/components/WelcomeItem.vue'
import DocumentationIcon from '@/components/icons/IconDocumentation.vue'
</script>
```

#### HelloWorld.vue (`frontend/src/components/HelloWorld.vue`)

**Props:**
- `msg` (string) - Message to display

**Usage:**
```vue
<template>
  <HelloWorld msg="Welcome to Your Vue.js App" />
</template>

<script setup>
import HelloWorld from '@/components/HelloWorld.vue'
</script>
```

### Icon Components

All icon components are located in `frontend/src/components/icons/`:

- `IconDocumentation.vue`
- `IconTooling.vue`
- `IconEcosystem.vue`
- `IconCommunity.vue`
- `IconSupport.vue`

**Usage:**
```vue
<template>
  <DocumentationIcon />
</template>

<script setup>
import DocumentationIcon from '@/components/icons/IconDocumentation.vue'
</script>
```

## Models

### Job Model (`app/Models/Job.php`)

**Attributes:**
- `id` (int) - Primary key
- `title` (string) - Job title
- `description` (text) - Job description
- `company_id` (int) - Company ID
- `job_category_id` (int) - Category ID
- `job_type_id` (int) - Job type ID
- `location` (string) - Job location
- `salary_from` (decimal) - Minimum salary
- `salary_to` (decimal) - Maximum salary
- `experience` (string) - Required experience
- `is_active` (boolean) - Job status
- `is_featured` (boolean) - Featured status
- `created_at` (timestamp)
- `updated_at` (timestamp)

**Relationships:**
- `company()` - Belongs to Company
- `jobCategory()` - Belongs to JobCategory
- `jobType()` - Belongs to JobType
- `applications()` - Has many JobApplication
- `skills()` - Belongs to many Skill
- `tags()` - Belongs to many Tag

**Scopes:**
- `active()` - Active jobs only
- `featured()` - Featured jobs only
- `byCategory($categoryId)` - Filter by category
- `byLocation($countryId, $stateId, $cityId)` - Filter by location
- `bySalaryRange($min, $max)` - Filter by salary range
- `withSkills($skills)` - Filter by skills

### Company Model (`app/Models/Company.php`)

**Attributes:**
- `id` (int) - Primary key
- `name` (string) - Company name
- `description` (text) - Company description
- `website` (string) - Company website
- `logo` (string) - Company logo path
- `size_id` (int) - Company size ID
- `industry_id` (int) - Industry ID
- `is_active` (boolean) - Company status
- `is_featured` (boolean) - Featured status
- `created_at` (timestamp)
- `updated_at` (timestamp)

**Relationships:**
- `jobs()` - Has many Job
- `size()` - Belongs to CompanySize
- `industry()` - Belongs to Industry
- `users()` - Has many User

### User Model (`app/Models/User.php`)

**Attributes:**
- `id` (int) - Primary key
- `name` (string) - User name
- `email` (string) - Email address
- `email_verified_at` (timestamp) - Email verification
- `password` (string) - Hashed password
- `role` (string) - User role
- `created_at` (timestamp)
- `updated_at` (timestamp)

**Relationships:**
- `company()` - Belongs to Company
- `applications()` - Has many JobApplication
- `profile()` - Has one UserProfile

## Error Handling

### Standard Error Response Format

```json
{
    "success": false,
    "message": "Error description",
    "errors": {
        "field_name": ["Validation error message"]
    },
    "error": "Detailed error message (debug mode only)"
}
```

### HTTP Status Codes

- `200` - Success
- `201` - Created
- `400` - Bad Request
- `401` - Unauthorized
- `403` - Forbidden
- `404` - Not Found
- `422` - Validation Error
- `429` - Too Many Requests
- `500` - Internal Server Error

### Validation Errors

```json
{
    "success": false,
    "message": "Validation failed",
    "errors": {
        "email": ["The email field is required."],
        "password": ["The password must be at least 8 characters."]
    }
}
```

## Rate Limiting

The system implements rate limiting using Laravel's built-in throttling:

### Default Limits
- **API Routes**: 60 requests per minute per user
- **Authentication**: 5 attempts per minute per IP
- **File Uploads**: 10 uploads per minute per user

### Custom Rate Limiting

```php
// In RateLimitingService
$rateLimit = $this->checkRateLimit('api:user:1', 100, 60);
```

## Examples

### Complete Job Search Example

```javascript
// Frontend JavaScript
const searchJobs = async (filters) => {
    const params = new URLSearchParams({
        search: filters.keyword,
        category_id: filters.categoryId,
        job_type_id: filters.jobTypeId,
        min_salary: filters.minSalary,
        max_salary: filters.maxSalary,
        per_page: 20
    });

    const response = await fetch(`/api/v1/jobs?${params}`, {
        headers: {
            'Authorization': `Bearer ${token}`,
            'Content-Type': 'application/json'
        }
    });

    return await response.json();
};

// Usage
const results = await searchJobs({
    keyword: 'PHP Developer',
    categoryId: 2,
    jobTypeId: 1,
    minSalary: 50000,
    maxSalary: 100000
});
```

### Job Creation Example

```javascript
const createJob = async (jobData) => {
    const response = await fetch('/api/v1/jobs', {
        method: 'POST',
        headers: {
            'Authorization': `Bearer ${token}`,
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            title: 'Senior PHP Developer',
            description: 'We are looking for an experienced PHP developer...',
            company_id: 1,
            category_id: 2,
            type_id: 1,
            location: 'New York, NY',
            salary_from: 80000,
            salary_to: 120000,
            experience: '5+ years',
            is_active: true,
            is_featured: false
        })
    });

    return await response.json();
};
```

### File Upload Example

```javascript
const uploadFile = async (file) => {
    const formData = new FormData();
    formData.append('file', file);

    const response = await fetch('/api/v1/file-upload', {
        method: 'POST',
        headers: {
            'Authorization': `Bearer ${token}`
        },
        body: formData
    });

    return await response.json();
};
```

### Service Usage Example

```php
// Backend PHP
use App\Services\JobService;

class JobController extends Controller
{
    protected $jobService;

    public function __construct(JobService $jobService)
    {
        $this->jobService = $jobService;
    }

    public function index(Request $request)
    {
        $filters = $request->only(['keyword', 'category_id', 'job_type_id']);
        $jobs = $this->jobService->searchJobs($filters, 20);

        return response()->json([
            'success' => true,
            'data' => $jobs
        ]);
    }

    public function store(Request $request)
    {
        $jobData = $request->validated();
        $job = $this->jobService->createJob($jobData);

        return response()->json([
            'success' => true,
            'data' => $job
        ], 201);
    }
}
```

### Vue Component Example

```vue
<template>
  <div class="job-search">
    <form @submit.prevent="searchJobs">
      <input v-model="filters.keyword" placeholder="Search jobs..." />
      <select v-model="filters.categoryId">
        <option value="">All Categories</option>
        <option v-for="category in categories" :key="category.id" :value="category.id">
          {{ category.name }}
        </option>
      </select>
      <button type="submit">Search</button>
    </form>

    <div v-if="jobs.length" class="jobs-list">
      <div v-for="job in jobs" :key="job.id" class="job-item">
        <h3>{{ job.title }}</h3>
        <p>{{ job.company.name }}</p>
        <p>{{ job.location }}</p>
        <p>${{ job.salary_from }} - ${{ job.salary_to }}</p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'

const jobs = ref([])
const categories = ref([])
const filters = ref({
  keyword: '',
  categoryId: ''
})

const searchJobs = async () => {
  try {
    const params = new URLSearchParams(filters.value)
    const response = await fetch(`/api/v1/jobs?${params}`)
    const data = await response.json()
    
    if (data.success) {
      jobs.value = data.data
    }
  } catch (error) {
    console.error('Error searching jobs:', error)
  }
}

onMounted(async () => {
  // Load categories
  const response = await fetch('/api/v1/job-categories')
  const data = await response.json()
  categories.value = data.data
})
</script>
```

This comprehensive documentation covers all public APIs, functions, and components in the system. Each section includes detailed information about parameters, return values, and usage examples to help developers integrate with the system effectively.