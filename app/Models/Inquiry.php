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
 * Inquiry Model - Enhanced with Enhanced patterns.
 *
 * @property int         $id
 * @property string      $name
 * @property string      $email
 * @property null|string $phone_no
 * @property string      $subject
 * @property string      $message
 * @property bool        $is_read
 * @property bool        $is_resolved
 * @property bool        $is_active
 * @property null|int    $priority
 * @property null|string $status
 * @property null|string $category
 * @property null|int    $assigned_to
 * @property null|Carbon $resolved_at
 * @property null|Carbon $created_at
 * @property null|Carbon $updated_at
 * @property null|Carbon $deleted_at
 * @property null|User   $assignedUser
 * @property bool        $is_recent
 * @property bool        $is_pending
 * @property bool        $is_high_priority
 * @property string      $status_label
 * @property string      $priority_label
 * @property string      $category_label
 *
 * Enhanced Enhanced Scopes:
 *
 * @method static Builder active()
 * @method static Builder inactive()
 * @method static Builder read()
 * @method static Builder unread()
 * @method static Builder resolved()
 * @method static Builder unresolved()
 * @method static Builder pending()
 * @method static Builder recent(int $days = 7)
 * @method static Builder old(int $days = 30)
 * @method static Builder byEmail(string $email)
 * @method static Builder byCategory(string $category)
 * @method static Builder byStatus(string $status)
 * @method static Builder byPriority(int $priority)
 * @method static Builder highPriority()
 * @method static Builder mediumPriority()
 * @method static Builder lowPriority()
 * @method static Builder urgent()
 * @method static Builder assignedTo(int $userId)
 * @method static Builder unassigned()
 * @method static Builder search(string $term)
 * @method static Builder latest()
 * @method static Builder oldest()
 * @method static Builder general()
 * @method static Builder technical()
 * @method static Builder billing()
 * @method static Builder support()
 *
 * @mixin \Eloquent
 */
class Inquiry extends Model
{
    use HasFactory;
    use SoftDeletes;
    use LogsActivity;

    /**
     * Status constants.
     */
    public const STATUS_PENDING = 'pending';
    public const STATUS_IN_PROGRESS = 'in_progress';
    public const STATUS_RESOLVED = 'resolved';
    public const STATUS_CLOSED = 'closed';

    /**
     * Priority constants.
     */
    public const PRIORITY_LOW = 1;
    public const PRIORITY_MEDIUM = 2;
    public const PRIORITY_HIGH = 3;
    public const PRIORITY_URGENT = 4;

    /**
     * Category constants.
     */
    public const CATEGORY_GENERAL = 'general';
    public const CATEGORY_TECHNICAL = 'technical';
    public const CATEGORY_BILLING = 'billing';
    public const CATEGORY_SUPPORT = 'support';
    public const CATEGORY_FEATURE_REQUEST = 'feature_request';
    public const CATEGORY_BUG_REPORT = 'bug_report';

    /**
     * Validation rules.
     */
    public static array $rules = [
        'name' => 'required|string|max:255',
        'email' => 'required|email|max:255',
        'phone_no' => 'nullable|string|max:20',
        'subject' => 'required|string|max:255',
        'message' => 'required|string|max:5000',
        'is_read' => 'boolean',
        'is_resolved' => 'boolean',
        'is_active' => 'boolean',
        'priority' => 'nullable|integer|min:1|max:4',
        'status' => 'nullable|string|in:pending,in_progress,resolved,closed',
        'category' => 'nullable|string|in:general,technical,billing,support,feature_request,bug_report',
        'assigned_to' => 'nullable|integer|exists:users,id',
        'resolved_at' => 'nullable|date',
    ];

    /**
     * The table associated with the model.
     */
    protected $table = 'inquiries';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'email',
        'phone_no',
        'subject',
        'message',
        'is_read',
        'is_resolved',
        'is_active',
        'priority',
        'status',
        'category',
        'assigned_to',
        'resolved_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     */
    protected $hidden = [
        'deleted_at',
    ];

    /**
     * Activity log configuration.
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'email', 'subject', 'is_read', 'is_resolved', 'priority', 'status'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn (string $eventName) => "Inquiry has been {$eventName}")
        ;
    }

    // =============================================
    // RELATIONSHIPS
    // =============================================

    /**
     * Get the user assigned to this inquiry.
     */
    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    // =============================================
    // SCOPES - Basic Status
    // =============================================

    /**
     * Scope for active inquiries.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for inactive inquiries.
     */
    public function scopeInactive(Builder $query): Builder
    {
        return $query->where('is_active', false);
    }

    /**
     * Scope for read inquiries.
     */
    public function scopeRead(Builder $query): Builder
    {
        return $query->where('is_read', true);
    }

    /**
     * Scope for unread inquiries.
     */
    public function scopeUnread(Builder $query): Builder
    {
        return $query->where('is_read', false);
    }

