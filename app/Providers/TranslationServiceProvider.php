<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Lang;
use App\Helpers\TranslationHelper;

class TranslationServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Register any bindings or singletons here
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Create custom trans directive for Blade templates
        Blade::directive('t', function ($expression) {
            return "<?php echo \\App\\Helpers\\TranslationHelper::getTranslation($expression); ?>";
        });
        
        // Create custom has_translation directive for Blade templates
        Blade::directive('hasTranslation', function ($expression) {
            return "<?php if (\\App\\Helpers\\TranslationHelper::hasTranslation($expression)): ?>";
        });
        
        Blade::directive('endhasTranslation', function () {
            return "<?php endif; ?>";
        });
        
        // Register view composers to share translation-related data with views
        view()->composer('*', function ($view) {
            $view->with('missingTranslations', TranslationHelper::getMissingTranslations());
        });
    }
} 