<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * App\Models\FavouriteCompany
 *
 * @property int $id
 * @property int $user_id
 * @property int company_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FavouriteJob newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FavouriteJob newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FavouriteJob query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FavouriteJob whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FavouriteJob whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FavouriteJob whereCompanyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FavouriteJob whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FavouriteJob whereUserId($value)
 *
 * @mixin \Eloquent
 *
 * @property-read \App\Models\Company $company
 * @property-read \App\Models\User $user
 */

    use Illuminate\Database\Eloquent\Factories\HasFactory;

    public $table = 'favourite_companies';

    public $fillable = [
        'user_id',
        'company_id',
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

        'user_id' => 'integer',
        'company_id' => 'integer',
    
        ];
    }


    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id')->with(['media']);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'company_id');
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
