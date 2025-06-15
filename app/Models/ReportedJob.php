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
 * ReportedJob Model - Enhanced with Enhanced patterns.
 *
 * @property int         $id
 * @property int         $user_id
 * @property int         $job_id
 * @property string      $note
 * @property null|string $reason
 * @property null|string $status
 * @property bool        $is_active
 * @property bool        $is_resolved
 * @property null|int    $priority
 * @property null|Carbon $resolved_at
 * @property null|int    $resolved_by
 * @property null|Carbon $created_at
 * @property null|Carbon $updated_at
 * @property null|Carbon $deleted_at
 * @property User        $user
 * @property Job         $job
 * @property null|User   $resolver
 * @property bool        $is_recent
 * @property bool        $is_pending
 * @property bool        $is_high_priority
 * @property string      $status_label
 * @property string      $priority_label
 *
 * Enhanced Enhanced Scopes:
 *
 * @method static Builder active()
 * @method static Builder inactive()
 * @method static Builder resolved()
 * @method static Builder unresolved()
 * @method static Builder pending()
 * @method static Builder recent(int $days = 30)
 * @method static Builder old(int $days = 365)
 * @method static Builder byUser(int $userId)
 * @method static Builder byJob(int $jobId)
 * @method static Builder byReason(string $reason)
 * @method static Builder byStatus(string $status)
 * @method static Builder byPriority(int $priority)
 * @method static Builder highPriority()
 * @method static Builder mediumPriority()
 * @method static Builder lowPriority()
 * @method static Builder search(string $term)
 * @method static Builder latest()
 * @method static Builder oldest()
 * @method static Builder spam()
 * @method static Builder inappropriate()
 * @method static Builder fraud()
 * @method static Builder duplicate()
 * @method static Builder other()
 *
 * @mixin \Eloquent
 */
class ReportedJob extends Model
{
    use HasFactory;
    use SoftDeletes;
    use LogsActivity;

    /**
     * Status constants.
     */
    public const STATUS_PENDING = 'pending';
    public const STATUS_INVESTIGATING = 'investigating';
    public const STATUS_RESOLVED = 'resolved';
    public const STATUS_DISMISSED = 'dismissed';

    /**
     * Reason constants.
     */
    public const REASON_SPAM = 'spam';
    public const REASON_INAPPROPRIATE = 'inappropriate';
    public const REASON_FRAUD = 'fraud';
    public const REASON_DUPLICATE = 'duplicate';
    public const REASON_MISLEADING = 'misleading';
    public const REASON_OTHER = 'other';

    /**
     * Priority constants.
     */
    public const PRIORITY_LOW = 1;
    public const PRIORITY_MEDIUM = 2;
    public const PRIORITY_HIGH = 3;
    public const PRIORITY_URGENT = 4;

    /**
     * Validation rules.
     */
    public static array $rules = [
        'user_id' => 'required|integer|exists:users,id',
        'job_id' => 'required|integer|exists:jobs,id',
        'note' => 'required|string|max:1000',
        'reason' => 'nullable|string|in:spam,inappropriate,fraud,duplicate,misleading,other',
        'status' => 'nullable|string|in:pending,investigating,resolved,dismissed',
        'is_active' => 'boolean',
        'is_resolved' => 'boolean',
        'priority' => 'nullable|integer|min:1|max:4',
        'resolved_by' => 'nullable|integer|exists:users,id',
        'resolved_at' => 'nullable|date',
    ];

    /**
     * The table associated with the model.
     */
    protected $table = 'reported_jobs';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'user_id',
        'job_id',
        'note',
        'reason',
        'status',
        'is_active',
        'is_resolved',
        'priority',
        'resolved_at',
        'resolved_by',
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
            ->logOnly(['user_id', 'job_id', 'note', 'reason', 'status', 'is_resolved', 'priority'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn (string $eventName) => "Reported job has been {$eventName}")
        ;
    }

    // =============================================
    // RELATIONSHIPS
    // =============================================

