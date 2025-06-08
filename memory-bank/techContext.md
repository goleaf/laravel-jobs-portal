# TECH CONTEXT - JOB PORTAL

## 🗄️ **DATABASE CONFIGURATION - CRITICAL**

**⚠️ ALWAYS USE SQLITE - NO MYSQL EVER ⚠️**

- **Database Type**: SQLite (file-based)
- **Database File**: `database/database.sqlite`
- **Connection**: `sqlite` driver in Laravel
- **NO MySQL**: Never use MySQL commands, connections, or references
- **Memory Efficiency**: SQLite prevents memory exhaustion issues
- **Development Focus**: Lightweight, portable, no server dependencies

## 🏗️ **LARAVEL ARCHITECTURE**

### **Framework**
- **Laravel Version**: 12.x
- **PHP Version**: 8.3.15
- **Environment**: Local development

### **Frontend Stack**
- **Vue.js**: 3.x with Composition API
- **TypeScript**: Full type safety
- **TailwindCSS**: Utility-first styling
- **Vite**: Build tool and dev server
- **Heroicons**: Icon library

### **Authentication**
- **Laravel Sanctum**: API token authentication
- **Multi-user types**: Admin, Employer, Candidate
- **Role-based access control**

## 🔧 **DEVELOPMENT COMMANDS**

### **Database Operations (SQLite Only)**
```bash
# Check database file exists
ls -la database/database.sqlite

# Run migrations
php artisan migrate

# Seed database
php artisan db:seed

# Check database with SQLite CLI
sqlite3 database/database.sqlite "SELECT * FROM users LIMIT 5;"

# Database status
php artisan migrate:status
```

### **Build Commands**
```bash
# Install dependencies
npm install

# Development server
npm run dev

# Production build
npm run build

# Laravel serve
php artisan serve
```

## 📊 **CURRENT PROJECT STATUS**

### **Completed Features**
- ✅ Vue 3 + TypeScript setup
- ✅ TailwindCSS integration
- ✅ Authentication system
- ✅ LoginInfoBlock component
- ✅ API endpoints for login info
- ✅ Database migrations (SQLite)

### **Active Development**
- 🔄 Admin login info block integration
- 🔄 Database connectivity testing
- 🔄 User seeding verification

## 🚨 **CRITICAL REMINDERS**

1. **ALWAYS SQLite**: Never use MySQL commands or connections
2. **Memory Management**: SQLite prevents PHP memory exhaustion
3. **File-based DB**: Database is in `database/database.sqlite`
4. **Vue 3 Components**: Use Composition API with TypeScript
5. **TailwindCSS**: Use utility classes for styling

## 🎯 **CURRENT TASK**

**Building Admin Login Info Block**:
- LoginInfoBlock Vue component ✅ Created
- Integration with Login.vue ✅ Complete
- Database connectivity testing ⚠️ In Progress (SQLite)
- API endpoint testing ⚠️ Needs SQLite verification
- User seeding verification ⚠️ Pending SQLite check

## Technology Stack

### Backend Framework
- **Laravel**: 12.17.0 (Latest Stable)
- **PHP**: 8.3+ (Required for Laravel 12)
- **Composer**: 2.7+ for dependency management

### Database
- **Primary**: MySQL 8.0+ / MariaDB 10.6+
- **Caching**: Redis 7.0+ for session storage and caching
- **Search**: Full-text search with MySQL or Elasticsearch (optional)
- **Queue**: Redis/Database for background job processing

### Frontend Technologies
- **CSS Framework**: TailwindCSS 3.4+ (migrated from Bootstrap)
- **Build Tool**: Vite 5.0+ (Laravel default)
- **JavaScript**: Vanilla JS / Alpine.js for interactive components
- **Dark Mode**: Native TailwindCSS dark mode support

### Development Tools
- **Testing**: Pest Framework with PHPUnit
- **Code Quality**: Laravel Pint (PSR-12 standard)
- **Asset Compilation**: NPM with Vite
- **Package Management**: Composer + NPM

## Server Configuration

