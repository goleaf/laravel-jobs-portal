<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Database\Eloquent\SoftDeletes;

class EnvSetting extends Model
{
    /** @use HasFactory<\Database\Factories\EnvSettingFactory> */
    use HasFactory, SoftDeletes;

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
        'type',
        'description',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Validate required fields using Laravel 12.16 Arr::hasAll()
     *
     * @param array $data
     * @return bool
     */
    public static function validateRequiredFields(array $data): bool
    {
        $requiredFields = ['key', 'value'];
        
        return Arr::hasAll($data, $requiredFields);
    }

    /**
     * Enhanced validation for environment settings
     *
     * @param array $attributes
     * @return array
     */
    public static function validateSettingData(array $attributes): array
    {
        // Use Arr::hasAll() to ensure core fields exist
        if (!self::validateRequiredFields($attributes)) {
            throw new \InvalidArgumentException('Missing required fields: key and value are mandatory');
        }

        // Validate key format (no spaces, alphanumeric with underscores)
        if (isset($attributes['key']) && !preg_match('/^[A-Z_][A-Z0-9_]*$/', $attributes['key'])) {
            throw new \InvalidArgumentException('Environment key must be uppercase with underscores only');
        }

        return $attributes;
    }

    /**
     * Scope for active settings
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for settings by key pattern
     */
    public function scopeByKeyPattern($query, string $pattern)
    {
        return $query->where('key', 'LIKE', $pattern);
    }

    public function scopeByKey($query, $key)
    {
        return $query->where('key', $key);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }
}
