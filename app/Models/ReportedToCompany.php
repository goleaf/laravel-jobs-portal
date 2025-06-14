<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

/**
 * ReportedToCompany Model - Enhanced with Enhanced patterns
 *
 * @property int $id
 * @property int $user_id
 * @property int $company_id
 * @property string $note
 * @property string $reason
 * @property string $status
 * @property bool $is_active
 * @property bool $is_resolved
 * @property string|null $admin_notes
 * @property Carbon|null $resolved_at
 * @property int|null $resolved_by
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 *
 * @property-read User $user
 * @property-read Company $company
 * @property-read User|null $resolver
 * @property-read bool $is_recent
 * @property-read bool $is_pending
 * @property-read string $status_label
 * @property-read string $reason_label
 *
 * Enhanced Enhanced Scopes:
 * @method static Builder active()
 * @method static Builder inactive()
 * @method static Builder resolved()
 * @method static Builder pending()
 * @method static Builder recent(int $days = 30)
 * @method static Builder old(int $days = 365)
 * @method static Builder byUser(int $userId)
 * @method static Builder byCompany(int $companyId)
 * @method static Builder byReason(string $reason)
 * @method static Builder byStatus(string $status)
 * @method static Builder today()
 * @method static Builder thisWeek()
 * @method static Builder thisMonth()
 * @method static Builder search(string $term)
 * @method static Builder latest()
 * @method static Builder oldest()
 * @method static Builder priority()
 * @method static Builder withRelations()
 *
 * @mixin \Eloquent
 */
