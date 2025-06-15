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
 * Todo Model - Enhanced with Enhanced patterns.
 *
 * @property int         $id
 * @property int         $user_id
 * @property string      $title
 * @property null|string $description
 * @property null|string $category
 * @property string      $priority
 * @property bool        $is_completed
 * @property bool        $is_active
 * @property bool        $is_recurring
 * @property null|Carbon $due_date
 * @property null|Carbon $completed_at
 * @property null|int    $estimated_minutes
 * @property null|int    $actual_minutes
 * @property null|array  $tags
 * @property null|int    $sort_order
 * @property null|Carbon $created_at
 * @property null|Carbon $updated_at
 * @property null|Carbon $deleted_at
 * @property User        $user
 * @property string      $status
 * @property string      $priority_label
 * @property string      $category_label
 * @property bool        $is_overdue
 * @property bool        $is_due_today
 * @property bool        $is_due_soon
 * @property int         $days_until_due
 * @property int         $days_overdue
 * @property string      $completion_status
 *
 * Enhanced Enhanced Scopes:
 *
 * @method static \Illuminate\Database\Eloquent\Builder active()
 * @method static \Illuminate\Database\Eloquent\Builder inactive()
 * @method static \Illuminate\Database\Eloquent\Builder completed()
 * @method static \Illuminate\Database\Eloquent\Builder incomplete()
 * @method static \Illuminate\Database\Eloquent\Builder pending()
 * @method static \Illuminate\Database\Eloquent\Builder overdue()
 * @method static \Illuminate\Database\Eloquent\Builder dueToday()
 * @method static \Illuminate\Database\Eloquent\Builder dueSoon(int $days = 7)
 * @method static \Illuminate\Database\Eloquent\Builder byPriority(string $priority)
 * @method static \Illuminate\Database\Eloquent\Builder byCategory(string $category)
 * @method static \Illuminate\Database\Eloquent\Builder byUser(int $userId)
 * @method static \Illuminate\Database\Eloquent\Builder search(string $term)
 * @method static \Illuminate\Database\Eloquent\Builder recent(int $days = 30)
 * @method static \Illuminate\Database\Eloquent\Builder highPriority()
 * @method static \Illuminate\Database\Eloquent\Builder mediumPriority()
 * @method static \Illuminate\Database\Eloquent\Builder lowPriority()
 * @method static \Illuminate\Database\Eloquent\Builder recurring()
 * @method static \Illuminate\Database\Eloquent\Builder nonRecurring()
 * @method static \Illuminate\Database\Eloquent\Builder withEstimatedTime()
 * @method static \Illuminate\Database\Eloquent\Builder withoutEstimatedTime()
 * @method static \Illuminate\Database\Eloquent\Builder ordered()
 * @method static \Illuminate\Database\Eloquent\Builder alphabetical()
 * @method static \Illuminate\Database\Eloquent\Builder byDueDate()
 *
 * @mixin \Eloquent
 */
class Todo extends Model
{
    use HasFactory;
    use SoftDeletes;
    use LogsActivity;

    /**
     * Priority constants.
     */
    public const PRIORITY_LOW = 'low';
    public const PRIORITY_MEDIUM = 'medium';
    public const PRIORITY_HIGH = 'high';
    public const PRIORITY_URGENT = 'urgent';

    /**
     * Status constants.
     */
    public const STATUS_PENDING = 'pending';
    public const STATUS_IN_PROGRESS = 'in_progress';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_CANCELLED = 'cancelled';

    /**
     * Category constants.
     */
    public const CATEGORY_PERSONAL = 'personal';
    public const CATEGORY_WORK = 'work';
    public const CATEGORY_PROJECT = 'project';
    public const CATEGORY_MEETING = 'meeting';
    public const CATEGORY_REMINDER = 'reminder';
    public const CATEGORY_ADMIN = 'admin';

