<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Glorand\Model\Settings\Traits\HasSettingsField;

/**
 * UserSettings Model - Demonstrates Laravel Model Settings Integration
 * 
 * This model showcases the full integration of glorand/laravel-model-settings
 * package with comprehensive settings management for job portal users.
 * 
 * Features:
 * - Field-based settings storage (JSON column)
 * - Default settings configuration
 * - Validation rules for settings data
 * - Type casting and data integrity
 * - Nested settings structure
 * 
 * @property int $id
 * @property int $user_id
 * @property array $settings
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class UserSettings extends Model
{
    use HasFactory;
    use HasSettingsField;

    protected $fillable = [
        'user_id',
        'settings',
    ];

    protected $casts = [
        'settings' => 'array',
        'user_id' => 'integer',
    ];

    /**
     * Default settings for User model.
     * 
     * These settings provide user preferences and configuration options
     * that can be customized per user without database schema changes.
     * 
     * @var array
     */
    public $defaultSettings = [
        'profile' => [
            'theme' => 'light',
            'language' => 'en',
            'timezone' => 'UTC',
            'notifications_enabled' => true,
        ],
        'job_preferences' => [
            'job_alerts' => true,
            'remote_work' => false,
            'salary_range' => [
                'min' => 0,
                'max' => 999999,
                'currency' => 'USD'
            ],
        ],
        'privacy' => [
            'profile_searchable' => true,
            'allow_recruiter_contact' => true,
        ]
    ];

    /**
     * Validation rules for settings data.
     * 
     * These rules ensure data integrity when updating user settings
     * and provide clear validation messages for the frontend.
     * 
     * @var array
     */
    public $settingsRules = [
        'profile.theme' => 'string|in:light,dark,auto',
        'profile.language' => 'string|in:en,es,fr,de,pt,ru,ar,zh,tr',
        'job_preferences.job_alerts' => 'boolean',
        'job_preferences.remote_work' => 'boolean',
        'privacy.profile_searchable' => 'boolean',
    ];

    /**
     * Relationship with User model.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get user's theme preference.
     */
    public function getTheme(): string
    {
        return $this->settings()->get('profile.theme', 'light');
    }

    /**
     * Get user's language preference.
     */
    public function getLanguage(): string
    {
        return $this->settings()->get('profile.language', 'en');
    }

    /**
     * Check if job alerts are enabled.
     */
    public function hasJobAlertsEnabled(): bool
    {
        return $this->settings()->get('job_preferences.job_alerts', true);
    }

    /**
     * Get user's preferred job types.
     */
    public function getPreferredJobTypes(): array
    {
        return $this->settings()->get('job_preferences.preferred_job_types', []);
    }

    /**
     * Get user's salary range.
     */
    public function getSalaryRange(): array
    {
        return $this->settings()->get('job_preferences.salary_range', [
            'min' => 0,
            'max' => 999999,
            'currency' => 'USD'
        ]);
    }

    /**
     * Check if profile is searchable.
     */
    public function isProfileSearchable(): bool
    {
        return $this->settings()->get('privacy.profile_searchable', true);
    }

    /**
     * Get dashboard layout preference.
     */
    public function getDashboardLayout(): string
    {
        return $this->settings()->get('dashboard.layout', 'grid');
    }

    /**
     * Get enabled dashboard widgets.
     */
    public function getEnabledWidgets(): array
    {
        $widgets = $this->settings()->get('dashboard.widgets', []);
        return array_keys(array_filter($widgets));
    }

    /**
     * Update multiple settings at once.
     */
    public function updateSettings(array $settings): bool
    {
        try {
            $this->settings()->apply($settings);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Reset settings to defaults.
     */
    public function resetToDefaults(): bool
    {
        try {
            $this->settings()->clear();
            $this->settings()->apply($this->defaultSettings);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
} 