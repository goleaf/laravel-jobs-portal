<?php

namespace App\Providers;

use App\Console\Commands\ConsolidateTranslations;
use App\Console\Commands\MigrateJsonTranslations;
use App\Console\Commands\SynchronizeTranslations;
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

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Cashier::ignoreMigrations(); - Removed for Laravel 12 compatibility

        // Removed localization.js singleton for Laravel 12 compatibility

        //        $this->app->singleton(
        //        // the original class
        //            'vendor/brotzka/laravel-dotenv-editor/src/DotenvEditor.php',
        //            // my custom class
        //            'app/DotenvEditor.php'
        //        );

        // Register commands
        $this->commands([
            MigrateJsonTranslations::class,
            SynchronizeTranslations::class,
            ConsolidateTranslations::class,
        ]);

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
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (App::environment('production')) {
            URL::forceScheme('https');
        }

        Schema::defaultStringLength(191);
        Paginator::useTailwind();
        app()->useLangPath(base_path('lang'));

        // Configure comprehensive rate limiting
        $this->configureRateLimiting();
        
        // Configure Universal database monitoring
        $this->configureDatabaseMonitoring();

        // Register class aliases
        $this->registerClassAliases();

        // Register translation commands when running in console
        if ($this->app->runningInConsole()) {
            $this->commands([
                \App\Console\Commands\MigrateJsonTranslations::class,
                \App\Console\Commands\SynchronizeTranslations::class,
                \App\Console\Commands\CheckTranslations::class,
                \App\Console\Commands\CleanupTranslations::class,
                \App\Console\Commands\ConsolidateTranslations::class,
                \App\Console\Commands\CreateLithuanianTranslations::class,
            ]);
        }

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

        // Comment out Livewire components registration to prevent errors
        // Livewire::component('language-table', LanguageTable::class);
        // Add other Livewire components here
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
        // These aliases can be used without importing the classes
        if (! class_exists('Column')) {
            class_alias(\App\Livewire\Column::class, 'Column');
        }

        if (! class_exists('Filter')) {
            class_alias(\App\Livewire\Filter::class, 'Filter');
        }
    }
}
