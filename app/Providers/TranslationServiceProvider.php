<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\View;
use App\Services\TranslationService;
use App\Helpers\LanguageHelper;
use App\Console\Commands\TranslationCommand;

/**
 * Translation Service Provider
 * Comprehensive internationalization service registration and configuration
 * 
 * Features:
 * - Service registration and binding
 * - Blade directive registration
 * - View composer registration
 * - Command registration
 * - Helper function registration
 * - Event listener registration
 */
class TranslationServiceProvider extends ServiceProvider
{
    /**
     * Register services
     */
    public function register(): void
    {
        // Register Translation Service as singleton
        $this->app->singleton(TranslationService::class, function ($app) {
            return new TranslationService();
        });

        // Register Language Helper as singleton
        $this->app->singleton(LanguageHelper::class, function ($app) {
            return new LanguageHelper();
        });

        // Bind aliases for easier access
        $this->app->alias(TranslationService::class, 'translation.service');
        $this->app->alias(LanguageHelper::class, 'language.helper');

        // Register console commands
        if ($this->app->runningInConsole()) {
            $this->commands([
                TranslationCommand::class,
            ]);
        }

        // Register helper functions if not already defined
        $this->registerHelperFunctions();

        // Register configuration
        $this->registerConfiguration();
    }

    /**
     * Bootstrap services
     */
    public function boot(): void
    {
        // Register Blade directives
        $this->registerBladeDirectives();

        // Register view composers
        $this->registerViewComposers();

        // Register macros
        $this->registerMacros();

        // Set up locale-specific configurations
        $this->setupLocaleConfiguration();

        // Register event listeners
        $this->registerEventListeners();

        // Preload critical translations
        $this->preloadCriticalTranslations();
    }

    /**
     * Register custom Blade directives for translation
     */
    private function registerBladeDirectives(): void
    {
        // @trans('key', ['param' => 'value'])
        Blade::directive('trans', function ($expression) {
            return "<?php echo app('translation.service')->get({$expression}); ?>";
        });

        // @transChoice('key', $count, ['param' => 'value'])
        Blade::directive('transChoice', function ($expression) {
            return "<?php echo trans_choice({$expression}); ?>";
        });

        // @locale('en')
        Blade::directive('locale', function ($expression) {
            return "<?php App::setLocale({$expression}); ?>";
        });

        // @direction - outputs 'rtl' or 'ltr'
        Blade::directive('direction', function ($expression) {
            return "<?php echo app('language.helper')->getDirection({$expression}); ?>";
        });

        // @isRTL - outputs boolean
        Blade::directive('isRTL', function ($expression) {
            return "<?php echo app('language.helper')->isRtl({$expression}) ? 'true' : 'false'; ?>";
        });

        // @langFlag('en') - outputs flag emoji
        Blade::directive('langFlag', function ($expression) {
            $flags = [
                'en' => '🇺🇸', 'ar' => '🇸🇦', 'de' => '🇩🇪', 'es' => '🇪🇸',
                'fr' => '🇫🇷', 'pt' => '🇵🇹', 'ru' => '🇷🇺', 'tr' => '🇹🇷', 'zh' => '🇨🇳'
            ];
            return "<?php 
                \$locale = {$expression};
                \$flags = " . var_export($flags, true) . ";
                echo \$flags[\$locale] ?? '🌐';
            ?>";
        });

        // @transExists('key') - check if translation exists
        Blade::directive('transExists', function ($expression) {
            return "<?php echo app('translation.service')->has({$expression}) ? 'true' : 'false'; ?>";
        });
    }

    /**
     * Register view composers
     */
    private function registerViewComposers(): void
    {
        // Share translation data with all views
        View::composer('*', function ($view) {
            $currentLocale = App::getLocale();
            $availableLocales = config('app.available_locales', []);
            $localeConfig = $availableLocales[$currentLocale] ?? [];

            $view->with([
                'currentLocale' => $currentLocale,
                'availableLocales' => $availableLocales,
                'isRTL' => $localeConfig['rtl'] ?? false,
                'localeDirection' => ($localeConfig['rtl'] ?? false) ? 'rtl' : 'ltr',
                'localeNative' => $localeConfig['native'] ?? $currentLocale,
                'localeName' => $localeConfig['name'] ?? $currentLocale,
            ]);
        });

        // Specific composers for translation-heavy views
        View::composer(['admin.translations.*', 'translation-manager.*'], function ($view) {
            $view->with([
                'translationStats' => TranslationService::getStatistics(),
                'translationService' => app(TranslationService::class),
            ]);
        });
    }

    /**
     * Register helpful macros
     */
    private function registerMacros(): void
    {
        // Add macro to Collection for translation
        if (!Collection::hasMacro('translate')) {
            Collection::macro('translate', function ($key, $params = []) {
                return $this->map(function ($item) use ($key, $params) {
                    return app('translation.service')->get($key, array_merge($params, ['item' => $item]));
                });
            });
        }

        // Add macro to Request for locale detection
        if (!Request::hasMacro('preferredLocale')) {
            Request::macro('preferredLocale', function () {
                $availableLocales = array_keys(config('app.available_locales', []));
                $acceptLanguage = $this->header('Accept-Language');
                
                if (!$acceptLanguage) {
                    return config('app.locale', 'en');
                }

                foreach (explode(',', $acceptLanguage) as $lang) {
                    $lang = trim(explode(';', $lang)[0]);
                    $lang = strtolower(substr($lang, 0, 2));
                    
                    if (in_array($lang, $availableLocales)) {
                        return $lang;
                    }
                }
                
                return config('app.locale', 'en');
            });
        }
    }

