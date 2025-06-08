# System Patterns - Laravel Job Portal Backend Architecture Refactoring

## 🏛️ **ENTERPRISE ARCHITECTURAL PATTERNS**

### **1. Clean Architecture Pattern**
```php
// Layered Architecture Implementation
namespace App\Domain\Job\Entities;

class Job
{
    private JobId $id;
    private JobTitle $title;
    private JobDescription $description;
    private CompanyId $companyId;
    private Collection $requirements;
    private JobStatus $status;
    private DateTimeImmutable $expiresAt;

    public function publish(): void
    {
        if (!$this->canBePublished()) {
            throw new JobCannotBePublishedException();
        }
        
        $this->status = JobStatus::published();
        
        event(new JobPublished($this));
    }

    private function canBePublished(): bool
    {
        return $this->isValid() && 
               $this->hasRequiredInformation() && 
               $this->expiresAt->isFuture();
    }
}
```

### **2. Domain-Driven Design Pattern**
```php
// Domain Service Example
namespace App\Domain\Application\Services;

class JobApplicationService
{
    public function __construct(
        private JobRepository $jobRepository,
        private CandidateRepository $candidateRepository,
        private ApplicationRepository $applicationRepository
    ) {}

    public function submitApplication(
        CandidateId $candidateId,
        JobId $jobId,
        ApplicationData $data
    ): Application {
        $job = $this->jobRepository->findById($jobId);
        $candidate = $this->candidateRepository->findById($candidateId);
        
        $this->validateApplicationEligibility($job, $candidate);
        
        $application = Application::create(
            ApplicationId::generate(),
            $candidateId,
            $jobId,
            $data
        );
        
        $this->applicationRepository->save($application);
        
        event(new ApplicationSubmitted($application));
        
        return $application;
    }
}
```

---

## 🔧 **SERVICE LAYER PATTERNS**

### **1. Application Service Pattern**
```php
// Application Service with Command/Query Separation
namespace App\Application\Services;

abstract class BaseApplicationService
{
    protected function executeCommand(Command $command): mixed
    {
        return DB::transaction(function () use ($command) {
            $result = $this->handleCommand($command);
            $this->dispatchEvents();
            return $result;
        });
    }

    protected function executeQuery(Query $query): mixed
    {
        return $this->cacheManager->remember(
            $query->getCacheKey(),
            fn() => $this->handleQuery($query),
            $query->getCacheTtl()
        );
    }

    abstract protected function handleCommand(Command $command): mixed;
    abstract protected function handleQuery(Query $query): mixed;
}

// Concrete Implementation
class JobApplicationService extends BaseApplicationService
{
    protected function handleCommand(Command $command): mixed
    {
        return match($command::class) {
            SubmitJobApplicationCommand::class => $this->submitApplication($command),
            WithdrawApplicationCommand::class => $this->withdrawApplication($command),
            default => throw new UnsupportedCommandException()
        };
    }
}
```

### **2. Service Container Pattern**
```php
// Service Provider for Clean DI
namespace App\Providers;

class DomainServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Domain Services
        $this->app->bind(JobApplicationServiceInterface::class, JobApplicationService::class);
        $this->app->bind(NotificationServiceInterface::class, NotificationService::class);
        
        // Repositories
        $this->app->bind(JobRepositoryInterface::class, EloquentJobRepository::class);
        $this->app->bind(ApplicationRepositoryInterface::class, EloquentApplicationRepository::class);
        
        // Value Objects and Factories
        $this->app->bind(JobFactoryInterface::class, JobFactory::class);
    }
}
```

---

## 📊 **REPOSITORY PATTERN ENHANCEMENTS**

### **1. Repository with Specification Pattern**
```php
// Repository Interface with Specifications
namespace App\Infrastructure\Repositories\Contracts;

interface JobRepositoryInterface
{
    public function findById(JobId $id): ?Job;
    public function findBySpecification(Specification $specification): Collection;
    public function save(Job $job): void;
    public function delete(JobId $id): void;
}

// Specification Pattern Implementation
namespace App\Infrastructure\Specifications;

class JobSpecification implements Specification
{
    public function __construct(
        private ?string $location = null,
        private ?JobTypeId $typeId = null,
        private ?int $minSalary = null,
        private ?CompanyId $companyId = null
    ) {}

    public function toQuery(Builder $query): Builder
    {
        return $query
            ->when($this->location, fn($q) => $q->where('location', 'like', "%{$this->location}%"))
            ->when($this->typeId, fn($q) => $q->where('job_type_id', $this->typeId->value))
            ->when($this->minSalary, fn($q) => $q->where('salary_min', '>=', $this->minSalary))
            ->when($this->companyId, fn($q) => $q->where('company_id', $this->companyId->value));
    }
}

// Enhanced Repository Implementation
class EloquentJobRepository implements JobRepositoryInterface
{
    use CacheableRepository, OptimizedQueries;

    public function findBySpecification(Specification $specification): Collection
    {
        $cacheKey = $this->generateCacheKey('specification', $specification);
        
        return $this->cacheManager->remember($cacheKey, function () use ($specification) {
            return $specification
                ->toQuery(Job::query())
                ->with(['company', 'jobType', 'requirements'])
                ->get()
                ->map(fn($model) => $this->toDomainEntity($model));
        });
    }
}
```

