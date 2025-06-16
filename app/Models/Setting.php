<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\Log;
use LumoSolutions\Actionable\Traits\ArrayConvertible;

/**
 * Setting Model - Enhanced with Context7 patterns.
 *
 * @property int         $id
 * @property string      $key
 * @property null|string $value
 * @property null|string $category
 * @property null|string $type
 * @property null|string $description
 * @property bool        $is_public
 * @property bool        $is_editable
 * @property bool        $is_active
 * @property null|array  $options
 * @property null|int    $sort_order
 * @property null|Carbon $created_at
 * @property null|Carbon $updated_at
 * @property string      $logo_url
 * @property mixed       $parsed_value
 * @property string      $display_name
 * @property string      $category_label
 * @property string      $type_label
 *
 * Context7 Scopes:
 *
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
    use LogsActivity;
    use ArrayConvertible;

    // =============================================
    // CONSTANTS
    // =============================================

    public const PATH = 'settings';
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
        'group',
        'validation_rules',
        'default_value',
        'created_by',
        'updated_by'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [];

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
            ->dontSubmitEmptyLogs()
        ;
    }

    /**
     * Update validation rules for settings.
     *
     * @return array<string, string>
     */
    public static function updateRules(int $id): array
    {
        return [
            'key' => 'required|string|max:255|unique:settings,key,'.$id,
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
    // SCOPES - Basic Status
    // =============================================

    /**
     * Scope a query to only include active settings.
     *
     * @param mixed $query
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include inactive settings.
     *
     * @param mixed $query
     */
    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    /**
     * Scope for public settings.
     *
     * @param mixed $query
     */
    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    /**
     * Scope for private settings.
     *
     * @param mixed $query
     */
    public function scopePrivate($query)
    {
        return $query->where('is_public', false);
    }

    /**
     * Scope for editable settings.
     *
     * @param mixed $query
     */
    public function scopeEditable($query)
    {
        return $query->where('is_editable', true);
    }

    /**
     * Scope for non-editable settings.
     *
     * @param mixed $query
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
     *
     * @param mixed $query
     */
    public function scopeByCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Scope for settings by type.
     *
     * @param mixed $query
     */
    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope for settings by key.
     *
     * @param mixed $query
     */
    public function scopeByKey($query, string $key)
    {
        return $query->where('key', $key);
    }

    /**
     * Scope for searching settings.
     *
     * @param mixed $query
     */
    public function scopeSearch($query, string $term)
    {
        return $query->where(function ($q) use ($term) {
            $q->where('key', 'like', "%{$term}%")
                ->orWhere('value', 'like', "%{$term}%")
                ->orWhere('category', 'like', "%{$term}%")
                ->orWhere('description', 'like', "%{$term}%")
            ;
        });
    }

    /**
     * Scope for recent settings.
     *
     * @param mixed $query
     */
    public function scopeRecent($query, int $days = 30)
    {
        return $query->where('updated_at', '>=', now()->subDays($days));
    }

    /**
     * Scope for old settings.
     *
     * @param mixed $query
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
     *
     * @param mixed $query
     */
    public function scopeGeneral($query)
    {
        return $query->where('category', 'general');
    }

    /**
     * Scope for email settings.
     *
     * @param mixed $query
     */
    public function scopeEmail($query)
    {
        return $query->where(function ($q) {
            $q->where('category', 'email')
                ->orWhere('key', 'like', '%email%')
                ->orWhere('key', 'like', '%mail%')
            ;
        });
    }

    /**
     * Scope for payment settings.
     *
     * @param mixed $query
     */
    public function scopePayment($query)
    {
        return $query->where(function ($q) {
            $q->where('category', 'payment')
                ->orWhere('key', 'like', '%payment%')
                ->orWhere('key', 'like', '%stripe%')
                ->orWhere('key', 'like', '%paypal%')
            ;
        });
    }

    /**
     * Scope for notification settings.
     *
     * @param mixed $query
     */
    public function scopeNotification($query)
    {
        return $query->where(function ($q) {
            $q->where('category', 'notification')
                ->orWhere('key', 'like', '%notification%')
                ->orWhere('key', 'like', '%alert%')
            ;
        });
    }

    /**
     * Scope for security settings.
     *
     * @param mixed $query
     */
    public function scopeSecurity($query)
    {
        return $query->where(function ($q) {
            $q->where('category', 'security')
                ->orWhere('key', 'like', '%security%')
                ->orWhere('key', 'like', '%password%')
                ->orWhere('key', 'like', '%auth%')
            ;
        });
    }

    /**
     * Scope for appearance settings.
     *
     * @param mixed $query
     */
    public function scopeAppearance($query)
    {
        return $query->where(function ($q) {
            $q->where('category', 'appearance')
                ->orWhere('key', 'like', '%theme%')
                ->orWhere('key', 'like', '%color%')
                ->orWhere('key', 'like', '%logo%')
            ;
        });
    }

    /**
     * Scope for social media settings.
     *
     * @param mixed $query
     */
    public function scopeSocial($query)
    {
        return $query->where(function ($q) {
            $q->where('category', 'social')
                ->orWhere('key', 'like', '%facebook%')
                ->orWhere('key', 'like', '%twitter%')
                ->orWhere('key', 'like', '%linkedin%')
                ->orWhere('key', 'like', '%instagram%')
            ;
        });
    }

    /**
     * Scope for SEO settings.
     *
     * @param mixed $query
     */
    public function scopeSeo($query)
    {
        return $query->where(function ($q) {
            $q->where('category', 'seo')
                ->orWhere('key', 'like', '%seo%')
                ->orWhere('key', 'like', '%meta%')
                ->orWhere('key', 'like', '%sitemap%')
            ;
        });
    }

    /**
     * Scope for API settings.
     *
     * @param mixed $query
     */
    public function scopeApi($query)
    {
        return $query->where(function ($q) {
            $q->where('category', 'api')
                ->orWhere('key', 'like', '%api%')
                ->orWhere('key', 'like', '%token%')
                ->orWhere('key', 'like', '%key%')
            ;
        });
    }

    /**
     * Scope for system settings.
     *
     * @param mixed $query
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
     *
     * @param mixed $query
     */
    public function scopeBoolean($query)
    {
        return $query->where('type', 'boolean');
    }

    /**
     * Scope for string type settings.
     *
     * @param mixed $query
     */
    public function scopeString($query)
    {
        return $query->where('type', 'string');
    }

    /**
     * Scope for integer type settings.
     *
     * @param mixed $query
     */
    public function scopeInteger($query)
    {
        return $query->where('type', 'integer');
    }

    /**
     * Scope for array type settings.
     *
     * @param mixed $query
     */
    public function scopeArray($query)
    {
        return $query->where('type', 'array');
    }

    /**
     * Scope for JSON type settings.
     *
     * @param mixed $query
     */
    public function scopeJson($query)
    {
        return $query->where('type', 'json');
    }

    /**
     * Scope for text type settings.
     *
     * @param mixed $query
     */
    public function scopeText($query)
    {
        return $query->where('type', 'text');
    }

    /**
     * Scope for email type settings.
     *
     * @param mixed $query
     */
    public function scopeEmailType($query)
    {
        return $query->where('type', 'email');
    }

    /**
     * Scope for URL type settings.
     *
     * @param mixed $query
     */
    public function scopeUrl($query)
    {
        return $query->where('type', 'url');
    }

    /**
     * Scope for color type settings.
     *
     * @param mixed $query
     */
    public function scopeColor($query)
    {
        return $query->where('type', 'color');
    }

    /**
     * Scope for file type settings.
     *
     * @param mixed $query
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
     *
     * @param mixed $query
     */
    public function scopePopular($query)
    {
        return $query->orderBy('updated_at', 'desc');
    }

    /**
     * Scope for user-configurable settings.
     *
     * @param mixed $query
     */
    public function scopeUser($query)
    {
        return $query->where('is_editable', true)
            ->where('is_public', true)
        ;
    }

    /**
     * Scope for required settings.
     *
     * @param mixed $query
     */
    public function scopeRequired($query)
    {
        return $query->where('is_editable', false);
    }

    /**
     * Scope for optional settings.
     *
     * @param mixed $query
     */
    public function scopeOptional($query)
    {
        return $query->where('is_editable', true);
    }

    /**
     * Scope for alphabetical ordering.
     *
     * @param mixed $query
     */
    public function scopeAlphabetical($query)
    {
        return $query->orderBy('key', 'asc');
    }

    /**
     * Scope for ordering by sort order.
     *
     * @param mixed $query
     */
    public function scopeBySortOrder($query)
    {
        return $query->orderBy('sort_order', 'asc')
            ->orderBy('key', 'asc')
        ;
    }

    // =============================================
    // CACHED METHODS
    // =============================================

    /**
     * Get cached settings by category.
     */
    public static function getCachedByCategory(string $category): Collection
    {
        return Cache::remember(
            "settings_category_{$category}",
            now()->addHours(24),
            fn () => static::active()
                ->byCategory($category)
                ->bySortOrder()
                ->get()
        );
    }

    /**
     * Get cached public settings.
     */
    public static function getCachedPublic(): Collection
    {
        return Cache::remember(
            'settings_public',
            now()->addHours(12),
            fn () => static::active()
                ->public()
                ->bySortOrder()
                ->get()
        );
    }

    /**
     * Get cached setting value by key.
     *
     * @param null|mixed $default
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
    public static function getCachedEditable(): Collection
    {
        return Cache::remember(
            'settings_editable',
            now()->addHours(6),
            fn () => static::active()
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
        return !$this->is_editable || 'system' === $this->category;
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
     *
     * @param mixed $value
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
        ];
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::saved(function ($setting) {
            $setting->clearCaches();
            Cache::forget("settings.{$setting->key}");
            Cache::forget('settings.all');
            
            Log::info('Setting updated', [
                'key' => $setting->key,
                'value' => $setting->value,
                'user_id' => auth()->id(),
            ]);
        });

        static::deleted(function ($setting) {
            $setting->clearCaches();
            Cache::forget("settings.{$setting->key}");
            Cache::forget('settings.all');
        });
    }

    /**
     * Scope to filter by group
     */
    public function scopeByGroup($query, string $group)
    {
        return $query->where('group', $group);
    }

    /**
     * Cast value attribute based on type
     */
    protected function value(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                return $this->castValue($value, $this->type);
            },
            set: function ($value) {
                return $this->serializeValue($value, $this->type);
            }
        );
    }

    /**
     * Cast value to appropriate type
     */
    private function castValue($value, $type)
    {
        switch ($type) {
            case 'boolean':
                return filter_var($value, FILTER_VALIDATE_BOOLEAN);
            case 'integer':
                return (int) $value;
            case 'float':
                return (float) $value;
            case 'array':
            case 'json':
                return json_decode($value, true);
            case 'object':
                return json_decode($value);
            default:
                return $value;
        }
    }

    /**
     * Serialize value for storage
     */
    private function serializeValue($value, $type)
    {
        switch ($type) {
            case 'boolean':
                return $value ? '1' : '0';
            case 'array':
            case 'json':
            case 'object':
                return json_encode($value);
            default:
                return (string) $value;
        }
    }

    /**
     * Get setting value with caching
     */
    public static function get(string $key, $default = null)
    {
        return Cache::remember("settings.{$key}", 3600, function () use ($key, $default) {
            $setting = static::where('key', $key)->first();
            return $setting ? $setting->value : $default;
        });
    }

    /**
     * Set setting value
     */
    public static function set(string $key, $value, array $options = [])
    {
        $setting = static::firstOrNew(['key' => $key]);
        
        $setting->fill([
            'value' => $value,
            'type' => $options['type'] ?? 'string',
            'group' => $options['group'] ?? 'general',
            'description' => $options['description'] ?? null,
            'is_public' => $options['is_public'] ?? false,
            'validation_rules' => $options['validation_rules'] ?? null,
            'default_value' => $options['default_value'] ?? null,
            'updated_by' => auth()->id(),
        ]);

        if ($setting->wasRecentlyCreated) {
            $setting->created_by = auth()->id();
        }

        return $setting->save();
    }

    /**
     * Get all settings as array with caching
     */
    public static function getAll($group = null): array
    {
        $cacheKey = $group ? "settings.group.{$group}" : 'settings.all';
        
        return Cache::remember($cacheKey, 3600, function () use ($group) {
            $query = static::query();
            
            if ($group) {
                $query->where('group', $group);
            }
            
            return $query->pluck('value', 'key')->map(function ($value, $key) {
                $setting = static::where('key', $key)->first();
                return $setting ? $setting->value : $value;
            })->toArray();
        });
    }

    /**
     * Check if setting exists
     */
    public static function exists(string $key): bool
    {
        return static::where('key', $key)->exists();
    }

    /**
     * Remove setting
     */
    public static function remove(string $key): bool
    {
        return static::where('key', $key)->delete();
    }

    /**
     * Get settings by group
     */
    public static function getGroup(string $group): array
    {
        return static::getAll($group);
    }

    /**
     * Validate setting value
     */
    public function validateValue($value): bool
    {
        if (!$this->validation_rules) {
            return true;
        }

        $validator = \Validator::make(['value' => $value], [
            'value' => $this->validation_rules
        ]);

        return !$validator->fails();
    }

    /**
     * Get default value
     */
    public function getDefaultValue()
    {
        return $this->castValue($this->default_value, $this->type);
    }

    /**
     * Reset to default value
     */
    public function resetToDefault(): bool
    {
        if ($this->default_value !== null) {
            $this->value = $this->default_value;
            return $this->save();
        }
        
        return false;
    }

    /**
     * Export settings for backup
     */
    public static function export($group = null): array
    {
        $query = static::query();
        
        if ($group) {
            $query->where('group', $group);
        }
        
        return $query->get()->map(function ($setting) {
            return [
                'key' => $setting->key,
                'value' => $setting->getRawOriginal('value'), // Get raw value without casting
                'type' => $setting->type,
                'group' => $setting->group,
                'description' => $setting->description,
                'is_public' => $setting->is_public,
                'validation_rules' => $setting->validation_rules,
                'default_value' => $setting->default_value,
            ];
        })->toArray();
    }

    /**
     * Import settings from backup
     */
    public static function import(array $settings): int
    {
        $imported = 0;
        
        foreach ($settings as $settingData) {
            if (isset($settingData['key'])) {
                static::updateOrCreate(
                    ['key' => $settingData['key']],
                    array_merge($settingData, ['updated_by' => auth()->id()])
                );
                $imported++;
            }
        }
        
        // Clear all caches
        Cache::flush();
        
        return $imported;
    }

    /**
     * Get settings schema for API documentation
     */
    public static function getSchema(): array
    {
        return [
            'groups' => static::distinct('group')->pluck('group')->filter()->values(),
            'types' => ['string', 'integer', 'float', 'boolean', 'array', 'json', 'object'],
            'total_settings' => static::count(),
            'public_settings' => static::where('is_public', true)->count(),
            'private_settings' => static::where('is_public', false)->count(),
        ];
    }
}