    /**
     * Setup locale-specific configurations
     */
    private function setupLocaleConfiguration(): void
    {
        // Set timezone based on locale if needed
        $currentLocale = App::getLocale();
        $localeConfig = config("app.available_locales.{$currentLocale}", []);
        
        // You could set timezone based on locale
        // if (isset($localeConfig['timezone'])) {
        //     config(['app.timezone' => $localeConfig['timezone']]);
        // }

        // Set currency based on locale
        if (isset($localeConfig['currency'])) {
            config(['app.currency' => $localeConfig['currency']]);
        }

        // Set date format based on locale
        if (isset($localeConfig['date_format'])) {
            config(['app.date_format' => $localeConfig['date_format']]);
        }
    }

    /**
     * Register event listeners
     */
    private function registerEventListeners(): void
    {
        // Listen for locale changes to clear caches
        app('events')->listen('locale.changed', function ($locale) {
            TranslationService::clearCache();
            app('cache')->forget("locale_config_{$locale}");
        });

        // Listen for translation updates
        app('events')->listen('translation.updated', function ($locale, $key = null) {
            TranslationService::clearCache();
            
            // Trigger frontend refresh if needed
            if (app()->bound('pusher')) {
                app('pusher')->trigger('translations', 'updated', [
                    'locale' => $locale,
                    'key' => $key,
                    'timestamp' => now()->toISOString(),
                ]);
            }
        });
    }

    /**
     * Preload critical translations for performance
     */
    private function preloadCriticalTranslations(): void
    {
        if (!app()->runningInConsole() && !app()->runningUnitTests()) {
            $currentLocale = App::getLocale();
            $criticalNamespaces = ['common', 'messages', 'validation', 'auth'];
            
            foreach ($criticalNamespaces as $namespace) {
                try {
                    TranslationService::getNamespaceTranslations($currentLocale, $namespace);
                } catch (\Exception $e) {
                    // Silently fail for performance
                }
            }
        }
    }

    /**
     * Register helper functions
     */
    private function registerHelperFunctions(): void
    {
        if (!function_exists('trans_json')) {
            /**
             * Get translation from JSON files with enhanced features
             */
            function trans_json(string $key, array $replace = [], string $locale = null): string {
                return app('translation.service')->get($key, $replace, $locale);
            }
        }

        if (!function_exists('is_rtl')) {
            /**
             * Check if locale is RTL with caching
             */
            function is_rtl(string $locale = null): bool {
                return app('language.helper')->isRtl($locale);
            }
        }

        if (!function_exists('lang_direction')) {
            /**
             * Get language direction with caching
             */
            function lang_direction(string $locale = null): string {
                return app('language.helper')->getDirection($locale);
            }
        }

        if (!function_exists('locale_flag')) {
            /**
             * Get flag emoji for locale
             */
            function locale_flag(string $locale): string {
                $flags = [
                    'en' => '🇺🇸', 'ar' => '🇸🇦', 'de' => '🇩🇪', 'es' => '🇪🇸',
                    'fr' => '🇫🇷', 'pt' => '🇵🇹', 'ru' => '🇷🇺', 'tr' => '🇹🇷', 'zh' => '🇨🇳'
                ];
                return $flags[$locale] ?? '🌐';
            }
        }

        if (!function_exists('trans_has')) {
            /**
             * Check if translation key exists
             */
            function trans_has(string $key, string $locale = null): bool {
                return app('translation.service')->has($key, $locale);
            }
        }

        if (!function_exists('available_locales')) {
            /**
             * Get available locales
             */
            function available_locales(): array {
                return config('app.available_locales', []);
            }
        }

        if (!function_exists('current_locale_config')) {
            /**
             * Get current locale configuration
             */
            function current_locale_config(): array {
                $currentLocale = App::getLocale();
                return config("app.available_locales.{$currentLocale}", []);
            }
        }

        if (!function_exists('format_number_locale')) {
            /**
             * Format number according to current locale
             */
            function format_number_locale($number, array $options = []): string {
                $currentLocale = App::getLocale();
                $localeMap = [
                    'en' => 'en-US', 'ar' => 'ar-SA', 'de' => 'de-DE', 'es' => 'es-ES',
                    'fr' => 'fr-FR', 'pt' => 'pt-PT', 'ru' => 'ru-RU', 'tr' => 'tr-TR', 'zh' => 'zh-CN'
                ];
                
                $locale = $localeMap[$currentLocale] ?? 'en-US';
                
                try {
                    $formatter = new \NumberFormatter($locale, \NumberFormatter::DECIMAL);
                    return $formatter->format($number);
                } catch (\Exception $e) {
                    return (string) $number;
                }
            }
        }
    }

    /**
     * Register configuration
     */
    private function registerConfiguration(): void
    {
        // Merge configuration if needed
        $this->mergeConfigFrom(__DIR__ . '/../../config/translation.php', 'translation');
    }

    /**
     * Get the services provided by the provider
     */
    public function provides(): array
    {
        return [
            TranslationService::class,
            LanguageHelper::class,
            'translation.service',
            'language.helper',
        ];
    }
}
