# 🔍 Laravel Job Portal - Comprehensive Analysis & Fixes

## 📊 Project Overview
This is a **Laravel-based Job Portal Application** with the following core features:
- User management (Admin, Employer, Candidate roles)
- Company management
- Job posting and applications
- Payment system integration (Stripe, PayPal, Paystack)
- Multi-language support
- Featured jobs/companies
- Real-time notifications

## 🚨 Critical Issues Identified

### 1. **Memory Issues** 
- **Problem**: Memory exhaustion (128MB limit) when running Artisan commands
- **Impact**: Cannot run tests, migrations, or heavy operations
- **Fix Priority**: HIGH

### 2. **Model Architecture Issues**
- **Problem**: Inconsistent relationship definitions and missing validation
- **Impact**: Data integrity and performance issues
- **Fix Priority**: HIGH

### 3. **Controller Design Issues**
- **Problem**: Controllers are too fat with business logic mixed with presentation
- **Impact**: Maintainability and testability issues
- **Fix Priority**: MEDIUM

### 4. **Testing Infrastructure**
- **Problem**: Tests fail due to memory issues and missing configurations
- **Impact**: No way to verify code quality and prevent regressions
- **Fix Priority**: HIGH

### 5. **Security Concerns**
- **Problem**: Missing CSRF protection, input validation, and proper authentication
- **Impact**: Application vulnerable to attacks
- **Fix Priority**: HIGH

## 🛠️ Comprehensive Fix Implementation

### Phase 1: Infrastructure & Memory Fixes

#### A. Memory Optimization
```php
// config/app.php - Add memory management
'memory_limit' => env('MEMORY_LIMIT', '512M'),

// Add to .env
MEMORY_LIMIT=512M
```

#### B. Update PHP Configuration
```ini
; php.ini optimizations
memory_limit = 512M
max_execution_time = 300
upload_max_filesize = 50M
post_max_size = 50M
```

### Phase 2: Model Architecture Improvements

#### A. User Model Fixes
```php
<?php
// app/Models/User.php - Enhanced version

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, HasRoles;

    // Constants for better maintainability
    public const ADMIN = 1;
    public const EMPLOYER = 2;
    public const CANDIDATE = 3;

    public const ACTIVE = 1;
    public const INACTIVE = 0;

    protected $fillable = [
        'first_name', 'last_name', 'email', 'password', 'phone',
        'country_id', 'state_id', 'city_id', 'is_active', 'user_type'
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_active' => 'boolean',
        'is_verified' => 'boolean',
        'user_type' => 'integer',
    ];

    // Improved relationships with proper return types
    public function candidate(): HasOne
    {
        return $this->hasOne(Candidate::class);
    }

    public function company(): HasOne
    {
        return $this->hasOne(Company::class);
    }

    public function jobs(): HasMany
    {
        return $this->hasMany(Job::class, 'company_id')
            ->whereHas('company', fn($q) => $q->where('user_id', $this->id));
    }

    // Scopes for better query building
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByRole($query, string $role)
    {
        return $query->whereHas('roles', fn($q) => $q->where('name', $role));
    }

    // Accessors
    public function getFullNameAttribute(): string
    {
        return trim("{$this->first_name} {$this->last_name}");
    }

    public function getIsAdminAttribute(): bool
    {
        return $this->hasRole('Admin');
    }

    public function getIsEmployerAttribute(): bool
    {
        return $this->hasRole('Employer');
    }

    public function getIsCandidateAttribute(): bool
    {
        return $this->hasRole('Candidate');
    }
}
```

