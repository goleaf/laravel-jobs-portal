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

    /**
     * Scope a query to only include popular records.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePopular(\Illuminate\Database\Eloquent\Builder $query): \Illuminate\Database\Eloquent\Builder
    {
        return $query->orderBy("views_count", "desc");
    }




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
        return $query->where('key', 'like', "%{$term}%")
                    ->orWhere('value', 'like', "%{$term}%")
                    ->orWhere('category', 'like', "%{$term}%");
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
        return $query->where('category', 'email')
                    ->orWhere('key', 'like', '%email%')
                    ->orWhere('key', 'like', '%mail%');
    }

    /**
     * Scope for payment settings.
     */
    public function scopePayment($query)
    {
        return $query->where('category', 'payment')
                    ->orWhere('key', 'like', '%payment%')
                    ->orWhere('key', 'like', '%stripe%')
                    ->orWhere('key', 'like', '%paypal%');
    }

    /**
     * Scope for notification settings.
     */
    public function scopeNotification($query)
    {
        return $query->where('category', 'notification')
                    ->orWhere('key', 'like', '%notification%')
                    ->orWhere('key', 'like', '%alert%');
    }

    /**
     * Scope for security settings.
     */
    public function scopeSecurity($query)
    {
        return $query->where('category', 'security')
                    ->orWhere('key', 'like', '%security%')
                    ->orWhere('key', 'like', '%password%')
                    ->orWhere('key', 'like', '%auth%');
    }

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
}

    /**
     * Scope a query to only include active records.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include inactive records.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }
