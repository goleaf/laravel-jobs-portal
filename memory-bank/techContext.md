# TECH CONTEXT - LEVEL 4 ENTERPRISE TECHNOLOGY STACK

**Project**: Laravel Job Portal - Level 4 Comprehensive System Transformation  
**Architecture**: Enterprise-Grade Full-Stack Technology Platform  
**Implementation**: Modern PHP 8.3, Vue 3 + TypeScript, Advanced Enterprise Tooling

---

## 🚀 **ENTERPRISE TECHNOLOGY STACK**

### **Backend Technology Platform**
```
┌─────────────────────────────────────────────────────────┐
│                  BACKEND STACK                          │
├─────────────────────────────────────────────────────────┤
│ Framework:     Laravel 12.17.0 (Latest LTS)           │
│ PHP Version:   8.3.15 (Latest Stable)                  │
│ Architecture:  Clean Architecture + DDD                │
│ ORM:          Eloquent with Advanced Query Optimization │
│ Database:     SQLite (Development) / PostgreSQL (Prod) │
│ Cache:        Redis 7.0+ with Multi-Layer Strategy     │
│ Queue:        Laravel Queues with Redis Driver         │
│ Search:       Laravel Scout with Algolia/Meilisearch   │
│ Storage:      Laravel Storage with S3/CDN Integration  │
│ Security:     Laravel Sanctum + Advanced RBAC          │
│ Testing:      PHPUnit 11.x + Pest Framework            │
│ Code Quality: Pint + Larastan + Rector                 │
└─────────────────────────────────────────────────────────┘
```

### **Frontend Technology Platform**
```
┌─────────────────────────────────────────────────────────┐
│                 FRONTEND STACK                          │
├─────────────────────────────────────────────────────────┤
│ Framework:     Vue 3.4+ with Composition API           │
│ Language:      TypeScript 5.3+ (Strict Mode)          │
│ Build Tool:    Vite 5.0+ with Modern Asset Pipeline    │
│ State Mgmt:    Pinia 2.1+ with Persistent State        │
│ Router:        Vue Router 4.2+ with Type Safety        │
│ UI Framework:  TailwindCSS 3.4+ + HeadlessUI           │
│ Icons:         Heroicons + Lucide Icons                │
│ Forms:         VeeValidate + Yup Validation            │
│ HTTP Client:   Axios with Interceptors + Retry Logic   │
│ Testing:       Vitest + Vue Test Utils + Playwright    │
│ Code Quality:  ESLint + Prettier + TypeScript ESLint   │
└─────────────────────────────────────────────────────────┘
```

### **Development & Deployment Platform**
```
┌─────────────────────────────────────────────────────────┐
│               DEVOPS & DEPLOYMENT                       │
├─────────────────────────────────────────────────────────┤
│ Version Control: Git with Conventional Commits         │
│ CI/CD:          GitHub Actions / GitLab CI             │
│ Containerization: Docker + Docker Compose             │
│ Orchestration:  Kubernetes / Docker Swarm             │
│ Monitoring:     Laravel Telescope + Sentry            │
│ Logging:        Laravel Log + ELK Stack               │
│ Performance:    Laravel Debugbar + New Relic          │
│ Security:       OWASP Tools + Security Headers        │
│ Documentation:  OpenAPI + Swagger + VitePress         │
│ Package Mgmt:   Composer 2.x + NPM/PNPM               │
└─────────────────────────────────────────────────────────┘
```

---

## 📊 **DATABASE ARCHITECTURE**

### **Current Database Configuration**
```yaml
# Primary Database (Development)
Database Type: SQLite
Location: database/database.sqlite
Size: ~50MB (with sample data)
Encoding: UTF-8
Collation: BINARY
PRAGMA Settings:
  - foreign_keys = ON
  - journal_mode = WAL
  - synchronous = NORMAL
  - cache_size = 10000
  - temp_store = MEMORY

# Production Database (Target)
Database Type: PostgreSQL 15+
Configuration:
  - Shared Buffers: 256MB
  - Effective Cache Size: 1GB
  - Work Memory: 16MB
  - Maintenance Work Memory: 64MB
  - Max Connections: 200
  - Connection Pool: PgBouncer
```

### **Database Optimization Strategies**
- **Indexing**: Strategic B-tree and GIN indexes on search columns
- **Partitioning**: Time-based partitioning for large tables (jobs, applications)
- **Materialized Views**: Pre-computed aggregations for analytics
- **Query Optimization**: N+1 prevention with eager loading
- **Connection Pooling**: PgBouncer for connection management
- **Read Replicas**: Separate read/write database instances

### **Enhanced Model Configurations**
```php
// Standard Model Configuration Pattern
class EnhancedModel extends Model
{
    // Modern Casting with PHP 8.3 Features
    protected function casts(): array
    {
        return [
            'status' => ModelStatus::class,           // Enum casting
            'created_at' => 'datetime:Y-m-d H:i:s',  // Formatted dates
            'metadata' => AsArrayObject::class,       // Advanced casting
            'settings' => AsEncryptedArrayObject::class, // Encrypted data
            'is_active' => 'boolean',                 // Boolean casting
            'priority' => 'integer',                  // Type safety
        ];
    }
    
    // Comprehensive Scopes (25+ per model)
    public function scopeActive(Builder $query): Builder
    public function scopeByStatus(Builder $query, ModelStatus $status): Builder
    public function scopeSearch(Builder $query, string $term): Builder
    public function scopeWithinDateRange(Builder $query, Carbon $start, Carbon $end): Builder
    // ... 21+ additional scopes per model
}
```

---

## 🎯 **API ARCHITECTURE**

### **RESTful API Configuration**
```yaml
API Version: v1
Base URL: /api/v1/
Authentication: Laravel Sanctum + Bearer Tokens
Authorization: Policy-based RBAC
Rate Limiting: 60 requests/minute per user
Response Format: JSON API Specification
Error Handling: RFC 7807 Problem Details
Documentation: OpenAPI 3.0 + Swagger UI
Versioning: Header-based versioning
```

### **Request/Response Architecture**
```php
// Standard Request Pattern (300+ Classes)
class CreateModelRequest extends FormRequest
{
    // Multilanguage validation rules
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'name.*' => ['required', 'string', 'max:255'], // Multilang
            'description' => ['nullable', 'string'],
            'description.*' => ['nullable', 'string'], // Multilang
        ];
    }
    
    // Localized validation messages
    public function messages(): array
    {
        return [
            'name.required' => __('validation.required', [
                'attribute' => __('models.name')
            ]),
        ];
    }
}

// Standard Resource Pattern (100+ Classes)
class ModelResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->getTranslation('name'),
            'description' => $this->getTranslation('description'),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            
            // Conditional fields based on permissions
            $this->mergeWhen($request->user()?->can('view-admin-data'), [
                'admin_notes' => $this->admin_notes,
            ]),
        ];
    }
}
```

### **API Security Configuration**
- **CORS**: Configured for specific origins
- **CSRF Protection**: SameSite cookies + CSRF tokens
- **Rate Limiting**: Multiple tiers (per user, per IP, per endpoint)
- **Input Validation**: 100% request validation coverage
- **Output Sanitization**: XSS prevention on all responses
- **SQL Injection**: Parameterized queries only
- **Authentication**: Multi-factor authentication support
- **Authorization**: Granular permission system

---

## 🎨 **FRONTEND ARCHITECTURE**

### **Vue 3 + TypeScript Configuration**
```typescript
// Modern Component Architecture
<script setup lang="ts">
import { ref, reactive, computed, onMounted } from 'vue';
import { useI18n } from 'vue-i18n';
import { storeToRefs } from 'pinia';
import type { Model, ModelFilters } from '@/types/models';

// TypeScript interfaces for type safety
interface Props {
  initialFilters?: ModelFilters;
  readonly?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
  initialFilters: () => ({}),
  readonly: false,
});

// Reactive state management
const filters = reactive<ModelFilters>({
  search: '',
  status: null,
  ...props.initialFilters,
});

// Computed properties
const filteredModels = computed(() => {
  return models.value.filter(model => {
    return model.name.includes(filters.search);
  });
});
</script>
```