#### B. Company Model Improvements
```php
<?php
// app/Models/Company.php - Enhanced version

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Company extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id', 'ceo', 'industry_id', 'ownership_type_id', 
        'company_size_id', 'established_in', 'details', 'website',
        'location', 'no_of_offices', 'unique_id'
    ];

    protected $casts = [
        'established_in' => 'integer',
        'no_of_offices' => 'integer',
        'is_active' => 'boolean',
    ];

    protected $with = ['industry', 'companySize', 'ownerShipType'];

    // Validation rules as class property for reusability
    public static array $rules = [
        'ceo' => 'required|string|max:180',
        'industry_id' => 'required|exists:industries,id',
        'ownership_type_id' => 'required|exists:ownership_types,id',
        'company_size_id' => 'required|exists:company_sizes,id',
        'established_in' => 'required|integer|min:1900|max:' . date('Y'),
        'website' => 'nullable|url|max:255',
        'location' => 'required|string|max:255',
        'no_of_offices' => 'required|integer|min:1|max:1000',
        'details' => 'nullable|string|max:5000',
    ];

    // Improved relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function industry(): BelongsTo
    {
        return $this->belongsTo(Industry::class);
    }

    public function ownerShipType(): BelongsTo
    {
        return $this->belongsTo(OwnerShipType::class, 'ownership_type_id');
    }

    public function companySize(): BelongsTo
    {
        return $this->belongsTo(CompanySize::class);
    }

    public function jobs(): HasMany
    {
        return $this->hasMany(Job::class);
    }

    public function activeJobs(): HasMany
    {
        return $this->jobs()->where('status', Job::STATUS_OPEN);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->whereHas('user', fn($q) => $q->where('is_active', true));
    }

    public function scopeFeatured($query)
    {
        return $query->whereHas('featured');
    }

    // Accessors
    public function getCompanyUrlAttribute(): string
    {
        return route('company.show', $this->id);
    }

    public function getLocationFullAttribute(): string
    {
        $parts = array_filter([
            $this->location,
            $this->user?->city?->name,
            $this->user?->state?->name,
            $this->user?->country?->name
        ]);
        
        return implode(', ', $parts);
    }
}
```

#### C. Job Model Enhancements
```php
<?php
// app/Models/Job.php - Enhanced version

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

class Job extends Model
{
    use HasFactory, SoftDeletes;

    // Status constants
    public const STATUS_DRAFT = 0;
    public const STATUS_OPEN = 1;
    public const STATUS_CLOSED = 2;
    public const STATUS_PAUSED = 3;
    public const STATUS_SUSPENDED = 4;

    // Gender preferences
    public const GENDER_MALE = 0;
    public const GENDER_FEMALE = 1;
    public const GENDER_BOTH = 2;

    protected $fillable = [
        'job_title', 'description', 'company_id', 'job_category_id',
        'job_type_id', 'career_level_id', 'functional_area_id',
        'salary_from', 'salary_to', 'currency_id', 'salary_period_id',
        'country_id', 'state_id', 'city_id', 'job_expiry_date',
        'experience', 'degree_level_id', 'job_shift_id',
        'no_preference', 'hide_salary', 'status'
    ];

    protected $casts = [
        'salary_from' => 'decimal:2',
        'salary_to' => 'decimal:2',
        'job_expiry_date' => 'datetime',
        'hide_salary' => 'boolean',
        'no_preference' => 'integer',
        'status' => 'integer',
        'experience' => 'integer',
    ];

    protected $with = ['company', 'jobType', 'jobCategory'];

    // Validation rules
    public static array $rules = [
        'job_title' => 'required|string|max:255',
        'description' => 'required|string|max:10000',
        'company_id' => 'required|exists:companies,id',
        'job_category_id' => 'required|exists:job_categories,id',
        'job_type_id' => 'required|exists:job_types,id',
        'salary_from' => 'nullable|numeric|min:0',
        'salary_to' => 'nullable|numeric|min:0|gte:salary_from',
        'job_expiry_date' => 'required|date|after:today',
        'experience' => 'nullable|integer|min:0|max:50',
    ];

    // Relationships
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function jobCategory(): BelongsTo
    {
        return $this->belongsTo(JobCategory::class);
    }

    public function jobType(): BelongsTo
    {
        return $this->belongsTo(JobType::class);
    }

    public function applications(): HasMany
    {
        return $this->hasMany(JobApplication::class);
    }

    public function skills(): BelongsToMany
    {
        return $this->belongsToMany(Skill::class, 'jobs_skill');
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'jobs_tag');
    }

    // Scopes
    public function scopeOpen(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_OPEN);
    }

    public function scopeNotExpired(Builder $query): Builder
    {
        return $query->where('job_expiry_date', '>', now());
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->open()->notExpired();
    }

    public function scopeByCategory(Builder $query, int $categoryId): Builder
    {
        return $query->where('job_category_id', $categoryId);
    }

    public function scopeByLocation(Builder $query, ?int $countryId = null, ?int $stateId = null, ?int $cityId = null): Builder
    {
        return $query->when($countryId, fn($q) => $q->where('country_id', $countryId))
                    ->when($stateId, fn($q) => $q->where('state_id', $stateId))
                    ->when($cityId, fn($q) => $q->where('city_id', $cityId));
    }

    // Accessors
    public function getStatusTextAttribute(): string
    {
        return match($this->status) {
            self::STATUS_DRAFT => 'Draft',
            self::STATUS_OPEN => 'Open',
            self::STATUS_CLOSED => 'Closed',
            self::STATUS_PAUSED => 'Paused',
            self::STATUS_SUSPENDED => 'Suspended',
            default => 'Unknown'
        };
    }

    public function getIsActiveAttribute(): bool
    {
        return $this->status === self::STATUS_OPEN && 
               $this->job_expiry_date > now();
    }

    public function getSalaryRangeAttribute(): string
    {
        if ($this->hide_salary || (!$this->salary_from && !$this->salary_to)) {
            return 'Negotiable';
        }

        $currency = $this->currency?->currency_code ?? '$';
        
        if ($this->salary_from && $this->salary_to) {
            return "{$currency}{$this->salary_from} - {$currency}{$this->salary_to}";
        }
        
        return $this->salary_from ? 
            "From {$currency}{$this->salary_from}" : 
            "Up to {$currency}{$this->salary_to}";
    }
}
```

