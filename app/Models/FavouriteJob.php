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
 * FavouriteJob Model - Enhanced with Context7 patterns
 *
 * @property int $id
 * @property int $user_id
 * @property int $job_id
 * @property bool $is_active
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 *
 * @property-read User $user
 * @property-read Job $job
 * @property-read bool $is_recent
 * @property-read bool $job_is_active
 * @property-read bool $job_is_featured
 *
 * Context7 Enhanced Scopes:
 * @method static Builder active()
 * @method static Builder inactive()
 * @method static Builder recent(int $days = 30)
 * @method static Builder today()
 * @method static Builder thisWeek()
 * @method static Builder thisMonth()
 * @method static Builder byUser(int $userId)
 * @method static Builder byJob(int $jobId)
 * @method static Builder withActiveJobs()
 * @method static Builder withInactiveJobs()
 * @method static Builder withFeaturedJobs()
 * @method static Builder withExpiredJobs()
 * @method static Builder search(string $term)
 * @method static Builder latest()
 * @method static Builder oldest()
 * @method static Builder popular()
 * @method static Builder trending()
 *
 * @mixin \Eloquent
 */
class FavouriteJob extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    /**
     * The table associated with the model.
     */
    protected $table = 'favourite_jobs';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'user_id',
        'job_id',
        'is_active',
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
            'job_id' => 'integer',
            'is_active' => 'boolean',
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
        'job_id' => 'required|integer|exists:jobs,id',
        'is_active' => 'boolean',
    ];

    /**
     * Activity log configuration
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['user_id', 'job_id', 'is_active'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn(string $eventName) => "Favourite job has been {$eventName}");
    }

    // =============================================
    // RELATIONSHIPS
    // =============================================

    /**
     * Get the user that owns the favourite job.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the job that is favourited.
     */
    public function job(): BelongsTo
    {
        return $this->belongsTo(Job::class);
    }

    // =============================================
    // SCOPES - Basic Status
    // =============================================

    /**
     * Scope for active favourite jobs.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for inactive favourite jobs.
     */
    public function scopeInactive(Builder $query): Builder
    {
        return $query->where('is_active', false);
    }

    // =============================================
    // SCOPES - Date-based
    // =============================================

    /**
     * Scope for recent favourite jobs.
     */
    public function scopeRecent(Builder $query, int $days = 30): Builder
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Scope for today's favourite jobs.
     */
    public function scopeToday(Builder $query): Builder
    {
        return $query->whereDate('created_at', today());
    }

    /**
     * Scope for this week's favourite jobs.
     */
    public function scopeThisWeek(Builder $query): Builder
    {
        return $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
    }

    /**
     * Scope for this month's favourite jobs.
     */
    public function scopeThisMonth(Builder $query): Builder
    {
        return $query->whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year);
    }

    // =============================================
    // SCOPES - User & Job Filtering
    // =============================================

    /**
     * Scope for favourite jobs by user.
     */
    public function scopeByUser(Builder $query, int $userId): Builder
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope for favourite jobs by job.
     */
    public function scopeByJob(Builder $query, int $jobId): Builder
    {
        return $query->where('job_id', $jobId);
    }

    // =============================================
    // SCOPES - Job Status Filtering
    // =============================================

    /**
     * Scope for favourites with active jobs.
     */
    public function scopeWithActiveJobs(Builder $query): Builder
    {
        return $query->whereHas('job', function ($jobQuery) {
            $jobQuery->where('is_active', true);
        });
    }

    /**
     * Scope for favourites with inactive jobs.
     */
    public function scopeWithInactiveJobs(Builder $query): Builder
    {
        return $query->whereHas('job', function ($jobQuery) {
            $jobQuery->where('is_active', false);
        });
    }

    /**
     * Scope for favourites with featured jobs.
     */
    public function scopeWithFeaturedJobs(Builder $query): Builder
    {
        return $query->whereHas('job', function ($jobQuery) {
            $jobQuery->where('is_featured', true);
        });
    }

    /**
     * Scope for favourites with expired jobs.
     */
    public function scopeWithExpiredJobs(Builder $query): Builder
    {
        return $query->whereHas('job', function ($jobQuery) {
            $jobQuery->where('deadline', '<', now());
        });
    }

    // =============================================
    // SCOPES - Search & Filtering
    // =============================================

    /**
     * Scope for searching favourite jobs.
     */
    public function scopeSearch(Builder $query, string $term): Builder
    {
        return $query->whereHas('job', function ($jobQuery) use ($term) {
            $jobQuery->where('title', 'like', '%' . $term . '%')
                    ->orWhere('description', 'like', '%' . $term . '%')
                    ->orWhereHas('company', function ($companyQuery) use ($term) {
                        $companyQuery->where('ceo', 'like', '%' . $term . '%');
                    });
        })->orWhereHas('user', function ($userQuery) use ($term) {
            $userQuery->where('first_name', 'like', '%' . $term . '%')
                     ->orWhere('last_name', 'like', '%' . $term . '%')
                     ->orWhere('email', 'like', '%' . $term . '%');
        });
    }

    // =============================================
    // SCOPES - Ordering
    // =============================================

    /**
     * Scope for latest favourite jobs.
     */
    public function scopeLatest(Builder $query): Builder
    {
        return $query->orderBy('created_at', 'desc');
    }

    /**
     * Scope for oldest favourite jobs.
     */
    public function scopeOldest(Builder $query): Builder
    {
        return $query->orderBy('created_at', 'asc');
    }

    /**
     * Scope for popular favourite jobs (most favourited jobs).
     */
    public function scopePopular(Builder $query): Builder
    {
        return $query->select('job_id')
                    ->selectRaw('COUNT(*) as favourites_count')
                    ->groupBy('job_id')
                    ->orderByDesc('favourites_count');
    }

    /**
     * Scope for trending favourite jobs (recently favourited).
     */
    public function scopeTrending(Builder $query): Builder
    {
        return $query->where('created_at', '>=', now()->subDays(7))
                    ->select('job_id')
                    ->selectRaw('COUNT(*) as recent_favourites_count')
                    ->groupBy('job_id')
                    ->orderByDesc('recent_favourites_count');
    }

    // =============================================
    // ATTRIBUTE ACCESSORS
    // =============================================

    /**
     * Check if favourite job is recent.
     */
    public function getIsRecentAttribute(): bool
    {
        return $this->created_at && $this->created_at->isAfter(now()->subDays(7));
    }

    /**
     * Check if the associated job is active.
     */
    public function getJobIsActiveAttribute(): bool
    {
        return $this->job ? $this->job->is_active : false;
    }

    /**
     * Check if the associated job is featured.
     */
    public function getJobIsFeaturedAttribute(): bool
    {
        return $this->job ? $this->job->is_featured : false;
    }

    // =============================================
    // HELPER METHODS
    // =============================================

    /**
     * Check if favourite job is active.
     */
    public function isActive(): bool
    {
        return $this->is_active;
    }

    /**
     * Check if the associated job is still available.
     */
    public function isJobAvailable(): bool
    {
        return $this->job && $this->job->is_active && !$this->job->isExpired();
    }

    /**
     * Check if this is a recent favourite.
     */
    public function isRecent(int $days = 7): bool
    {
        return $this->created_at && $this->created_at->isAfter(now()->subDays($days));
    }

    /**
     * Get user's favourite jobs count.
     */
    public static function getUserFavouritesCount(int $userId): int
    {
        return Cache::remember("user.{$userId}.favourites_count", 3600, function () use ($userId) {
            return self::where('user_id', $userId)->active()->count();
        });
    }

    /**
     * Get job's favourites count.
     */
    public static function getJobFavouritesCount(int $jobId): int
    {
        return Cache::remember("job.{$jobId}.favourites_count", 3600, function () use ($jobId) {
            return self::where('job_id', $jobId)->active()->count();
        });
    }

    /**
     * Check if user has favourited a job.
     */
    public static function isJobFavouritedByUser(int $jobId, int $userId): bool
    {
        return Cache::remember("user.{$userId}.job.{$jobId}.favourited", 1800, function () use ($jobId, $userId) {
            return self::where('job_id', $jobId)
                      ->where('user_id', $userId)
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
            'favourite_jobs.active',
            'favourite_jobs.recent',
            'favourite_jobs.popular',
            'favourite_jobs.trending',
        ];

        foreach ($cacheKeys as $key) {
            Cache::forget($key);
        }

        // Clear user and job specific caches
        Cache::forget("user.{$this->user_id}.favourites_count");
        Cache::forget("job.{$this->job_id}.favourites_count");
        Cache::forget("user.{$this->user_id}.job.{$this->job_id}.favourited");
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
