<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\NotificationSetting.
 *
 * @property int                             $id
 * @property string                          $key
 * @property string                          $value
 * @property null|string                     $type
 * @property null|\Illuminate\Support\Carbon $created_at
 * @property null|\Illuminate\Support\Carbon $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder|NotificationSetting newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|NotificationSetting newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|NotificationSetting query()
 * @method static \Illuminate\Database\Eloquent\Builder|NotificationSetting whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NotificationSetting whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NotificationSetting whereKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NotificationSetting whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NotificationSetting whereValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NotificationSetting whereType($value)
 *
 * @mixin \Eloquent
 */
class NotificationSetting extends Model
{
    use HasFactory;

    public $table = 'notification_settings';

    public $fillable = [
        'key',
        'value',
        'type',
    ];

    /**
     * Validation rules.
     *
     * @var array
     */
    public static $rules = [
        'key' => 'required',
        'value' => 'required',
    ];

    /**
     * Scope a query to only include active records.
     *
     * @param Builder $query
     *
     * @return Builder
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include inactive records.
     *
     * @param Builder $query
     *
     * @return Builder
     */
    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    /**
     * Scope a query to only include recent records.
     *
     * @param Builder $query
     *
     * @return Builder
     */
    public function scopeRecent($query, int $days = 30)
    {
        return $query->where('created_at', '>=', Carbon::now()->subDays($days));
    }

    /**
     * Scope a query to search records by name or relevant fields.
     *
     * @param Builder $query
     * @param string  $search
     *
     * @return Builder
     */
    public function scopeSearch($query, $search)
    {
        return $query->where('key', 'like', '%'.$search.'%')
            ->orWhere('value', 'like', '%'.$search.'%')
        ;
    }

    /**
     * The attributes that should be casted to native types.
     */
    protected function casts(): array
    {
        return [
            'id' => 'integer',
            'key' => 'string',
            'value' => 'string',
            'type' => 'string',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }
}
