<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

/**
 * Setting Model - Enhanced with Context7 patterns
 *
 * @property int $id
 * @property string $key
 * @property string|null $value
 * @property string|null $category
 * @property string|null $type
 * @property string|null $description
 * @property bool $is_public
 * @property bool $is_editable
 * @property bool $is_active
 * @property array|null $options
 * @property int|null $sort_order
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 *
 * @property-read string $logo_url
 * @property-read mixed $parsed_value
 * @property-read string $display_name
 * @property-read string $category_label
 * @property-read string $type_label
 *
 * Context7 Enhanced Scopes:
 * @method static \Illuminate\Database\Eloquent\Builder active()
 * @method static \Illuminate\Database\Eloquent\Builder inactive()
 * @method static \Illuminate\Database\Eloquent\Builder public()
 * @method static \Illuminate\Database\Eloquent\Builder private()
 * @method static \Illuminate\Database\Eloquent\Builder editable()
 * @method static \Illuminate\Database\Eloquent\Builder nonEditable()
 * @method static \Illuminate\Database\Eloquent\Builder byCategory(string $category)
 * @method static \Illuminate\Database\Eloquent\Builder byType(string $type)
 * @method static \Illuminate\Database\Eloquent\Builder byKey(string $key)
 * @method static \Illuminate\Database\Eloquent\Builder search(string $term)
 * @method static \Illuminate\Database\Eloquent\Builder recent(int $days = 30)
 * @method static \Illuminate\Database\Eloquent\Builder old(int $days = 365)
 * @method static \Illuminate\Database\Eloquent\Builder general()
 * @method static \Illuminate\Database\Eloquent\Builder email()
 * @method static \Illuminate\Database\Eloquent\Builder payment()
 * @method static \Illuminate\Database\Eloquent\Builder notification()
 * @method static \Illuminate\Database\Eloquent\Builder security()
 * @method static \Illuminate\Database\Eloquent\Builder boolean()
 * @method static \Illuminate\Database\Eloquent\Builder string()
 * @method static \Illuminate\Database\Eloquent\Builder integer()
 * @method static \Illuminate\Database\Eloquent\Builder array()
 * @method static \Illuminate\Database\Eloquent\Builder popular()
 * @method static \Illuminate\Database\Eloquent\Builder system()
 * @method static \Illuminate\Database\Eloquent\Builder user()
 * @method static \Illuminate\Database\Eloquent\Builder required()
 * @method static \Illuminate\Database\Eloquent\Builder optional()
 * @method static \Illuminate\Database\Eloquent\Builder alphabetical()
 * @method static \Illuminate\Database\Eloquent\Builder bySortOrder()
 *
 * @mixin \Eloquent
 */
