<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * FavouriteCompany Model - Enhanced with Enhanced patterns.
 *
 * @property int $id
 * @property int $user_id
 * @property int $company_id
 * @property bool $is_active
 * @property bool $is_featured
 * @property null|string $notes
 * @property null|Carbon $created_at
 * @property null|Carbon $updated_at
 * @property null|Carbon $deleted_at
 * @property User $user
 * @property Company $company
 * @property bool $is_recent
 * @property string $status_label
 *
 * Enhanced Enhanced Scopes:
 *
 * @method static Builder active()
 * @method static Builder inactive()
 * @method static Builder featured()
 * @method static Builder notFeatured()
 * @method static Builder recent(int $days = 30)
 * @method static Builder old(int $days = 365)
 * @method static Builder byUser(int $userId)
 * @method static Builder byCompany(int $companyId)
 * @method static Builder byUserAndCompany(int $userId, int $companyId)
 * @method static Builder today()
 * @method static Builder thisWeek()
 * @method static Builder thisMonth()
 * @method static Builder search(string $term)
 * @method static Builder latest()
 * @method static Builder oldest()
 * @method static Builder popular()
 * @method static Builder withRelations()
 *
 * @mixin \Eloquent
 */
class FavouriteCompany extends Model
{
    use HasFactory;
    use LogsActivity;
    use SoftDeletes;

    /**
     * Validation rules.
     */
    public static array $rules = [
        'user_id' => 'required|integer|exists:users,id',
        'company_id' => 'required|integer|exists:companies,id',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'notes' => 'nullable|string|max:500',
    ];

    /**
     * Custom validation messages.
     */
    public static array $messages = [
        'user_id.required' => 'User is required',
        'user_id.exists' => 'Selected user does not exist',
        'company_id.required' => 'Company is required',
        'company_id.exists' => 'Selected company does not exist',
        'notes.max' => 'Notes cannot exceed 500 characters',
    ];

    /**
     * The table associated with the model.
     */
    protected $table = 'favourite_companies';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'user_id',
        'company_id',
        'is_active',
        'is_featured',
        'notes',
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
            ->logOnly(['user_id', 'company_id', 'is_active', 'is_featured'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn (string $eventName) => "Favourite company has been {$eventName}");
    }

    // =============================================
    // RELATIONSHIPS
    // =============================================

    /**
     * Get the user who favourited the company.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the favourited company.
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    // =============================================
    // SCOPES - Basic Status
    // =============================================

    /**
     * Scope for active favourite companies.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for inactive favourite companies.
     */
    public function scopeInactive(Builder $query): Builder
    {
        return $query->where('is_active', false);
    }