### **2. Query Optimization Pattern**
```php
// Query Builder with Performance Optimization
namespace App\Infrastructure\Database;

class OptimizedQueryBuilder
{
    public function __construct(
        private Builder $query,
        private CacheManager $cache,
        private QueryProfiler $profiler
    ) {}

    public function findWithOptimizations(array $criteria): Collection
    {
        $this->profiler->start('optimized_query');
        
        $query = $this->query
            ->select($this->getOptimalColumns())
            ->with($this->getEagerLoadRelations())
            ->when($criteria['filters'] ?? null, fn($q) => $this->applyFilters($q, $criteria['filters']))
            ->when($criteria['search'] ?? null, fn($q) => $this->applySearch($q, $criteria['search']));
            
        $result = $this->cache->remember(
            $this->generateCacheKey($criteria),
            fn() => $query->get(),
            $this->getCacheTtl()
        );
        
        $this->profiler->end('optimized_query');
        
        return $result;
    }
}
```

---

## 🎯 **CONTROLLER PATTERNS**

### **1. Thin Controller Pattern**
```php
// Refactored Thin Controller
namespace App\Http\Controllers\Api;

class JobController extends BaseApiController
{
    public function __construct(
        private JobApplicationService $jobService,
        private JobQueryService $queryService
    ) {}

    public function index(IndexJobsRequest $request): JsonResponse
    {
        $query = new FindJobsQuery(
            filters: $request->getFilters(),
            pagination: $request->getPagination(),
            sorting: $request->getSorting()
        );

        $jobs = $this->queryService->execute($query);

        return $this->successResponse(
            JobResourceCollection::make($jobs),
            'Jobs retrieved successfully'
        );
    }

    public function store(CreateJobRequest $request): JsonResponse
    {
        $command = new CreateJobCommand(
            title: $request->validated('title'),
            description: $request->validated('description'),
            companyId: $request->user()->company->id,
            requirements: $request->validated('requirements'),
            expiresAt: $request->validated('expires_at')
        );

        $job = $this->jobService->execute($command);

        return $this->createdResponse(
            JobResource::make($job),
            'Job created successfully'
        );
    }
}
```

### **2. Base Controller Enhancement**
```php
// Enhanced Base Controller with Patterns
namespace App\Http\Controllers;

abstract class BaseApiController extends Controller
{
    use AuthorizesRequests, ValidatesRequests;

    protected function successResponse(
        mixed $data = null,
        string $message = 'Success',
        array $meta = []
    ): JsonResponse {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
            'meta' => array_merge([
                'timestamp' => now()->toISOString(),
                'request_id' => request()->header('X-Request-ID'),
            ], $meta)
        ]);
    }

    protected function errorResponse(
        string $message = 'Error',
        int $status = 400,
        array $errors = [],
        ?string $code = null
    ): JsonResponse {
        $response = [
            'success' => false,
            'message' => $message,
            'errors' => $errors,
            'meta' => [
                'timestamp' => now()->toISOString(),
                'request_id' => request()->header('X-Request-ID'),
                'status_code' => $status,
            ]
        ];

        if ($code) {
            $response['error_code'] = $code;
        }

        return response()->json($response, $status);
    }

    protected function executeWithRateLimit(
        string $action,
        callable $callback,
        int $maxAttempts = 60
    ): mixed {
        $key = $this->getRateLimitKey($action);
        
        if (RateLimiter::tooManyAttempts($key, $maxAttempts)) {
            throw new TooManyRequestsHttpException(
                RateLimiter::availableIn($key)
            );
        }

        RateLimiter::hit($key);
        
        return $callback();
    }
}
```

---

## 🔐 **SECURITY PATTERNS**