### Phase 3: Service Layer Implementation

#### A. Create Service Classes
```php
<?php
// app/Services/CompanyService.php

namespace App\Services;

use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class CompanyService
{
    public function __construct(
        private UserService $userService
    ) {}

    public function getAllPaginated(int $perPage = 15): LengthAwarePaginator
    {
        return Company::with(['user', 'industry', 'companySize'])
            ->active()
            ->latest()
            ->paginate($perPage);
    }

    public function create(array $data): Company
    {
        \DB::beginTransaction();
        
        try {
            // Create user first
            $userData = [
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'email' => $data['email'],
                'password' => bcrypt($data['password']),
                'user_type' => User::EMPLOYER,
                'is_active' => $data['is_active'] ?? true,
            ];
            
            $user = $this->userService->create($userData);
            $user->assignRole('Employer');
            
            // Create company
            $companyData = array_merge($data, ['user_id' => $user->id]);
            $company = Company::create($companyData);
            
            \DB::commit();
            return $company;
            
        } catch (\Exception $e) {
            \DB::rollBack();
            throw $e;
        }
    }

    public function update(Company $company, array $data): Company
    {
        \DB::beginTransaction();
        
        try {
            // Update user data if provided
            if (isset($data['first_name']) || isset($data['last_name']) || isset($data['email'])) {
                $userData = array_intersect_key($data, array_flip(['first_name', 'last_name', 'email']));
                $company->user->update($userData);
            }
            
            // Update company data
            $company->update($data);
            
            \DB::commit();
            return $company->fresh();
            
        } catch (\Exception $e) {
            \DB::rollBack();
            throw $e;
        }
    }

    public function delete(Company $company): bool
    {
        \DB::beginTransaction();
        
        try {
            // Soft delete jobs first
            $company->jobs()->delete();
            
            // Delete company
            $company->delete();
            
            // Deactivate user
            $company->user->update(['is_active' => false]);
            
            \DB::commit();
            return true;
            
        } catch (\Exception $e) {
            \DB::rollBack();
            throw $e;
        }
    }

    public function search(string $query): Collection
    {
        return Company::where('ceo', 'like', "%{$query}%")
            ->orWhereHas('user', fn($q) => $q->where('first_name', 'like', "%{$query}%"))
            ->orWhereHas('industry', fn($q) => $q->where('name', 'like', "%{$query}%"))
            ->with(['user', 'industry'])
            ->get();
    }

    public function getFeatured(int $limit = 10): Collection
    {
        return Company::featured()
            ->active()
            ->with(['user', 'industry'])
            ->limit($limit)
            ->get();
    }
}
```