    /**
     * Scope for featured favourite companies.
     */
    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope for non-featured favourite companies.
     */
    public function scopeNotFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', false);
    }

    // =============================================
    // SCOPES - Date-based
    // =============================================

    /**
     * Scope for recent favourite companies.
     */
    public function scopeRecent(Builder $query, int $days = 30): Builder
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Scope for old favourite companies.
     */
    public function scopeOld(Builder $query, int $days = 365): Builder
    {
        return $query->where('created_at', '<=', now()->subDays($days));
    }

    /**
     * Scope for today's favourite companies.
     */
    public function scopeToday(Builder $query): Builder
    {
        return $query->whereDate('created_at', today());
    }

    /**
     * Scope for this week's favourite companies.
     */
    public function scopeThisWeek(Builder $query): Builder
    {
        return $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
    }

    /**
     * Scope for this month's favourite companies.
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
     * Scope for favourite companies by user.
     */
    public function scopeByUser(Builder $query, int $userId): Builder
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope for favourite companies by company.
     */
    public function scopeByCompany(Builder $query, int $companyId): Builder
    {
        return $query->where('company_id', $companyId);
    }

    /**
     * Scope for specific user and company combination.
     */
    public function scopeByUserAndCompany(Builder $query, int $userId, int $companyId): Builder
    {
        return $query->where('user_id', $userId)->where('company_id', $companyId);
    }

    // =============================================
    // SCOPES - Aggregation
    // =============================================

    /**
     * Scope for popular companies (most favourited).
     */
    public function scopePopular(Builder $query): Builder
    {
        return $query->select('company_id')
            ->selectRaw('COUNT(*) as favourites_count')
            ->groupBy('company_id')
            ->orderByDesc('favourites_count');
    }

    // =============================================
    // SCOPES - Search & Ordering
    // =============================================

    /**
     * Scope for searching favourite companies.
     */
    public function scopeSearch(Builder $query, string $term): Builder
    {
        return $query->where('notes', 'like', '%'.$term.'%')
            ->orWhereHas('company', function ($companyQuery) use ($term) {
                $companyQuery->where('name', 'like', '%'.$term.'%')
                    ->orWhere('slug', 'like', '%'.$term.'%')
                    ->orWhere('industry', 'like', '%'.$term.'%');
            })
            ->orWhereHas('user', function ($userQuery) use ($term) {
                $userQuery->where('first_name', 'like', '%'.$term.'%')
                    ->orWhere('last_name', 'like', '%'.$term.'%')
                    ->orWhere('email', 'like', '%'.$term.'%');
            });
    }

    /**
     * Scope for latest favourite companies.
     */
    public function scopeLatest(Builder $query): Builder
    {
        return $query->orderBy('created_at', 'desc');
    }

    /**
     * Scope for oldest favourite companies.
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
        return $query->with(['user', 'company']);
    }

    // =============================================
    // ATTRIBUTE ACCESSORS
    // =============================================

    /**
     * Check if favourite company is recent.
     */
    public function getIsRecentAttribute(): bool
    {
        return $this->created_at && $this->created_at->isAfter(now()->subDays(7));
    }

    /**
     * Get status label.
     */
    public function getStatusLabelAttribute(): string
    {
        if (! $this->is_active) {
            return 'Inactive';
        }

        return $this->is_featured ? 'Featured Favourite' : 'Favourite';
    }

    // =============================================
    // HELPER METHODS
    // =============================================

    /**
     * Check if favourite company is active.
     */
    public function isActive(): bool
    {
        return $this->is_active;
    }

    /**
     * Check if favourite company is featured.
     */
    public function isFeatured(): bool
    {
        return $this->is_featured;
    }

    /**
     * Toggle active status.
     */
    public function toggleActive(): bool
    {
        return $this->update(['is_active' => ! $this->is_active]);
    }

    /**
     * Toggle featured status.
     */
    public function toggleFeatured(): bool
    {
        return $this->update(['is_featured' => ! $this->is_featured]);
    }

    /**
     * Check if user has favourited a company.
     */
    public static function isFavouritedByUser(int $userId, int $companyId): bool
    {
        return Cache::remember("user.{$userId}.company.{$companyId}.favourited", 3600, function () use ($userId, $companyId) {
            return self::where('user_id', $userId)
                ->where('company_id', $companyId)
                ->active()
                ->exists();
        });
    }

    /**
     * Get user's favourite companies count.
     */
    public static function getUserFavouritesCount(int $userId): int
    {
        return Cache::remember("user.{$userId}.favourite_companies_count", 3600, function () use ($userId) {
            return self::where('user_id', $userId)->active()->count();
        });
    }

    /**
     * Get company's favourites count.
     */
    public static function getCompanyFavouritesCount(int $companyId): int
    {
        return Cache::remember("company.{$companyId}.favourites_count", 3600, function () use ($companyId) {
            return self::where('company_id', $companyId)->active()->count();
        });
    }

    /**
     * Get most favourited companies.
     */
    public static function getMostFavourited(int $limit = 10): Collection
    {
        return Cache::remember("companies.most_favourited.{$limit}", 3600, function () use ($limit) {
            return self::select('company_id')
                ->selectRaw('COUNT(*) as favourites_count')
                ->active()
                ->groupBy('company_id')
                ->orderByDesc('favourites_count')
                ->limit($limit)
                ->with('company')
                ->get();
        });
    }

    /**
     * Add company to user's favourites.
     */
    public static function addToFavourites(int $userId, int $companyId, array $options = []): self
    {
        $favourite = self::updateOrCreate(
            ['user_id' => $userId, 'company_id' => $companyId],
            array_merge([
                'is_active' => true,
                'is_featured' => false,
            ], $options)
        );

        // Clear related caches
        Cache::forget("user.{$userId}.company.{$companyId}.favourited");
        Cache::forget("user.{$userId}.favourite_companies_count");
        Cache::forget("company.{$companyId}.favourites_count");

        return $favourite;
    }

    /**
     * Remove company from user's favourites.
     */
    public static function removeFromFavourites(int $userId, int $companyId): bool
    {
        $result = self::where('user_id', $userId)
            ->where('company_id', $companyId)
            ->delete();

        // Clear related caches
        Cache::forget("user.{$userId}.company.{$companyId}.favourited");
        Cache::forget("user.{$userId}.favourite_companies_count");
        Cache::forget("company.{$companyId}.favourites_count");

        return $result > 0;
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
            'favourite_companies.active',
            'favourite_companies.featured',
            'favourite_companies.popular',
            'companies.most_favourited.10',
        ];

        foreach ($cacheKeys as $key) {
            Cache::forget($key);
        }

        // Clear specific caches
        Cache::forget("user.{$this->user_id}.company.{$this->company_id}.favourited");
        Cache::forget("user.{$this->user_id}.favourite_companies_count");
        Cache::forget("company.{$this->company_id}.favourites_count");
    }

    /**
     * Get the attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'id' => 'integer',
            'user_id' => 'integer',
            'company_id' => 'integer',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
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
            if (is_null($model->is_featured)) {
                $model->is_featured = false;
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