### **1. Authorization Service Pattern**
```php
// Centralized Authorization
namespace App\Services\Security;

class AuthorizationService
{
    public function __construct(
        private PermissionRegistry $permissions,
        private RoleRepository $roleRepository
    ) {}

    public function authorize(User $user, string $permission, ?Model $resource = null): bool
    {
        $userPermissions = $this->getUserPermissions($user);
        
        if ($this->hasDirectPermission($userPermissions, $permission)) {
            return true;
        }

        if ($resource && $this->hasResourcePermission($user, $permission, $resource)) {
            return true;
        }

        $this->logAuthorizationAttempt($user, $permission, $resource, false);
        
        return false;
    }

    private function hasResourcePermission(User $user, string $permission, Model $resource): bool
    {
        return match($resource::class) {
            Job::class => $this->authorizeJobAccess($user, $permission, $resource),
            Company::class => $this->authorizeCompanyAccess($user, $permission, $resource),
            default => false
        };
    }
}
```

### **2. Security Middleware Pattern**
```php
// Enhanced Security Middleware
namespace App\Http\Middleware;

class SecurityEnforcementMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $this->validateSecurityHeaders($request);
        $this->checkRateLimit($request);
        $this->validateCSRF($request);
        $this->logSecurityEvent($request);

        $response = $next($request);

        $this->addSecurityHeaders($response);
        $this->sanitizeResponse($response);

        return $response;
    }

    private function addSecurityHeaders(Response $response): void
    {
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'DENY');
        $response->headers->set('X-XSS-Protection', '1; mode=block');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        
        if (config('app.env') === 'production') {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        }
    }
}
```

---

## 📝 **VALIDATION PATTERNS**

### **1. Enhanced Form Request Pattern**
```php
// Base Form Request with Security
namespace App\Http\Requests;

abstract class BaseFormRequest extends FormRequest
{
    protected array $securityRules = [
        'no_sql_injection',
        'no_xss_attempts',
        'content_length_limit',
        'file_type_validation'
    ];

    public function authorize(): bool
    {
        return $this->authorizeWithPolicy();
    }

    protected function prepareForValidation(): void
    {
        $this->sanitizeInput();
        $this->validateSecurityConstraints();
    }

    protected function sanitizeInput(): void
    {
        $input = $this->all();
        
        array_walk_recursive($input, function (&$value) {
            if (is_string($value)) {
                $value = strip_tags($value);
                $value = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
            }
        });
        
        $this->replace($input);
    }

    protected function authorizeWithPolicy(): bool
    {
        $user = $this->user();
        $resource = $this->getResourceForAuthorization();
        $action = $this->getActionForAuthorization();

        return app(AuthorizationService::class)->authorize($user, $action, $resource);
    }

    abstract protected function getResourceForAuthorization(): ?Model;
    abstract protected function getActionForAuthorization(): string;
}
```

### **2. Rule Factory Pattern**
```php
// Reusable Validation Rules
namespace App\Rules\Factory;

class ValidationRuleFactory
{
    public static function createJobValidationRules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255', new NoMaliciousContent()],
            'description' => ['required', 'string', 'max:5000', new RichTextValidation()],
            'salary_min' => ['nullable', 'integer', 'min:0', 'max:1000000'],
            'salary_max' => ['nullable', 'integer', 'min:0', 'max:1000000', 'gte:salary_min'],
            'location' => ['required', 'string', 'max:255', new LocationValidation()],
            'expires_at' => ['required', 'date', 'after:today', 'before:+1 year'],
            'requirements' => ['array', 'max:10'],
            'requirements.*' => ['string', 'max:500', new SkillValidation()],
        ];
    }

    public static function createCompanyValidationRules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', new CompanyNameValidation()],
            'email' => ['required', 'email', 'max:255', new BusinessEmailValidation()],
            'phone' => ['nullable', 'string', new PhoneValidation()],
            'website' => ['nullable', 'url', 'max:255', new WebsiteValidation()],
            'description' => ['nullable', 'string', 'max:2000', new SafeContentValidation()],
        ];
    }
}
```

---

## 🔄 **EVENT-DRIVEN PATTERNS**

### **1. Domain Events Pattern**
```php
// Domain Event Implementation
namespace App\Domain\Events;

abstract class DomainEvent
{
    public readonly DateTimeImmutable $occurredAt;
    public readonly string $eventId;

    public function __construct()
    {
        $this->occurredAt = new DateTimeImmutable();
        $this->eventId = Str::uuid();
    }

    abstract public function getAggregateId(): string;
    abstract public function getEventName(): string;
}

class JobPublished extends DomainEvent
{
    public function __construct(
        public readonly Job $job
    ) {
        parent::__construct();
    }

    public function getAggregateId(): string
    {
        return $this->job->getId()->value;
    }

    public function getEventName(): string
    {
        return 'job.published';
    }
}

// Event Handler
class NotifySubscribersWhenJobPublished
{
    public function handle(JobPublished $event): void
    {
        $subscribers = $this->subscriptionService->getSubscribersForJob($event->job);
        
        foreach ($subscribers as $subscriber) {
            $this->notificationService->sendJobNotification(
                $subscriber,
                $event->job
            );
        }
    }
}
```