class ReportedToCompany extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    /**
     * The table associated with the model.
     */
    protected $table = 'reported_to_companies';

    /**
     * Status constants
     */
    public const STATUS_PENDING = 'pending';
    public const STATUS_UNDER_REVIEW = 'under_review';
    public const STATUS_RESOLVED = 'resolved';
    public const STATUS_DISMISSED = 'dismissed';
    public const STATUS_ESCALATED = 'escalated';

    /**
     * Reason constants
     */
    public const REASON_FAKE_COMPANY = 'fake_company';
    public const REASON_INAPPROPRIATE_CONTENT = 'inappropriate_content';
    public const REASON_SPAM = 'spam';
    public const REASON_MISLEADING_INFO = 'misleading_info';
    public const REASON_HARASSMENT = 'harassment';
    public const REASON_COPYRIGHT = 'copyright';
    public const REASON_OTHER = 'other';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'user_id',
        'company_id',
        'note',
        'reason',
        'status',
        'is_active',
        'is_resolved',
        'admin_notes',
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
     * Get the attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'id' => 'integer',
            'user_id' => 'integer',
            'company_id' => 'integer',
            'resolved_by' => 'integer',
            'is_active' => 'boolean',
            'is_resolved' => 'boolean',
            'resolved_at' => 'datetime',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }

    /**
     * Validation rules
     */
    public static array $rules = [
        'user_id' => 'required|integer|exists:users,id',
        'company_id' => 'required|integer|exists:companies,id',
        'note' => 'required|string|max:1000',
        'reason' => 'required|string|in:fake_company,inappropriate_content,spam,misleading_info,harassment,copyright,other',
        'status' => 'nullable|string|in:pending,under_review,resolved,dismissed,escalated',
        'is_active' => 'boolean',
        'is_resolved' => 'boolean',
        'admin_notes' => 'nullable|string|max:1000',
        'resolved_by' => 'nullable|integer|exists:users,id',
    ];

    /**
     * Custom validation messages
     */
    public static array $messages = [
        'user_id.required' => 'User is required',
        'user_id.exists' => 'Selected user does not exist',
        'company_id.required' => 'Company is required',
        'company_id.exists' => 'Selected company does not exist',
        'note.required' => 'Report note is required',
        'note.max' => 'Report note cannot exceed 1000 characters',
        'reason.required' => 'Report reason is required',
        'reason.in' => 'Invalid report reason selected',
        'admin_notes.max' => 'Admin notes cannot exceed 1000 characters',
        'resolved_by.exists' => 'Selected resolver does not exist',
    ];

    /**
     * Activity log configuration
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['user_id', 'company_id', 'reason', 'status', 'is_resolved'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn(string $eventName) => "Company report has been {$eventName}");
    }

    // =============================================
    // RELATIONSHIPS
    // =============================================

    /**
     * Get the user who reported the company.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the reported company.
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
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
     * Scope for pending reports.
     */
    public function scopePending(Builder $query): Builder
    {
        return $query->where('is_resolved', false);
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

    /**
     * Scope for today's reports.
     */
    public function scopeToday(Builder $query): Builder
    {
        return $query->whereDate('created_at', today());
    }

    /**
     * Scope for this week's reports.
     */
    public function scopeThisWeek(Builder $query): Builder
    {
        return $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
    }

    /**
     * Scope for this month's reports.
     */
    public function scopeThisMonth(Builder $query): Builder
    {
        return $query->whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year);
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
     * Scope for reports by company.
     */
    public function scopeByCompany(Builder $query, int $companyId): Builder
    {
        return $query->where('company_id', $companyId);
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

    // =============================================
    // SCOPES - Priority & Aggregation
    // =============================================

    /**
     * Scope for priority reports (multiple reports on same company).
     */
    public function scopePriority(Builder $query): Builder
    {
        return $query->select('company_id')
                    ->selectRaw('COUNT(*) as reports_count')
                    ->groupBy('company_id')
                    ->having('reports_count', '>', 1)
                    ->orderByDesc('reports_count');
    }

    // =============================================
    // SCOPES - Search & Ordering
    // =============================================

    /**
     * Scope for searching reports.
     */
    public function scopeSearch(Builder $query, string $term): Builder
    {
        return $query->where('note', 'like', '%' . $term . '%')
                    ->orWhere('admin_notes', 'like', '%' . $term . '%')
                    ->orWhereHas('company', function ($companyQuery) use ($term) {
                        $companyQuery->where('name', 'like', '%' . $term . '%')
                                   ->orWhere('slug', 'like', '%' . $term . '%');
                    })
                    ->orWhereHas('user', function ($userQuery) use ($term) {
                        $userQuery->where('first_name', 'like', '%' . $term . '%')
                                 ->orWhere('last_name', 'like', '%' . $term . '%')
                                 ->orWhere('email', 'like', '%' . $term . '%');
                    });
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

    /**
     * Scope with common relationships.
     */
    public function scopeWithRelations(Builder $query): Builder
    {
        return $query->with(['user', 'company', 'resolver']);
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
        return !$this->is_resolved;
    }

    /**
     * Get status label.
     */
    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            self::STATUS_PENDING => 'Pending',
            self::STATUS_UNDER_REVIEW => 'Under Review',
            self::STATUS_RESOLVED => 'Resolved',
            self::STATUS_DISMISSED => 'Dismissed',
            self::STATUS_ESCALATED => 'Escalated',
            default => 'Unknown'
        };
    }

    /**
     * Get reason label.
     */
    public function getReasonLabelAttribute(): string
    {
        return match($this->reason) {
            self::REASON_FAKE_COMPANY => 'Fake Company',
            self::REASON_INAPPROPRIATE_CONTENT => 'Inappropriate Content',
            self::REASON_SPAM => 'Spam',
            self::REASON_MISLEADING_INFO => 'Misleading Information',
            self::REASON_HARASSMENT => 'Harassment',
            self::REASON_COPYRIGHT => 'Copyright Violation',
            self::REASON_OTHER => 'Other',
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
        return $this->is_pending;
    }

    /**
     * Mark report as resolved.
     */
    public function markAsResolved(int $resolvedBy, string $adminNotes = null): bool
    {
        return $this->update([
            'is_resolved' => true,
            'status' => self::STATUS_RESOLVED,
            'resolved_at' => now(),
            'resolved_by' => $resolvedBy,
            'admin_notes' => $adminNotes,
        ]);
    }

    /**
     * Mark report as dismissed.
     */
    public function markAsDismissed(int $resolvedBy, string $adminNotes = null): bool
    {
        return $this->update([
            'is_resolved' => true,
            'status' => self::STATUS_DISMISSED,
            'resolved_at' => now(),
            'resolved_by' => $resolvedBy,
            'admin_notes' => $adminNotes,
        ]);
    }

    /**
     * Escalate report.
     */
    public function escalate(string $adminNotes = null): bool
    {
        return $this->update([
            'status' => self::STATUS_ESCALATED,
            'admin_notes' => $adminNotes,
        ]);
    }

    /**
     * Get reports count for company.
     */
    public static function getCompanyReportsCount(int $companyId): int
    {
        return Cache::remember("company.{$companyId}.reports_count", 3600, function () use ($companyId) {
            return self::where('company_id', $companyId)->active()->count();
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

    /**
     * Get pending reports count.
     */
    public static function getPendingReportsCount(): int
    {
        return Cache::remember('reports.pending_count', 1800, function () {
            return self::pending()->active()->count();
        });
    }

    /**
     * Get most reported companies.
     */
    public static function getMostReported(int $limit = 10): \Illuminate\Support\Collection
    {
        return Cache::remember("companies.most_reported.{$limit}", 3600, function () use ($limit) {
            return self::select('company_id')
                      ->selectRaw('COUNT(*) as reports_count')
                      ->active()
                      ->groupBy('company_id')
                      ->orderByDesc('reports_count')
                      ->limit($limit)
                      ->with('company')
                      ->get();
        });
    }

    /**
     * Check if user has already reported company.
     */
    public static function hasUserReported(int $userId, int $companyId): bool
    {
        return Cache::remember("user.{$userId}.company.{$companyId}.reported", 3600, function () use ($userId, $companyId) {
            return self::where('user_id', $userId)
                      ->where('company_id', $companyId)
                      ->active()
                      ->exists();
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
            'reports.pending_count',
            'reports.active',
            'reports.resolved',
        ];

        foreach ($cacheKeys as $key) {
            Cache::forget($key);
        }

        // Clear specific caches
        Cache::forget("company.{$this->company_id}.reports_count");
        Cache::forget("user.{$this->user_id}.reports_count");
        Cache::forget("user.{$this->user_id}.company.{$this->company_id}.reported");
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