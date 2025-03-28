<?php

namespace App\Providers;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Laravel\Cashier\Cashier;
use Mariuzzo\LaravelJsLocalization\Commands\LangJsCommand;
use Mariuzzo\LaravelJsLocalization\Generators\LangJsGenerator;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Blade;
use App\Helpers\TranslationHelper;
use App\Console\Commands\MigrateJsonTranslations;
use App\Console\Commands\SynchronizeTranslations;
use App\Console\Commands\ConsolidateTranslations;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        Cashier::ignoreMigrations();
        $this->app->singleton('localization.js', function ($app) {
            $app = $this->app;
            $laravelMajorVersion = (int) $app::VERSION;

            $files = $app['files'];

            if ($laravelMajorVersion === 4) {
                $langs = $app['path.base'].'/app/lang';
            } elseif ($laravelMajorVersion >= 5 && $laravelMajorVersion < 9) {
                $langs = $app['path.base'].'/resources/lang';
            } elseif ($laravelMajorVersion >= 9) {
                $langs = app()->langPath();
            }
            $messages = $app['config']->get('localization-js.messages');
            $generator = new LangJsGenerator($files, $langs, $messages);

            return new LangJsCommand($generator);
        });
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

        \Illuminate\Pagination\Paginator::useBootstrap();
        Schema::defaultStringLength(191);
        app()->useLangPath(base_path('lang'));

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
            return "<?php endif; ?>";
        });
    }

    /**
     * Register class aliases.
     */
    private function registerClassAliases(): void
    {
        // These aliases can be used without importing the classes
        if (!class_exists('Column')) {
            class_alias(\App\Livewire\Column::class, 'Column');
        }
        
        if (!class_exists('Filter')) {
            class_alias(\App\Livewire\Filter::class, 'Filter');
        }
    }
}