### Phase 4: Controller Improvements

#### A. Enhanced CompanyController
```php
<?php
// app/Http/Controllers/CompanyController.php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCompanyRequest;
use App\Http\Requests\UpdateCompanyRequest;
use App\Models\Company;
use App\Services\CompanyService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class CompanyController extends Controller
{
    public function __construct(
        private CompanyService $companyService
    ) {
        $this->middleware('auth')->except(['index', 'show']);
        $this->middleware('role:Admin')->only(['destroy', 'toggleStatus']);
    }

    public function index(Request $request): View
    {
        $companies = $this->companyService->getAllPaginated(
            $request->get('per_page', 15)
        );

        return view('companies.index', compact('companies'));
    }

    public function show(Company $company): View
    {
        $company->load(['user', 'industry', 'companySize', 'activeJobs']);
        
        return view('companies.show', compact('company'));
    }

    public function create(): View
    {
        return view('companies.create');
    }

    public function store(StoreCompanyRequest $request): RedirectResponse
    {
        try {
            $company = $this->companyService->create($request->validated());
            
            return redirect()
                ->route('companies.show', $company)
                ->with('success', 'Company created successfully!');
                
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Failed to create company: ' . $e->getMessage());
        }
    }

    public function edit(Company $company): View
    {
        $this->authorize('update', $company);
        
        return view('companies.edit', compact('company'));
    }

    public function update(UpdateCompanyRequest $request, Company $company): RedirectResponse
    {
        $this->authorize('update', $company);
        
        try {
            $this->companyService->update($company, $request->validated());
            
            return redirect()
                ->route('companies.show', $company)
                ->with('success', 'Company updated successfully!');
                
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Failed to update company: ' . $e->getMessage());
        }
    }

    public function destroy(Company $company): JsonResponse
    {
        try {
            $this->companyService->delete($company);
            
            return response()->json([
                'success' => true,
                'message' => 'Company deleted successfully!'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete company: ' . $e->getMessage()
            ], 500);
        }
    }

    public function search(Request $request): JsonResponse
    {
        $request->validate(['q' => 'required|string|min:2']);
        
        $companies = $this->companyService->search($request->q);
        
        return response()->json($companies);
    }
}
```

### Phase 5: Form Request Validation

#### A. Company Validation
```php
<?php
// app/Http/Requests/StoreCompanyRequest.php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCompanyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            // User data
            'first_name' => 'required|string|max:50',
            'last_name' => 'required|string|max:50',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            
            // Company data
            'ceo' => 'required|string|max:180',
            'industry_id' => 'required|exists:industries,id',
            'ownership_type_id' => 'required|exists:ownership_types,id',
            'company_size_id' => 'required|exists:company_sizes,id',
            'established_in' => 'required|integer|min:1900|max:' . date('Y'),
            'website' => 'nullable|url|max:255',
            'location' => 'required|string|max:255',
            'no_of_offices' => 'required|integer|min:1|max:1000',
            'details' => 'nullable|string|max:5000',
            'is_active' => 'boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'ceo.required' => 'CEO name is required.',
            'established_in.min' => 'Establishment year must be after 1900.',
            'established_in.max' => 'Establishment year cannot be in the future.',
            'no_of_offices.min' => 'Number of offices must be at least 1.',
            'no_of_offices.max' => 'Number of offices cannot exceed 1000.',
        ];
    }
}
```

### Phase 6: Testing Infrastructure

