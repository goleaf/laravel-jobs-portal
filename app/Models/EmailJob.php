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
 * EmailJob Model - Enhanced with Enhanced patterns.
 *
 * @property int         $id
 * @property int         $user_id
 * @property int         $job_id
 * @property string      $job_url
 * @property string      $friend_name
 * @property string      $friend_email
 * @property bool        $is_sent
 * @property bool        $is_active
 * @property null|string $message
 * @property null|string $status
 * @property null|Carbon $sent_at
 * @property null|Carbon $opened_at
 * @property null|Carbon $clicked_at
 * @property null|int    $open_count
 * @property null|int    $click_count
 * @property null|Carbon $created_at
 * @property null|Carbon $updated_at
 * @property null|Carbon $deleted_at
 * @property User        $user
 * @property Job         $job
 * @property bool        $is_recent
 * @property bool        $is_opened
 * @property bool        $is_clicked
 * @property string      $status_label
 * @property string      $friend_domain
 *
 * Enhanced Enhanced Scopes:
 *
 * @method static Builder active()
 * @method static Builder inactive()
 * @method static Builder sent()
 * @method static Builder pending()
 * @method static Builder opened()
 * @method static Builder clicked()
 * @method static Builder recent(int $days = 30)
 * @method static Builder old(int $days = 365)
 * @method static Builder byUser(int $userId)
 * @method static Builder byJob(int $jobId)
 * @method static Builder byEmail(string $email)
 * @method static Builder byDomain(string $domain)
 * @method static Builder byStatus(string $status)
 * @method static Builder today()
 * @method static Builder thisWeek()
 * @method static Builder thisMonth()
 * @method static Builder search(string $term)
 * @method static Builder latest()
 * @method static Builder oldest()
 * @method static Builder popular()
 * @method static Builder engaged()
 *
 * @mixin \Eloquent
 */
class EmailJob extends Model
{
    use HasFactory;
    use SoftDeletes;
    use LogsActivity;

    /**
     * Status constants.
     */
    public const STATUS_PENDING = 'pending';
    public const STATUS_SENT = 'sent';
    public const STATUS_DELIVERED = 'delivered';
    public const STATUS_OPENED = 'opened';
    public const STATUS_CLICKED = 'clicked';
    public const STATUS_FAILED = 'failed';
    public const STATUS_BOUNCED = 'bounced';

    /**
     * Validation rules.
     */
    public static array $rules = [
        'user_id' => 'required|integer|exists:users,id',
        'job_id' => 'required|integer|exists:jobs,id',
        'job_url' => 'required|url|max:500',
        'friend_name' => 'required|string|max:255',
        'friend_email' => 'required|email:filter|max:255',
        'is_sent' => 'boolean',
        'is_active' => 'boolean',
        'message' => 'nullable|string|max:1000',
        'status' => 'nullable|string|in:pending,sent,delivered,opened,clicked,failed,bounced',
        'sent_at' => 'nullable|date',
        'opened_at' => 'nullable|date',
        'clicked_at' => 'nullable|date',
        'open_count' => 'nullable|integer|min:0',
        'click_count' => 'nullable|integer|min:0',
    ];

    /**
     * The table associated with the model.
     */
    protected $table = 'email_jobs';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'user_id',
        'job_id',
        'job_url',
        'friend_name',
        'friend_email',
        'is_active',
        'is_sent',
        'status',
        'open_count',
        'click_count'
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
            ->logOnly(['user_id', 'job_id', 'friend_name', 'friend_email', 'is_sent', 'status'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn (string $eventName) => "Email job has been {$eventName}")
        ;
    }

    // =============================================
    // RELATIONSHIPS
    // =============================================

    /**
     * Get the user who sent the email.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the job that was shared.
     */
    public function job(): BelongsTo
    {
        return $this->belongsTo(Job::class);
    }

    // =============================================
    // SCOPES - Basic Status
    // =============================================

    /**
     * Scope for active email jobs.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for inactive email jobs.
     */
    public function scopeInactive(Builder $query): Builder
    {
        return $query->where('is_active', false);
    }