### **State Management with Pinia**
```typescript
// Modern Store Pattern
export const useModelStore = defineStore('models', () => {
  // State
  const models = ref<Model[]>([]);
  const loading = ref(false);
  const error = ref<string | null>(null);
  
  // Actions
  const fetchModels = async (filters: ModelFilters = {}): Promise<void> => {
    loading.value = true;
    try {
      const response = await modelApi.getModels(filters);
      models.value = response.data;
    } catch (err) {
      error.value = err instanceof Error ? err.message : 'Unknown error';
    } finally {
      loading.value = false;
    }
  };
  
  return { models, loading, error, fetchModels };
});
```

### **Build Configuration**
```typescript
// Vite Configuration
export default defineConfig({
  plugins: [
    vue(),
    laravel(['resources/js/app.ts']),
  ],
  build: {
    target: 'es2022',
    sourcemap: true,
    rollupOptions: {
      output: {
        manualChunks: {
          vendor: ['vue', 'pinia', 'vue-router'],
          ui: ['@headlessui/vue', '@heroicons/vue'],
        },
      },
    },
  },
  optimizeDeps: {
    include: ['vue', 'pinia', 'vue-router', 'axios'],
  },
});
```

---

## 🔒 **SECURITY CONFIGURATION**

### **Laravel Security Features**
```php
// Security Middleware Stack
protected $middleware = [
    \App\Http\Middleware\TrustProxies::class,
    \App\Http\Middleware\PreventRequestsDuringMaintenance::class,
    \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,
    \App\Http\Middleware\TrimStrings::class,
    \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
    \App\Http\Middleware\SecurityHeaders::class, // Custom security headers
];

// Route-specific middleware
protected $routeMiddleware = [
    'auth' => \App\Http\Middleware\Authenticate::class,
    'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
    'cache.headers' => \Illuminate\Http\Middleware\SetCacheHeaders::class,
    'can' => \Illuminate\Auth\Middleware\Authorize::class,
    'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
    'password.confirm' => \Illuminate\Auth\Middleware\RequirePassword::class,
    'signed' => \Illuminate\Routing\Middleware\ValidateSignature::class,
    'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
    'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
    'role' => \App\Http\Middleware\RoleMiddleware::class, // Custom RBAC
    'permission' => \App\Http\Middleware\PermissionMiddleware::class, // Custom permissions
];
```

### **Security Headers Configuration**
```php
// Custom Security Headers Middleware
class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);
        
        return $response->withHeaders([
            'X-Content-Type-Options' => 'nosniff',
            'X-Frame-Options' => 'DENY',
            'X-XSS-Protection' => '1; mode=block',
            'Referrer-Policy' => 'strict-origin-when-cross-origin',
            'Permissions-Policy' => 'geolocation=(), microphone=(), camera=()',
            'Content-Security-Policy' => "default-src 'self'; script-src 'self' 'unsafe-inline'",
            'Strict-Transport-Security' => 'max-age=31536000; includeSubDomains',
        ]);
    }
}
```

---

## 📱 **RESPONSIVE & ACCESSIBILITY**

### **TailwindCSS Configuration**
```javascript
// Tailwind Configuration
module.exports = {
  content: [
    './resources/**/*.blade.php',
    './resources/**/*.js',
    './resources/**/*.vue',
    './resources/**/*.ts',
  ],
  theme: {
    extend: {
      colors: {
        primary: {
          50: '#eff6ff',
          500: '#3b82f6',
          900: '#1e3a8a',
        },
      },
      fontFamily: {
        sans: ['Inter var', 'sans-serif'],
      },
      screens: {
        'xs': '475px',
      },
    },
  },
  plugins: [
    require('@tailwindcss/forms'),
    require('@tailwindcss/typography'),
    require('@tailwindcss/aspect-ratio'),
  ],
};
```