    /**
     * Get the user who reported the job.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the reported job.
     */
    public function job(): BelongsTo
    {
        return $this->belongsTo(Job::class, 'job_id');
    }

    /**
     * Get the user who resolved the report.
     */
    public function resolver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'resolved_by');
    }

    // =============================================
    // SCOPES - Basic Status
    // =============================================

    /**
     * Scope for active reports.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for inactive reports.
     */
    public function scopeInactive(Builder $query): Builder
    {
        return $query->where('is_active', false);
    }

    /**
     * Scope for resolved reports.
     */
    public function scopeResolved(Builder $query): Builder
    {
        return $query->where('is_resolved', true);
    }

    /**
     * Scope for unresolved reports.
     */
    public function scopeUnresolved(Builder $query): Builder
    {
        return $query->where('is_resolved', false);
    }

    /**
     * Scope for pending reports.
     */
    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    // =============================================
    // SCOPES - Date-based
    // =============================================

    /**
     * Scope for recent reports.
     */
    public function scopeRecent(Builder $query, int $days = 30): Builder
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Scope for old reports.
     */
    public function scopeOld(Builder $query, int $days = 365): Builder
    {
        return $query->where('created_at', '<=', now()->subDays($days));
    }

    // =============================================
    // SCOPES - Filtering
    // =============================================

    /**
     * Scope for reports by user.
     */
    public function scopeByUser(Builder $query, int $userId): Builder
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope for reports by job.
     */
    public function scopeByJob(Builder $query, int $jobId): Builder
    {
        return $query->where('job_id', $jobId);
    }

    /**
     * Scope for reports by reason.
     */
    public function scopeByReason(Builder $query, string $reason): Builder
    {
        return $query->where('reason', $reason);
    }

    /**
     * Scope for reports by status.
     */
    public function scopeByStatus(Builder $query, string $status): Builder
    {
        return $query->where('status', $status);
    }

    /**
     * Scope for reports by priority.
     */
    public function scopeByPriority(Builder $query, int $priority): Builder
    {
        return $query->where('priority', $priority);
    }

    // =============================================
    // SCOPES - Priority-based
    // =============================================

    /**
     * Scope for high priority reports.
     */
    public function scopeHighPriority(Builder $query): Builder
    {
        return $query->where('priority', '>=', self::PRIORITY_HIGH);
    }

    /**
     * Scope for medium priority reports.
     */
    public function scopeMediumPriority(Builder $query): Builder
    {
        return $query->where('priority', self::PRIORITY_MEDIUM);
    }

    /**
     * Scope for low priority reports.
     */
    public function scopeLowPriority(Builder $query): Builder
    {
        return $query->where('priority', self::PRIORITY_LOW);
    }

    // =============================================
    // SCOPES - Reason-based
    // =============================================

    /**
     * Scope for spam reports.
     */
    public function scopeSpam(Builder $query): Builder
    {
        return $query->where('reason', self::REASON_SPAM);
    }

    /**
     * Scope for inappropriate content reports.
     */
    public function scopeInappropriate(Builder $query): Builder
    {
        return $query->where('reason', self::REASON_INAPPROPRIATE);
    }

    /**
     * Scope for fraud reports.
     */
    public function scopeFraud(Builder $query): Builder
    {
        return $query->where('reason', self::REASON_FRAUD);
    }

    /**
     * Scope for duplicate reports.
     */
    public function scopeDuplicate(Builder $query): Builder
    {
        return $query->where('reason', self::REASON_DUPLICATE);
    }

    /**
     * Scope for other reason reports.
     */
    public function scopeOther(Builder $query): Builder
    {
        return $query->where('reason', self::REASON_OTHER);
    }

    // =============================================
    // SCOPES - Search & Ordering
    // =============================================

    /**
     * Scope for searching reports.
     */
    public function scopeSearch(Builder $query, string $term): Builder
    {
        return $query->where('note', 'like', '%'.$term.'%')
            ->orWhere('reason', 'like', '%'.$term.'%')
            ->orWhereHas('job', function ($jobQuery) use ($term) {
                $jobQuery->where('title', 'like', '%'.$term.'%');
            })
            ->orWhereHas('user', function ($userQuery) use ($term) {
                $userQuery->where('first_name', 'like', '%'.$term.'%')
                    ->orWhere('last_name', 'like', '%'.$term.'%')
                    ->orWhere('email', 'like', '%'.$term.'%')
                ;
            })
        ;
    }

    /**
     * Scope for latest reports.
     */
    public function scopeLatest(Builder $query): Builder
    {
        return $query->orderBy('created_at', 'desc');
    }

    /**
     * Scope for oldest reports.
     */
    public function scopeOldest(Builder $query): Builder
    {
        return $query->orderBy('created_at', 'asc');
    }

    // =============================================
    // ATTRIBUTE ACCESSORS
    // =============================================

    /**
     * Check if report is recent.
     */
    public function getIsRecentAttribute(): bool
    {
        return $this->created_at && $this->created_at->isAfter(now()->subDays(7));
    }

    /**
     * Check if report is pending.
     */
    public function getIsPendingAttribute(): bool
    {
        return self::STATUS_PENDING === $this->status;
    }

    /**
     * Check if report is high priority.
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
            self::STATUS_INVESTIGATING => 'Investigating',
            self::STATUS_RESOLVED => 'Resolved',
            self::STATUS_DISMISSED => 'Dismissed',
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

    // =============================================
    // HELPER METHODS
    // =============================================

    /**
     * Check if report is active.
     */
    public function isActive(): bool
    {
        return $this->is_active;
    }

    /**
     * Check if report is resolved.
     */
    public function isResolved(): bool
    {
        return $this->is_resolved;
    }

    /**
     * Check if report is pending.
     */
    public function isPending(): bool
    {
        return self::STATUS_PENDING === $this->status;
    }

    /**
     * Check if report is high priority.
     */
    public function isHighPriority(): bool
    {
        return $this->priority >= self::PRIORITY_HIGH;
    }

    /**
     * Mark report as resolved.
     */
    public function markAsResolved(?int $resolvedBy = null): bool
    {
        return $this->update([
            'is_resolved' => true,
            'status' => self::STATUS_RESOLVED,
            'resolved_at' => now(),
            'resolved_by' => $resolvedBy,
        ]);
    }

    /**
     * Mark report as dismissed.
     */
    public function markAsDismissed(?int $resolvedBy = null): bool
    {
        return $this->update([
            'is_resolved' => true,
            'status' => self::STATUS_DISMISSED,
            'resolved_at' => now(),
            'resolved_by' => $resolvedBy,
        ]);
    }

    /**
     * Get reports count for a job.
     */
    public static function getJobReportsCount(int $jobId): int
    {
        return Cache::remember("job.{$jobId}.reports_count", 3600, function () use ($jobId) {
            return self::where('job_id', $jobId)->active()->count();
        });
    }

    /**
     * Get user's reports count.
     */
    public static function getUserReportsCount(int $userId): int
    {
        return Cache::remember("user.{$userId}.reports_count", 3600, function () use ($userId) {
            return self::where('user_id', $userId)->active()->count();
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
            'reported_jobs.active',
            'reported_jobs.pending',
            'reported_jobs.high_priority',
        ];

        foreach ($cacheKeys as $key) {
            Cache::forget($key);
        }

        // Clear specific caches
        Cache::forget("job.{$this->job_id}.reports_count");
        Cache::forget("user.{$this->user_id}.reports_count");
    }

    /**
     * Get the attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'id' => 'integer',
            'user_id' => 'integer',
            'job_id' => 'integer',
            'is_active' => 'boolean',
            'is_resolved' => 'boolean',
            'priority' => 'integer',
            'resolved_by' => 'integer',
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
            if (is_null($model->is_resolved)) {
                $model->is_resolved = false;
            }
            if (is_null($model->status)) {
                $model->status = self::STATUS_PENDING;
            }
            if (is_null($model->priority)) {
                $model->priority = self::PRIORITY_MEDIUM;
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
