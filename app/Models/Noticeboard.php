<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Noticeboard
 *
 * @property int $id
 * @property string $title
 * @property string|null $description
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Noticeboard newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Noticeboard newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Noticeboard query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Noticeboard whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Noticeboard whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Noticeboard whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Noticeboard whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Noticeboard whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 *
 * @property int $is_active
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Noticeboard whereIsActive($value)
 */
class Noticeboard extends Model
{
    use HasFactory;
    const STATUS = [
        1 => 'Active',
        0 => 'Deactive',
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'title' => 'required',
    ];

    public $table = 'noticeboards';

    public $fillable = [
        'title',
        'description',
        'is_active',
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'title' => 'string',
        'description' => 'string',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'is_urgent' => 'boolean',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Scope for active notices.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for inactive notices.
     */
    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    /**
     * Scope for featured notices.
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope for non-featured notices.
     */
    public function scopeNotFeatured($query)
    {
        return $query->where('is_featured', false);
    }

    /**
     * Scope for urgent notices.
     */
    public function scopeUrgent($query)
    {
        return $query->where('is_urgent', true);
    }

    /**
     * Scope for non-urgent notices.
     */
    public function scopeNotUrgent($query)
    {
        return $query->where('is_urgent', false);
    }

    /**
     * Scope for current notices (within date range).
     */
    public function scopeCurrent($query)
    {
        $now = now();
        return $query->where(function ($query) use ($now) {
            $query->where('start_date', '<=', $now)
                  ->where(function ($q) use ($now) {
                      $q->whereNull('end_date')
                        ->orWhere('end_date', '>=', $now);
                  });
        });
    }

    /**
     * Scope for expired notices.
     */
    public function scopeExpired($query)
    {
        return $query->whereNotNull('end_date')
                    ->where('end_date', '<', now());
    }

    /**
     * Scope for future notices.
     */
    public function scopeFuture($query)
    {
        return $query->where('start_date', '>', now());
    }

    /**
     * Scope for searching notices.
     */
    public function scopeSearch($query, string $term)
    {
        return $query->where('title', 'like', "%{$term}%")
                    ->orWhere('description', 'like', "%{$term}%");
    }

    /**
     * Scope for recent notices.
     */
    public function scopeRecent($query, int $days = 7)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Scope for old notices.
     */
    public function scopeOld($query, int $days = 30)
    {
        return $query->where('created_at', '<', now()->subDays($days));
    }

    /**
     * Scope for notices with end dates.
     */
    public function scopeWithEndDate($query)
    {
        return $query->whereNotNull('end_date');
    }

    /**
     * Scope for notices without end dates (permanent).
     */
    public function scopePermanent($query)
    {
        return $query->whereNull('end_date');
    }

    /**
     * Scope for priority notices (urgent and featured).
     */
    public function scopePriority($query)
    {
        return $query->where('is_urgent', true)->orWhere('is_featured', true);
    }

    /**
     * Scope for latest notices.
     */
    public function scopeLatest($query, int $limit = 5)
    {
        return $query->orderBy('created_at', 'desc')->limit($limit);
    }
}
