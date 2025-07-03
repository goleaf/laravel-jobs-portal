<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * Notification Model - Enhanced with Enhanced patterns.
 *
 * @property int $id
 * @property int $type
 * @property int $notification_for
 * @property int $user_id
 * @property string $title
 * @property null|string $text
 * @property null|string $data
 * @property null|string $action_url
 * @property null|string $icon
 * @property null|string $read_at
 * @property bool $is_read
 * @property bool $is_important
 * @property null|string $category
 * @property null|Carbon $created_at
 * @property null|Carbon $updated_at
 * @property null|Carbon $deleted_at
 * @property User $user
 * @property string $type_label
 * @property string $category_label
 * @property string $time_ago
 * @property bool $is_recent
 * @property array $parsed_data
 *
 * Enhanced Enhanced Scopes:
 *
 * @method static \Illuminate\Database\Eloquent\Builder read()
 * @method static \Illuminate\Database\Eloquent\Builder unread()
 * @method static \Illuminate\Database\Eloquent\Builder important()
 * @method static \Illuminate\Database\Eloquent\Builder normal()
 * @method static \Illuminate\Database\Eloquent\Builder byUser(int $userId)
 * @method static \Illuminate\Database\Eloquent\Builder byType(int $type)
 * @method static \Illuminate\Database\Eloquent\Builder byCategory(string $category)
 * @method static \Illuminate\Database\Eloquent\Builder search(string $term)
 * @method static \Illuminate\Database\Eloquent\Builder recent(int $days = 7)
 * @method static \Illuminate\Database\Eloquent\Builder old(int $days = 30)
 * @method static \Illuminate\Database\Eloquent\Builder today()
 * @method static \Illuminate\Database\Eloquent\Builder thisWeek()
 * @method static \Illuminate\Database\Eloquent\Builder thisMonth()
 * @method static \Illuminate\Database\Eloquent\Builder job()
 * @method static \Illuminate\Database\Eloquent\Builder application()
 * @method static \Illuminate\Database\Eloquent\Builder company()
 * @method static \Illuminate\Database\Eloquent\Builder system()
 * @method static \Illuminate\Database\Eloquent\Builder marketing()
 * @method static \Illuminate\Database\Eloquent\Builder security()
 * @method static \Illuminate\Database\Eloquent\Builder latest()
 * @method static \Illuminate\Database\Eloquent\Builder oldest()
 *
 * @mixin \Eloquent
 */
class Notification extends Model
{
    use HasFactory;
    use LogsActivity;
    use SoftDeletes;

    // =============================================
    // CONSTANTS
    // =============================================

    public const TYPE_JOB_APPLICATION = 1;
    public const TYPE_JOB_APPROVED = 2;
    public const TYPE_JOB_REJECTED = 3;
    public const TYPE_COMPANY_APPROVED = 4;
    public const TYPE_COMPANY_REJECTED = 5;
    public const TYPE_PROFILE_UPDATE = 6;
    public const TYPE_SYSTEM_NOTIFICATION = 7;
    public const TYPE_MARKETING = 8;
    public const TYPE_SECURITY = 9;
    public const TYPE_REMINDER = 10;

    public const TYPES = [
        self::TYPE_JOB_APPLICATION => 'Job Application',
        self::TYPE_JOB_APPROVED => 'Job Approved',
        self::TYPE_JOB_REJECTED => 'Job Rejected',
        self::TYPE_COMPANY_APPROVED => 'Company Approved',
        self::TYPE_COMPANY_REJECTED => 'Company Rejected',
        self::TYPE_PROFILE_UPDATE => 'Profile Update',
        self::TYPE_SYSTEM_NOTIFICATION => 'System Notification',
        self::TYPE_MARKETING => 'Marketing',
        self::TYPE_SECURITY => 'Security Alert',
        self::TYPE_REMINDER => 'Reminder',
    ];

    public const CATEGORIES = [
        'job' => 'Job Related',
        'application' => 'Application Related',
        'company' => 'Company Related',
        'system' => 'System Notifications',
        'marketing' => 'Marketing',
        'security' => 'Security',
        'reminder' => 'Reminders',
        'update' => 'Updates',
        'other' => 'Other',
    ];

    public const NOTIFICATION_FOR_CANDIDATE = 1;
    public const NOTIFICATION_FOR_COMPANY = 2;
    public const NOTIFICATION_FOR_ADMIN = 3;

    /**
     * Validation rules for creating notifications.
     *
     * @var array<string, string>
     */
    public static array $rules = [
        'type' => 'required|integer|min:1',
        'notification_for' => 'required|integer|min:1',
        'user_id' => 'required|integer|exists:users,id',
        'title' => 'required|string|max:255',
        'text' => 'nullable|string',
        'data' => 'nullable|string',
        'action_url' => 'nullable|string|max:500',
        'icon' => 'nullable|string|max:100',
        'is_read' => 'boolean',
        'is_important' => 'boolean',
        'category' => 'nullable|string|max:100',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'type',
        'notification_for',
        'user_id',
        'title',
        'text',
        'data',
        'action_url',
        'icon',
        'read_at',
        'is_read',
        'is_important',
        'category',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'deleted_at',
    ];

