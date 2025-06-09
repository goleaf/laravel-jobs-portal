<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/**
 * App\Models\FrontSetting
 *
 * @property int $id
 * @property string $key
 * @property string $value
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder|FrontSetting newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FrontSetting newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FrontSetting query()
 * @method static \Illuminate\Database\Eloquent\Builder|FrontSetting whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FrontSetting whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FrontSetting whereKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FrontSetting whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FrontSetting whereValue($value)
 *
 * @mixin Eloquent
 *
 * @property-read MediaCollection|Media[] $media
 * @property-read int|null $media_count
 */
class FrontSetting extends Model implements HasMedia
{
    use InteractsWithMedia;

    public $table = 'front_settings';

    public $fillable = [
        'key',
        'value',
    ];

    const FEATURED_JOBS_ENABLED = 1;

    public const PATH = 'advertise_image';

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
     * The attributes that should be casted to native types.
     *
     * @var array
     */
        protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',

        'id' => 'integer',
        'key' => 'string',
        'value' => 'string',
    
        ];
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

    /**
     * Scope a query to only include recent records.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  int  $days
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeRecent($query, int $days = 30)
    {
        return $query->where('created_at', '>=', \Carbon\Carbon::now()->subDays($days));
    }

    /**
     * Scope a query to search records by name or relevant fields.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $search
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSearch($query, $search)
    {
        return $query->where('name', 'like', '%' . $search . '%');
    }