### **Accessibility Standards**
- **WCAG 2.1 AA Compliance**: Full accessibility compliance
- **Semantic HTML**: Proper HTML5 semantic elements
- **ARIA Labels**: Comprehensive ARIA labeling
- **Keyboard Navigation**: Full keyboard accessibility
- **Screen Reader Support**: Optimized for screen readers
- **Color Contrast**: Minimum 4.5:1 contrast ratio
- **Focus Management**: Proper focus management
- **Alternative Text**: All images have alt text

---

## 🧪 **TESTING INFRASTRUCTURE**

### **Backend Testing Stack**
```php
// PHPUnit Configuration
<?xml version="1.0" encoding="UTF-8"?>
<phpunit xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
         xsi:noNamespaceSchemaLocation="vendor/phpunit/phpunit/phpunit.xsd"
         bootstrap="vendor/autoload.php"
         colors="true">
    <testsuites>
        <testsuite name="Unit">
            <directory suffix="Test.php">./tests/Unit</directory>
        </testsuite>
        <testsuite name="Feature">
            <directory suffix="Test.php">./tests/Feature</directory>
        </testsuite>
    </testsuites>
    <coverage processUncoveredFiles="true">
        <include>
            <directory suffix=".php">./app</directory>
        </include>
        <report>
            <html outputDirectory="build/coverage"/>
            <text outputFile="build/coverage.txt"/>
            <clover outputFile="build/logs/clover.xml"/>
        </report>
    </coverage>
    <php>
        <env name="APP_ENV" value="testing"/>
        <env name="DB_CONNECTION" value="sqlite"/>
        <env name="DB_DATABASE" value=":memory:"/>
    </php>
</phpunit>
```

### **Frontend Testing Stack**
```typescript
// Vitest Configuration
export default defineConfig({
  test: {
    globals: true,
    environment: 'jsdom',
    setupFiles: ['./tests/setup.ts'],
    coverage: {
      provider: 'v8',
      reporter: ['text', 'json', 'html'],
      exclude: [
        'node_modules/',
        'tests/',
        '**/*.d.ts',
      ],
    },
  },
});

// Vue Test Utils Setup
import { config } from '@vue/test-utils';
import { createI18n } from 'vue-i18n';
import { createPinia } from 'pinia';

config.global.plugins = [
  createI18n({
    locale: 'en',
    messages: {},
  }),
  createPinia(),
];
```

---

## 🔧 **DEVELOPMENT ENVIRONMENT**

### **Local Development Setup**
```bash
# Development Commands
php artisan serve --host=0.0.0.0 --port=8000
npm run dev          # Vite development server
npm run build        # Production build
npm run preview      # Preview production build

# Database Commands
php artisan migrate:fresh --seed  # Fresh database with seeds
php artisan db:seed               # Run seeders only
php artisan tinker               # Interactive PHP shell

# Testing Commands
php artisan test                 # Run Laravel tests
npm run test                     # Run Vue tests
npm run test:coverage           # Test coverage report

# Code Quality Commands
./vendor/bin/pint               # PHP CS Fixer
./vendor/bin/larastan analyse   # Static analysis
npm run lint                    # ESLint
npm run type-check             # TypeScript checking
```

### **Environment Configuration**
```bash
# .env (Development)
APP_NAME="Laravel Job Portal"
APP_ENV=local
APP_KEY=base64:generated_key
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=sqlite
DB_DATABASE=database/database.sqlite

CACHE_DRIVER=file
QUEUE_CONNECTION=sync
SESSION_DRIVER=file

VITE_APP_NAME="${APP_NAME}"
VITE_API_URL="${APP_URL}/api"

# Security
SANCTUM_STATEFUL_DOMAINS=localhost:8000,127.0.0.1:8000
SESSION_DOMAIN=localhost
```

