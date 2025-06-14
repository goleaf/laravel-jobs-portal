<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

/**
 * EnvSetting Model - Enhanced with Context7 patterns
 *
 * @property int $id
 * @property string $key
 * @property string|null $value
 * @property string|null $description
 * @property string|null $type
 * @property bool $is_active
 * @property bool $is_required
 * @property bool $is_sensitive
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 *
 * @property-read mixed $parsed_value
 * @property-read string $display_name
 * @property-read string $type_label
 * @property-read bool $is_boolean
 * @property-read bool $is_numeric
 *
 * Context7 Enhanced Scopes:
 * @method static \Illuminate\Database\Eloquent\Builder active()
 * @method static \Illuminate\Database\Eloquent\Builder inactive()
 * @method static \Illuminate\Database\Eloquent\Builder required()
 * @method static \Illuminate\Database\Eloquent\Builder optional()
 * @method static \Illuminate\Database\Eloquent\Builder sensitive()
 * @method static \Illuminate\Database\Eloquent\Builder nonSensitive()
 * @method static \Illuminate\Database\Eloquent\Builder byType(string $type)
 * @method static \Illuminate\Database\Eloquent\Builder byKey(string $key)
 * @method static \Illuminate\Database\Eloquent\Builder search(string $term)
 * @method static \Illuminate\Database\Eloquent\Builder recent(int $days = 30)
 * @method static \Illuminate\Database\Eloquent\Builder old(int $days = 365)
 * @method static \Illuminate\Database\Eloquent\Builder boolean()
 * @method static \Illuminate\Database\Eloquent\Builder numeric()
 * @method static \Illuminate\Database\Eloquent\Builder string()
 * @method static \Illuminate\Database\Eloquent\Builder environment()
 * @method static \Illuminate\Database\Eloquent\Builder database()
 * @method static \Illuminate\Database\Eloquent\Builder mail()
 * @method static \Illuminate\Database\Eloquent\Builder cache()
 * @method static \Illuminate\Database\Eloquent\Builder queue()
 * @method static \Illuminate\Database\Eloquent\Builder logging()
 * @method static \Illuminate\Database\Eloquent\Builder alphabetical()
 * @method static \Illuminate\Database\Eloquent\Builder priority()
 *
 * @mixin \Eloquent
 */
