<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Collection;

/**
 * Settings Manager Service
 * 
 * Based on Habr community best practices for Laravel settings management
 * Provides a clean interface for managing application settings
 */
class SettingsManager
{
    /**
     * Cache key prefix
     */
    private const CACHE_PREFIX = 'settings.';

    /**
     * Cache TTL in seconds (1 hour)
     */
    private const CACHE_TTL = 3600;

    /**
     * Get a setting value
     */
    public function get(string $key, $default = null)
    {
        // Try Laravel config first (for performance)
        $configValue = Config::get("settings.{$key}");
        if ($configValue !== null) {
            return $configValue;
        }

        // Fallback to cache/database
        return Cache::remember(self::CACHE_PREFIX . $key, self::CACHE_TTL, function () use ($key, $default) {
            $setting = Setting::where('key', $key)->first();
            return $setting ? $setting->value : $default;
        });
    }

    /**
     * Set a setting value
     */
    public function set(string $key, $value, array $options = []): bool
    {
        $result = Setting::set($key, $value, $options);
        
        if ($result) {
            // Update config
            Config::set("settings.{$key}", $value);
            
            // Clear cache
            Cache::forget(self::CACHE_PREFIX . $key);
            Cache::forget('laravel_settings');
            
            // Clear group cache if group is specified
            if (isset($options['group'])) {
                Cache::forget("settings_group_{$options['group']}");
            }
        }
        
        return $result;
    }

    /**
     * Get all settings
     */
    public function all(): array
    {
        return Cache::remember('laravel_settings', self::CACHE_TTL, function () {
            return Setting::all()->pluck('value', 'key')->toArray();
        });
    }

    /**
     * Get settings by group
     */
    public function getGroup(string $group): array
    {
        return Cache::remember("settings_group_{$group}", self::CACHE_TTL, function () use ($group) {
            return Setting::where('group', $group)->pluck('value', 'key')->toArray();
        });
    }

    /**
     * Check if setting exists
     */
    public function exists(string $key): bool
    {
        return Setting::exists($key);
    }

    /**
     * Remove a setting
     */
    public function remove(string $key): bool
    {
        $result = Setting::remove($key);
        
        if ($result) {
            Cache::forget(self::CACHE_PREFIX . $key);
            Cache::forget('laravel_settings');
            Config::forget("settings.{$key}");
        }
        
        return $result;
    }

    /**
     * Get public settings (for frontend)
     */
    public function getPublic(): array
    {
        return Cache::remember('settings_public', self::CACHE_TTL, function () {
            return Setting::where('is_public', true)->pluck('value', 'key')->toArray();
        });
    }

    /**
     * Bulk update settings
     */
    public function setBulk(array $settings): int
    {
        $updated = 0;
        
        foreach ($settings as $key => $value) {
            if ($this->set($key, $value)) {
                $updated++;
            }
        }
        
        // Clear all cache after bulk update
        $this->clearCache();
        
        return $updated;
    }

    /**
     * Export settings
     */
    public function export(?string $group = null): array
    {
        return Setting::export($group);
    }

    /**
     * Import settings
     */
    public function import(array $settings): int
    {
        $result = Setting::import($settings);
        $this->clearCache();
        return $result;
    }

    /**
     * Get settings schema
     */
    public function getSchema(): array
    {
        return Setting::getSchema();
    }

    /**
     * Clear all settings cache
     */
    public function clearCache(): void
    {
        Cache::forget('laravel_settings');
        Cache::forget('settings_public');
        
        // Clear group caches
        $groups = Setting::distinct('group')->pluck('group')->filter();
        foreach ($groups as $group) {
            Cache::forget("settings_group_{$group}");
        }
        
        // Clear individual setting caches (this is expensive, consider if needed)
        $keys = Setting::pluck('key');
        foreach ($keys as $key) {
            Cache::forget(self::CACHE_PREFIX . $key);
        }
    }

    /**
     * Get settings grouped by category
     */
    public function getGrouped(): Collection
    {
        return Cache::remember('settings_grouped', self::CACHE_TTL, function () {
            return Setting::all()->groupBy('group');
        });
    }

    /**
     * Validate a setting value
     */
    public function validate(string $key, $value): bool
    {
        $setting = Setting::where('key', $key)->first();
        return $setting ? $setting->validateValue($value) : true;
    }

    /**
     * Reset setting to default value
     */
    public function resetToDefault(string $key): bool
    {
        $setting = Setting::where('key', $key)->first();
        if ($setting && $setting->resetToDefault()) {
            Cache::forget(self::CACHE_PREFIX . $key);
            Config::set("settings.{$key}", $setting->getDefaultValue());
            return true;
        }
        
        return false;
    }

    /**
     * Get setting with metadata
     */
    public function getWithMetadata(string $key): ?array
    {
        $setting = Setting::where('key', $key)->first();
        
        if (!$setting) {
            return null;
        }
        
        return [
            'key' => $setting->key,
            'value' => $setting->value,
            'type' => $setting->type,
            'group' => $setting->group,
            'description' => $setting->description,
            'is_public' => $setting->is_public,
            'validation_rules' => $setting->validation_rules,
            'default_value' => $setting->getDefaultValue(),
            'created_at' => $setting->created_at,
            'updated_at' => $setting->updated_at,
        ];
    }

    /**
     * Search settings by key or description
     */
    public function search(string $query): Collection
    {
        return Setting::where('key', 'like', "%{$query}%")
            ->orWhere('description', 'like', "%{$query}%")
            ->get();
    }

    /**
     * Get settings that need attention (no default value, validation issues, etc.)
     */
    public function getSettingsNeedingAttention(): Collection
    {
        return Setting::whereNull('default_value')
            ->orWhereNull('description')
            ->orWhereNull('validation_rules')
            ->get();
    }
} 