### Production Environment
- **Web Server**: Nginx 1.24+ / Apache 2.4+
- **PHP-FPM**: 8.3+ with optimized settings
- **Memory Limit**: 256MB minimum (512MB recommended)
- **Redis**: 7.0+ for caching and sessions
- **SSL**: Let's Encrypt / CloudFlare SSL

### Development Environment
- **Local Server**: `php artisan serve` or Laravel Sail
- **Database**: MySQL via Docker or local installation
- **Node.js**: 20.x LTS for frontend builds
- **NPM/Yarn**: Latest stable for package management

## Key Dependencies

### Backend Packages
```json
{
    "laravel/framework": "^12.0",
    "spatie/laravel-permission": "^6.0",
    "laravel/sanctum": "^4.0",
    "intervention/image": "^3.0",
    "maatwebsite/excel": "^3.1",
    "spatie/laravel-medialibrary": "^11.0"
}
```

### Frontend Packages
```json
{
    "tailwindcss": "^3.4.0",
    "vite": "^5.0.0",
    "alpinejs": "^3.13.0",
    "@tailwindcss/forms": "^0.5.7",
    "@tailwindcss/typography": "^0.5.10"
}
```

## Security Configuration

### Authentication
- **Default**: Laravel Sanctum for API authentication
- **Session**: Redis-backed sessions with secure settings
- **Password Hashing**: Bcrypt with cost factor 12+
- **2FA**: TOTP-based two-factor authentication

### Authorization
```php
// Role-based permissions with Spatie
'admin' => [
    'users.view', 'users.create', 'users.edit', 'users.delete',
    'jobs.view', 'jobs.moderate', 'reports.view'
],
'employer' => [
    'jobs.create', 'jobs.edit', 'applications.view'
],
'candidate' => [
    'jobs.view', 'applications.create', 'profile.edit'
]
```

### Rate Limiting
```php
// API rate limits
RateLimiter::for('api', function (Request $request) {
    return $request->user()
        ? Limit::perMinute(120)->by($request->user()->id)
        : Limit::perMinute(60)->by($request->ip());
});

// Authentication attempts
RateLimiter::for('login', function (Request $request) {
    return [
        Limit::perMinute(10)->response(function () {
            return response('Too many login attempts.', 429);
        }),
        Limit::perMinute(3)->by($request->input('email')),
    ];
});
```

## Database Schema Highlights

### Core Tables
```sql
-- Jobs table with optimized indexing
CREATE TABLE jobs (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    company_id BIGINT UNSIGNED,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    salary_min DECIMAL(10,2),
    salary_max DECIMAL(10,2),
    location VARCHAR(255),
    job_type_id BIGINT UNSIGNED,
    status ENUM('draft', 'published', 'closed', 'expired'),
    expires_at TIMESTAMP,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    
    INDEX idx_jobs_search (status, location, job_type_id),
    INDEX idx_jobs_company (company_id, created_at),
    INDEX idx_jobs_salary (salary_min, salary_max),
    FULLTEXT(title, description)
);

-- Applications with relationship tracking
CREATE TABLE job_applications (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    job_id BIGINT UNSIGNED,
    user_id BIGINT UNSIGNED,
    status ENUM('pending', 'reviewed', 'interview', 'hired', 'rejected'),
    cover_letter TEXT,
    resume_path VARCHAR(500),
    applied_at TIMESTAMP,
    
    UNIQUE KEY unique_application (job_id, user_id),
    INDEX idx_applications_job (job_id, status),
    INDEX idx_applications_user (user_id, applied_at)
);
```

### Performance Indexes
```sql
-- Search optimization
CREATE INDEX idx_jobs_location ON jobs (location);
CREATE INDEX idx_jobs_featured ON jobs (is_featured, created_at);
CREATE INDEX idx_companies_active ON companies (is_active, created_at);

-- User activity tracking
CREATE INDEX idx_users_last_login ON users (last_login_at);
CREATE INDEX idx_applications_recent ON job_applications (created_at);
```

## Caching Strategy