class Setting extends Model
{
    use HasFactory;
    use SoftDeletes;
    use LogsActivity;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'key',
        'value',
        'category',
        'type',
        'description',
        'is_public',
        'is_editable',
        'is_active',
        'options',
        'sort_order',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'deleted_at',
    ];

    /**
     * Get the attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'is_public' => 'boolean',
            'is_editable' => 'boolean',
            'is_active' => 'boolean',
            'options' => 'array',
            'sort_order' => 'integer',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }

    /**
     * Configure activity logging.
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'key',
                'value',
                'category',
                'type',
                'description',
                'is_public',
                'is_editable',
                'is_active',
                'options',
                'sort_order',
            ])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    /**
     * Validation rules for creating settings.
     *
     * @var array<string, string>
     */
    public static array $rules = [
        'key' => 'required|string|max:255|unique:settings,key',
        'value' => 'nullable|string',
        'category' => 'nullable|string|max:100',
        'type' => 'nullable|string|in:string,integer,boolean,array,json,text,email,url,color,file',
        'description' => 'nullable|string|max:500',
        'is_public' => 'boolean',
        'is_editable' => 'boolean',
        'is_active' => 'boolean',
        'options' => 'nullable|array',
        'sort_order' => 'nullable|integer|min:0',
    ];

    /**
     * Update validation rules for settings.
     *
     * @param int $id
     * @return array<string, string>
     */
    public static function updateRules(int $id): array
    {
        return [
            'key' => 'required|string|max:255|unique:settings,key,' . $id,
            'value' => 'nullable|string',
            'category' => 'nullable|string|max:100',
            'type' => 'nullable|string|in:string,integer,boolean,array,json,text,email,url,color,file',
            'description' => 'nullable|string|max:500',
            'is_public' => 'boolean',
            'is_editable' => 'boolean',
            'is_active' => 'boolean',
            'options' => 'nullable|array',
            'sort_order' => 'nullable|integer|min:0',
        ];
    }

    // =============================================
    // CONSTANTS
    // =============================================

    public const COLOR = [
        '0' => '#5EBEC4',
        '1' => '#2568FB',
        '2' => '#6E6E6E',
        '3' => '#394F8A',
        '4' => '#4A5FC1',
        '5' => '#BD1E51',
        '6' => '#490B3D',
        '7' => '#161F6D',
        '8' => '#00A9D8',
        '9' => '#7DA2A9',
        '10' => '#8DA242',
        '11' => '#D48166',
        '12' => '#438945',
        '13' => '#5C6E58',
        '14' => '#E60576',
        '15' => '#FB9039',
        '16' => '#0B4141',
        '17' => '#3F6844',
    ];

    public const CATEGORIES = [
        'general' => 'General Settings',
        'email' => 'Email Settings',
        'payment' => 'Payment Settings',
        'notification' => 'Notification Settings',
        'security' => 'Security Settings',
        'appearance' => 'Appearance Settings',
        'social' => 'Social Media Settings',
        'seo' => 'SEO Settings',
        'api' => 'API Settings',
        'system' => 'System Settings',
    ];

    public const TYPES = [
        'string' => 'Text',
        'integer' => 'Number',
        'boolean' => 'Yes/No',
        'array' => 'Array',
        'json' => 'JSON',
        'text' => 'Long Text',
        'email' => 'Email',
        'url' => 'URL',
        'color' => 'Color',
        'file' => 'File',
    ];

    // =============================================
    // SCOPES - Basic Status
    // =============================================

    /**
     * Scope a query to only include active settings.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include inactive settings.
     */
    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    /**
     * Scope for public settings.
     */
    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    /**
     * Scope for private settings.
     */
    public function scopePrivate($query)
    {
        return $query->where('is_public', false);
    }

    /**
     * Scope for editable settings.
     */
    public function scopeEditable($query)
    {
        return $query->where('is_editable', true);
    }

    /**
     * Scope for non-editable settings.
     */
    public function scopeNonEditable($query)
    {
        return $query->where('is_editable', false);
    }

    // =============================================
    // SCOPES - Filtering & Search
    // =============================================

    /**
     * Scope for settings by category.
     */
    public function scopeByCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Scope for settings by type.
     */
    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope for settings by key.
     */
    public function scopeByKey($query, string $key)
    {
        return $query->where('key', $key);
    }

    /**
     * Scope for searching settings.
     */
    public function scopeSearch($query, string $term)
    {
        return $query->where(function ($q) use ($term) {
            $q->where('key', 'like', "%{$term}%")
                    ->orWhere('value', 'like', "%{$term}%")
              ->orWhere('category', 'like', "%{$term}%")
              ->orWhere('description', 'like', "%{$term}%");
        });
    }

    /**
     * Scope for recent settings.
     */
    public function scopeRecent($query, int $days = 30)
    {
        return $query->where('updated_at', '>=', now()->subDays($days));
    }

    /**
     * Scope for old settings.
     */
    public function scopeOld($query, int $days = 365)
    {
        return $query->where('updated_at', '<', now()->subDays($days));
    }

    // =============================================
    // SCOPES - Category Specific
    // =============================================

    /**
     * Scope for general settings.
     */
    public function scopeGeneral($query)
    {
        return $query->where('category', 'general');
    }

    /**
     * Scope for email settings.
     */
    public function scopeEmail($query)
    {
        return $query->where(function ($q) {
            $q->where('category', 'email')
                    ->orWhere('key', 'like', '%email%')
                    ->orWhere('key', 'like', '%mail%');
        });
    }

    /**
     * Scope for payment settings.
     */
    public function scopePayment($query)
    {
        return $query->where(function ($q) {
            $q->where('category', 'payment')
                    ->orWhere('key', 'like', '%payment%')
                    ->orWhere('key', 'like', '%stripe%')
                    ->orWhere('key', 'like', '%paypal%');
        });
    }

    /**
     * Scope for notification settings.
     */
    public function scopeNotification($query)
    {
        return $query->where(function ($q) {
            $q->where('category', 'notification')
                    ->orWhere('key', 'like', '%notification%')
                    ->orWhere('key', 'like', '%alert%');
        });
    }

    /**
     * Scope for security settings.
     */
    public function scopeSecurity($query)
    {
        return $query->where(function ($q) {
            $q->where('category', 'security')
                    ->orWhere('key', 'like', '%security%')
                    ->orWhere('key', 'like', '%password%')
                    ->orWhere('key', 'like', '%auth%');
        });
    }

    /**
     * Scope for appearance settings.
     */
    public function scopeAppearance($query)
    {
        return $query->where(function ($q) {
            $q->where('category', 'appearance')
              ->orWhere('key', 'like', '%theme%')
              ->orWhere('key', 'like', '%color%')
              ->orWhere('key', 'like', '%logo%');
        });
    }

    /**
     * Scope for social media settings.
     */
    public function scopeSocial($query)
    {
        return $query->where(function ($q) {
            $q->where('category', 'social')
              ->orWhere('key', 'like', '%facebook%')
              ->orWhere('key', 'like', '%twitter%')
              ->orWhere('key', 'like', '%linkedin%')
              ->orWhere('key', 'like', '%instagram%');
        });
    }

    /**
     * Scope for SEO settings.
     */
    public function scopeSeo($query)
    {
        return $query->where(function ($q) {
            $q->where('category', 'seo')
              ->orWhere('key', 'like', '%seo%')
              ->orWhere('key', 'like', '%meta%')
              ->orWhere('key', 'like', '%sitemap%');
        });
    }

    /**
     * Scope for API settings.
     */
    public function scopeApi($query)
    {
        return $query->where(function ($q) {
            $q->where('category', 'api')
              ->orWhere('key', 'like', '%api%')
              ->orWhere('key', 'like', '%token%')
              ->orWhere('key', 'like', '%key%');
        });
    }

    /**
     * Scope for system settings.
     */
    public function scopeSystem($query)
    {
        return $query->where('category', 'system');
    }

    // =============================================
    // SCOPES - Type Specific
    // =============================================

    /**
     * Scope for boolean type settings.
     */
    public function scopeBoolean($query)
    {
        return $query->where('type', 'boolean');
    }

    /**
     * Scope for string type settings.
     */
    public function scopeString($query)
    {
        return $query->where('type', 'string');
    }

    /**
     * Scope for integer type settings.
     */
    public function scopeInteger($query)
    {
        return $query->where('type', 'integer');
    }

    /**
     * Scope for array type settings.
     */
    public function scopeArray($query)
    {
        return $query->where('type', 'array');
    }

    /**
     * Scope for JSON type settings.
     */
    public function scopeJson($query)
    {
        return $query->where('type', 'json');
}

    /**
     * Scope for text type settings.
     */
    public function scopeText($query)
    {
        return $query->where('type', 'text');
    }

    /**
     * Scope for email type settings.
     */
    public function scopeEmailType($query)
    {
        return $query->where('type', 'email');
    }

    /**
     * Scope for URL type settings.
     */
    public function scopeUrl($query)
    {
        return $query->where('type', 'url');
    }

    /**
     * Scope for color type settings.
     */
    public function scopeColor($query)
    {
        return $query->where('type', 'color');
    }

    /**
     * Scope for file type settings.
     */
    public function scopeFile($query)
    {
        return $query->where('type', 'file');
    }

    // =============================================
    // SCOPES - Additional
    // =============================================

    /**
     * Scope a query to only include popular records.
     */
    public function scopePopular($query)
    {
        return $query->orderBy('updated_at', 'desc');
    }

    /**
     * Scope for user-configurable settings.
     */
    public function scopeUser($query)
    {
        return $query->where('is_editable', true)
                    ->where('is_public', true);
    }

    /**
     * Scope for required settings.
     */
    public function scopeRequired($query)
    {
        return $query->where('is_editable', false);
    }

    /**
     * Scope for optional settings.
     */
    public function scopeOptional($query)
    {
        return $query->where('is_editable', true);
    }

    /**
     * Scope for alphabetical ordering.
     */
    public function scopeAlphabetical($query)
    {
        return $query->orderBy('key', 'asc');
    }

    /**
     * Scope for ordering by sort order.
     */
    public function scopeBySortOrder($query)
    {
        return $query->orderBy('sort_order', 'asc')
                    ->orderBy('key', 'asc');
    }

    // =============================================
    // CACHED METHODS
    // =============================================

    /**
     * Get cached settings by category.
     */
    public static function getCachedByCategory(string $category): \Illuminate\Database\Eloquent\Collection
    {
        return Cache::remember(
            "settings_category_{$category}",
            now()->addHours(24),
            fn() => static::active()
                ->byCategory($category)
                ->bySortOrder()
                ->get()
        );
    }

    /**
     * Get cached public settings.
     */
    public static function getCachedPublic(): \Illuminate\Database\Eloquent\Collection
    {
        return Cache::remember(
            'settings_public',
            now()->addHours(12),
            fn() => static::active()
                ->public()
                ->bySortOrder()
                ->get()
        );
    }

    /**
     * Get cached setting value by key.
     */
    public static function getCachedValue(string $key, $default = null)
    {
        return Cache::remember(
            "setting_value_{$key}",
            now()->addHours(6),
            function () use ($key, $default) {
                $setting = static::active()->byKey($key)->first();
                return $setting ? $setting->parsed_value : $default;
            }
        );
    }

    /**
     * Get cached editable settings.
     */
    public static function getCachedEditable(): \Illuminate\Database\Eloquent\Collection
    {
        return Cache::remember(
            'settings_editable',
            now()->addHours(6),
            fn() => static::active()
                ->editable()
                ->bySortOrder()
                ->get()
        );
    }

    // =============================================
    // HELPER METHODS & ATTRIBUTES
    // =============================================

    /**
     * Get logo URL attribute.
     */
    public function getLogoUrlAttribute(): string
    {
        // Since MediaLibrary was removed, just return the asset URL from the value
        return asset($this->value ?? '');
    }

    /**
     * Get parsed value based on type.
     */
    public function getParsedValueAttribute()
    {
        if (is_null($this->value)) {
            return null;
        }

        return match ($this->type) {
            'boolean' => filter_var($this->value, FILTER_VALIDATE_BOOLEAN),
            'integer' => (int) $this->value,
            'array', 'json' => json_decode($this->value, true) ?? [],
            default => $this->value,
        };
    }

    /**
     * Get display name attribute.
     */
    public function getDisplayNameAttribute(): string
    {
        return ucwords(str_replace(['_', '-'], ' ', $this->key));
    }

    /**
     * Get category label attribute.
     */
    public function getCategoryLabelAttribute(): string
    {
        return self::CATEGORIES[$this->category] ?? ucwords($this->category ?? 'General');
    }

    /**
     * Get type label attribute.
     */
    public function getTypeLabelAttribute(): string
    {
        return self::TYPES[$this->type] ?? ucwords($this->type ?? 'Text');
    }

    /**
     * Check if setting is system-level.
     */
    public function isSystem(): bool
    {
        return !$this->is_editable || $this->category === 'system';
    }

    /**
     * Check if setting is user-configurable.
     */
    public function isUserConfigurable(): bool
    {
        return $this->is_editable && $this->is_public;
    }

    /**
     * Check if setting is sensitive.
     */
    public function isSensitive(): bool
    {
        $sensitiveKeys = ['password', 'secret', 'key', 'token', 'api'];
        
        foreach ($sensitiveKeys as $sensitive) {
            if (str_contains(strtolower($this->key), $sensitive)) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Set setting value with type casting.
     */
    public function setValue($value): void
    {
        $this->value = match ($this->type) {
            'array', 'json' => is_array($value) ? json_encode($value) : $value,
            'boolean' => $value ? '1' : '0',
            default => (string) $value,
        };
    }

    /**
     * Clear setting-related caches.
     */
    public function clearCaches(): void
    {
        $cacheKeys = [
            'settings_public',
            'settings_editable',
            "setting_value_{$this->key}",
        ];

        if ($this->category) {
            $cacheKeys[] = "settings_category_{$this->category}";
        }

        foreach ($cacheKeys as $key) {
            Cache::forget($key);
        }
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::saved(function ($setting) {
            $setting->clearCaches();
        });

        static::deleted(function ($setting) {
            $setting->clearCaches();
        });

        static::restored(function ($setting) {
            $setting->clearCaches();
        });
    }
    }
