<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\NewsLetter
 *
 * @property int $id
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\NewsLetter newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\NewsLetter newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\NewsLetter query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\NewsLetter whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\NewsLetter whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\NewsLetter whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\NewsLetter whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class NewsLetter extends Model
{
    use HasFactory;
    public $table = 'news_letters';

    public $fillable = [
        'email',
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'email' => 'string',
        'name' => 'string',
        'is_active' => 'boolean',
        'is_verified' => 'boolean',
        'subscribed_at' => 'datetime',
        'unsubscribed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'email' => 'required|email:filter|unique:news_letters,email',
    ];

    /**
     * Scope for active subscribers.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for inactive subscribers.
     */
    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    /**
     * Scope for verified subscribers.
     */
    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    /**
     * Scope for unverified subscribers.
     */
    public function scopeUnverified($query)
    {
        return $query->where('is_verified', false);
    }

    /**
     * Scope for subscribed users.
     */
    public function scopeSubscribed($query)
    {
        return $query->whereNotNull('subscribed_at')->whereNull('unsubscribed_at');
    }

    /**
     * Scope for unsubscribed users.
     */
    public function scopeUnsubscribed($query)
    {
        return $query->whereNotNull('unsubscribed_at');
    }

    /**
     * Scope for searching subscribers.
     */
    public function scopeSearch($query, string $term)
    {
        return $query->where('email', 'like', "%{$term}%")
                    ->orWhere('name', 'like', "%{$term}%");
    }

    /**
     * Scope for recent subscribers.
     */
    public function scopeRecent($query, int $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Scope for old subscribers.
     */
    public function scopeOld($query, int $days = 365)
    {
        return $query->where('created_at', '<', now()->subDays($days));
    }

    /**
     * Scope for subscribers by email domain.
     */
    public function scopeByDomain($query, string $domain)
    {
        return $query->where('email', 'like', "%@{$domain}");
    }

    /**
     * Scope for active verified subscribers (ready for emails).
     */
    public function scopeReadyForEmail($query)
    {
        return $query->active()->verified()->subscribed();
    }

    /**
     * Scope for subscribers who joined this month.
     */
    public function scopeThisMonth($query)
    {
        return $query->whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year);
    }

    /**
     * Scope for alphabetical ordering by email.
     */
    public function scopeAlphabetical($query)
    {
        return $query->orderBy('email', 'asc');
    }

    /**
     * Scope for latest subscribers.
     */
    public function scopeLatest($query, int $limit = 10)
    {
        return $query->orderBy('created_at', 'desc')->limit($limit);
    }
}
