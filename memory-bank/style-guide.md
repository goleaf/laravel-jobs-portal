# Style Guide - Laravel Job Portal

## Code Style Standards

### PHP Standards (PSR-12 + Laravel)
- Use type declarations for all method parameters and return types
- Use constructor property promotion in PHP 8.1+
- Follow Laravel naming conventions for models, controllers, and services
- Use descriptive variable and method names
- Keep methods under 20 lines when possible

### Frontend Standards
- Use TailwindCSS utility classes exclusively
- Follow mobile-first responsive design
- Implement dark mode for all components
- Use semantic HTML elements
- Maintain accessibility standards (WCAG 2.1 AA)

### Database Standards
- Use descriptive table and column names
- Create appropriate indexes for queries
- Use foreign key constraints
- Follow Laravel migration conventions

## Component Guidelines

### Blade Components
- Create reusable components for repeated UI elements
- Use component slots for flexible content
- Pass data through component attributes
- Keep component logic minimal

### Form Components
- Use consistent styling across all forms
- Implement client-side validation
- Show clear error messages
- Support dark mode

### Navigation Components
- Responsive design for mobile and desktop
- Clear visual hierarchy
- Accessible keyboard navigation
- Active state indicators

## Testing Standards

### Feature Tests
- Test complete user workflows
- Use factory methods for test data
- Assert both successful and error conditions
- Test authentication and authorization

### Unit Tests
- Test individual methods and classes
- Mock external dependencies
- Test edge cases and error conditions
- Maintain high test coverage

## Documentation Standards

### Code Comments
- Document complex business logic
- Explain non-obvious code decisions
- Use PHPDoc blocks for classes and methods
- Keep comments up to date with code changes

### API Documentation
- Document all endpoints
- Include request/response examples
- Specify authentication requirements
- Document error responses

## Coding Standards

### PHP Code Style (PSR-12 + Laravel Conventions)

#### Class Structure
```php
<?php

namespace App\Services;

use App\Models\Job;
use App\Events\JobCreated;
use Illuminate\Support\Facades\Cache;

/**
 * Service for managing job operations
 */
class JobService
{
    public function __construct(
        private JobRepository $repository,
        private NotificationService $notifications
    ) {}

    public function createJob(array $data): Job
    {
        $job = $this->repository->create($data);
        
        event(new JobCreated($job));
        
        return $job;
    }
}
```

#### Controller Structure
```php
<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateJobRequest;
use App\Services\JobService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class JobController extends Controller
{
    public function __construct(
        private JobService $jobService
    ) {}

    public function create(): View
    {
        return view('jobs.create');
    }

    public function store(CreateJobRequest $request): RedirectResponse
    {
        $job = $this->jobService->createJob($request->validated());
        
        return redirect()
            ->route('jobs.show', $job)
            ->with('success', __('jobs.created_successfully'));
    }
}
```

#### Form Request Validation
```php
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class CreateJobRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', Job::class);
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'min:100'],
            'salary_min' => ['required', 'numeric', 'min:0'],
            'salary_max' => ['required', 'numeric', 'gt:salary_min'],
            'location' => ['required', 'string', 'max:255'],
            'job_type_id' => ['required', 'exists:job_types,id'],
            'expires_at' => ['required', 'date', 'after:today'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => __('validation.job_title_required'),
            'description.min' => __('validation.job_description_min'),
            'salary_max.gt' => __('validation.salary_max_greater'),
        ];
    }
}
```

### Database Standards

#### Migration Structure
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jobs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('description');
            $table->decimal('salary_min', 10, 2)->nullable();
            $table->decimal('salary_max', 10, 2)->nullable();
            $table->string('location');
            $table->foreignId('job_type_id')->constrained();
            $table->enum('status', ['draft', 'published', 'closed', 'expired'])
                  ->default('draft');
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();

            // Indexes for performance
            $table->index(['status', 'expires_at']);
            $table->index(['company_id', 'created_at']);
            $table->index(['location', 'job_type_id']);
            $table->fullText(['title', 'description']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jobs');
    }
};
```

#### Model Structure
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class Job extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'title',
        'description',
        'salary_min',
        'salary_max',
        'location',
        'job_type_id',
        'status',
        'expires_at',
    ];

    protected $casts = [
        'salary_min' => 'decimal:2',
        'salary_max' => 'decimal:2',
        'expires_at' => 'datetime',
    ];

    // Relationships
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function applications(): HasMany
    {
        return $this->hasMany(JobApplication::class);
    }

    public function jobType(): BelongsTo
    {
        return $this->belongsTo(JobType::class);
    }

    // Scopes
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published')
                    ->where('expires_at', '>', now());
    }

    public function scopeByLocation(Builder $query, string $location): Builder
    {
        return $query->where('location', 'like', "%{$location}%");
    }

    // Accessors & Mutators
    public function getSalaryRangeAttribute(): string
    {
        if ($this->salary_min && $this->salary_max) {
            return "$" . number_format($this->salary_min) . " - $" . number_format($this->salary_max);
        }
        
        if ($this->salary_min) {
            return "$" . number_format($this->salary_min) . "+";
        }
        
        return __('jobs.salary_negotiable');
    }
}
```