class EnvSetting extends Model
{
    use HasFactory;
    use SoftDeletes;
    use LogsActivity;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'env_settings';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'key',
        'value',
        'description',
        'type',
        'is_active',
        'is_required',
        'is_sensitive',
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
            'is_active' => 'boolean',
            'is_required' => 'boolean',
            'is_sensitive' => 'boolean',
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
                'description',
                'type',
                'is_active',
                'is_required',
                'is_sensitive',
            ])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    /**
     * Validation rules for creating env settings.
     *
     * @var array<string, string>
     */
    public static array $rules = [
        'key' => 'required|string|max:255|unique:env_settings,key',
        'value' => 'nullable|string',
        'description' => 'nullable|string|max:500',
        'type' => 'nullable|string|in:string,integer,boolean,float,array,json',
        'is_active' => 'boolean',
        'is_required' => 'boolean',
        'is_sensitive' => 'boolean',
    ];

    /**
     * Update validation rules for env settings.
     *
     * @param int $id
     * @return array<string, string>
     */
    public static function updateRules(int $id): array
    {
        return [
            'key' => 'required|string|max:255|unique:env_settings,key,' . $id,
            'value' => 'nullable|string',
            'description' => 'nullable|string|max:500',
            'type' => 'nullable|string|in:string,integer,boolean,float,array,json',
            'is_active' => 'boolean',
            'is_required' => 'boolean',
            'is_sensitive' => 'boolean',
        ];
    }

    // =============================================
    // CONSTANTS
    // =============================================

    public const TYPES = [
        'string' => 'String',
        'integer' => 'Integer',
        'boolean' => 'Boolean',
        'float' => 'Float',
        'array' => 'Array',
        'json' => 'JSON',
    ];

    public const CATEGORIES = [
        'environment' => 'Environment',
        'database' => 'Database',
        'mail' => 'Mail Configuration',
        'cache' => 'Cache Configuration',
        'queue' => 'Queue Configuration',
        'logging' => 'Logging Configuration',
        'api' => 'API Configuration',
        'security' => 'Security Settings',
        'other' => 'Other Settings',
    ];

    // =============================================
    // SCOPES - Basic Status
    // =============================================

    /**
     * Scope a query to only include active records.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include inactive records.
     */
    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    /**
     * Scope a query to only include required settings.
     */
    public function scopeRequired($query)
    {
        return $query->where('is_required', true);
    }

    /**
     * Scope a query to only include optional settings.
     */
    public function scopeOptional($query)
    {
        return $query->where('is_required', false);
    }

    /**
     * Scope a query to only include sensitive settings.
     */
    public function scopeSensitive($query)
    {
        return $query->where('is_sensitive', true);
    }

    /**
     * Scope a query to only include non-sensitive settings.
     */
    public function scopeNonSensitive($query)
    {
        return $query->where('is_sensitive', false);
    }

    // =============================================
    // SCOPES - Filtering
    // =============================================

    /**
     * Scope a query to filter by type.
     */
    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope a query to filter by key.
     */
    public function scopeByKey($query, string $key)
    {
        return $query->where('key', $key);
    }

    /**
     * Scope a query to search by key or description.
     */
    public function scopeSearch($query, string $term)
    {
        return $query->where(function ($q) use ($term) {
            $q->where('key', 'like', "%{$term}%")
              ->orWhere('description', 'like', "%{$term}%")
              ->orWhere('value', 'like', "%{$term}%");
        });
    }

    // =============================================
    // SCOPES - Date-based
    // =============================================

    /**
     * Scope a query to only include recent records.
     */
    public function scopeRecent($query, int $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Scope a query to only include old records.
     */
    public function scopeOld($query, int $days = 365)
    {
        return $query->where('created_at', '<', now()->subDays($days));
    }

    // =============================================
    // SCOPES - Type-based
    // =============================================

    /**
     * Scope a query to only include boolean settings.
     */
    public function scopeBoolean($query)
    {
        return $query->where('type', 'boolean');
    }

    /**
     * Scope a query to only include numeric settings.
     */
    public function scopeNumeric($query)
    {
        return $query->whereIn('type', ['integer', 'float']);
    }

    /**
     * Scope a query to only include string settings.
     */
    public function scopeString($query)
    {
        return $query->where('type', 'string');
    }

    // =============================================
    // SCOPES - Category-based
    // =============================================

    /**
     * Scope a query to only include environment settings.
     */
    public function scopeEnvironment($query)
    {
        return $query->where('key', 'like', 'APP_%')
                    ->orWhere('key', 'like', 'ENV_%');
    }

    /**
     * Scope a query to only include database settings.
     */
    public function scopeDatabase($query)
    {
        return $query->where('key', 'like', 'DB_%');
    }

    /**
     * Scope a query to only include mail settings.
     */
    public function scopeMail($query)
    {
        return $query->where('key', 'like', 'MAIL_%');
    }

    /**
     * Scope a query to only include cache settings.
     */
    public function scopeCache($query)
    {
        return $query->where('key', 'like', 'CACHE_%')
                    ->orWhere('key', 'like', 'REDIS_%');
    }

    /**
     * Scope a query to only include queue settings.
     */
    public function scopeQueue($query)
    {
        return $query->where('key', 'like', 'QUEUE_%');
    }

    /**
     * Scope a query to only include logging settings.
     */
    public function scopeLogging($query)
    {
        return $query->where('key', 'like', 'LOG_%');
    }

    // =============================================
    // SCOPES - Ordering
    // =============================================

    /**
     * Scope a query to order alphabetically by key.
     */
    public function scopeAlphabetical($query)
    {
        return $query->orderBy('key', 'asc');
    }

    /**
     * Scope a query to order by priority (required first, then active).
     */
    public function scopePriority($query)
    {
        return $query->orderBy('is_required', 'desc')
                    ->orderBy('is_active', 'desc')
                    ->orderBy('key', 'asc');
    }

    // =============================================
    // CACHE METHODS
    // =============================================

    /**
     * Get cached active settings.
     */
    public static function getCachedActive(): \Illuminate\Database\Eloquent\Collection
    {
        return Cache::remember('env_settings.active', 3600, function () {
            return self::active()->get();
        });
    }

    /**
     * Get cached required settings.
     */
    public static function getCachedRequired(): \Illuminate\Database\Eloquent\Collection
    {
        return Cache::remember('env_settings.required', 3600, function () {
            return self::required()->get();
        });
    }

    /**
     * Get cached setting by key.
     */
    public static function getCachedByKey(string $key): ?self
    {
        return Cache::remember("env_settings.{$key}", 3600, function () use ($key) {
            return self::where('key', $key)->first();
        });
    }

    // =============================================
    // ACCESSORS
    // =============================================

    /**
     * Get the parsed value attribute.
     */
    public function getParsedValueAttribute()
    {
        return match ($this->type) {
            'boolean' => filter_var($this->value, FILTER_VALIDATE_BOOLEAN),
            'integer' => (int) $this->value,
            'float' => (float) $this->value,
            'array', 'json' => json_decode($this->value, true) ?? [],
            default => $this->value,
        };
    }

    /**
     * Get the display name attribute.
     */
    public function getDisplayNameAttribute(): string
    {
        return str_replace('_', ' ', ucwords(strtolower($this->key), '_'));
    }

    /**
     * Get the type label attribute.
     */
    public function getTypeLabelAttribute(): string
    {
        return self::TYPES[$this->type] ?? 'Unknown';
    }

    /**
     * Get the is boolean attribute.
     */
    public function getIsBooleanAttribute(): bool
    {
        return $this->type === 'boolean';
    }

    /**
     * Get the is numeric attribute.
     */
    public function getIsNumericAttribute(): bool
    {
        return in_array($this->type, ['integer', 'float']);
    }

    // =============================================
    // HELPER METHODS
    // =============================================

    /**
     * Check if the setting is system-critical.
     */
    public function isCritical(): bool
    {
        return $this->is_required && $this->is_active;
    }

    /**
     * Check if the setting is safe to display.
     */
    public function isSafeToDisplay(): bool
    {
        return !$this->is_sensitive;
    }

    /**
     * Get the masked value for sensitive settings.
     */
    public function getMaskedValue(): string
    {
        if ($this->is_sensitive && !empty($this->value)) {
            return str_repeat('*', min(strlen($this->value), 8));
        }

        return $this->value ?? '';
    }

    /**
     * Set the value with proper type casting.
     */
    public function setValue($value): void
    {
        $this->value = match ($this->type) {
            'array', 'json' => is_array($value) ? json_encode($value) : $value,
            'boolean' => $value ? 'true' : 'false',
            default => (string) $value,
        };
    }

    /**
     * Clear all caches for this model.
     */
    public function clearCaches(): void
    {
        Cache::forget('env_settings.active');
        Cache::forget('env_settings.required');
        Cache::forget("env_settings.{$this->key}");
    }

    // =============================================
    // BOOT METHOD
    // =============================================

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::saved(function ($model) {
            $model->clearCaches();
        });

        static::deleted(function ($model) {
            $model->clearCaches();
        });
    }
}
