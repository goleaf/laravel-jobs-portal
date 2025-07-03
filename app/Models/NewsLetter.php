<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * NewsLetter Model - Enhanced with Enhanced patterns.
 *
 * @property int $id
 * @property string $email
 * @property null|string $name
 * @property bool $is_active
 * @property bool $is_verified
 * @property null|string $verification_token
 * @property null|string $unsubscribe_token
 * @property null|Carbon $subscribed_at
 * @property null|Carbon $unsubscribed_at
 * @property null|Carbon $verified_at
 * @property null|Carbon $last_email_sent_at
 * @property null|int $emails_sent_count
 * @property null|array $preferences
 * @property null|string $source
 * @property null|Carbon $created_at
 * @property null|Carbon $updated_at
 * @property null|Carbon $deleted_at
 * @property bool $is_subscribed
 * @property bool $is_unsubscribed
 * @property bool $is_recent
 * @property string $status
 * @property string $domain
 * @property int $days_since_subscription
 *
 * Enhanced Enhanced Scopes:
 *
 * @method static Builder active()
 * @method static Builder inactive()
 * @method static Builder verified()
 * @method static Builder unverified()
 * @method static Builder subscribed()
 * @method static Builder unsubscribed()
 * @method static Builder readyForEmail()
 * @method static Builder recent(int $days = 30)
 * @method static Builder old(int $days = 365)
 * @method static Builder thisMonth()
 * @method static Builder thisWeek()
 * @method static Builder today()
 * @method static Builder byDomain(string $domain)
 * @method static Builder bySource(string $source)
 * @method static Builder search(string $term)
 * @method static Builder alphabetical()
 * @method static Builder latest()
 * @method static Builder oldest()
 * @method static Builder popular()
 * @method static Builder engaged()
 * @method static Builder dormant(int $days = 90)
 *
 * @mixin \Eloquent
 */
class NewsLetter extends Model
{
    use HasFactory;
    use LogsActivity;
    use SoftDeletes;

    /**
     * Source constants.
     */
    public const SOURCE_WEBSITE = 'website';
    public const SOURCE_POPUP = 'popup';
    public const SOURCE_FOOTER = 'footer';
    public const SOURCE_LANDING_PAGE = 'landing_page';
    public const SOURCE_SOCIAL_MEDIA = 'social_media';
    public const SOURCE_MANUAL = 'manual';
    public const SOURCE_IMPORT = 'import';

    /**
     * Status constants.
     */
    public const STATUS_PENDING = 'pending';
    public const STATUS_VERIFIED = 'verified';
    public const STATUS_UNSUBSCRIBED = 'unsubscribed';
    public const STATUS_BOUNCED = 'bounced';

    /**
     * Validation rules.
     */
    public static array $rules = [
        'email' => 'required|email:filter|unique:news_letters,email',
        'name' => 'nullable|string|max:255',
        'is_active' => 'boolean',
        'is_verified' => 'boolean',
        'verification_token' => 'nullable|string|max:255',
        'unsubscribe_token' => 'nullable|string|max:255',
        'subscribed_at' => 'nullable|date',
        'unsubscribed_at' => 'nullable|date',
        'verified_at' => 'nullable|date',
        'last_email_sent_at' => 'nullable|date',
        'emails_sent_count' => 'nullable|integer|min:0',
        'preferences' => 'nullable|array',
        'source' => 'nullable|string|max:50',
    ];