    /**
     * Scope for sent emails.
     */
    public function scopeSent(Builder $query): Builder
    {
        return $query->where('is_sent', true);
    }

    /**
     * Scope for pending emails.
     */
    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope for opened emails.
     */
    public function scopeOpened(Builder $query): Builder
    {
        return $query->whereNotNull('opened_at');
    }

    /**
     * Scope for clicked emails.
     */
    public function scopeClicked(Builder $query): Builder
    {
        return $query->whereNotNull('clicked_at');
    }

    // =============================================
    // SCOPES - Date-based
    // =============================================

    /**
     * Scope for recent email jobs.
     */
    public function scopeRecent(Builder $query, int $days = 30): Builder
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Scope for old email jobs.
     */
    public function scopeOld(Builder $query, int $days = 365): Builder
    {
        return $query->where('created_at', '<=', now()->subDays($days));
    }

    /**
     * Scope for today's email jobs.
     */
    public function scopeToday(Builder $query): Builder
    {
        return $query->whereDate('created_at', today());
    }

    /**
     * Scope for this week's email jobs.
     */
    public function scopeThisWeek(Builder $query): Builder
    {
        return $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
    }

    /**
     * Scope for this month's email jobs.
     */
    public function scopeThisMonth(Builder $query): Builder
    {
        return $query->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
        ;
    }

    // =============================================
    // SCOPES - Filtering
    // =============================================

    /**
     * Scope for email jobs by user.
     */
    public function scopeByUser(Builder $query, int $userId): Builder
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope for email jobs by job.
     */
    public function scopeByJob(Builder $query, int $jobId): Builder
    {
        return $query->where('job_id', $jobId);
    }

    /**
     * Scope for email jobs by friend email.
     */
    public function scopeByEmail(Builder $query, string $email): Builder
    {
        return $query->where('friend_email', $email);
    }

    /**
     * Scope for email jobs by email domain.
     */
    public function scopeByDomain(Builder $query, string $domain): Builder
    {
        return $query->where('friend_email', 'like', "%@{$domain}");
    }

    /**
     * Scope for email jobs by status.
     */
    public function scopeByStatus(Builder $query, string $status): Builder
    {
        return $query->where('status', $status);
    }

    // =============================================
    // SCOPES - Engagement
    // =============================================

    /**
     * Scope for popular email jobs (most shared jobs).
     */
    public function scopePopular(Builder $query): Builder
    {
        return $query->select('job_id')
            ->selectRaw('COUNT(*) as shares_count')
            ->groupBy('job_id')
            ->orderByDesc('shares_count')
        ;
    }

    /**
     * Scope for engaged emails (opened or clicked).
     */
    public function scopeEngaged(Builder $query): Builder
    {
        return $query->where(function ($q) {
            $q->whereNotNull('opened_at')
                ->orWhereNotNull('clicked_at')
            ;
        });
    }

    // =============================================
    // SCOPES - Search & Ordering
    // =============================================