    /**
     * Validation rules for creating todos.
     *
     * @var array<string, string>
     */
    public static array $rules = [
        'user_id' => 'required|integer|exists:users,id',
        'title' => 'required|string|max:255',
        'description' => 'nullable|string|max:1000',
        'category' => 'nullable|string|max:50',
        'priority' => 'required|string|in:low,medium,high,urgent',
        'is_completed' => 'boolean',
        'is_active' => 'boolean',
        'is_recurring' => 'boolean',
        'due_date' => 'nullable|date|after:now',
        'completed_at' => 'nullable|date',
        'estimated_minutes' => 'nullable|integer|min:1|max:1440',
        'actual_minutes' => 'nullable|integer|min:1|max:1440',
        'tags' => 'nullable|array',
        'tags.*' => 'string|max:50',
        'sort_order' => 'nullable|integer|min:0',
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'todos';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'title',
        'description',
        'category',
        'priority',
        'is_completed',
        'is_active',
        'is_recurring',
        'due_date',
        'completed_at',
        'estimated_minutes',
        'actual_minutes',
        'tags',
        'sort_order',
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
     * Get the activity log options for the model.
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['title', 'description', 'priority', 'is_completed', 'due_date'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
        ;
    }

    /**
     * Update validation rules for todos.
     *
     * @return array<string, string>
     */
    public static function updateRules(int $id): array
    {
        return [
            'user_id' => 'required|integer|exists:users,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'category' => 'nullable|string|max:50',
            'priority' => 'required|string|in:low,medium,high,urgent',
            'is_completed' => 'boolean',
            'is_active' => 'boolean',
            'is_recurring' => 'boolean',
            'due_date' => 'nullable|date',
            'completed_at' => 'nullable|date',
            'estimated_minutes' => 'nullable|integer|min:1|max:1440',
            'actual_minutes' => 'nullable|integer|min:1|max:1440',
            'tags' => 'nullable|array',
            'tags.*' => 'string|max:50',
            'sort_order' => 'nullable|integer|min:0',
        ];
    }

    // =============================================
    // RELATIONSHIPS
    // =============================================

    /**
     * Get the user that owns the todo.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // =============================================
    // SCOPES - Basic Status
    // =============================================

    /**
     * Scope to only include active todos.
     *
     * @param mixed $query
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to only include inactive todos.
     *
     * @param mixed $query
     */
    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    /**
     * Scope to only include completed todos.
     *
     * @param mixed $query
     */
    public function scopeCompleted($query)
    {
        return $query->where('is_completed', true);
    }

    /**
     * Scope to only include incomplete todos.
     *
     * @param mixed $query
     */
    public function scopeIncomplete($query)
    {
        return $query->where('is_completed', false);
    }

    /**
     * Scope to only include pending todos (active and incomplete).
     *
     * @param mixed $query
     */
    public function scopePending($query)
    {
        return $query->where('is_active', true)->where('is_completed', false);
    }

    /**
     * Scope to only include recurring todos.
     *
     * @param mixed $query
     */
    public function scopeRecurring($query)
    {
        return $query->where('is_recurring', true);
    }

    /**
     * Scope to only include non-recurring todos.
     *
     * @param mixed $query
     */
    public function scopeNonRecurring($query)
    {
        return $query->where('is_recurring', false);
    }

    // =============================================
    // SCOPES - Due Date & Time
    // =============================================

    /**
     * Scope to only include overdue todos.
     *
     * @param mixed $query
     */
    public function scopeOverdue($query)
    {
        return $query->where('due_date', '<', now())
            ->where('is_completed', false)
            ->where('is_active', true)
        ;
    }

    /**
     * Scope to only include todos due today.
     *
     * @param mixed $query
     */
    public function scopeDueToday($query)
    {
        return $query->whereDate('due_date', today())
            ->where('is_completed', false)
            ->where('is_active', true)
        ;
    }

    /**
     * Scope to only include todos due soon.
     *
     * @param mixed $query
     */
    public function scopeDueSoon($query, int $days = 7)
    {
        return $query->where('due_date', '<=', now()->addDays($days))
            ->where('due_date', '>=', now())
            ->where('is_completed', false)
            ->where('is_active', true)
        ;
    }

    /**
     * Scope to only include todos due this week.
     *
     * @param mixed $query
     */
    public function scopeDueThisWeek($query)
    {
        return $query->whereBetween('due_date', [now()->startOfWeek(), now()->endOfWeek()])
            ->where('is_completed', false)
            ->where('is_active', true)
        ;
    }

    /**
     * Scope to only include todos due this month.
     *
     * @param mixed $query
     */
    public function scopeDueThisMonth($query)
    {
        return $query->whereBetween('due_date', [now()->startOfMonth(), now()->endOfMonth()])
            ->where('is_completed', false)
            ->where('is_active', true)
        ;
    }

    // =============================================
    // SCOPES - Priority & Category
    // =============================================

    /**
     * Scope to get todos by priority.
     *
     * @param mixed $query
     */
    public function scopeByPriority($query, string $priority)
    {
        return $query->where('priority', $priority);
    }

    /**
     * Scope to only include high priority todos.
     *
     * @param mixed $query
     */
    public function scopeHighPriority($query)
    {
        return $query->whereIn('priority', [self::PRIORITY_HIGH, self::PRIORITY_URGENT]);
    }

    /**
     * Scope to only include medium priority todos.
     *
     * @param mixed $query
     */
    public function scopeMediumPriority($query)
    {
        return $query->where('priority', self::PRIORITY_MEDIUM);
    }

    /**
     * Scope to only include low priority todos.
     *
     * @param mixed $query
     */
    public function scopeLowPriority($query)
    {
        return $query->where('priority', self::PRIORITY_LOW);
    }

    /**
     * Scope to only include urgent priority todos.
     *
     * @param mixed $query
     */
    public function scopeUrgent($query)
    {
        return $query->where('priority', self::PRIORITY_URGENT);
    }

    /**
     * Scope to get todos by category.
     *
     * @param mixed $query
     */
    public function scopeByCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    // =============================================
    // SCOPES - User & Search
    // =============================================

    /**
     * Scope to get todos by user.
     *
     * @param mixed $query
     */
    public function scopeByUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope to search todos by title or description.
     *
     * @param mixed $query
     */
    public function scopeSearch($query, string $term)
    {
        return $query->where(function ($q) use ($term) {
            $q->where('title', 'like', '%'.$term.'%')
                ->orWhere('description', 'like', '%'.$term.'%')
                ->orWhere('category', 'like', '%'.$term.'%')
            ;
        });
    }

    /**
     * Scope to get todos created within specified days.
     *
     * @param mixed $query
     */
    public function scopeRecent($query, int $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    // =============================================
    // SCOPES - Time Estimation
    // =============================================

    /**
     * Scope to get todos with estimated time.
     *
     * @param mixed $query
     */
    public function scopeWithEstimatedTime($query)
    {
        return $query->whereNotNull('estimated_minutes');
    }

    /**
     * Scope to get todos without estimated time.
     *
     * @param mixed $query
     */
    public function scopeWithoutEstimatedTime($query)
    {
        return $query->whereNull('estimated_minutes');
    }

    /**
     * Scope to get todos with actual time tracked.
     *
     * @param mixed $query
     */
    public function scopeWithActualTime($query)
    {
        return $query->whereNotNull('actual_minutes');
    }

    /**
     * Scope to get todos by estimated duration range.
     *
     * @param mixed $query
     */
    public function scopeByEstimatedDuration($query, int $minMinutes, int $maxMinutes)
    {
        return $query->whereBetween('estimated_minutes', [$minMinutes, $maxMinutes]);
    }

    // =============================================
    // SCOPES - Ordering & Sorting
    // =============================================

    /**
     * Scope to order todos by sort order.
     *
     * @param mixed $query
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order', 'asc')
            ->orderBy('priority', 'desc')
            ->orderBy('due_date', 'asc')
        ;
    }

    /**
     * Scope to order todos alphabetically.
     *
     * @param mixed $query
     */
    public function scopeAlphabetical($query)
    {
        return $query->orderBy('title', 'asc');
    }

    /**
     * Scope to order todos by due date.
     *
     * @param mixed $query
     */
    public function scopeByDueDate($query, string $direction = 'asc')
    {
        return $query->orderBy('due_date', $direction);
    }

    /**
     * Scope to order todos by priority.
     *
     * @param mixed $query
     */
    public function scopeByPriorityOrder($query)
    {
        return $query->orderByRaw("FIELD(priority, 'urgent', 'high', 'medium', 'low')");
    }

    // =============================================
    // CACHE METHODS - Enhanced Caching Strategy
    // =============================================

    /**
     * Get cached todos for a user.
     */
    public static function getCachedForUser(int $userId): Collection
    {
        return Cache::remember("todos.user.{$userId}", now()->addMinutes(30), function () use ($userId) {
            return static::byUser($userId)->active()->ordered()->get();
        });
    }

    /**
     * Get cached pending todos for a user.
     */
    public static function getCachedPendingForUser(int $userId): Collection
    {
        return Cache::remember("todos.pending.user.{$userId}", now()->addMinutes(15), function () use ($userId) {
            return static::byUser($userId)->pending()->ordered()->get();
        });
    }

    /**
     * Get cached overdue todos for a user.
     */
    public static function getCachedOverdueForUser(int $userId): Collection
    {
        return Cache::remember("todos.overdue.user.{$userId}", now()->addMinutes(15), function () use ($userId) {
            return static::byUser($userId)->overdue()->ordered()->get();
        });
    }

    // =============================================
    // ACCESSOR METHODS
    // =============================================

    /**
     * Get the status attribute.
     */
    public function getStatusAttribute(): string
    {
        if (!$this->is_active) {
            return self::STATUS_CANCELLED;
        }
        if ($this->is_completed) {
            return self::STATUS_COMPLETED;
        }
        if ($this->is_overdue) {
            return 'overdue';
        }

        return self::STATUS_PENDING;
    }

    /**
     * Get the priority label attribute.
     */
    public function getPriorityLabelAttribute(): string
    {
        return match ($this->priority) {
            self::PRIORITY_URGENT => 'Urgent',
            self::PRIORITY_HIGH => 'High',
            self::PRIORITY_MEDIUM => 'Medium',
            self::PRIORITY_LOW => 'Low',
            default => 'Unknown',
        };
    }

    /**
     * Get the category label attribute.
     */
    public function getCategoryLabelAttribute(): string
    {
        return match ($this->category) {
            self::CATEGORY_PERSONAL => 'Personal',
            self::CATEGORY_WORK => 'Work',
            self::CATEGORY_PROJECT => 'Project',
            self::CATEGORY_MEETING => 'Meeting',
            self::CATEGORY_REMINDER => 'Reminder',
            self::CATEGORY_ADMIN => 'Admin',
            default => $this->category ? ucfirst($this->category) : 'General',
        };
    }

    /**
     * Check if todo is overdue.
     */
    public function getIsOverdueAttribute(): bool
    {
        return $this->due_date
               && $this->due_date->isPast()
               && !$this->is_completed
               && $this->is_active;
    }

    /**
     * Check if todo is due today.
     */
    public function getIsDueTodayAttribute(): bool
    {
        return $this->due_date && $this->due_date->isToday() && !$this->is_completed;
    }

    /**
     * Check if todo is due soon.
     */
    public function getIsDueSoonAttribute(): bool
    {
        return $this->due_date
               && $this->due_date->isFuture()
               && $this->due_date->diffInDays(now()) <= 7
               && !$this->is_completed;
    }

    /**
     * Get days until due.
     */
    public function getDaysUntilDueAttribute(): int
    {
        if (!$this->due_date || $this->is_completed) {
            return 0;
        }

        return $this->due_date->diffInDays(now(), false);
    }

    /**
     * Get days overdue.
     */
    public function getDaysOverdueAttribute(): int
    {
        if (!$this->is_overdue) {
            return 0;
        }

        return now()->diffInDays($this->due_date);
    }

    /**
     * Get completion status.
     */
    public function getCompletionStatusAttribute(): string
    {
        if ($this->is_completed) {
            return 'Completed';
        }
        if ($this->is_overdue) {
            return 'Overdue';
        }
        if ($this->is_due_today) {
            return 'Due Today';
        }
        if ($this->is_due_soon) {
            return 'Due Soon';
        }

        return 'Pending';
    }

    // =============================================
    // HELPER METHODS
    // =============================================

    /**
     * Mark todo as completed.
     */
    public function markAsCompleted(): bool
    {
        return $this->update([
            'is_completed' => true,
            'completed_at' => now(),
        ]);
    }

    /**
     * Mark todo as incomplete.
     */
    public function markAsIncomplete(): bool
    {
        return $this->update([
            'is_completed' => false,
            'completed_at' => null,
        ]);
    }

    /**
     * Check if todo is high priority.
     */
    public function isHighPriority(): bool
    {
        return in_array($this->priority, [self::PRIORITY_HIGH, self::PRIORITY_URGENT]);
    }

    /**
     * Check if todo is urgent.
     */
    public function isUrgent(): bool
    {
        return self::PRIORITY_URGENT === $this->priority;
    }

    /**
     * Get estimated duration in hours.
     */
    public function getEstimatedHours(): float
    {
        return $this->estimated_minutes ? round($this->estimated_minutes / 60, 2) : 0;
    }

    /**
     * Get actual duration in hours.
     */
    public function getActualHours(): float
    {
        return $this->actual_minutes ? round($this->actual_minutes / 60, 2) : 0;
    }

    /**
     * Get priority color.
     */
    public function getPriorityColor(): string
    {
        return match ($this->priority) {
            self::PRIORITY_URGENT => '#dc3545',
            self::PRIORITY_HIGH => '#fd7e14',
            self::PRIORITY_MEDIUM => '#ffc107',
            self::PRIORITY_LOW => '#28a745',
            default => '#6c757d',
        };
    }

    /**
     * Get badge HTML for the todo priority.
     */
    public function getPriorityBadgeHtml(): string
    {
        $color = $this->getPriorityColor();

        return "<span class=\"badge\" style=\"background-color: {$color};\">{$this->priority_label}</span>";
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
            "todos.user.{$this->user_id}",
            "todos.pending.user.{$this->user_id}",
            "todos.overdue.user.{$this->user_id}",
        ];

        foreach ($cacheKeys as $key) {
            Cache::forget($key);
        }
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'id' => 'integer',
            'user_id' => 'integer',
            'is_completed' => 'boolean',
            'is_active' => 'boolean',
            'is_recurring' => 'boolean',
            'due_date' => 'datetime',
            'completed_at' => 'datetime',
            'estimated_minutes' => 'integer',
            'actual_minutes' => 'integer',
            'tags' => 'array',
            'sort_order' => 'integer',
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