### Redis Configuration
```php
// config/cache.php
'redis' => [
    'client' => 'phpredis',
    'options' => [
        'cluster' => env('REDIS_CLUSTER', 'redis'),
        'prefix' => env('REDIS_PREFIX', Str::slug(env('APP_NAME', 'laravel'), '_').'_cache:'),
    ],
    'default' => [
        'url' => env('REDIS_URL'),
        'host' => env('REDIS_HOST', '127.0.0.1'),
        'password' => env('REDIS_PASSWORD'),
        'port' => env('REDIS_PORT', '6379'),
        'database' => env('REDIS_CACHE_DB', '1'),
    ],
]
```

### Cache Keys Strategy
```php
// Featured jobs cache (1 hour)
Cache::remember('featured_jobs', 3600, $callback);

// User permissions cache (until role changes)
Cache::remember("user_permissions_{$userId}", 86400, $callback);

// Job search results (15 minutes)
Cache::remember("job_search_" . md5(serialize($filters)), 900, $callback);
```

## File Storage

### Local Development
```php
// config/filesystems.php
'local' => [
    'driver' => 'local',
    'root' => storage_path('app'),
    'throw' => false,
],

'public' => [
    'driver' => 'local',
    'root' => storage_path('app/public'),
    'url' => env('APP_URL').'/storage',
    'visibility' => 'public',
    'throw' => false,
],
```

### Production (S3/CloudFlare R2)
```php
's3' => [
    'driver' => 's3',
    'key' => env('AWS_ACCESS_KEY_ID'),
    'secret' => env('AWS_SECRET_ACCESS_KEY'),
    'region' => env('AWS_DEFAULT_REGION'),
    'bucket' => env('AWS_BUCKET'),
    'url' => env('AWS_URL'),
    'endpoint' => env('AWS_ENDPOINT'),
    'use_path_style_endpoint' => env('AWS_USE_PATH_STYLE_ENDPOINT', false),
],
```

## Queue Configuration

### Redis Queue
```php
// config/queue.php
'redis' => [
    'driver' => 'redis',
    'connection' => 'default',
    'queue' => env('REDIS_QUEUE', 'default'),
    'retry_after' => 90,
    'block_for' => null,
],
```

### Background Jobs
```php
// Email notifications queue
ProcessJobApplication::dispatch($application)->onQueue('emails');

// Image processing queue  
ProcessCompanyLogo::dispatch($company)->onQueue('images');

// Report generation queue
GenerateMonthlyReport::dispatch($month)->onQueue('reports');
```

## Performance Monitoring

### Laravel Telescope (Development)
```php
// config/telescope.php
'watchers' => [
    'cache' => env('TELESCOPE_CACHE_WATCHER', true),
    'commands' => env('TELESCOPE_COMMAND_WATCHER', true),
    'dumps' => env('TELESCOPE_DUMP_WATCHER', true),
    'events' => env('TELESCOPE_EVENT_WATCHER', true),
    'exceptions' => env('TELESCOPE_EXCEPTION_WATCHER', true),
    'jobs' => env('TELESCOPE_JOB_WATCHER', true),
    'logs' => env('TELESCOPE_LOG_WATCHER', true),
    'queries' => env('TELESCOPE_QUERY_WATCHER', true),
],
```

### Production Monitoring
- **Application Performance**: Laravel Horizon for queue monitoring
- **Error Tracking**: Sentry or Bugsnag integration
- **Uptime Monitoring**: UptimeRobot or Pingdom
- **Log Management**: Centralized logging with ELK stack or CloudWatch

## Deployment Pipeline

### Build Process
```bash
# Frontend build
npm install
npm run build

# Backend optimization
composer install --optimize-autoloader --no-dev
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Environment Variables
```env
# Core Laravel
APP_NAME="Job Portal"
APP_ENV=production
APP_KEY=base64:your-key-here
APP_DEBUG=false
APP_URL=https://yourjobportal.com

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=jobportal
DB_USERNAME=username
DB_PASSWORD=password

# Redis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

# Mail
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="noreply@jobportal.com"
MAIL_FROM_NAME="${APP_NAME}"
``` 