    /**
     * Scope for resolved inquiries.
     */
    public function scopeResolved(Builder $query): Builder
    {
        return $query->where('is_resolved', true);
    }

    /**
     * Scope for unresolved inquiries.
     */
    public function scopeUnresolved(Builder $query): Builder
    {
        return $query->where('is_resolved', false);
    }

    /**
     * Scope for pending inquiries.
     */
    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    // =============================================
    // SCOPES - Date-based
    // =============================================

    /**
     * Scope for recent inquiries.
     */
    public function scopeRecent(Builder $query, int $days = 7): Builder
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Scope for old inquiries.
     */
    public function scopeOld(Builder $query, int $days = 30): Builder
    {
        return $query->where('created_at', '<', now()->subDays($days));
    }

    // =============================================
    // SCOPES - Filtering
    // =============================================

    /**
     * Scope for inquiries by email.
     */
    public function scopeByEmail(Builder $query, string $email): Builder
    {
        return $query->where('email', $email);
    }

    /**
     * Scope for inquiries by category.
     */
    public function scopeByCategory(Builder $query, string $category): Builder
    {
        return $query->where('category', $category);
    }

    /**
     * Scope for inquiries by status.
     */
    public function scopeByStatus(Builder $query, string $status): Builder
    {
        return $query->where('status', $status);
    }

    /**
     * Scope for inquiries by priority.
     */
    public function scopeByPriority(Builder $query, int $priority): Builder
    {
        return $query->where('priority', $priority);
    }

    // =============================================
    // SCOPES - Priority-based
    // =============================================

    /**
     * Scope for high priority inquiries.
     */
    public function scopeHighPriority(Builder $query): Builder
    {
        return $query->where('priority', '>=', self::PRIORITY_HIGH);
    }

    /**
     * Scope for medium priority inquiries.
     */
    public function scopeMediumPriority(Builder $query): Builder
    {
        return $query->where('priority', self::PRIORITY_MEDIUM);
    }

    /**
     * Scope for low priority inquiries.
     */
    public function scopeLowPriority(Builder $query): Builder
    {
        return $query->where('priority', self::PRIORITY_LOW);
    }

    /**
     * Scope for urgent inquiries.
     */
    public function scopeUrgent(Builder $query): Builder
    {
        return $query->where('priority', self::PRIORITY_URGENT);
    }

    // =============================================
    // SCOPES - Assignment
    // =============================================

    /**
     * Scope for inquiries assigned to a user.
     */
    public function scopeAssignedTo(Builder $query, int $userId): Builder
    {
        return $query->where('assigned_to', $userId);
    }

    /**
     * Scope for unassigned inquiries.
     */
    public function scopeUnassigned(Builder $query): Builder
    {
        return $query->whereNull('assigned_to');
    }

    // =============================================
    // SCOPES - Category-based
    // =============================================

    /**
     * Scope for general inquiries.
     */
    public function scopeGeneral(Builder $query): Builder
    {
        return $query->where('category', self::CATEGORY_GENERAL);
    }

    /**
     * Scope for technical inquiries.
     */
    public function scopeTechnical(Builder $query): Builder
    {
        return $query->where('category', self::CATEGORY_TECHNICAL);
    }

    /**
     * Scope for billing inquiries.
     */
    public function scopeBilling(Builder $query): Builder
    {
        return $query->where('category', self::CATEGORY_BILLING);
    }

    /**
     * Scope for support inquiries.
     */
    public function scopeSupport(Builder $query): Builder
    {
        return $query->where('category', self::CATEGORY_SUPPORT);
    }

    // =============================================
    // SCOPES - Search & Ordering
    // =============================================

    /**
     * Scope for searching inquiries.
     */
    public function scopeSearch(Builder $query, string $term): Builder
    {
        return $query->where('name', 'like', "%{$term}%")
            ->orWhere('email', 'like', "%{$term}%")
            ->orWhere('subject', 'like', "%{$term}%")
            ->orWhere('message', 'like', "%{$term}%")
        ;
    }

    /**
     * Scope for latest inquiries.
     */
    public function scopeLatest(Builder $query): Builder
    {
        return $query->orderBy('created_at', 'desc');
    }

    /**
     * Scope for oldest inquiries.
     */
    public function scopeOldest(Builder $query): Builder
    {
        return $query->orderBy('created_at', 'asc');
    }

    /**
     * Scope for ordering by priority.
     */
    public function scopeByPriorityOrder(Builder $query): Builder
    {
        return $query->orderBy('priority', 'desc')->orderBy('created_at', 'asc');
    }

    // =============================================
    // ATTRIBUTE ACCESSORS
    // =============================================

    /**
     * Check if inquiry is recent.
     */
    public function getIsRecentAttribute(): bool
    {
        return $this->created_at && $this->created_at->isAfter(now()->subDays(7));
    }

