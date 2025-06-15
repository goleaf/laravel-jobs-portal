<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * SocialAccount Model - Enhanced with Enhanced patterns.
 *
 * @property int         $id
 * @property int         $user_id
 * @property string      $provider
 * @property string      $provider_id
 * @property null|string $name
 * @property null|string $email
 * @property null|string $avatar
 * @property null|array  $provider_data
 * @property bool        $is_active
 * @property null|Carbon $last_used_at
 * @property null|Carbon $created_at
 * @property null|Carbon $updated_at
 * @property null|Carbon $deleted_at
 * @property User        $user
 * @property string      $provider_label
 * @property string      $provider_icon
 * @property bool        $is_recent
 * @property bool        $has_avatar
 * @property bool        $is_verified
 *
 * Enhanced Enhanced Scopes:
 *
 * @method static Builder active()
 * @method static Builder inactive()
 * @method static Builder byProvider(string $provider)
 * @method static Builder byUser(int $userId)
 * @method static Builder recent(int $days = 30)
 * @method static Builder recentlyUsed(int $days = 7)
 * @method static Builder withAvatar()
 * @method static Builder withoutAvatar()
 * @method static Builder verified()
 * @method static Builder unverified()
 * @method static Builder search(string $term)
 * @method static Builder orderByCreated(string $direction = 'desc')
 * @method static Builder orderByUsed(string $direction = 'desc')
 * @method static Builder popular()
 * @method static Builder facebook()
 * @method static Builder google()
 * @method static Builder twitter()
 * @method static Builder linkedin()
 * @method static Builder github()
 *
 * @mixin \Eloquent
 */
class SocialAccount extends Model
{
    use HasFactory;
    use SoftDeletes;
    use LogsActivity;

    /**
     * Provider constants.
     */
    public const PROVIDER_FACEBOOK = 'facebook';
    public const PROVIDER_GOOGLE = 'google';
    public const PROVIDER_TWITTER = 'twitter';
    public const PROVIDER_LINKEDIN = 'linkedin';
    public const PROVIDER_GITHUB = 'github';

    /**
     * Available providers.
     */
    public const AVAILABLE_PROVIDERS = [
        self::PROVIDER_FACEBOOK,
        self::PROVIDER_GOOGLE,
        self::PROVIDER_TWITTER,
        self::PROVIDER_LINKEDIN,
        self::PROVIDER_GITHUB,
    ];

    /**
     * Validation rules.
     */
    public static array $rules = [
        'user_id' => 'required|integer|exists:users,id',
        'provider' => 'required|string|in:facebook,google,twitter,linkedin,github',
        'provider_id' => 'required|string|max:255',
        'name' => 'nullable|string|max:255',
        'email' => 'nullable|email|max:255',
        'avatar' => 'nullable|url|max:500',
        'provider_data' => 'nullable|array',
        'is_active' => 'boolean',
        'last_used_at' => 'nullable|date',
    ];

    /**
     * The table associated with the model.
     */
    protected $table = 'social_accounts';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'user_id',
        'provider',
        'provider_id',
        'name',
        'email',
        'avatar',
        'provider_data',
        'is_active',
        'last_used_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     */
    protected $hidden = [
        'provider_data',
        'deleted_at',
    ];

    /**
     * Activity log configuration.
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['provider', 'is_active', 'last_used_at'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn (string $eventName) => "Social account has been {$eventName}")
        ;
    }

    // =============================================
    // RELATIONSHIPS
    // =============================================

    /**
     * Get the user that owns the social account.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // =============================================
    // SCOPES - Basic Status
    // =============================================

    /**
     * Scope for active social accounts.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for inactive social accounts.
     */
    public function scopeInactive(Builder $query): Builder
    {
        return $query->where('is_active', false);
    }

    // =============================================
    // SCOPES - Provider-based
    // =============================================

    /**
     * Scope for accounts by provider.
     */
    public function scopeByProvider(Builder $query, string $provider): Builder
    {
        return $query->where('provider', $provider);
    }

    /**
     * Scope for Facebook accounts.
     */
    public function scopeFacebook(Builder $query): Builder
    {
        return $query->where('provider', self::PROVIDER_FACEBOOK);
    }

    /**
     * Scope for Google accounts.
     */
    public function scopeGoogle(Builder $query): Builder
    {
        return $query->where('provider', self::PROVIDER_GOOGLE);
    }

    /**
     * Scope for Twitter accounts.
     */
    public function scopeTwitter(Builder $query): Builder
    {
        return $query->where('provider', self::PROVIDER_TWITTER);
    }

    /**
     * Scope for LinkedIn accounts.
     */
    public function scopeLinkedin(Builder $query): Builder
    {
        return $query->where('provider', self::PROVIDER_LINKEDIN);
    }

    /**
     * Scope for GitHub accounts.
     */
    public function scopeGithub(Builder $query): Builder
    {
        return $query->where('provider', self::PROVIDER_GITHUB);
    }

    // =============================================
    // SCOPES - User-based
    // =============================================

    /**
     * Scope for accounts by user.
     */
    public function scopeByUser(Builder $query, int $userId): Builder
    {
        return $query->where('user_id', $userId);
    }

    // =============================================
    // SCOPES - Date-based
    // =============================================

    /**
     * Scope for recent accounts.
     */
    public function scopeRecent(Builder $query, int $days = 30): Builder
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Scope for recently used accounts.
     */
    public function scopeRecentlyUsed(Builder $query, int $days = 7): Builder
    {
        return $query->where('last_used_at', '>=', now()->subDays($days));
    }

