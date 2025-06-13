<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Illuminate\Support\Carbon;

/**
 * App\Models\Application
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
 * @property-read Job $job
 * @property-read Candidate $candidate
 * @property-read Resume|null $resume
 */
class Application extends Model
{
    use HasFactory, LogsActivity, SoftDeletes;

    /**
     * The table associated with the model.
     */
    protected $table = 'applications';

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

    /**
     * Scope a query to only include old records.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOld(\Illuminate\Database\Eloquent\Builder $query): \Illuminate\Database\Eloquent\Builder
    {
        return $query->orderBy("created_at", "asc");
    }

    // Relations

    /**
     * Get the job that the application is for.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function job(): BelongsTo
    {
        return $this->belongsTo(Job::class);
    }

    /**
     * Get the candidate who made the application.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function candidate(): BelongsTo
    {
        return $this->belongsTo(Candidate::class);
    }

    /**
     * Get the resume associated with this application.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function resume(): BelongsTo
    {
        return $this->belongsTo(Resume::class);
    }

    // Scopes

    /**
     * Scope a query to only include applications with a specific status.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $status
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope a query to only include recent applications.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $date
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeRecent($query, $date = '1 month ago')
    {
        return $query->where('applied_at', '>=', now()->sub($date));
    }

    /**
     * Scope a query to only include active records.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include inactive records.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    /**
     * Scope a query to search records by name or relevant fields.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $search
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSearch($query, $search)
    {
        return $query->whereHas('candidate', function ($q) use ($search) {
            $q->where('first_name', 'like', "%{$search}%")
              ->orWhere('last_name', 'like', "%{$search}%")
              ->orWhere('email', 'like', "%{$search}%");
        })->orWhereHas('job', function ($q) use ($search) {
            $q->where('title', 'like', "%{$search}%")
              ->orWhere('description', 'like', "%{$search}%");
        })->orWhere('notes', 'like', "%{$search}%")
          ->orWhere('expected_salary', 'like', "%{$search}%");
    }

    /**
     * Scope for applications created today
     */
    public function scopeToday($query)
    {
        return $query->whereDate('created_at', today());
    }

    /**
     * Scope for applications created this week
     */
    public function scopeThisWeek($query)
    {
        return $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
    }

    /**
     * Scope for applications created this month
     */
    public function scopeThisMonth($query)
    {
        return $query->whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year);
    }

    /**
     * Scope for shortlisted applications
     */
    public function scopeShortlisted($query)
    {
        return $query->where('status', self::STATUS_SHORTLISTED);
    }

    /**
     * Scope for interviewed applications
     */
    public function scopeInterviewed($query)
    {
        return $query->where('status', self::STATUS_INTERVIEWED);
    }

    /**
     * Scope for reviewing applications
     */
    public function scopeReviewing($query)
    {
        return $query->where('status', self::STATUS_REVIEWING);
    }

    // Context7 Enhanced Scopes

    /**
     * Scope a query to filter applications by candidate.
     */
    public function scopeByCandidate($query, int $candidateId)
    {
        return $query->where('candidate_id', $candidateId);
    }

    /**
     * Scope a query to filter applications by job.
     */
    public function scopeByJob($query, int $jobId)
    {
        return $query->where('job_id', $jobId);
    }

    /**
     * Scope a query to only include pending applications.
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /**
     * Scope a query to only include hired applications.
     */
    public function scopeHired($query)
    {
        return $query->where('status', self::STATUS_HIRED);
    }

    /**
     * Scope a query to only include rejected applications.
     */
    public function scopeRejected($query)
    {
        return $query->where('status', self::STATUS_REJECTED);
    }

    /**
     * Scope a query to filter by salary range.
     */
    public function scopeBySalaryRange($query, float $min = null, float $max = null)
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
     * Scope a query to order by latest applications.
     */
    public function scopeLatest($query)
    {
        return $query->orderBy('applied_at', 'desc');
    }

    /**
     * Scope a query to order by oldest applications.
     */
    public function scopeOldest($query)
    {
        return $query->orderBy('applied_at', 'asc');
    }

    // Context7 Helper Methods

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
     * Get formatted expected salary.
     */
    public function getFormattedSalaryAttribute(): string
    {
        return number_format($this->expected_salary, 2);
    }

    /**
     * Get human readable applied date.
     */
    public function getAppliedDateAttribute(): string
    {
        return $this->applied_at ? $this->applied_at->format('M d, Y') : 'Not specified';
    }
}