    /**
     * Scope for searching email jobs.
     */
    public function scopeSearch(Builder $query, string $term): Builder
    {
        return $query->where('friend_name', 'like', '%'.$term.'%')
            ->orWhere('friend_email', 'like', '%'.$term.'%')
            ->orWhere('message', 'like', '%'.$term.'%')
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
     * Scope for latest email jobs.
     */
    public function scopeLatest(Builder $query): Builder
    {
        return $query->orderBy('created_at', 'desc');
    }

    /**
     * Scope for oldest email jobs.
     */
    public function scopeOldest(Builder $query): Builder
    {
        return $query->orderBy('created_at', 'asc');
    }

    // =============================================
    // ATTRIBUTE ACCESSORS
    // =============================================

    /**
     * Check if email job is recent.
     */
    public function getIsRecentAttribute(): bool
    {
        return $this->created_at && $this->created_at->isAfter(now()->subDays(7));
    }

    /**
     * Check if email was opened.
     */
    public function getIsOpenedAttribute(): bool
    {
        return !is_null($this->opened_at);
    }

    /**
     * Check if email was clicked.
     */
    public function getIsClickedAttribute(): bool
    {
        return !is_null($this->clicked_at);
    }

    /**
     * Get status label.
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING => 'Pending',
            self::STATUS_SENT => 'Sent',
            self::STATUS_DELIVERED => 'Delivered',
            self::STATUS_OPENED => 'Opened',
            self::STATUS_CLICKED => 'Clicked',
            self::STATUS_FAILED => 'Failed',
            self::STATUS_BOUNCED => 'Bounced',
            default => 'Unknown'
        };
    }

    /**
     * Get friend email domain.
     */
    public function getFriendDomainAttribute(): string
    {
        return substr(strrchr($this->friend_email, '@'), 1);
    }

    // =============================================
    // HELPER METHODS
    // =============================================

    /**
     * Check if email job is active.
     */
    public function isActive(): bool
    {
        return $this->is_active;
    }

    /**
     * Check if email is sent.
     */
    public function isSent(): bool
    {
        return $this->is_sent;
    }

    /**
     * Check if email was opened.
     */
    public function isOpened(): bool
    {
        return $this->is_opened;
    }

    /**
     * Check if email was clicked.
     */
    public function isClicked(): bool
    {
        return $this->is_clicked;
    }

    /**
     * Mark email as sent.
     */
    public function markAsSent(): bool
    {
        return $this->update([
            'is_sent' => true,
            'status' => self::STATUS_SENT,
            'sent_at' => now(),
        ]);
    }

    /**
     * Mark email as opened.
     */
    public function markAsOpened(): bool
    {
        return $this->update([
            'status' => self::STATUS_OPENED,
            'opened_at' => now(),
            'open_count' => ($this->open_count ?? 0) + 1,
        ]);
    }

    /**
     * Mark email as clicked.
     */
    public function markAsClicked(): bool
    {
        return $this->update([
            'status' => self::STATUS_CLICKED,
            'clicked_at' => now(),
            'click_count' => ($this->click_count ?? 0) + 1,
        ]);
    }

    /**
     * Get email jobs count by user.
     */
    public static function getUserEmailJobsCount(int $userId): int
    {
        return Cache::remember("user.{$userId}.email_jobs_count", 3600, function () use ($userId) {
            return self::where('user_id', $userId)->active()->count();
        });
    }

    /**
     * Get job shares count.
     */
    public static function getJobSharesCount(int $jobId): int
    {
        return Cache::remember("job.{$jobId}.shares_count", 3600, function () use ($jobId) {
            return self::where('job_id', $jobId)->active()->count();
        });
    }

    /**
     * Get engagement rate for user.
     */
    public static function getUserEngagementRate(int $userId): float
    {
        return Cache::remember("user.{$userId}.engagement_rate", 3600, function () use ($userId) {
            $total = self::where('user_id', $userId)->sent()->count();
            if (0 === $total) {
                return 0.0;
            }

            $engaged = self::where('user_id', $userId)->sent()->engaged()->count();

            return round(($engaged / $total) * 100, 2);
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
            'email_jobs.active',
            'email_jobs.sent',
            'email_jobs.popular',
            'email_jobs.engaged',
        ];

        foreach ($cacheKeys as $key) {
            Cache::forget($key);
        }

        // Clear specific caches
        Cache::forget("user.{$this->user_id}.email_jobs_count");
        Cache::forget("job.{$this->job_id}.shares_count");
        Cache::forget("user.{$this->user_id}.engagement_rate");
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
            'is_sent' => 'boolean',
            'is_active' => 'boolean',
            'open_count' => 'integer',
            'click_count' => 'integer',
            'sent_at' => 'datetime',
            'opened_at' => 'datetime',
            'clicked_at' => 'datetime',
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
            if (is_null($model->is_sent)) {
                $model->is_sent = false;
            }
            if (is_null($model->status)) {
                $model->status = self::STATUS_PENDING;
            }
            if (is_null($model->open_count)) {
                $model->open_count = 0;
            }
            if (is_null($model->click_count)) {
                $model->click_count = 0;
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
