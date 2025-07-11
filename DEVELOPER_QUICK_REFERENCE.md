# Developer Quick Reference Guide

## Quick Commands

### Backend (Laravel)

```bash
# Install dependencies
composer install

# Generate application key
php artisan key:generate

# Run migrations
php artisan migrate

# Seed database
php artisan db:seed

# Clear cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Start development server
php artisan serve

# Run tests
php artisan test

# Generate model with migration
php artisan make:model Job -m

# Generate controller
php artisan make:controller Api/V1/JobController

# Generate service
php artisan make:service JobService

# List routes
php artisan route:list

# Tinker (interactive shell)
php artisan tinker
```

### Frontend (Vue.js)

```bash
# Install dependencies
npm install

# Start development server
npm run dev

# Build for production
npm run build

# Run tests
npm run test
npm run test:e2e

# Lint code
npm run lint

# Format code
npm run format
```

## Common Patterns

### API Response Format

```php
// Success response
return response()->json([
    'success' => true,
    'message' => 'Operation successful',
    'data' => $data
]);

// Error response
return response()->json([
    'success' => false,
    'message' => 'Error description',
    'errors' => $errors
], 422);
```

### Service Method Pattern

```php
public function someMethod(array $data): mixed
{
    try {
        // Validate input
        $validated = $this->validate($data, $rules);
        
        // Process data
        $result = $this->processData($validated);
        
        // Log success
        $this->log('Operation successful', ['data' => $result]);
        
        return $result;
    } catch (\Exception $e) {
        $this->log('Operation failed', ['error' => $e->getMessage()]);
        throw $e;
    }
}
```

### Vue Component Pattern

```vue
<template>
  <div class="component">
    <slot name="header" />
    <div class="content">
      <slot />
    </div>
    <slot name="footer" />
  </div>
</template>

<script setup lang="ts">
interface Props {
  title?: string
}

interface Emits {
  (e: 'update', value: string): void
}

const props = withDefaults(defineProps<Props>(), {
  title: 'Default Title'
})

const emit = defineEmits<Emits>()
</script>
```

## Database Operations

### Eloquent Queries

```php
// Basic queries
$jobs = Job::where('is_active', true)->get();
$job = Job::findOrFail($id);
$jobs = Job::with(['company', 'category'])->paginate(20);

// Advanced queries
$jobs = Job::whereHas('skills', function ($query) {
    $query->whereIn('name', ['PHP', 'Laravel']);
})->get();

// Scopes
$activeJobs = Job::active()->featured()->get();

// Aggregates
$count = Job::count();
$avgSalary = Job::avg('salary_from');
```

### Migrations

```php
// Create migration
php artisan make:migration create_jobs_table

// Migration structure
public function up(): void
{
    Schema::create('jobs', function (Blueprint $table) {
        $table->id();
        $table->string('title');
        $table->text('description');
        $table->foreignId('company_id')->constrained();
        $table->boolean('is_active')->default(true);
        $table->timestamps();
    });
}
```

## Caching

### Redis Cache

```php
// Set cache
Cache::put('key', $value, 3600);

// Get cache
$value = Cache::get('key', $default);

// Remember pattern
$data = Cache::remember('key', 3600, function () {
    return ExpensiveOperation::execute();
});

// Tags
Cache::tags(['jobs', 'featured'])->put('key', $value);
Cache::tags(['jobs'])->flush();
```

## Authentication

### Sanctum Token

```php
// Create token
$token = $user->createToken('auth_token')->plainTextToken;

// Validate token
$user = $request->user();

// Revoke token
$request->user()->currentAccessToken()->delete();
```

## File Upload

### Laravel File Upload

```php
// Upload file
$path = $request->file('document')->store('uploads');

// Validate file
$request->validate([
    'document' => 'required|file|mimes:pdf,doc,docx|max:2048'
]);

// Get file URL
$url = Storage::url($path);
```

## Testing

### PHPUnit Tests

```php
// Feature test
public function test_can_create_job(): void
{
    $response = $this->postJson('/api/v1/jobs', [
        'title' => 'Test Job',
        'description' => 'Test Description'
    ]);

    $response->assertStatus(201)
             ->assertJson(['success' => true]);
}

// Unit test
public function test_job_service_creates_job(): void
{
    $service = new JobService();
    $job = $service->createJob(['title' => 'Test']);
    
    $this->assertInstanceOf(Job::class, $job);
}
```

### Vue Component Tests