## Frontend Standards

### TailwindCSS Component Structure

#### Base Layout Component
```html
<!-- resources/views/layouts/app.blade.php -->
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>{{ config('app.name', 'Job Portal') }}</title>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full bg-gray-50 dark:bg-gray-900">
    <div id="app" class="min-h-full">
        @include('components.navigation.header')
        
        <main class="py-6">
            @yield('content')
        </main>
        
        @include('components.navigation.footer')
    </div>
    
    @stack('scripts')
</body>
</html>
```

#### Job Card Component
```html
<!-- resources/views/components/job-card.blade.php -->
<div class="bg-white dark:bg-gray-800 rounded-lg shadow-md hover:shadow-lg transition-all duration-300 border border-gray-200 dark:border-gray-700">
    <div class="p-6">
        <!-- Header -->
        <div class="flex justify-between items-start mb-4">
            <div class="flex-1">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                    <a href="{{ route('jobs.show', $job) }}">
                        {{ $job->title }}
                    </a>
                </h3>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                    {{ $job->company->name }}
                </p>
            </div>
            
            @if($job->is_featured)
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                    {{ __('jobs.featured') }}
                </span>
            @endif
        </div>
        
        <!-- Details -->
        <div class="space-y-2 mb-4">
            <div class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
                {{ $job->location }}
            </div>
            
            <div class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                </svg>
                {{ $job->salary_range }}
            </div>
            
            <div class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a1 1 0 011-1h6a1 1 0 011 1v4h3a1 1 0 011 1v9a1 1 0 01-1 1H5a1 1 0 01-1-1V8a1 1 0 011-1h3z"></path>
                </svg>
                {{ $job->jobType->name }}
            </div>
        </div>
        
        <!-- Footer -->
        <div class="flex justify-between items-center pt-4 border-t border-gray-200 dark:border-gray-700">
            <span class="text-sm text-gray-500 dark:text-gray-400">
                {{ $job->created_at->diffForHumans() }}
            </span>
            
            <a href="{{ route('jobs.show', $job) }}" 
               class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                {{ __('jobs.view_details') }}
            </a>
        </div>
    </div>
</div>
```

#### Form Component
```html
<!-- resources/views/components/forms/input.blade.php -->
@props([
    'label' => null,
    'name',
    'type' => 'text',
    'required' => false,
    'error' => null,
    'help' => null
])

<div class="space-y-1">
    @if($label)
        <label for="{{ $name }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
            {{ $label }}
            @if($required)
                <span class="text-red-500">*</span>
            @endif
        </label>
    @endif
    
    <input 
        type="{{ $type }}"
        name="{{ $name }}"
        id="{{ $name }}"
        {{ $attributes->merge([
            'class' => 'block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm' . ($error ? ' border-red-300 focus:ring-red-500 focus:border-red-500' : '')
        ]) }}
        value="{{ old($name, $slot) }}"
    />
    
    @if($error)
        <p class="text-sm text-red-600 dark:text-red-400">{{ $error }}</p>
    @endif
    
    @if($help)
        <p class="text-sm text-gray-500 dark:text-gray-400">{{ $help }}</p>
    @endif
</div>
```

### Dark Mode Implementation

#### JavaScript Theme Toggle
```javascript
// resources/js/theme.js
class ThemeManager {
    constructor() {
        this.theme = localStorage.getItem('theme') || 'system';
        this.init();
    }
    
    init() {
        this.updateTheme();
        this.setupEventListeners();
    }
    
    updateTheme() {
        const isDark = this.theme === 'dark' || 
            (this.theme === 'system' && window.matchMedia('(prefers-color-scheme: dark)').matches);
        
        document.documentElement.classList.toggle('dark', isDark);
        this.updateToggleButtons();
    }
    
    setTheme(newTheme) {
        this.theme = newTheme;
        localStorage.setItem('theme', newTheme);
        this.updateTheme();
    }
    
    setupEventListeners() {
        // Theme toggle buttons
        document.querySelectorAll('[data-theme]').forEach(button => {
            button.addEventListener('click', (e) => {
                const theme = e.target.dataset.theme;
                this.setTheme(theme);
            });
        });
        
        // System theme changes
        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', () => {
            if (this.theme === 'system') {
                this.updateTheme();
            }
        });
    }
    
    updateToggleButtons() {
        document.querySelectorAll('[data-theme]').forEach(button => {
            button.classList.toggle('active', button.dataset.theme === this.theme);
        });
    }
}

// Initialize theme manager
document.addEventListener('DOMContentLoaded', () => {
    new ThemeManager();
});
```

