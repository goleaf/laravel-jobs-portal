<?php

namespace App\Providers;

// Temporarily removed problematic command imports
// use App\Console\Commands\MigrateJsonTranslations;

// Temporarily disabled command imports
// use App\Console\Commands\ConsolidateTranslations;
// use App\Console\Commands\SynchronizeTranslations;
use App\Livewire\LanguageTable;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\MasterDataController;
use App\Http\Controllers\Admin\BrandingSliderController;
use App\Http\Controllers\Admin\HeaderSliderController;
use App\Http\Controllers\Admin\ImageSliderController;
use App\Http\Controllers\Admin\CmsController;
use App\Http\Controllers\Admin\EmailTemplateController;
use App\Http\Controllers\Admin\ReportedJobController;
use App\Http\Controllers\Admin\SalaryPeriodController;
use App\Http\Controllers\Admin\FunctionalAreaController;
use App\Http\Controllers\Admin\SalaryCurrencyController;
use App\Http\Controllers\Admin\OwnershipTypeController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Candidate\ApplicationController as CandidateApplicationController;
use App\Http\Controllers\Employer\ApplicationController as EmployerApplicationController;
use App\Http\Controllers\Front\BlogCommentController;
use Illuminate\Database\Eloquent\Model;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Temporarily disabled command registrations to fix memory issues
        /*
        if ($this->app->runningInConsole()) {
            $this->commands([
                MigrateJsonTranslations::class,
            ]);
        }
        */

        // Removed localization.js singleton for Laravel 12 compatibility

        //        $this->app->singleton(
        //        // the original class
        //            'vendor/brotzka/laravel-dotenv-editor/src/DotenvEditor.php',
        //            // my custom class
        //            'app/DotenvEditor.php'
        //        );

        // Temporarily disabled command registrations to fix memory issues
        // $this->commands([
        //     ConsolidateTranslations::class,
        //     SynchronizeTranslations::class,
        // ]);

        // Legacy Services
        $this->app->singleton(\App\Services\ProfileService::class);
        $this->app->singleton(\App\Services\BookmarkService::class);
        $this->app->singleton(\App\Services\SettingService::class);
        $this->app->singleton(\App\Services\NotificationService::class);
        $this->app->singleton(\App\Services\TwoFaService::class);
        $this->app->singleton(\App\Services\JobService::class);
        $this->app->singleton(\App\Services\EducationService::class);
        $this->app->singleton(\App\Services\ExperienceService::class);
        $this->app->singleton(\App\Services\SkillService::class);
        $this->app->singleton(\App\Services\CompanyService::class);
        $this->app->singleton(\App\Services\AuthService::class);
        $this->app->singleton(\App\Services\JobCategoryService::class);
        $this->app->singleton(\App\Services\SeoService::class);

        // Universal Repository Pattern - Context7 Implementation (Phase 2)
        // $this->app->singleton(\App\Repositories\JobRepository::class);
        // $this->app->singleton(\App\Repositories\CompanyRepository::class);
        // $this->app->singleton(\App\Repositories\CandidateRepository::class);
        // $this->app->singleton(\App\Repositories\UserRepository::class);

        // Universal Service Layer - Context7 Implementation (Phase 2)
        // $this->app->singleton(\App\Services\UniversalJobService::class);
        // $this->app->singleton(\App\Services\UniversalCompanyService::class);
        // $this->app->singleton(\App\Services\UniversalCandidateService::class);
        // $this->app->singleton(\App\Services\UniversalSearchService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Model::preventLazyLoading(!app()->isProduction());
        Model::preventSilentlyDiscardingAttributes(!app()->isProduction());

        // Force HTTPS in production
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }
        
        Schema::defaultStringLength(191);
        
        Paginator::useBootstrap();
        
        // Disable resource wrapping
        JsonResource::withoutWrapping();
        
        // Boot other services as needed
        $this->bootCustomServices();

        // Configure comprehensive rate limiting
        $this->configureRateLimiting();
        
        // Configure Universal database monitoring
        $this->configureDatabaseMonitoring();

        // Register class aliases
        $this->registerClassAliases();

        // Register translation commands when running in console
        // Temporarily disabled console command registrations
        // if ($this->app->runningInConsole()) {
        //     $this->commands([
        //         \App\Console\Commands\CheckTranslations::class,
        //         \App\Console\Commands\CleanupTranslations::class,
        //         \App\Console\Commands\CreateLithuanianTranslations::class,
        //     ]);
        // }

        // Register Blade directives for translations

        // Directive for translation: @t('app.welcome')
        Blade::directive('t', function ($expression) {
            return "<?php echo App\\Helpers\\TranslationHelper::get($expression); ?>";
        });

        // Directive to check if a translation exists: @hasTranslation('app.welcome')
        Blade::directive('hasTranslation', function ($expression) {
            return "<?php if(App\\Helpers\\TranslationHelper::has($expression)): ?>";
        });

        // Closing directive for @hasTranslation
        Blade::directive('endHasTranslation', function () {
            return '<?php endif; ?>';
        });

        // Register custom Blade directives
        Blade::directive('money', function ($expression) {
            return "<?php echo number_format($expression, 2); ?>";
        });

        // Register API routes for admin controllers
        Route::middleware('api')
            ->prefix('api/admin')
            ->namespace($this->app->getNamespace())
            ->group(function () {
                // Admin Dashboard API
                Route::get('/dashboard-stats', [AdminDashboardController::class, 'getStats']);
                Route::get('/dashboard-overview', [AdminDashboardController::class, 'getOverview']);
                
                // Admin Management API
                Route::apiResource('admins', AdminController::class);
                Route::patch('/admins/{admin}/toggle-status', [AdminController::class, 'toggleStatus']);
            });

        // Register API routes for candidate controllers
        Route::middleware('api')
            ->prefix('api/candidate')
            ->namespace($this->app->getNamespace())
            ->group(function () {
                // Candidate Applications API
                Route::apiResource('applications', CandidateApplicationController::class);
            });

        // Register API routes for employer controllers
        Route::middleware('api')
            ->prefix('api/employer')
            ->namespace($this->app->getNamespace())
            ->group(function () {
                // Employer Applications API
                Route::apiResource('applications', EmployerApplicationController::class);
            });

        // Register API routes for front-end controllers
        Route::middleware('api')
            ->prefix('api/front')
            ->namespace($this->app->getNamespace())
            ->group(function () {
                // Blog Comment API
                Route::apiResource('blog-comments', BlogCommentController::class);
            });

        // Register API routes for admin email templates
        Route::middleware('api')
            ->prefix('api/admin')
            ->namespace($this->app->getNamespace())
            ->group(function () {
                // Email Template API
                Route::apiResource('email-templates', EmailTemplateController::class);
            });

        // Register API routes for admin reported jobs
        Route::middleware('api')
            ->prefix('api/admin')
            ->namespace($this->app->getNamespace())
            ->group(function () {
                // Reported Jobs API
                Route::apiResource('reported-jobs', ReportedJobController::class);
            });

        // Register API routes for admin salary periods
        Route::middleware('api')
            ->prefix('api/admin')
            ->namespace($this->app->getNamespace())
            ->group(function () {
                // Salary Periods API
                Route::apiResource('salary-periods', SalaryPeriodController::class);
            });

        // Register API routes for admin functional areas
        Route::middleware('api')
            ->prefix('api/admin')
            ->namespace($this->app->getNamespace())
            ->group(function () {
                // Functional Areas API
                Route::apiResource('functional-areas', FunctionalAreaController::class);
            });

        // Register API routes for admin salary currencies
        Route::middleware('api')
            ->prefix('api/admin')
            ->namespace($this->app->getNamespace())
            ->group(function () {
                // Salary Currencies API
                Route::apiResource('salary-currencies', SalaryCurrencyController::class);
            });

        // Register API routes for admin ownership types
        Route::middleware('api')
            ->prefix('api/admin')
            ->namespace($this->app->getNamespace())
            ->group(function () {
                // Ownership Types API
                Route::apiResource('ownership-types', OwnershipTypeController::class);
            });

        // Register API routes for admin master data
        Route::middleware('api')
            ->prefix('api/admin')
            ->namespace($this->app->getNamespace())
            ->group(function () {
                // Master Data API
                Route::apiResource('master-data', MasterDataController::class);
            });

        // Register API routes for admin branding sliders
        Route::middleware('api')
            ->prefix('api/admin')
            ->namespace($this->app->getNamespace())
            ->group(function () {
                // Branding Sliders API
                Route::apiResource('branding-sliders', BrandingSliderController::class);
            });

        // Register API routes for admin header sliders
        Route::middleware('api')
            ->prefix('api/admin')
            ->namespace($this->app->getNamespace())
            ->group(function () {
                // Header Sliders API
                Route::apiResource('header-sliders', HeaderSliderController::class);
            });

        // Register API routes for admin image sliders
        Route::middleware('api')
            ->prefix('api/admin')
            ->namespace($this->app->getNamespace())
            ->group(function () {
                // Image Sliders API
                Route::apiResource('image-sliders', ImageSliderController::class);
            });

        // Register API routes for admin CMS
        Route::middleware('api')
            ->prefix('api/admin')
            ->namespace($this->app->getNamespace())
            ->group(function () {
                // CMS API
                Route::apiResource('cms', CmsController::class);
            });

        // Removed Livewire component registration as part of Vue3 migration
    }

    /**
     * Boot custom application services
     */
    protected function bootCustomServices(): void
    {
        // Custom service bootstrapping logic
    }

    /**
     * Configure comprehensive rate limiting for security and performance.
     */
    protected function configureRateLimiting(): void
    {
        // API Rate Limiting
        RateLimiter::for('api', function (Request $request) {
            return $request->user()
                ? Limit::perMinute(120)->by($request->user()->id)
                : Limit::perMinute(60)->by($request->ip());
        });

        // Login Attempts Rate Limiting
        RateLimiter::for('login', function (Request $request) {
            return [
                Limit::perMinute(10)->by('global-login'),
                Limit::perMinute(3)->by($request->string('email'))
            ];
        });

        // Registration Rate Limiting
        RateLimiter::for('registration', function (Request $request) {
            return [
                Limit::perMinute(5)->by($request->ip()),
                Limit::perDay(10)->by($request->ip())
            ];
        });

        // Password Reset Rate Limiting
        RateLimiter::for('password-reset', function (Request $request) {
            return Limit::perMinute(3)->by($request->string('email'));
        });

        // Job Search Rate Limiting
        RateLimiter::for('job-search', function (Request $request) {
            return $request->user()
                ? Limit::perMinute(100)->by($request->user()->id)
                : Limit::perMinute(30)->by($request->ip());
        });

        // Job Application Rate Limiting
        RateLimiter::for('job-applications', function (Request $request) {
            return $request->user()
                ? Limit::perHour(20)->by($request->user()->id)
                : Limit::perHour(5)->by($request->ip());
        });

        // File Upload Rate Limiting
        RateLimiter::for('file-uploads', function (Request $request) {
            return $request->user()
                ? Limit::perMinute(10)->by($request->user()->id)
                : Limit::perMinute(3)->by($request->ip());
        });

        // Admin Operations Rate Limiting
        RateLimiter::for('admin', function (Request $request) {
            return $request->user() && $request->user()->hasRole('admin')
                ? Limit::perMinute(300)->by($request->user()->id)
                : Limit::perMinute(10)->by($request->ip());
        });

        // Company Updates Rate Limiting
        RateLimiter::for('company-updates', function (Request $request) {
            return $request->user()
                ? Limit::perHour(50)->by($request->user()->id)
                : Limit::perHour(5)->by($request->ip());
        });

        // Email Verification Rate Limiting
        RateLimiter::for('email-verification', function (Request $request) {
            return Limit::perMinute(6)->by($request->user()?->id ?: $request->ip());
        });

        // Global Application Rate Limiting
        RateLimiter::for('global', function (Request $request) {
            return Limit::perMinute(1000)->by($request->ip());
        });
    }

    /**
     * Configure Universal database monitoring for performance optimization.
     */
    protected function configureDatabaseMonitoring(): void
    {
        // Universal Pattern: Monitor slow queries for performance optimization
        \DB::whenQueryingForLongerThan(500, function (\Illuminate\Database\Connection $connection, \Illuminate\Database\Events\QueryExecuted $event) {
            \Log::warning('Universal: Slow query detected', [
                'sql' => $event->sql,
                'time' => $event->time . 'ms',
                'connection' => $connection->getName(),
                'bindings' => $event->bindings
            ]);
        });

        // Universal Pattern: Query listener for development debugging
        if (config('app.debug')) {
            \DB::listen(function (\Illuminate\Database\Events\QueryExecuted $query) {
                if ($query->time > 1000) { // Over 1 second
                    \Log::debug('Universal: Very slow query', [
                        'sql' => $query->sql,
                        'time' => $query->time . 'ms',
                        'bindings' => $query->bindings,
                        'raw_sql' => $query->toRawSql()
                    ]);
                }
            });
        }
    }

    /**
     * Register class aliases.
     */
    private function registerClassAliases(): void
    {
        // Livewire class aliases removed as part of Vue3 migration
        // Universal patterns will replace these with Vue3 components
    }

    // Additional methods or properties can be added here if needed
    protected $listen = [
        // Add event listeners if necessary
    ];
}
