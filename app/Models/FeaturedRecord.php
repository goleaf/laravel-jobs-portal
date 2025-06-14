<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\FeaturedRecord
 *
 * @property int $id
 * @property int $owner_id
 * @property string $owner_type
 * @property int $user_id
 * @property string|null $stripe_id
 * @property string $start_time
 * @property string $end_time
 * @property string|null $meta
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $user
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FeaturedRecord newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FeaturedRecord newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FeaturedRecord query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FeaturedRecord whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FeaturedRecord whereEndTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FeaturedRecord whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FeaturedRecord whereMeta($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FeaturedRecord whereOwnerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FeaturedRecord whereOwnerType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FeaturedRecord whereStartTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FeaturedRecord whereStripeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FeaturedRecord whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FeaturedRecord whereUserId($value)
 *
 * @mixin \Eloquent
 */

    use Illuminate\Database\Eloquent\Factories\HasFactory;

    public $table = 'featured_records';

    public $fillable = [
        'owner_id',
        'owner_type',
        'user_id',
        'stripe_id',
        'start_time',
        'end_time',
        'meta',
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
        'owner_id' => 'integer',
        'owner_type' => 'string',
        'user_id' => 'integer',
        'stripe_id' => 'string',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'meta' => 'string',
    
        ];
    }


    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
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