#### Theme Toggle Component
```html
<!-- resources/views/components/theme-toggle.blade.php -->
<div class="relative">
    <button type="button" 
            class="flex items-center p-2 text-gray-500 rounded-lg hover:text-gray-900 hover:bg-gray-100 dark:text-gray-400 dark:hover:text-white dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-200 dark:focus:ring-gray-700"
            data-dropdown-toggle="theme-dropdown">
        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
            <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path>
        </svg>
    </button>
    
    <div id="theme-dropdown" class="hidden absolute right-0 mt-2 w-32 bg-white rounded-lg shadow-lg dark:bg-gray-800 border border-gray-200 dark:border-gray-700">
        <button data-theme="light" class="flex items-center w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700">
            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z" clip-rule="evenodd"></path>
            </svg>
            {{ __('theme.light') }}
        </button>
        
        <button data-theme="dark" class="flex items-center w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700">
            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path>
            </svg>
            {{ __('theme.dark') }}
        </button>
        
        <button data-theme="system" class="flex items-center w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700">
            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M3 5a2 2 0 012-2h10a2 2 0 012 2v8a2 2 0 01-2 2h-2.22l.123.489.804.804A1 1 0 0113 18H7a1 1 0 01-.707-1.707l.804-.804L7.22 15H5a2 2 0 01-2-2V5zm5.771 7H5V5h10v7H8.771z" clip-rule="evenodd"></path>
            </svg>
            {{ __('theme.system') }}
        </button>
    </div>
</div>
```

## Testing Standards

### Feature Test Structure
```php
<?php

use App\Models\User;
use App\Models\Job;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->job = Job::factory()->create();
});

test('user can view job details', function () {
    $response = $this->actingAs($this->user)
        ->get(route('jobs.show', $this->job));
    
    $response->assertOk()
        ->assertSee($this->job->title)
        ->assertSee($this->job->company->name);
});

test('user can apply for job', function () {
    $response = $this->actingAs($this->user)
        ->post(route('jobs.apply', $this->job), [
            'cover_letter' => 'I am interested in this position.',
        ]);
    
    $response->assertRedirect()
        ->assertSessionHas('success');
    
    expect($this->job->applications)->toHaveCount(1);
    expect($this->job->applications->first()->user_id)->toBe($this->user->id);
});
```

### Unit Test Structure
```php
<?php

use App\Services\JobService;
use App\Models\Job;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('job service creates job correctly', function () {
    $service = app(JobService::class);
    
    $data = [
        'title' => 'Software Developer',
        'description' => 'We are looking for a skilled developer.',
        'salary_min' => 50000,
        'salary_max' => 80000,
        'location' => 'New York',
        'job_type_id' => 1,
        'company_id' => 1,
    ];
    
    $job = $service->createJob($data);
    
    expect($job)->toBeInstanceOf(Job::class);
    expect($job->title)->toBe('Software Developer');
    expect($job->status)->toBe('draft');
});
```

## Multilingual Standards

### Translation Key Structure
```json
// lang/en_json/jobs.json
{
    "title": "Job Title",
    "description": "Job Description",
    "location": "Location",
    "salary_range": "Salary Range",
    "apply_now": "Apply Now",
    "view_details": "View Details",
    "created_at": "Posted :time",
    "expires_at": "Expires :date",
    "featured": "Featured",
    "salary_negotiable": "Salary Negotiable",
    "applications_count": "{0} No applications|{1} :count application|[2,*] :count applications",
    "status": {
        "draft": "Draft",
        "published": "Published",
        "closed": "Closed",
        "expired": "Expired"
    },
    "messages": {
        "created_successfully": "Job created successfully!",
        "updated_successfully": "Job updated successfully!",
        "deleted_successfully": "Job deleted successfully!",
        "application_submitted": "Your application has been submitted!"
    }
}
```

### Translation Usage in Blade
```html
<!-- Simple translation -->
<h1>{{ __('jobs.title') }}</h1>

<!-- Translation with parameters -->
<p>{{ __('jobs.created_at', ['time' => $job->created_at->diffForHumans()]) }}</p>

<!-- Pluralization -->
<span>{{ trans_choice('jobs.applications_count', $job->applications_count, ['count' => $job->applications_count]) }}</span>

<!-- Nested translation -->
<span class="badge">{{ __('jobs.status.' . $job->status) }}</span>
```

### Translation Usage in PHP
```php
// In controllers
return redirect()->back()->with('success', __('jobs.messages.created_successfully'));

// In services
throw new ValidationException(__('jobs.messages.application_already_exists'));

// In notifications
$this->line(__('jobs.messages.application_submitted'));
``` 