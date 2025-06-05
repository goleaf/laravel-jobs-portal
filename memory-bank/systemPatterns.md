# System Patterns - Laravel Job Portal

## Architectural Patterns

### 1. Service-Oriented Architecture
```php
// Example: Job Application Service
class JobApplicationService
{
    public function __construct(
        private NotificationService $notifications,
        private EmailService $emails,
        private ValidationService $validator
    ) {}
    
    public function processApplication(array $data): ApplicationResult
    {
        $validated = $this->validator->validate($data);
        $application = $this->createApplication($validated);
        $this->notifications->notify($application);
        
        return new ApplicationResult($application);
    }
}
```

### 2. Repository Pattern
```php
// Example: Job Repository with Eloquent
class JobRepository implements JobRepositoryInterface
{
    public function findWithFilters(array $filters): Collection
    {
        return Job::query()
            ->when($filters['location'] ?? null, fn($q, $location) => 
                $q->where('location', 'like', "%{$location}%"))
            ->when($filters['salary_min'] ?? null, fn($q, $salary) => 
                $q->where('salary_min', '>=', $salary))
            ->with(['company', 'jobType'])
            ->paginate();
    }
}
```

### 3. Event-Driven Architecture
```php
// Example: Job Application Events
class JobApplicationSubmitted
{
    public function __construct(
        public JobApplication $application,
        public User $applicant
    ) {}
}

// Listener for automatic notifications
class SendApplicationNotification
{
    public function handle(JobApplicationSubmitted $event): void
    {
        $event->application->employer->notify(
            new NewApplicationNotification($event->application)
        );
    }
}
```

## Security Patterns

### 1. Enhanced Authentication Middleware
```php
class EnhancedAuthentication
{
    public function handle(Request $request, Closure $next): Response
    {
        // Account lockout check
        if ($this->isAccountLocked($request->user())) {
            throw new AccountLockedException();
        }
        
        // Suspicious activity detection
        $this->detectSuspiciousActivity($request);
        
        return $next($request);
    }
}
```

### 2. Rate Limiting Pattern
```php
RateLimiter::for('job-applications', function (Request $request) {
    return $request->user()
        ? Limit::perMinute(10)->by($request->user()->id)
        : Limit::perMinute(3)->by($request->ip());
});
```

### 3. Form Request Validation
```php
class CreateJobRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'required|string|min:100',
            'salary_min' => 'required|numeric|min:0',
            'location' => 'required|string|max:255',
        ];
    }
    
    public function messages(): array
    {
        return [
            'title.required' => __('validation.job_title_required'),
            'description.min' => __('validation.job_description_min'),
        ];
    }
}
```

## Database Patterns

### 1. Optimized Indexing
```sql
-- Job search optimization
CREATE INDEX idx_jobs_search ON jobs (status, location, job_type_id, salary_min);
CREATE INDEX idx_jobs_company ON jobs (company_id, created_at);
CREATE FULLTEXT INDEX idx_jobs_fulltext ON jobs (title, description);
```

### 2. Relationship Patterns
```php
// Job Model with optimized relationships
class Job extends Model
{
    public function applications(): HasMany
    {
        return $this->hasMany(JobApplication::class);
    }
    
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
    
    // Scope for published jobs
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published')
                    ->where('expires_at', '>', now());
    }
}
```

## Frontend Patterns

### 1. TailwindCSS Component Structure
```html
<!-- Job Card Component -->
<div class="bg-white dark:bg-gray-800 rounded-lg shadow-md hover:shadow-lg transition-shadow duration-300">
    <div class="p-6">
        <div class="flex justify-between items-start">
            <div class="flex-1">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                    {{ $job->title }}
                </h3>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                    {{ $job->company->name }}
                </p>
            </div>
            <div class="text-right">
                <span class="text-lg font-bold text-green-600">
                    ${{ number_format($job->salary_min) }}+
                </span>
            </div>
        </div>
    </div>
</div>
```

### 2. Dark Mode Implementation
```javascript
// Theme management
const themeManager = {
    init() {
        const theme = localStorage.getItem('theme') || 'system';
        this.setTheme(theme);
    },
    
    setTheme(theme) {
        if (theme === 'dark' || (theme === 'system' && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
        localStorage.setItem('theme', theme);
    }
};
```

## Testing Patterns

### 1. Feature Testing with Context7
```php
test('job application can be submitted', function () {
    $user = User::factory()->create();
    $job = Job::factory()->create();
    
    $response = $this->actingAs($user)
        ->post("/jobs/{$job->id}/apply", [
            'cover_letter' => 'I am interested in this position...',
            'resume' => UploadedFile::fake()->create('resume.pdf', 1000)
        ]);
    
    $response->assertRedirect();
    expect($job->applications)->toHaveCount(1);
});
```

### 2. Security Testing
```php
test('rate limiting prevents spam applications', function () {
    $user = User::factory()->create();
    $job = Job::factory()->create();
    
    // Submit applications up to limit
    for ($i = 0; $i < 10; $i++) {
        $this->actingAs($user)->post("/jobs/{$job->id}/apply");
    }
    
    // 11th application should be rate limited
    $response = $this->actingAs($user)->post("/jobs/{$job->id}/apply");
    $response->assertStatus(429);
});
```

## Performance Patterns

### 1. Caching Strategy
```php
// Job listing with caching
class JobService
{
    public function getFeaturedJobs(): Collection
    {
        return Cache::remember('featured_jobs', 3600, function () {
            return Job::published()
                ->where('is_featured', true)
                ->with(['company', 'jobType'])
                ->take(10)
                ->get();
        });
    }
}
```

### 2. Queue Processing
```php
// Background job processing
class ProcessJobApplication implements ShouldQueue
{
    public function handle(): void
    {
        // Process application
        $this->application->update(['status' => 'processed']);
        
        // Send notifications
        $this->application->employer->notify(
            new NewApplicationNotification($this->application)
        );
    }
}
```

## Multilingual Patterns

### 1. JSON Language Files
```json
// en_json/jobs.json
{
    "title": "Job Title",
    "description": "Job Description",
    "apply_now": "Apply Now",
    "salary_range": "Salary Range: :min - :max",
    "applications_count": "{0} No applications|{1} :count application|[2,*] :count applications"
}
```

### 2. Translation Usage
```php
// In controllers
__('jobs.applications_count', ['count' => $job->applications_count]);

// In Blade templates
{{ __('jobs.apply_now') }}
@lang('jobs.salary_range', ['min' => $job->salary_min, 'max' => $job->salary_max])
``` 