### **2. Event Sourcing Pattern (Basic)**
```php
// Event Store for Audit Trail
namespace App\Infrastructure\EventStore;

class EventStore
{
    public function append(DomainEvent $event): void
    {
        EventLog::create([
            'event_id' => $event->eventId,
            'aggregate_id' => $event->getAggregateId(),
            'event_name' => $event->getEventName(),
            'event_data' => json_encode($event),
            'occurred_at' => $event->occurredAt,
            'user_id' => auth()->id(),
            'ip_address' => request()->ip(),
        ]);
    }

    public function getEventsForAggregate(string $aggregateId): Collection
    {
        return EventLog::where('aggregate_id', $aggregateId)
            ->orderBy('occurred_at')
            ->get()
            ->map(fn($log) => $this->deserializeEvent($log));
    }
}
```

---

## 🔧 **PERFORMANCE OPTIMIZATION PATTERNS**

### **1. Multi-Layer Caching Pattern**
```php
// Comprehensive Caching Strategy
namespace App\Services\Cache;

class LayeredCacheService
{
    public function __construct(
        private Repository $l1Cache, // Memory cache (APCu)
        private Repository $l2Cache, // Redis cache
        private Repository $l3Cache  // Database cache
    ) {}

    public function remember(string $key, callable $callback, int $ttl = 3600): mixed
    {
        // L1: Memory cache (fastest)
        if ($value = $this->l1Cache->get($key)) {
            return $value;
        }

        // L2: Redis cache (fast)
        if ($value = $this->l2Cache->get($key)) {
            $this->l1Cache->put($key, $value, min($ttl, 300)); // 5 min max in memory
            return $value;
        }

        // L3: Generate and cache
        $value = $callback();
        
        $this->l3Cache->put($key, $value, $ttl);
        $this->l2Cache->put($key, $value, $ttl);
        $this->l1Cache->put($key, $value, min($ttl, 300));

        return $value;
    }
}
```

### **2. Query Optimization Pattern**
```php
// Database Query Optimization
namespace App\Services\Database;

class QueryOptimizationService
{
    public function optimizeQuery(Builder $query, array $options = []): Builder
    {
        // Select only needed columns
        if (isset($options['select'])) {
            $query->select($options['select']);
        }

        // Eager load relationships efficiently
        if (isset($options['with'])) {
            $query->with($options['with']);
        }

        // Add database hints for complex queries
        if ($options['hint'] ?? false) {
            $query->hint($options['hint']);
        }

        // Use index hints for specific queries
        if (isset($options['index'])) {
            $query->useIndex($options['index']);
        }

        return $query;
    }

    public function addPerformanceMonitoring(Builder $query): Builder
    {
        return $query->tap(function ($query) {
            $start = microtime(true);
            
            $query->macro('getElapsedTime', function () use ($start) {
                return microtime(true) - $start;
            });
        });
    }
}
```

---

## 📊 **MONITORING AND OBSERVABILITY PATTERNS**

### **1. Application Performance Monitoring**
```php
// Performance Monitoring Service
namespace App\Services\Monitoring;

class PerformanceMonitor
{
    public function trackOperation(string $operation, callable $callback): mixed
    {
        $startTime = microtime(true);
        $startMemory = memory_get_usage(true);

        try {
            $result = $callback();
            
            $this->recordSuccess($operation, $startTime, $startMemory);
            
            return $result;
        } catch (Exception $e) {
            $this->recordFailure($operation, $e, $startTime, $startMemory);
            throw $e;
        }
    }

    private function recordSuccess(string $operation, float $startTime, int $startMemory): void
    {
        $metrics = [
            'operation' => $operation,
            'duration' => (microtime(true) - $startTime) * 1000, // ms
            'memory_usage' => memory_get_usage(true) - $startMemory,
            'peak_memory' => memory_get_peak_usage(true),
            'status' => 'success',
            'timestamp' => now(),
        ];

        Log::channel('performance')->info('Operation completed', $metrics);
    }
}
```

These patterns provide a comprehensive foundation for the backend architecture refactoring, ensuring scalability, maintainability, security, and performance optimization throughout the Laravel job portal application. 