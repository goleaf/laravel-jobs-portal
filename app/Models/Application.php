<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Illuminate\Support\Carbon;

/**
 * Application Model - Enhanced with Enhanced patterns
 *
 * @property int $id
 * @property int $job_id
 * @property int $candidate_id
 * @property int|null $resume_id
 * @property float $expected_salary
 * @property string|null $status
 * @property string|null $notes
 * @property bool $is_active
 * @property Carbon|null $applied_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 *
 * @property-read Job $job
 * @property-read Candidate $candidate
 * @property-read Resume|null $resume
 * @property-read string $formatted_salary
 * @property-read string $applied_date
 * @property-read string $status_label
 * @property-read string $status_color
 * @property-read bool $is_recent
 * @property-read bool $is_pending
 * @property-read bool $is_hired
 * @property-read bool $is_rejected
 *
 * Enhanced Enhanced Scopes:
 * @method static Builder active()
 * @method static Builder inactive()
 * @method static Builder byStatus(string $status)
 * @method static Builder pending()
 * @method static Builder reviewing()
 * @method static Builder shortlisted()
 * @method static Builder interviewed()
 * @method static Builder hired()
 * @method static Builder rejected()
 * @method static Builder withdrawn()
 * @method static Builder recent(int $days = 30)
 * @method static Builder today()
 * @method static Builder thisWeek()
 * @method static Builder thisMonth()
 * @method static Builder thisYear()
 * @method static Builder byCandidate(int $candidateId)
 * @method static Builder byJob(int $jobId)
 * @method static Builder bySalaryRange(float $min = null, float $max = null)
 * @method static Builder search(string $term)
 * @method static Builder latest()
 * @method static Builder oldest()
 * @method static Builder withCounts()
 * @method static Builder popular()
 * @method static Builder successful()
 * @method static Builder unsuccessful()
 *
 * @mixin \Eloquent
 */
class Application extends Model
{
    use HasFactory, LogsActivity, SoftDeletes;

    /**
     * The table associated with the model.
     */
    protected $table = 'job_applications';

    /**
     * Application status constants
     */
    public const STATUS_PENDING = 'pending';
    public const STATUS_REVIEWING = 'reviewing';
    public const STATUS_SHORTLISTED = 'shortlisted';
    public const STATUS_INTERVIEWED = 'interviewed';
    public const STATUS_HIRED = 'hired';
    public const STATUS_REJECTED = 'rejected';
    public const STATUS_WITHDRAWN = 'withdrawn';

    /**
     * Successful status list
     */
    public const SUCCESSFUL_STATUSES = [
        self::STATUS_HIRED,
        self::STATUS_SHORTLISTED,
        self::STATUS_INTERVIEWED,
    ];

