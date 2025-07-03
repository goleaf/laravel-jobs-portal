<?php

namespace App\Providers;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class SettingsServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register settings manager singleton
        $this->app->singleton('settings', function ($app) {
            return new \App\Services\SettingsManager;
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Only load settings if the settings table exists
        if ($this->settingsTableExists()) {
            $this->loadSettingsIntoConfig();
            $this->registerSettingsHelpers();
        }
    }

    /**
     * Check if the settings table exists
     */
    private function settingsTableExists(): bool
    {
        try {
            return Schema::hasTable('settings');
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Load settings from database into Laravel's config
     */
    private function loadSettingsIntoConfig(): void
    {
        try {
            // Cache settings for 1 hour to improve performance
            $settings = Cache::remember('laravel_settings', 3600, function () {
                return Setting::all()->pluck('value', 'key')->toArray();
            });

            // Load settings into config with 'settings' prefix
            Config::set('settings', $settings);

            // Load specific groups into their own config keys
            $this->loadGroupSettings();

        } catch (\Exception $e) {
            // If there's any error loading settings, log it but don't break the app
            \Log::error('Failed to load settings: '.$e->getMessage());
        }
    }

    /**
     * Load settings grouped by category
     */
    private function loadGroupSettings(): void
    {
        try {
            $groups = Setting::distinct('group')->pluck('group')->filter();

            foreach ($groups as $group) {
                $groupSettings = Cache::remember("settings_group_{$group}", 3600, function () use ($group) {
                    return Setting::where('group', $group)->pluck('value', 'key')->toArray();
                });

                Config::set("settings.{$group}", $groupSettings);
            }
        } catch (\Exception $e) {
            \Log::error('Failed to load group settings: '.$e->getMessage());
        }
    }

    /**
     * Register global helper functions for settings
     */
    private function registerSettingsHelpers(): void
    {
        if (! function_exists('setting')) {
            /**
             * Get a setting value
             *
             * @param  mixed  $default
             * @return mixed
             */
            function setting(string $key, $default = null)
            {
                return app('settings')->get($key, $default);
            }
        }

        if (! function_exists('setting_set')) {
            /**
             * Set a setting value
             *
             * @param  mixed  $value
             */
            function setting_set(string $key, $value, array $options = []): bool
            {
                return app('settings')->set($key, $value, $options);
            }
        }

        if (! function_exists('settings_group')) {
            /**
             * Get all settings from a group
             */
            function settings_group(string $group): array
            {
                return app('settings')->getGroup($group);
            }
        }
    }
}