    /**
     * Check if inquiry is pending.
     */
    public function getIsPendingAttribute(): bool
    {
        return self::STATUS_PENDING === $this->status || (!$this->is_read && !$this->is_resolved);
    }

    /**
     * Check if inquiry is high priority.
     */
    public function getIsHighPriorityAttribute(): bool
    {
        return $this->priority >= self::PRIORITY_HIGH;
    }

    /**
     * Get status label.
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING => 'Pending',
            self::STATUS_IN_PROGRESS => 'In Progress',
            self::STATUS_RESOLVED => 'Resolved',
            self::STATUS_CLOSED => 'Closed',
            default => 'Unknown'
        };
    }

    /**
     * Get priority label.
     */
    public function getPriorityLabelAttribute(): string
    {
        return match ($this->priority) {
            self::PRIORITY_LOW => 'Low',
            self::PRIORITY_MEDIUM => 'Medium',
            self::PRIORITY_HIGH => 'High',
            self::PRIORITY_URGENT => 'Urgent',
            default => 'Unknown'
        };
    }

    /**
     * Get category label.
     */
    public function getCategoryLabelAttribute(): string
    {
        return match ($this->category) {
            self::CATEGORY_GENERAL => 'General',
            self::CATEGORY_TECHNICAL => 'Technical',
            self::CATEGORY_BILLING => 'Billing',
            self::CATEGORY_SUPPORT => 'Support',
            self::CATEGORY_FEATURE_REQUEST => 'Feature Request',
            self::CATEGORY_BUG_REPORT => 'Bug Report',
            default => 'Unknown'
        };
    }

    // =============================================
    // HELPER METHODS
    // =============================================

    /**
     * Check if inquiry is active.
     */
    public function isActive(): bool
    {
        return $this->is_active;
    }

    /**
     * Check if inquiry is read.
     */
    public function isRead(): bool
    {
        return $this->is_read;
    }

    /**
     * Check if inquiry is resolved.
     */
    public function isResolved(): bool
    {
        return $this->is_resolved;
    }

    /**
     * Check if inquiry is pending.
     */
    public function isPending(): bool
    {
        return self::STATUS_PENDING === $this->status || (!$this->is_read && !$this->is_resolved);
    }

    /**
     * Check if inquiry is high priority.
     */
    public function isHighPriority(): bool
    {
        return $this->priority >= self::PRIORITY_HIGH;
    }

    /**
     * Mark inquiry as read.
     */
    public function markAsRead(): bool
    {
        return $this->update(['is_read' => true]);
    }

    /**
     * Mark inquiry as unread.
     */
    public function markAsUnread(): bool
    {
        return $this->update(['is_read' => false]);
    }

    /**
     * Mark inquiry as resolved.
     */
    public function markAsResolved(): bool
    {
        return $this->update([
            'is_resolved' => true,
            'status' => self::STATUS_RESOLVED,
            'resolved_at' => now(),
        ]);
    }

    /**
     * Assign inquiry to a user.
     */
    public function assignTo(int $userId): bool
    {
        return $this->update([
            'assigned_to' => $userId,
            'status' => self::STATUS_IN_PROGRESS,
        ]);
    }

    /**
     * Get inquiries count by email.
     */
    public static function getInquiriesCountByEmail(string $email): int
    {
        return Cache::remember("inquiries.email.{$email}.count", 3600, function () use ($email) {
            return self::where('email', $email)->active()->count();
        });
    }

    /**
     * Get unread inquiries count.
     */
    public static function getUnreadCount(): int
    {
        return Cache::remember('inquiries.unread.count', 1800, function () {
            return self::unread()->active()->count();
        });
    }

    /**
     * Get pending inquiries count.
     */
    public static function getPendingCount(): int
    {
        return Cache::remember('inquiries.pending.count', 1800, function () {
            return self::pending()->active()->count();
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
            'inquiries.unread.count',
            'inquiries.pending.count',
            'inquiries.high_priority.count',
        ];

        foreach ($cacheKeys as $key) {
            Cache::forget($key);
        }

        // Clear email specific cache
        Cache::forget("inquiries.email.{$this->email}.count");
    }

    /**
     * Get the attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'id' => 'integer',
            'is_read' => 'boolean',
            'is_resolved' => 'boolean',
            'is_active' => 'boolean',
            'priority' => 'integer',
            'assigned_to' => 'integer',
            'resolved_at' => 'datetime',
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
            if (is_null($model->is_read)) {
                $model->is_read = false;
            }
            if (is_null($model->is_resolved)) {
                $model->is_resolved = false;
            }
            if (is_null($model->status)) {
                $model->status = self::STATUS_PENDING;
            }
            if (is_null($model->priority)) {
                $model->priority = self::PRIORITY_MEDIUM;
            }
            if (is_null($model->category)) {
                $model->category = self::CATEGORY_GENERAL;
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