#### A. Model Tests
```php
<?php
// tests/Unit/Models/CompanyModelTest.php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\Company;
use App\Models\User;
use App\Models\Industry;
use App\Models\CompanySize;
use App\Models\OwnerShipType;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CompanyModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_company_belongs_to_user(): void
    {
        $user = User::factory()->create();
        $company = Company::factory()->create(['user_id' => $user->id]);

        $this->assertInstanceOf(User::class, $company->user);
        $this->assertEquals($user->id, $company->user->id);
    }

    public function test_company_belongs_to_industry(): void
    {
        $industry = Industry::factory()->create();
        $company = Company::factory()->create(['industry_id' => $industry->id]);

        $this->assertInstanceOf(Industry::class, $company->industry);
        $this->assertEquals($industry->name, $company->industry->name);
    }

    public function test_company_url_attribute(): void
    {
        $company = Company::factory()->create();
        
        $expectedUrl = route('company.show', $company->id);
        $this->assertEquals($expectedUrl, $company->company_url);
    }

    public function test_company_has_many_jobs(): void
    {
        $company = Company::factory()
            ->hasJobs(3)
            ->create();

        $this->assertCount(3, $company->jobs);
    }

    public function test_active_scope_filters_by_user_status(): void
    {
        $activeUser = User::factory()->create(['is_active' => true]);
        $inactiveUser = User::factory()->create(['is_active' => false]);
        
        $activeCompany = Company::factory()->create(['user_id' => $activeUser->id]);
        $inactiveCompany = Company::factory()->create(['user_id' => $inactiveUser->id]);

        $activeCompanies = Company::active()->get();

        $this->assertTrue($activeCompanies->contains($activeCompany));
        $this->assertFalse($activeCompanies->contains($inactiveCompany));
    }
}
```

#### B. Service Tests
```php
<?php
// tests/Unit/Services/CompanyServiceTest.php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Services\CompanyService;
use App\Services\UserService;
use App\Models\Company;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Database\Eloquent\Collection;

class CompanyServiceTest extends TestCase
{
    use RefreshDatabase;

    private CompanyService $companyService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->companyService = app(CompanyService::class);
    }

    public function test_can_create_company_with_user(): void
    {
        $data = [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
            'ceo' => 'John Doe',
            'industry_id' => Industry::factory()->create()->id,
            'ownership_type_id' => OwnerShipType::factory()->create()->id,
            'company_size_id' => CompanySize::factory()->create()->id,
            'established_in' => 2020,
            'location' => 'New York',
            'no_of_offices' => 1,
        ];

        $company = $this->companyService->create($data);

        $this->assertInstanceOf(Company::class, $company);
        $this->assertEquals('John Doe', $company->ceo);
        $this->assertEquals('john@example.com', $company->user->email);
        $this->assertTrue($company->user->hasRole('Employer'));
    }

    public function test_can_search_companies(): void
    {
        $company1 = Company::factory()->create(['ceo' => 'John Smith']);
        $company2 = Company::factory()->create(['ceo' => 'Jane Doe']);
        $company3 = Company::factory()->create(['ceo' => 'Bob Wilson']);

        $results = $this->companyService->search('John');

        $this->assertInstanceOf(Collection::class, $results);
        $this->assertTrue($results->contains($company1));
        $this->assertFalse($results->contains($company2));
        $this->assertFalse($results->contains($company3));
    }

    public function test_can_delete_company_and_deactivate_user(): void
    {
        $company = Company::factory()->create();
        $user = $company->user;

        $result = $this->companyService->delete($company);

        $this->assertTrue($result);
        $this->assertTrue($company->fresh()->trashed());
        $this->assertFalse($user->fresh()->is_active);
    }
}
```

### Phase 7: Security Enhancements

#### A. Authorization Policies
```php
<?php
// app/Policies/CompanyPolicy.php

namespace App\Policies;

use App\Models\Company;
use App\Models\User;

class CompanyPolicy
{
    public function viewAny(User $user): bool
    {
        return true; // Anyone can view companies list
    }

    public function view(User $user, Company $company): bool
    {
        return true; // Anyone can view individual company
    }

    public function create(User $user): bool
    {
        return $user->hasRole(['Admin', 'Employer']);
    }

    public function update(User $user, Company $company): bool
    {
        return $user->hasRole('Admin') || 
               ($user->hasRole('Employer') && $user->id === $company->user_id);
    }

    public function delete(User $user, Company $company): bool
    {
        return $user->hasRole('Admin');
    }

    public function toggleStatus(User $user): bool
    {
        return $user->hasRole('Admin');
    }
}
```