---

## 📊 **PERFORMANCE MONITORING**

### **Laravel Telescope Configuration**
```php
// Telescope Configuration
'watchers' => [
    Watchers\CacheWatcher::class => env('TELESCOPE_CACHE_WATCHER', true),
    Watchers\CommandWatcher::class => env('TELESCOPE_COMMAND_WATCHER', true),
    Watchers\DumpWatcher::class => env('TELESCOPE_DUMP_WATCHER', true),
    Watchers\EventWatcher::class => env('TELESCOPE_EVENT_WATCHER', true),
    Watchers\ExceptionWatcher::class => env('TELESCOPE_EXCEPTION_WATCHER', true),
    Watchers\JobWatcher::class => env('TELESCOPE_JOB_WATCHER', true),
    Watchers\LogWatcher::class => env('TELESCOPE_LOG_WATCHER', true),
    Watchers\MailWatcher::class => env('TELESCOPE_MAIL_WATCHER', true),
    Watchers\ModelWatcher::class => env('TELESCOPE_MODEL_WATCHER', true),
    Watchers\NotificationWatcher::class => env('TELESCOPE_NOTIFICATION_WATCHER', true),
    Watchers\QueryWatcher::class => env('TELESCOPE_QUERY_WATCHER', true),
    Watchers\RedisWatcher::class => env('TELESCOPE_REDIS_WATCHER', true),
    Watchers\RequestWatcher::class => env('TELESCOPE_REQUEST_WATCHER', true),
    Watchers\ScheduleWatcher::class => env('TELESCOPE_SCHEDULE_WATCHER', true),
],
```

### **Performance Metrics**
- **Response Time**: < 200ms for API endpoints
- **Database Queries**: < 10 queries per request
- **Memory Usage**: < 512MB per request
- **Cache Hit Rate**: > 80%
- **Core Web Vitals**: LCP < 2.5s, FID < 100ms, CLS < 0.1
- **Bundle Size**: < 300KB gzipped for initial load

---

## 🌍 **INTERNATIONALIZATION**

### **Multi-language Support**
```php
// Supported Languages
'supported_locales' => [
    'en' => ['name' => 'English', 'script' => 'Latn', 'native' => 'English'],
    'fr' => ['name' => 'French', 'script' => 'Latn', 'native' => 'Français'],
    'es' => ['name' => 'Spanish', 'script' => 'Latn', 'native' => 'Español'],
    'de' => ['name' => 'German', 'script' => 'Latn', 'native' => 'Deutsch'],
    'ar' => ['name' => 'Arabic', 'script' => 'Arab', 'native' => 'العربية'],
    'zh' => ['name' => 'Chinese', 'script' => 'Hans', 'native' => '中文'],
    'ru' => ['name' => 'Russian', 'script' => 'Cyrl', 'native' => 'Русский'],
    'pt' => ['name' => 'Portuguese', 'script' => 'Latn', 'native' => 'Português'],
    'tr' => ['name' => 'Turkish', 'script' => 'Latn', 'native' => 'Türkçe'],
];

// Spatie Translatable Configuration
'translatable' => [
    'locales' => ['en', 'fr', 'es', 'de', 'ar', 'zh', 'ru', 'pt', 'tr'],
    'fallback_locale' => 'en',
    'json_column' => 'translations',
];
```

---

## 🚀 **DEPLOYMENT ARCHITECTURE**

### **Production Environment**
```yaml
Infrastructure:
  - Server: Ubuntu 22.04 LTS
  - Web Server: Nginx 1.22+ with HTTP/2
  - Application: PHP-FPM 8.3 with OPcache
  - Database: PostgreSQL 15+ with read replicas
  - Cache: Redis 7.0+ cluster
  - CDN: CloudFlare with global edge locations
  - SSL: Let's Encrypt with auto-renewal
  - Monitoring: New Relic + Sentry + DataDog

Scaling Strategy:
  - Load Balancer: HAProxy/Nginx upstream
  - Application Servers: Multiple PHP-FPM instances
  - Database: Master-slave replication
  - Session Storage: Redis cluster
  - File Storage: S3-compatible object storage
  - Queue Workers: Dedicated worker processes
```