    /**
     * Configure activity logging.
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'type',
                'notification_for',
                'user_id',
                'title',
                'text',
                'data',
                'action_url',
                'icon',
                'read_at',
                'is_read',
                'is_important',
                'category',
            ])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    /**
     * Update validation rules for notifications.
     *
     * @return array<string, string>
     */
    public static function updateRules(int $id): array
    {
        return [
            'type' => 'required|integer|min:1',
            'notification_for' => 'required|integer|min:1',
            'user_id' => 'required|integer|exists:users,id',
            'title' => 'required|string|max:255',
            'text' => 'nullable|string',
            'data' => 'nullable|string',
            'action_url' => 'nullable|string|max:500',
            'icon' => 'nullable|string|max:100',
            'is_read' => 'boolean',
            'is_important' => 'boolean',
            'category' => 'nullable|string|max:100',
        ];
    }

    // =============================================
    // RELATIONSHIPS
    // =============================================

    /**
     * Get the user that owns the notification.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // =============================================
    // SCOPES - Basic Status
    // =============================================

    /**
     * Scope a query to only include read notifications.
     *
     * @param  mixed  $query
     */
    public function scopeRead($query)
    {
        return $query->where('is_read', true);
    }

    /**
     * Scope a query to only include unread notifications.
     *
     * @param  mixed  $query
     */
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    /**
     * Scope a query to only include important notifications.
     *
     * @param  mixed  $query
     */
    public function scopeImportant($query)
    {
        return $query->where('is_important', true);
    }

    /**
     * Scope a query to only include normal notifications.
     *
     * @param  mixed  $query
     */
    public function scopeNormal($query)
    {
        return $query->where('is_important', false);
    }

    // =============================================
    // SCOPES - Filtering
    // =============================================

    /**
     * Scope for notifications by user.
     *
     * @param  mixed  $query
     */
    public function scopeByUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope for notifications by type.
     *
     * @param  mixed  $query
     */
    public function scopeByType($query, int $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope for notifications by category.
     *
     * @param  mixed  $query
     */
    public function scopeByCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Scope for notifications by notification_for.
     *
     * @param  mixed  $query
     */
    public function scopeByNotificationFor($query, int $notificationFor)
    {
        return $query->where('notification_for', $notificationFor);
    }

    /**
     * Scope for searching notifications.
     *
     * @param  mixed  $query
     */
    public function scopeSearch($query, string $term)
    {
        return $query->where(function ($q) use ($term) {
            $q->where('title', 'like', "%{$term}%")
                ->orWhere('text', 'like', "%{$term}%");
        });
    }

    // =============================================
    // SCOPES - Time Based
    // =============================================

    /**
     * Scope for recent notifications.
     *
     * @param  mixed  $query
     */
    public function scopeRecent($query, int $days = 7)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Scope for old notifications.
     *
     * @param  mixed  $query
     */
    public function scopeOld($query, int $days = 30)
    {
        return $query->where('created_at', '<', now()->subDays($days));
    }

    /**
     * Scope for today's notifications.
     *
     * @param  mixed  $query
     */
    public function scopeToday($query)
    {
        return $query->whereDate('created_at', today());
    }

    /**
     * Scope for this week's notifications.
     *
     * @param  mixed  $query
     */
    public function scopeThisWeek($query)
    {
        return $query->whereBetween('created_at', [
            now()->startOfWeek(),
            now()->endOfWeek(),
        ]);
    }

    /**
     * Scope for this month's notifications.
     *
     * @param  mixed  $query
     */
    public function scopeThisMonth($query)
    {
        return $query->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year);
    }

    // =============================================
    // SCOPES - Category Specific
    // =============================================

    /**
     * Scope for job-related notifications.
     *
     * @param  mixed  $query
     */
    public function scopeJob($query)
    {
        return $query->where(function ($q) {
            $q->where('category', 'job')
                ->orWhereIn('type', [
                    self::TYPE_JOB_APPLICATION,
                    self::TYPE_JOB_APPROVED,
                    self::TYPE_JOB_REJECTED,
                ]);
        });
    }

    /**
     * Scope for application-related notifications.
     *
     * @param  mixed  $query
     */
    public function scopeApplication($query)
    {
        return $query->where(function ($q) {
            $q->where('category', 'application')
                ->orWhere('type', self::TYPE_JOB_APPLICATION);
        });
    }

    /**
     * Scope for company-related notifications.
     *
     * @param  mixed  $query
     */
    public function scopeCompany($query)
    {
        return $query->where(function ($q) {
            $q->where('category', 'company')
                ->orWhereIn('type', [
                    self::TYPE_COMPANY_APPROVED,
                    self::TYPE_COMPANY_REJECTED,
                ]);
        });
    }