#### B. Middleware for API Protection
```php
<?php
// app/Http/Middleware/EnsureUserIsActive.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureUserIsActive
{
    public function handle(Request $request, Closure $next)
    {
        if (auth()->check() && !auth()->user()->is_active) {
            auth()->logout();
            
            return redirect('/')
                ->with('error', 'Your account has been deactivated. Please contact support.');
        }

        return $next($request);
    }
}
```

### Phase 8: Performance Optimizations

#### A. Database Query Optimization
```php
<?php
// app/Http/Controllers/JobController.php - Optimized queries

public function index(Request $request): View
{
    $jobs = Job::query()
        ->with([
            'company:id,ceo,user_id',
            'company.user:id,first_name,last_name',
            'jobCategory:id,name',
            'jobType:id,name'
        ])
        ->active()
        ->when($request->category, fn($q, $cat) => $q->byCategory($cat))
        ->when($request->location, fn($q, $loc) => $q->byLocation($loc))
        ->when($request->search, fn($q, $search) => $q->where('job_title', 'like', "%{$search}%"))
        ->latest()
        ->paginate(15);

    return view('jobs.index', compact('jobs'));
}
```

#### B. Caching Strategy
```php
<?php
// app/Services/CacheService.php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

class CacheService
{
    public function rememberForever(string $key, callable $callback)
    {
        return Cache::tags(['static'])->rememberForever($key, $callback);
    }

    public function rememberFor(string $key, int $seconds, callable $callback)
    {
        return Cache::remember($key, $seconds, $callback);
    }

    public function flushByTags(array $tags): void
    {
        Cache::tags($tags)->flush();
    }

    public function getPopularJobs(int $limit = 10)
    {
        return $this->rememberFor('popular_jobs_' . $limit, 3600, function() use ($limit) {
            return Job::withCount('applications')
                ->active()
                ->orderBy('applications_count', 'desc')
                ->limit($limit)
                ->get();
        });
    }

    public function getFeaturedCompanies(int $limit = 10)
    {
        return $this->rememberFor('featured_companies_' . $limit, 3600, function() use ($limit) {
            return Company::featured()
                ->active()
                ->with(['industry', 'user'])
                ->limit($limit)
                ->get();
        });
    }
}
```

## 🎯 Implementation Roadmap

### Week 1: Foundation
- [ ] Fix memory issues and PHP configuration
- [ ] Set up proper testing environment
- [ ] Implement core model improvements

### Week 2: Architecture
- [ ] Create service layer classes
- [ ] Implement form request validations
- [ ] Add authorization policies

### Week 3: Security & Performance
- [ ] Add security middleware
- [ ] Implement caching strategies
- [ ] Optimize database queries

### Week 4: Testing & Documentation
- [ ] Write comprehensive tests
- [ ] Add API documentation
- [ ] Performance monitoring setup

## 📋 Quality Checklist

### Code Quality
- [ ] All models have proper relationships and validation
- [ ] Controllers use service classes for business logic
- [ ] Form requests handle validation
- [ ] Policies protect sensitive operations

### Security
- [ ] CSRF protection on all forms
- [ ] Input validation and sanitization
- [ ] Authorization on all sensitive routes
- [ ] SQL injection prevention

### Performance
- [ ] Database queries are optimized
- [ ] Caching implemented for static data
- [ ] File uploads handled efficiently
- [ ] Memory usage monitored

### Testing
- [ ] Unit tests for all models
- [ ] Feature tests for critical paths
- [ ] Integration tests for complex workflows
- [ ] Test coverage above 80%

## 🚀 Next Steps

1. **Immediate**: Fix memory issues and basic model relationships
2. **Short-term**: Implement service layer and proper validation
3. **Medium-term**: Add comprehensive testing and security
4. **Long-term**: Performance optimization and monitoring

This comprehensive analysis provides a roadmap for transforming the job portal into a production-ready, maintainable, and secure application following Laravel best practices. 