### **CI/CD Pipeline**
```yaml
# GitHub Actions Workflow
name: Laravel Job Portal CI/CD
on: [push, pull_request]

jobs:
  test:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v4
      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: '8.3'
      - name: Install dependencies
        run: composer install --no-dev --optimize-autoloader
      - name: Run tests
        run: php artisan test --coverage
      - name: Upload coverage
        uses: codecov/codecov-action@v3

  deploy:
    needs: test
    runs-on: ubuntu-latest
    if: github.ref == 'refs/heads/main'
    steps:
      - name: Deploy to production
        run: |
          php artisan migrate --force
          php artisan config:cache
          php artisan route:cache
          php artisan view:cache
          npm run build
```

---

## 📋 **DEVELOPMENT STANDARDS**

### **Code Quality Standards**
- **PHP Standards**: PSR-12, PHPDoc comments
- **TypeScript Standards**: Strict mode, ESLint rules
- **Vue Standards**: Vue 3 style guide, composition API
- **Commit Standards**: Conventional commits
- **Branch Strategy**: GitFlow with feature branches
- **Code Review**: Required for all changes
- **Testing Coverage**: Minimum 95% coverage
- **Documentation**: Complete PHPDoc and TSDoc

### **Performance Standards**
- **API Response Time**: < 200ms average
- **Database Query Limit**: < 10 queries per request
- **Bundle Size**: < 300KB initial load
- **Lighthouse Score**: > 90 for all metrics
- **Memory Usage**: < 512MB per request
- **Cache Hit Rate**: > 80%

**STATUS: COMPREHENSIVE LEVEL 4 ENTERPRISE TECHNOLOGY STACK CONFIGURED** ⚡

This technology stack ensures:
- **Enterprise-Grade**: Production-ready technology choices
- **Modern Standards**: Latest stable versions of all technologies
- **Performance**: Optimized for speed and scalability
- **Security**: Enterprise security standards and best practices
- **Maintainability**: Clean, documented, and testable code
- **Accessibility**: WCAG 2.1 AA compliance
- **Internationalization**: Multi-language support
- **Monitoring**: Comprehensive observability and performance tracking 

# Tech Context - Laravel Job Portal Transformation

## Technology Stack
The following technologies and frameworks are utilized in the comprehensive transformation of the Laravel job portal system:

### Backend Technologies
- **Laravel 12**: The core PHP framework for building the job portal, providing robust features for routing, middleware, ORM, and more.
- **PHP 8.2+**: The programming language powering the backend, ensuring modern features and performance improvements.
- **MySQL/MariaDB**: The relational database management system for storing job listings, user data, applications, and other critical information.
- **Redis**: Used for caching and session management to enhance application performance and scalability.
- **Spatie Laravel Permission**: For implementing role-based access control and managing user permissions.
- **Laravel Activity Log**: For logging user activities and changes within the system for audit purposes.

### Frontend Technologies
- **Vue 3**: The JavaScript framework for building dynamic user interfaces, both for frontend user-facing components and backend admin interfaces.
- **TypeScript**: Adding static typing to JavaScript for improved code maintainability and error prevention in Vue components.
- **TailwindCSS**: A utility-first CSS framework for creating responsive, modern designs without relying on Bootstrap.
- **Vite**: The build tool for fast development and optimized production builds of frontend assets.
- **Pinia**: State management library for Vue, providing a modern alternative to Vuex for managing application state.

### API and Integration
- **Laravel API Resources**: For creating consistent and optimized JSON responses for API endpoints.
- **Rate Limiting**: Implemented with Laravel's built-in middleware and Enhanced patterns to prevent API abuse.
- **RESTful API Design**: Following best practices for creating scalable and maintainable API endpoints.