    /**
     * Unsuccessful status list
     */
    public const UNSUCCESSFUL_STATUSES = [
        self::STATUS_REJECTED,
        self::STATUS_WITHDRAWN,
    ];

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'job_id',
        'candidate_id', 
        'resume_id',
        'expected_salary',
        'status',
        'notes',
        'is_active',
        'applied_at'
    ];

    /**
     * The attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'id' => 'integer',
            'job_id' => 'integer',
            'candidate_id' => 'integer',
            'resume_id' => 'integer',
            'expected_salary' => 'decimal:2',
            'is_active' => 'boolean',
            'applied_at' => 'datetime',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }

    /**
     * The attributes that should be hidden for serialization.
     */
    protected $hidden = [
        'deleted_at',
    ];

    /**
     * Validation rules
     */
    public static array $rules = [
        'job_id' => 'required|integer|exists:jobs,id',
        'candidate_id' => 'required|integer|exists:candidates,id',
        'resume_id' => 'nullable|integer|exists:resumes,id',
        'expected_salary' => 'required|numeric|min:0|max:9999999999',
        'status' => 'nullable|string|in:pending,reviewing,shortlisted,interviewed,hired,rejected,withdrawn',
        'notes' => 'nullable|string|max:2000',
        'is_active' => 'boolean',
        'applied_at' => 'nullable|date',
    ];

    /**
     * Activity log configuration for spatie/laravel-activitylog
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['job_id', 'candidate_id', 'status', 'expected_salary', 'is_active'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn(string $eventName) => "Application has been {$eventName}");
    }

    // =============================================
    // RELATIONSHIPS
    // =============================================

    /**
     * Get the job that the application is for.
     */
    public function job(): BelongsTo
    {
        return $this->belongsTo(Job::class);
    }

    /**
     * Get the candidate who made the application.
     */
    public function candidate(): BelongsTo
    {
        return $this->belongsTo(Candidate::class);
    }

    /**
     * Get the resume associated with this application.
     */
    public function resume(): BelongsTo
    {
        return $this->belongsTo(Resume::class);
    }

    // =============================================
    // SCOPES - Basic Status
    // =============================================

    /**
     * Scope for active applications.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for inactive applications.
     */
    public function scopeInactive(Builder $query): Builder
    {
        return $query->where('is_active', false);
    }

    /**
     * Scope for applications by status.
     */
    public function scopeByStatus(Builder $query, string $status): Builder
    {
        return $query->where('status', $status);
    }

    /**
     * Scope for pending applications.
     */
    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /**
     * Scope for reviewing applications.
     */
    public function scopeReviewing(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_REVIEWING);
    }

    /**
     * Scope for shortlisted applications.
     */
    public function scopeShortlisted(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_SHORTLISTED);
    }

    /**
     * Scope for interviewed applications.
     */
    public function scopeInterviewed(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_INTERVIEWED);
    }

    /**
     * Scope for hired applications.
     */
    public function scopeHired(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_HIRED);
    }

    /**
     * Scope for rejected applications.
     */
    public function scopeRejected(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_REJECTED);
    }

    /**
     * Scope for withdrawn applications.
     */
    public function scopeWithdrawn(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_WITHDRAWN);
    }

    /**
     * Scope for successful applications.
     */
    public function scopeSuccessful(Builder $query): Builder
    {
        return $query->whereIn('status', self::SUCCESSFUL_STATUSES);
    }

    /**
     * Scope for unsuccessful applications.
     */
    public function scopeUnsuccessful(Builder $query): Builder
    {
        return $query->whereIn('status', self::UNSUCCESSFUL_STATUSES);
    }

    // =============================================
    // SCOPES - Date-based
    // =============================================

    /**
     * Scope for recent applications.
     */
    public function scopeRecent(Builder $query, int $days = 30): Builder
    {
        return $query->where('applied_at', '>=', now()->subDays($days));
    }

    /**
     * Scope for today's applications.
     */
    public function scopeToday(Builder $query): Builder
    {
        return $query->whereDate('applied_at', today());
    }

    /**
     * Scope for this week's applications.
     */
    public function scopeThisWeek(Builder $query): Builder
    {
        return $query->whereBetween('applied_at', [now()->startOfWeek(), now()->endOfWeek()]);
    }

    /**
     * Scope for this month's applications.
     */
    public function scopeThisMonth(Builder $query): Builder
    {
        return $query->whereMonth('applied_at', now()->month)
                    ->whereYear('applied_at', now()->year);
    }

    /**
     * Scope for this year's applications.
     */
    public function scopeThisYear(Builder $query): Builder
    {
        return $query->whereYear('applied_at', now()->year);
    }

    // =============================================
    // SCOPES - Filtering
    // =============================================

    /**
     * Scope for applications by candidate.
     */
    public function scopeByCandidate(Builder $query, int $candidateId): Builder
    {
        return $query->where('candidate_id', $candidateId);
    }

    /**
     * Scope for applications by job.
     */
    public function scopeByJob(Builder $query, int $jobId): Builder
    {
        return $query->where('job_id', $jobId);
    }

    /**
     * Scope for applications by salary range.
     */
    public function scopeBySalaryRange(Builder $query, float $min = null, float $max = null): Builder
    {
        if ($min !== null) {
            $query->where('expected_salary', '>=', $min);
        }
        
        if ($max !== null) {
            $query->where('expected_salary', '<=', $max);
        }
        
        return $query;
    }

    /**
     * Scope for searching applications.
     */
    public function scopeSearch(Builder $query, string $term): Builder
    {
        return $query->where(function ($q) use ($term) {
            $q->where('notes', 'like', '%' . $term . '%')
              ->orWhereHas('candidate', function ($candidateQuery) use ($term) {
                  $candidateQuery->where('first_name', 'like', '%' . $term . '%')
                                ->orWhere('last_name', 'like', '%' . $term . '%')
                                ->orWhere('email', 'like', '%' . $term . '%');
              })
              ->orWhereHas('job', function ($jobQuery) use ($term) {
                  $jobQuery->where('title', 'like', '%' . $term . '%');
              });
        });
    }

    // =============================================
    // SCOPES - Ordering
    // =============================================

    /**
     * Scope for latest applications.
     */
    public function scopeLatest(Builder $query): Builder
    {
        return $query->orderBy('applied_at', 'desc');
    }

    /**
     * Scope for oldest applications.
     */
    public function scopeOldest(Builder $query): Builder
    {
        return $query->orderBy('applied_at', 'asc');
    }

    /**
     * Scope for applications with counts.
     */
    public function scopeWithCounts(Builder $query): Builder
    {
        return $query->withCount(['job', 'candidate']);
    }

    /**
     * Scope for popular applications (by job popularity).
     */
    public function scopePopular(Builder $query): Builder
    {
        return $query->whereHas('job', function ($jobQuery) {
            $jobQuery->where('is_featured', true)
                    ->orWhere('views_count', '>', 100);
        });
    }

    // =============================================
    // ATTRIBUTE ACCESSORS
    // =============================================

    /**
     * Get formatted salary attribute.
     */
    public function getFormattedSalaryAttribute(): string
    {
        return number_format($this->expected_salary, 2);
    }

    /**
     * Get applied date attribute.
     */
    public function getAppliedDateAttribute(): string
    {
        return $this->applied_at ? $this->applied_at->format('M d, Y') : '';
    }

    /**
     * Get status label attribute.
     */
    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            self::STATUS_PENDING => 'Pending Review',
            self::STATUS_REVIEWING => 'Under Review',
            self::STATUS_SHORTLISTED => 'Shortlisted',
            self::STATUS_INTERVIEWED => 'Interviewed',
            self::STATUS_HIRED => 'Hired',
            self::STATUS_REJECTED => 'Rejected',
            self::STATUS_WITHDRAWN => 'Withdrawn',
            default => 'Unknown',
        };
    }

    /**
     * Get status color attribute.
     */
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            self::STATUS_PENDING => 'warning',
            self::STATUS_REVIEWING => 'info',
            self::STATUS_SHORTLISTED => 'primary',
            self::STATUS_INTERVIEWED => 'secondary',
            self::STATUS_HIRED => 'success',
            self::STATUS_REJECTED => 'danger',
            self::STATUS_WITHDRAWN => 'dark',
            default => 'light',
        };
    }

    /**
     * Check if application is recent.
     */
    public function getIsRecentAttribute(): bool
    {
        return $this->applied_at && $this->applied_at->isAfter(now()->subDays(7));
    }

    // =============================================
    // HELPER METHODS
    // =============================================

    /**
     * Check if application is pending.
     */
    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * Check if application is hired.
     */
    public function isHired(): bool
    {
        return $this->status === self::STATUS_HIRED;
    }

    /**
     * Check if application is rejected.
     */
    public function isRejected(): bool
    {
        return $this->status === self::STATUS_REJECTED;
    }

    /**
     * Check if application is successful.
     */
    public function isSuccessful(): bool
    {
        return in_array($this->status, self::SUCCESSFUL_STATUSES);
    }

    /**
     * Check if application is unsuccessful.
     */
    public function isUnsuccessful(): bool
    {
        return in_array($this->status, self::UNSUCCESSFUL_STATUSES);
    }

    /**
     * Check if application is active.
     */
    public function isActive(): bool
    {
        return $this->is_active;
    }

    /**
     * Check if application has resume.
     */
    public function hasResume(): bool
    {
        return !is_null($this->resume_id);
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
            'applications.active',
            'applications.pending',
            'applications.hired',
            'applications.recent',
            'applications.today',
            'applications.this_week',
            'applications.this_month',
        ];

        foreach ($cacheKeys as $key) {
            Cache::forget($key);
        }

        // Clear candidate and job specific caches
        Cache::forget("candidate.{$this->candidate_id}.applications");
        Cache::forget("job.{$this->job_id}.applications");
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

        // Set default applied_at timestamp
        static::creating(function ($model) {
            if (is_null($model->applied_at)) {
                $model->applied_at = now();
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