    // =============================================
    // SCOPES - Feature-based
    // =============================================

    /**
     * Scope for accounts with avatar.
     */
    public function scopeWithAvatar(Builder $query): Builder
    {
        return $query->whereNotNull('avatar');
    }

    /**
     * Scope for accounts without avatar.
     */
    public function scopeWithoutAvatar(Builder $query): Builder
    {
        return $query->whereNull('avatar');
    }

    /**
     * Scope for verified accounts.
     */
    public function scopeVerified(Builder $query): Builder
    {
        return $query->whereNotNull('email');
    }

    /**
     * Scope for unverified accounts.
     */
    public function scopeUnverified(Builder $query): Builder
    {
        return $query->whereNull('email');
    }

    // =============================================
    // SCOPES - Search & Filtering
    // =============================================

    /**
     * Scope for searching accounts.
     */
    public function scopeSearch(Builder $query, string $term): Builder
    {
        return $query->where(function ($q) use ($term) {
            $q->where('name', 'like', '%'.$term.'%')
                ->orWhere('email', 'like', '%'.$term.'%')
                ->orWhere('provider', 'like', '%'.$term.'%')
                ->orWhereHas('user', function ($userQuery) use ($term) {
                    $userQuery->where('first_name', 'like', '%'.$term.'%')
                        ->orWhere('last_name', 'like', '%'.$term.'%')
                        ->orWhere('email', 'like', '%'.$term.'%')
                    ;
                })
            ;
        });
    }

    // =============================================
    // SCOPES - Ordering
    // =============================================

    /**
     * Scope for ordering by creation date.
     */
    public function scopeOrderByCreated(Builder $query, string $direction = 'desc'): Builder
    {
        return $query->orderBy('created_at', $direction);
    }

    /**
     * Scope for ordering by last used date.
     */
    public function scopeOrderByUsed(Builder $query, string $direction = 'desc'): Builder
    {
        return $query->orderBy('last_used_at', $direction);
    }

    /**
     * Scope for popular providers.
     */
    public function scopePopular(Builder $query): Builder
    {
        return $query->select('provider')
            ->selectRaw('COUNT(*) as count')
            ->groupBy('provider')
            ->orderByDesc('count')
        ;
    }

    // =============================================
    // ATTRIBUTE ACCESSORS
    // =============================================

    /**
     * Get provider label attribute.
     */
    public function getProviderLabelAttribute(): string
    {
        return match ($this->provider) {
            self::PROVIDER_FACEBOOK => 'Facebook',
            self::PROVIDER_GOOGLE => 'Google',
            self::PROVIDER_TWITTER => 'Twitter',
            self::PROVIDER_LINKEDIN => 'LinkedIn',
            self::PROVIDER_GITHUB => 'GitHub',
            default => ucfirst($this->provider),
        };
    }

    /**
     * Get provider icon attribute.
     */
    public function getProviderIconAttribute(): string
    {
        return match ($this->provider) {
            self::PROVIDER_FACEBOOK => 'fab fa-facebook',
            self::PROVIDER_GOOGLE => 'fab fa-google',
            self::PROVIDER_TWITTER => 'fab fa-twitter',
            self::PROVIDER_LINKEDIN => 'fab fa-linkedin',
            self::PROVIDER_GITHUB => 'fab fa-github',
            default => 'fas fa-link',
        };
    }

    /**
     * Check if account is recent.
     */
    public function getIsRecentAttribute(): bool
    {
        return $this->created_at && $this->created_at->isAfter(now()->subDays(7));
    }

    /**
     * Check if account has avatar.
     */
    public function getHasAvatarAttribute(): bool
    {
        return !empty($this->avatar);
    }

    /**
     * Check if account is verified.
     */
    public function getIsVerifiedAttribute(): bool
    {
        return !empty($this->email);
    }

    // =============================================
    // HELPER METHODS
    // =============================================

    /**
     * Check if account is active.
     */
    public function isActive(): bool
    {
        return $this->is_active;
    }

    /**
     * Check if account has been used recently.
     */
    public function isRecentlyUsed(int $days = 7): bool
    {
        return $this->last_used_at && $this->last_used_at->isAfter(now()->subDays($days));
    }

    /**
     * Mark account as used.
     */
    public function markAsUsed(): bool
    {
        return $this->update(['last_used_at' => now()]);
    }

    /**
     * Get Facebook fields for API requests.
     */
    public static function facebookFields(): array
    {
        return [
            'first_name',
            'email',
            'gender',
            'id',
            'last_name',
            'name',
            'location',
            'verified',
            'birthday',
            'link',
            'locale',
        ];
    }

    /**
     * Get provider statistics.
     */
    public static function getProviderStats(): array
    {
        return Cache::remember('social_accounts.provider_stats', 3600, function () {
            return self::selectRaw('provider, COUNT(*) as count')
                ->groupBy('provider')
                ->pluck('count', 'provider')
                ->toArray()
            ;
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
            'social_accounts.provider_stats',
            'social_accounts.active',
            'social_accounts.recent',
        ];

        foreach ($cacheKeys as $key) {
            Cache::forget($key);
        }

        // Clear user-specific caches
        Cache::forget("user.{$this->user_id}.social_accounts");
    }

    /**
     * Get the attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'id' => 'integer',
            'user_id' => 'integer',
            'provider_data' => 'array',
            'is_active' => 'boolean',
            'last_used_at' => 'datetime',
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