```typescript
// Component test
import { mount } from '@vue/test-utils'
import HelloWorld from '../HelloWorld.vue'

describe('HelloWorld', () => {
  it('renders message prop', () => {
    const wrapper = mount(HelloWorld, {
      props: { msg: 'Test Message' }
    })
    
    expect(wrapper.text()).toContain('Test Message')
  })
})
```

## Error Handling

### Laravel Exception Handling

```php
// Custom exception
class JobNotFoundException extends Exception
{
    public function render($request)
    {
        return response()->json([
            'success' => false,
            'message' => 'Job not found'
        ], 404);
    }
}

// Try-catch pattern
try {
    $job = Job::findOrFail($id);
} catch (ModelNotFoundException $e) {
    return response()->json(['error' => 'Job not found'], 404);
}
```

### Vue Error Handling

```vue
<script setup lang="ts">
import { ref } from 'vue'

const error = ref(null)

const fetchData = async () => {
  try {
    const response = await fetch('/api/data')
    const data = await response.json()
    return data
  } catch (err) {
    error.value = err
  }
}
</script>
```

## Performance Optimization

### Database Optimization

```php
// Eager loading
$jobs = Job::with(['company', 'category', 'skills'])->get();

// Query optimization
$jobs = Job::select(['id', 'title', 'company_id'])
    ->with(['company:id,name'])
    ->get();

// Index hints
$jobs = Job::from('jobs USE INDEX (idx_active)')
    ->where('is_active', true)
    ->get();
```

### Vue Performance

```vue
<script setup lang="ts">
import { computed, ref } from 'vue'

// Computed properties for expensive operations
const expensiveValue = computed(() => {
  return heavyCalculation(props.data)
})

// Lazy loading
const AsyncComponent = defineAsyncComponent(() => 
  import('./HeavyComponent.vue')
)
</script>
```

## Debugging

### Laravel Debugging

```php
// Debug bar
dd($variable);
dump($variable);

// Logging
Log::info('Debug message', ['data' => $data]);
Log::error('Error message', ['error' => $e->getMessage()]);

// Query logging
DB::enableQueryLog();
// ... your queries
dd(DB::getQueryLog());
```

### Vue Debugging

```javascript
// Console logging
console.log('Debug:', data)
console.error('Error:', error)

// Vue devtools
// Install Vue devtools browser extension
```

## Common Issues & Solutions

### Backend Issues

**Issue**: Database connection failed
```bash
# Solution
php artisan config:clear
php artisan cache:clear
# Check .env file for correct database credentials
```

**Issue**: Migration failed
```bash
# Solution
php artisan migrate:rollback
php artisan migrate
```

**Issue**: Cache issues
```bash
# Solution
php artisan cache:clear
php artisan config:clear
php artisan route:clear
```

### Frontend Issues

**Issue**: Build failed
```bash
# Solution
rm -rf node_modules package-lock.json
npm install
npm run build
```

**Issue**: Hot reload not working
```bash
# Solution
npm run dev -- --host
# Check if port is available
```

**Issue**: TypeScript errors
```bash
# Solution
npm run type-check
# Fix type errors in code
```

## Environment Setup

### Required Extensions (PHP)

```ini
extension=pdo_mysql
extension=redis
extension=gd
extension=mbstring
extension=openssl
extension=fileinfo
```

### Node.js Version

```bash
# Check version
node --version  # Should be 18+

# Use nvm to manage versions
nvm use 18
```

### Database Setup

```sql
-- Create database
CREATE DATABASE job_portal CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Create user
CREATE USER 'job_portal'@'localhost' IDENTIFIED BY 'password';
GRANT ALL PRIVILEGES ON job_portal.* TO 'job_portal'@'localhost';
FLUSH PRIVILEGES;
```

## Git Workflow

```bash
# Create feature branch
git checkout -b feature/new-feature

# Make changes and commit
git add .
git commit -m "feat: add new feature"

# Push to remote
git push origin feature/new-feature

# Create pull request
# Merge after review
```

## Deployment Checklist

- [ ] Environment variables configured
- [ ] Database migrations run
- [ ] Cache cleared and optimized
- [ ] File permissions set correctly
- [ ] SSL certificate installed
- [ ] CDN configured
- [ ] Monitoring tools set up
- [ ] Backup strategy implemented

## Useful Links

- [Laravel Documentation](https://laravel.com/docs)
- [Vue.js Documentation](https://vuejs.org/guide/)
- [Tailwind CSS Documentation](https://tailwindcss.com/docs)
- [PHPUnit Documentation](https://phpunit.de/documentation.html)
- [Vitest Documentation](https://vitest.dev/guide/)

---

**Last Updated**: January 2024  
**Maintainers**: Development Team