### Testing and Quality Assurance
- **Pest**: A modern PHP testing framework for unit, feature, and integration tests, ensuring high code coverage.
- **Laravel Dusk**: For browser automation testing (used only on Windows 11 OS as per user rules).
- **Selenium**: For end-to-end testing of user journeys (where applicable).
- **PHPUnit**: As a fallback testing framework for compatibility with existing tests.

### Performance Optimization
- **Query Optimization**: Using Laravel's Eloquent scopes, eager loading, and database indexing for efficient data retrieval.
- **Caching Strategies**: Implementing Redis-based caching with intelligent invalidation for frequently accessed data.
- **Asset Optimization**: Leveraging Vite for optimized frontend asset bundling and minification.

### Security
- **Authentication Middleware**: Enhanced with account lockout and suspicious activity detection.
- **Input Validation**: Comprehensive request validation with multilingual error messages using Laravel's validation system.
- **Content Security Policy (CSP)**: Configured to prevent cross-site scripting (XSS) attacks.
- **OWASP Top 10 Compliance**: Ensuring the application addresses common security vulnerabilities.

### Multilingual System
- **JSON-Based Translations**: Supporting 9 languages (English, Arabic, German, Spanish, French, Portuguese, Russian, Turkish, Chinese) with RTL support for Arabic.
- **Dynamic Language Switching**: Implemented with browser detection and localStorage persistence for seamless user experience.

### Development Tools
- **Composer**: For managing PHP dependencies and autoloading.
- **NPM**: For managing frontend dependencies and running build scripts.
- **Git**: For version control, with a focus on clean main branch workflow and systematic commits.
- **VS Code**: Recommended IDE for development, with extensions for PHP, Laravel, Vue, and TailwindCSS support.

### Deployment and Environment
- **Linux**: Primary development and deployment environment (as per user OS: Linux 5.14.0-503.40.1.el9_5.x86_64).
- **No Docker**: As per user rules, Docker is not used in this project.
- **Local Asset Hosting**: All CSS and JS files are hosted locally via NPM packages, avoiding CDN usage as per user guidelines.

This technology stack is aligned with Enhanced best practices and user-specific rules to ensure a modern, secure, and efficient job portal system. 

# Technical Context: Laravel Job Portal Modernization

## Technology Stack
- **Backend Framework**: Laravel 12
  - Used for core application logic, routing, middleware, and API development.
- **Frontend Framework**: Vue 3 with TypeScript
  - For building dynamic, reusable UI components with strong typing.
- **Styling Framework**: TailwindCSS
  - Utility-first CSS framework for rapid, consistent design, managed locally via npm (no CDNs).
- **Database**: MySQL
  - Primary relational database for storing application data.
- **Caching**: Redis
  - For caching frequently accessed data to improve performance and support 1000+ concurrent users.
- **Testing Frameworks**: PHPUnit, Pest
  - For unit, feature, and integration testing aiming for 95%+ coverage.
- **Package Management**: Composer (PHP), npm (JavaScript/CSS)
  - For dependency management and build processes.
- **Version Control**: Git
  - For source code management with a clean main branch workflow.

## Development Practices
- **Enhanced Patterns**: All code follows Enhanced design principles for consistency and scalability.
- **Multilingual System**: JSON-based translations supporting 9 languages with RTL for Arabic, integrated with Enhanced I18n.
- **Build Process**: Run `npm run build` after CSS/JS edits to compile assets.
- **Environment**: Development and production environments with specific configurations (e.g., HTTPS forced in production).

## Constraints
- **No Bootstrap**: Exclusively use TailwindCSS for styling.
- **No CDNs**: All JS/CSS managed locally via npm.
- **No Docker or Livewire**: Focus on native Laravel and Vue implementations.
- **No User System Development**: Do not create or modify user-related features.

## Performance Goals
- **Concurrent Users**: System optimized for 1000+ concurrent users.
- **Response Time**: Target sub-200ms response times for API endpoints through caching and query optimization. 