    /**
     * Scope for system notifications.
     *
     * @param  mixed  $query
     */
    public function scopeSystem($query)
    {
        return $query->where(function ($q) {
            $q->where('category', 'system')
                ->orWhere('type', self::TYPE_SYSTEM_NOTIFICATION);
        });
    }

    /**
     * Scope for marketing notifications.
     *
     * @param  mixed  $query
     */
    public function scopeMarketing($query)
    {
        return $query->where(function ($q) {
            $q->where('category', 'marketing')
                ->orWhere('type', self::TYPE_MARKETING);
        });
    }

    /**
     * Scope for security notifications.
     *
     * @param  mixed  $query
     */
    public function scopeSecurity($query)
    {
        return $query->where(function ($q) {
            $q->where('category', 'security')
                ->orWhere('type', self::TYPE_SECURITY);
        });
    }

    // =============================================
    // SCOPES - Ordering
    // =============================================

    /**
     * Scope for latest notifications.
     *
     * @param  mixed  $query
     */
    public function scopeLatest($query)
    {
        return $query->orderBy('created_at', 'desc');
    }

    /**
     * Scope for oldest notifications.
     *
     * @param  mixed  $query
     */
    public function scopeOldest($query)
    {
        return $query->orderBy('created_at', 'asc');
    }

    // =============================================
    // CACHED METHODS
    // =============================================

    /**
     * Get cached unread count for user.
     */
    public static function getCachedUnreadCount(int $userId): int
    {
        return Cache::remember(
            "notifications_unread_count_{$userId}",
            now()->addMinutes(5),
            fn () => static::unread()
                ->byUser($userId)
                ->count()
        );
    }

    /**
     * Get cached recent notifications for user.
     */
    public static function getCachedRecent(int $userId, int $limit = 10): Collection
    {
        return Cache::remember(
            "notifications_recent_{$userId}_{$limit}",
            now()->addMinutes(10),
            fn () => static::byUser($userId)
                ->with('user')
                ->latest()
                ->limit($limit)
                ->get()
        );
    }

    /**
     * Get cached important notifications for user.
     */
    public static function getCachedImportant(int $userId): Collection
    {
        return Cache::remember(
            "notifications_important_{$userId}",
            now()->addMinutes(15),
            fn () => static::byUser($userId)
                ->important()
                ->unread()
                ->latest()
                ->get()
        );
    }

    // =============================================
    // HELPER METHODS & ATTRIBUTES
    // =============================================

    /**
     * Get type label attribute.
     */
    public function getTypeLabelAttribute(): string
    {
        return self::TYPES[$this->type] ?? 'Unknown';
    }

    /**
     * Get category label attribute.
     */
    public function getCategoryLabelAttribute(): string
    {
        return self::CATEGORIES[$this->category] ?? ucwords($this->category ?? 'Other');
    }

    /**
     * Get time ago attribute.
     */
    public function getTimeAgoAttribute(): string
    {
        return $this->created_at->diffForHumans();
    }

    /**
     * Get is recent attribute.
     */
    public function getIsRecentAttribute(): bool
    {
        return $this->created_at->isAfter(now()->subHours(24));
    }

    /**
     * Get parsed data attribute.
     */
    public function getParsedDataAttribute(): array
    {
        if (! $this->data) {
            return [];
        }

        return json_decode($this->data, true) ?? [];
    }

    /**
     * Mark notification as read.
     */
    public function markAsRead(): bool
    {
        if ($this->is_read) {
            return true;
        }

        $this->update([
            'is_read' => true,
            'read_at' => now(),
        ]);

        $this->clearUserCaches();

        return true;
    }

    /**
     * Mark notification as unread.
     */
    public function markAsUnread(): bool
    {
        if (! $this->is_read) {
            return true;
        }

        $this->update([
            'is_read' => false,
            'read_at' => null,
        ]);

        $this->clearUserCaches();

        return true;
    }

    /**
     * Check if notification is for candidate.
     */
    public function isForCandidate(): bool
    {
        return $this->notification_for === self::NOTIFICATION_FOR_CANDIDATE;
    }

    /**
     * Check if notification is for company.
     */
    public function isForCompany(): bool
    {
        return $this->notification_for === self::NOTIFICATION_FOR_COMPANY;
    }

    /**
     * Check if notification is for admin.
     */
    public function isForAdmin(): bool
    {
        return $this->notification_for === self::NOTIFICATION_FOR_ADMIN;
    }

    /**
     * Set notification data.
     */
    public function setData(array $data): void
    {
        $this->data = json_encode($data);
    }

    /**
     * Clear user-related caches.
     */
    public function clearUserCaches(): void
    {
        $cacheKeys = [
            "notifications_unread_count_{$this->user_id}",
            "notifications_recent_{$this->user_id}_10",
            "notifications_important_{$this->user_id}",
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
            'is_read' => 'boolean',
            'is_important' => 'boolean',
            'read_at' => 'datetime',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::saved(function ($notification) {
            $notification->clearUserCaches();
        });

        static::deleted(function ($notification) {
            $notification->clearUserCaches();
        });

        static::restored(function ($notification) {
            $notification->clearUserCaches();
        });
    }
}
