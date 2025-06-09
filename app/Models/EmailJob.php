<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * App\Models\EmailJob
 *
 * @property int $id
 * @property int $user_id
 * @property string $job_url
 * @property string $friend_name
 * @property string $friend_email
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\EmailJob newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\EmailJob newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\EmailJob query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\EmailJob whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\EmailJob whereFriendEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\EmailJob whereFriendName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\EmailJob whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\EmailJob whereJobUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\EmailJob whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\EmailJob whereUserId($value)
 *
 * @mixin \Eloquent
 *
 * @property int $job_id
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\EmailJob whereJobId($value)
 */

    use Illuminate\Database\Eloquent\Factories\HasFactory;

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'friend_name' => 'required',
        'friend_email' => 'required|email:filter',
    ];

    public $table = 'email_jobs';

    public $fillable = [
        'user_id', 'job_id', 'job_url', 'friend_name', 'friend_email',
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
        'user_id' => 'integer',
        'job_id' => 'integer',
        'job_url' => 'string',
        'friend_name' => 'string',
        'friend_email' => 'string',
    
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