    /**
     * The table associated with the model.
     */
    protected $table = 'news_letters';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'email',
        'name',
        'is_active',
        'is_verified',
        'verification_token',
        'unsubscribe_token',
        'subscribed_at',
        'unsubscribed_at',
        'verified_at',
        'last_email_sent_at',
        'emails_sent_count',
        'preferences',
        'source',
    ];

    /**
     * The attributes that should be hidden for serialization.
     */
    protected $hidden = [
        'verification_token',
        'unsubscribe_token',
        'deleted_at',
    ];

    /**
     * Activity log configuration.
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['email', 'name', 'is_active', 'is_verified', 'source'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn (string $eventName) => "Newsletter subscription has been {$eventName}");
    }

    // =============================================
    // SCOPES - Basic Status
    // =============================================

    /**
     * Scope for active subscribers.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for inactive subscribers.
     */
    public function scopeInactive(Builder $query): Builder
    {
        return $query->where('is_active', false);
    }

    /**
     * Scope for verified subscribers.
     */
    public function scopeVerified(Builder $query): Builder
    {
        return $query->where('is_verified', true);
    }

    /**
     * Scope for unverified subscribers.
     */
    public function scopeUnverified(Builder $query): Builder
    {
        return $query->where('is_verified', false);
    }

    /**
     * Scope for subscribed users.
     */
    public function scopeSubscribed(Builder $query): Builder
    {
        return $query->whereNotNull('subscribed_at')->whereNull('unsubscribed_at');
    }

    /**
     * Scope for unsubscribed users.
     */
    public function scopeUnsubscribed(Builder $query): Builder
    {
        return $query->whereNotNull('unsubscribed_at');
    }

    /**
     * Scope for active verified subscribers (ready for emails).
     */
    public function scopeReadyForEmail(Builder $query): Builder
    {
        return $query->active()->verified()->subscribed();
    }

    // =============================================
    // SCOPES - Date-based
    // =============================================

    /**
     * Scope for recent subscribers.
     */
    public function scopeRecent(Builder $query, int $days = 30): Builder
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Scope for old subscribers.
     */
    public function scopeOld(Builder $query, int $days = 365): Builder
    {
        return $query->where('created_at', '<', now()->subDays($days));
    }

    /**
     * Scope for subscribers who joined this month.
     */
    public function scopeThisMonth(Builder $query): Builder
    {
        return $query->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year);
    }

    /**
     * Scope for subscribers who joined this week.
     */
    public function scopeThisWeek(Builder $query): Builder
    {
        return $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
    }

    /**
     * Scope for subscribers who joined today.
     */
    public function scopeToday(Builder $query): Builder
    {
        return $query->whereDate('created_at', today());
    }

    // =============================================
    // SCOPES - Filtering
    // =============================================

    /**
     * Scope for subscribers by email domain.
     */
    public function scopeByDomain(Builder $query, string $domain): Builder
    {
        return $query->where('email', 'like', "%@{$domain}");
    }

    /**
     * Scope for subscribers by source.
     */
    public function scopeBySource(Builder $query, string $source): Builder
    {
        return $query->where('source', $source);
    }

    // =============================================
    // SCOPES - Engagement
    // =============================================

    /**
     * Scope for engaged subscribers (received emails recently).
     */
    public function scopeEngaged(Builder $query): Builder
    {
        return $query->where('last_email_sent_at', '>=', now()->subDays(30))
            ->where('emails_sent_count', '>', 0);
    }

    /**
     * Scope for dormant subscribers.
     */
    public function scopeDormant(Builder $query, int $days = 90): Builder
    {
        return $query->where(function ($q) use ($days) {
            $q->whereNull('last_email_sent_at')
                ->orWhere('last_email_sent_at', '<', now()->subDays($days));
        });
    }

    // =============================================
    // SCOPES - Search & Ordering
    // =============================================

    /**
     * Scope for searching subscribers.
     */
    public function scopeSearch(Builder $query, string $term): Builder
    {
        return $query->where('email', 'like', "%{$term}%")
            ->orWhere('name', 'like', "%{$term}%");
    }

    /**
     * Scope for alphabetical ordering by email.
     */
    public function scopeAlphabetical(Builder $query): Builder
    {
        return $query->orderBy('email', 'asc');
    }

    /**
     * Scope for latest subscribers.
     */
    public function scopeLatest(Builder $query): Builder
    {
        return $query->orderBy('created_at', 'desc');
    }

    /**
     * Scope for oldest subscribers.
     */
    public function scopeOldest(Builder $query): Builder
    {
        return $query->orderBy('created_at', 'asc');
    }

    /**
     * Scope for popular subscribers (most emails sent).
     */
    public function scopePopular(Builder $query): Builder
    {
        return $query->orderBy('emails_sent_count', 'desc');
    }

    // =============================================
    // ATTRIBUTE ACCESSORS
    // =============================================

    /**
     * Check if subscriber is subscribed.
     */
    public function getIsSubscribedAttribute(): bool
    {
        return ! is_null($this->subscribed_at) && is_null($this->unsubscribed_at);
    }

    /**
     * Check if subscriber is unsubscribed.
     */
    public function getIsUnsubscribedAttribute(): bool
    {
        return ! is_null($this->unsubscribed_at);
    }

    /**
     * Check if subscriber is recent.
     */
    public function getIsRecentAttribute(): bool
    {
        return $this->created_at && $this->created_at->isAfter(now()->subDays(7));
    }

    /**
     * Get subscriber status.
     */
    public function getStatusAttribute(): string
    {
        if ($this->is_unsubscribed) {
            return self::STATUS_UNSUBSCRIBED;
        }

        if ($this->is_verified) {
            return self::STATUS_VERIFIED;
        }

        return self::STATUS_PENDING;
    }

    /**
     * Get email domain.
     */
    public function getDomainAttribute(): string
    {
        return substr(strrchr($this->email, '@'), 1);
    }

    /**
     * Get days since subscription.
     */
    public function getDaysSinceSubscriptionAttribute(): int
    {
        if (! $this->subscribed_at) {
            return 0;
        }

        return $this->subscribed_at->diffInDays(now());
    }

    // =============================================
    // HELPER METHODS
    // =============================================

    /**
     * Check if subscriber is active.
     */
    public function isActive(): bool
    {
        return $this->is_active;
    }

    /**
     * Check if subscriber is verified.
     */
    public function isVerified(): bool
    {
        return $this->is_verified;
    }

    /**
     * Check if subscriber is subscribed.
     */
    public function isSubscribed(): bool
    {
        return $this->is_subscribed;
    }

    /**
     * Check if subscriber is ready for email.
     */
    public function isReadyForEmail(): bool
    {
        return $this->is_active && $this->is_verified && $this->is_subscribed;
    }

    /**
     * Verify subscriber.
     */
    public function verify(): bool
    {
        return $this->update([
            'is_verified' => true,
            'verified_at' => now(),
            'verification_token' => null,
        ]);
    }

    /**
     * Subscribe user.
     */
    public function subscribe(): bool
    {
        return $this->update([
            'is_active' => true,
            'subscribed_at' => now(),
            'unsubscribed_at' => null,
        ]);
    }

    /**
     * Unsubscribe user.
     */
    public function unsubscribe(): bool
    {
        return $this->update([
            'is_active' => false,
            'unsubscribed_at' => now(),
        ]);
    }

    /**
     * Record email sent.
     */
    public function recordEmailSent(): bool
    {
        return $this->update([
            'last_email_sent_at' => now(),
            'emails_sent_count' => ($this->emails_sent_count ?? 0) + 1,
        ]);
    }

    /**
     * Generate verification token.
     */
    public function generateVerificationToken(): string
    {
        $token = bin2hex(random_bytes(32));
        $this->update(['verification_token' => $token]);

        return $token;
    }

    /**
     * Generate unsubscribe token.
     */
    public function generateUnsubscribeToken(): string
    {
        $token = bin2hex(random_bytes(32));
        $this->update(['unsubscribe_token' => $token]);

        return $token;
    }

    /**
     * Get total subscribers count.
     */
    public static function getTotalSubscribersCount(): int
    {
        return Cache::remember('newsletter.total_subscribers', 3600, function () {
            return self::active()->verified()->subscribed()->count();
        });
    }

    /**
     * Get new subscribers count for period.
     */
    public static function getNewSubscribersCount(int $days = 30): int
    {
        return Cache::remember("newsletter.new_subscribers.{$days}days", 1800, function () use ($days) {
            return self::recent($days)->count();
        });
    }

    /**
     * Get subscribers by domain.
     */
    public static function getSubscribersByDomain(): array
    {
        return Cache::remember('newsletter.subscribers_by_domain', 3600, function () {
            return self::readyForEmail()
                ->selectRaw('SUBSTRING_INDEX(email, "@", -1) as domain, COUNT(*) as count')
                ->groupBy('domain')
                ->orderByDesc('count')
                ->limit(10)
                ->pluck('count', 'domain')
                ->toArray();
        });
    }

    // =============================================
    // CACHE MANAGEMENT
    // =============================================

    /**
     * Clear all related caches.
     */
    public function clearCaches(): void
    {
        $cacheKeys = [
            'newsletter.total_subscribers',
            'newsletter.subscribers_by_domain',
            'newsletter.new_subscribers.30days',
            'newsletter.new_subscribers.7days',
        ];

        foreach ($cacheKeys as $key) {
            Cache::forget($key);
        }
    }

    /**
     * Get the attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'id' => 'integer',
            'is_active' => 'boolean',
            'is_verified' => 'boolean',
            'subscribed_at' => 'datetime',
            'unsubscribed_at' => 'datetime',
            'verified_at' => 'datetime',
            'last_email_sent_at' => 'datetime',
            'emails_sent_count' => 'integer',
            'preferences' => 'array',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }

    // =============================================
    // BOOT METHOD
    // =============================================

    /**
     * Boot the model and register model events.
     */
    protected static function boot()
    {
        parent::boot();

        // Set default values
        static::creating(function ($model) {
            if (is_null($model->is_active)) {
                $model->is_active = true;
            }
            if (is_null($model->is_verified)) {
                $model->is_verified = false;
            }
            if (is_null($model->subscribed_at)) {
                $model->subscribed_at = now();
            }
            if (is_null($model->emails_sent_count)) {
                $model->emails_sent_count = 0;
            }
            if (is_null($model->source)) {
                $model->source = self::SOURCE_WEBSITE;
            }

            // Generate tokens
            if (is_null($model->verification_token)) {
                $model->verification_token = bin2hex(random_bytes(32));
            }
            if (is_null($model->unsubscribe_token)) {
                $model->unsubscribe_token = bin2hex(random_bytes(32));
            }
        });

        // Clear caches when model is modified
        static::saved(function ($model) {
            $model->clearCaches();
        });

        static::deleted(function ($model) {
            $model->clearCaches();
        });

        static::restored(function ($model) {
            $model->clearCaches();
        });
    }
}
