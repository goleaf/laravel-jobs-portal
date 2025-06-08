<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Rennokki\QueryCache\Traits\QueryCacheable;

/**
 * App\Models\Setting
 *
 * @property int $id
 * @property string $key
 * @property string $value
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Setting newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Setting newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Setting query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Setting whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Setting whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Setting whereKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Setting whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Setting whereValue($value)
 *
 * @mixin \Eloquent
 *
 * @property-read mixed $logo_url
 */
class Setting extends Model
{
    use HasFactory;
    use QueryCacheable;

    public $cacheFor = 3600; // cache time, in seconds

    /**
     * Invalidate the cache automatically
     * upon update in the database.
     *
     * @var bool
     */
    protected static $flushCacheOnUpdate = true;

    public const PATH = 'settings';

    public $table = 'settings';

    public $fillable = [
        'key',
        'value',
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'key' => 'required',
        'value' => 'required',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'id' => 'integer',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /**
     * @return mixed
     */
    public function getLogoUrlAttribute()
    {
        // Since MediaLibrary was removed, just return the asset URL from the value
        return asset($this->value);
    }

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

    /**
     * Scope for settings by key.
     */
    public function scopeByKey($query, string $key)
    {
        return $query->where('key', $key);
    }

    /**
     * Scope for settings by key pattern.
     */
    public function scopeByKeyPattern($query, string $pattern)
    {
        return $query->where('key', 'like', $pattern);
    }

    /**
     * Scope for global settings.
     */
    public function scopeGlobal($query)
    {
        return $query->whereNotIn('key', [
            'user_preference',
            'candidate_setting',
            'employer_setting'
        ]);
    }

    /**
     * Scope for user-specific settings.
     */
    public function scopeUserSpecific($query)
    {
        return $query->whereIn('key', [
            'user_preference',
            'candidate_setting',
            'employer_setting'
        ]);
    }

    /**
     * Scope for theme settings.
     */
    public function scopeTheme($query)
    {
        return $query->where('key', 'like', 'theme_%')
                    ->orWhere('key', 'like', 'color_%')
                    ->orWhere('key', 'like', 'logo_%');
    }

    /**
     * Scope for email settings.
     */
    public function scopeEmail($query)
    {
        return $query->where('key', 'like', 'email_%')
                    ->orWhere('key', 'like', 'mail_%')
                    ->orWhere('key', 'like', 'smtp_%');
    }

    /**
     * Scope for social media settings.
     */
    public function scopeSocialMedia($query)
    {
        return $query->where('key', 'like', 'social_%')
                    ->orWhere('key', 'like', 'facebook_%')
                    ->orWhere('key', 'like', 'twitter_%')
                    ->orWhere('key', 'like', 'linkedin_%');
    }

    /**
     * Scope for payment settings.
     */
    public function scopePayment($query)
    {
        return $query->where('key', 'like', 'payment_%')
                    ->orWhere('key', 'like', 'stripe_%')
                    ->orWhere('key', 'like', 'paypal_%');
    }
}
