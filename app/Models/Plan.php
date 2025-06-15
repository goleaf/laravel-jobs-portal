<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

/**
 * Class Plan
 *
 * @property int $id
 * @property string $name
 * @property string|null $stripe_plan_id
 * @property int $allowed_jobs
 * @property float $amount
 * @property bool $is_trial_plan
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Subscription[] $subscriptions
 */
class Plan extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'stripe_plan_id',
        'allowed_jobs',
        'amount',
        'is_trial_plan',
        'salary_currency_id',
        'is_active',
        'is_featured',
        'priority_support',
        'analytics_access',
        'max_featured_jobs',
        'duration_days'
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_trial_plan' => 'boolean',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'priority_support' => 'boolean',
            'analytics_access' => 'boolean',
            'amount' => 'decimal:2',
            'allowed_jobs' => 'integer',
            'max_featured_jobs' => 'integer',
            'duration_days' => 'integer',
            'salary_currency_id' => 'integer',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /**
     * Relationships
     */
    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    public function salaryCurrency()
    {
        return $this->belongsTo(SalaryCurrency::class, 'salary_currency_id');
    }

    /**
     * Scopes
     */

    /**
     * Scope for trial plans.
     */
    public function scopeTrial($query)
    {
        return $query->where('is_trial_plan', true);
    }

    /**
     * Scope for paid plans.
     */
    public function scopePaid($query)
    {
        return $query->where('is_trial_plan', false);
    }

    /**
     * Scope for plans with Stripe integration.
     */
    public function scopeWithStripe($query)
    {
        return $query->whereNotNull('stripe_plan_id');
    }

    /**
     * Scope for plans without Stripe integration.
     */
    public function scopeWithoutStripe($query)
    {
        return $query->whereNull('stripe_plan_id');
    }

    /**
     * Scope for plans by price range.
     */
    public function scopeByPriceRange($query, $min, $max)
    {
        return $query->whereBetween('amount', [$min, $max]);
    }

    /**
     * Scope for free plans.
     */
    public function scopeFree($query)
    {
        return $query->where('amount', 0);
    }

    /**
     * Scope for premium plans.
     */
    public function scopePremium($query)
    {
        return $query->where('amount', '>', 0);
    }

    /**
     * Scope for plans with unlimited jobs.
     */
    public function scopeUnlimited($query)
    {
        return $query->where('allowed_jobs', -1);
    }

    /**
     * Scope for plans with job limits.
     */
    public function scopeLimited($query)
    {
        return $query->where('allowed_jobs', '>', 0);
    }

    /**
     * Scope for searching plans.
     */
    public function scopeSearch($query, string $term)
    {
        return $query->where('name', 'like', "%{$term}%");
    }

    /**
     * Scope for recent plans.
     */
    public function scopeRecent($query, int $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Scope for old plans.
     */
    public function scopeOld($query, int $days = 365)
    {
        return $query->where('created_at', '<', now()->subDays($days));
    }

    /**
     * Scope for popular plans (with most subscriptions).
     */
    public function scopePopular($query, int $limit = 5)
    {
        return $query->withCount('subscriptions')
                    ->orderBy('subscriptions_count', 'desc')
                    ->limit($limit);
    }

    /**
     * Scope for alphabetical ordering.
     */
    public function scopeAlphabetical($query)
    {
        return $query->orderBy('name', 'asc');
    }

    /**
     * Scope for ordering by price.
     */
    public function scopeOrderByPrice($query, string $direction = 'asc')
    {
        return $query->orderBy('amount', $direction);
    }

    /**
     * Scope for ordering by job allowance.
     */
    public function scopeOrderByJobs($query, string $direction = 'desc')
    {
        return $query->orderBy('allowed_jobs', $direction);
    }

    /**
     * Scope for plans by currency.
     */
    public function scopeByCurrency($query, int $currencyId)
    {
        return $query->where('salary_currency_id', $currencyId);
    }

    /**
     * Scope for plans by duration.
     */
    public function scopeByDuration($query, int $days)
    {
        return $query->where('duration_days', $days);
    }

    /**
     * Scope for monthly plans.
     */
    public function scopeMonthly($query)
    {
        return $query->where('duration_days', 30);
    }

    /**
     * Scope for yearly plans.
     */
    public function scopeYearly($query)
    {
        return $query->where('duration_days', 365);
    }

    /**
     * Helper Methods
     */

    /**
     * Check if plan is free.
     */
    public function isFree(): bool
    {
        return $this->amount == 0;
    }

    /**
     * Check if plan has unlimited jobs.
     */
    public function hasUnlimitedJobs(): bool
    {
        return $this->allowed_jobs == -1;
    }

    /**
     * Get formatted price.
     */
    public function getFormattedPrice(): string
    {
        if ($this->isFree()) {
            return 'Free';
        }
        
        $currency = $this->salaryCurrency ? $this->salaryCurrency->currency_symbol : '$';
        return $currency . number_format($this->amount, 2);
    }

    // =============================================
    // CACHE METHODS - Enhanced Caching Strategy
    // =============================================

    /**
     * Get cached active plans.
     */
    public static function getCachedActive(): \Illuminate\Database\Eloquent\Collection
    {
        return Cache::remember('plans.active', now()->addHours(6), function () {
            return static::active()->orderByPrice()->get();
        });
    }

    /**
     * Get cached featured plans.
     */
    public static function getCachedFeatured(): \Illuminate\Database\Eloquent\Collection
    {
        return Cache::remember('plans.featured', now()->addHours(3), function () {
            return static::featured()->active()->orderByPrice()->get();
        });
    }

    /**
     * Get cached popular plans.
     */
    public static function getCachedPopular(int $limit = 5): \Illuminate\Database\Eloquent\Collection
    {
        return Cache::remember("plans.popular.{$limit}", now()->addHours(1), function () use ($limit) {
            return static::popular($limit)->active()->get();
        });
    }

    /**
     * Get cached free plans.
     */
    public static function getCachedFree(): \Illuminate\Database\Eloquent\Collection
    {
        return Cache::remember('plans.free', now()->addHours(12), function () {
            return static::free()->active()->get();
        });
    }

    // =============================================
    // ADDITIONAL HELPER METHODS
    // =============================================

    /**
     * Check if plan is trial.
     */
    public function isTrial(): bool
    {
        return $this->is_trial_plan;
    }

    /**
     * Check if plan is active.
     */
    public function isActive(): bool
    {
        return $this->is_active;
    }

    /**
     * Check if plan is featured.
     */
    public function isFeatured(): bool
    {
        return $this->is_featured;
    }

    /**
     * Check if plan has priority support.
     */
    public function hasPrioritySupport(): bool
    {
        return $this->priority_support;
    }

    /**
     * Check if plan has analytics access.
     */
    public function hasAnalyticsAccess(): bool
    {
        return $this->analytics_access;
    }

    /**
     * Get plan type label.
     */
    public function getTypeLabel(): string
    {
        if ($this->isFree()) return 'Free';
        if ($this->isTrial()) return 'Trial';
        return 'Premium';
    }

    /**
     * Get plan duration in human readable format.
     */
    public function getDurationLabel(): string
    {
        if ($this->duration_days >= 365) {
            $years = round($this->duration_days / 365);
            return $years == 1 ? '1 Year' : "{$years} Years";
        }
        if ($this->duration_days >= 30) {
            $months = round($this->duration_days / 30);
            return $months == 1 ? '1 Month' : "{$months} Months";
        }
        return "{$this->duration_days} Days";
    }

    /**
     * Get jobs allowance label.
     */
    public function getJobsLabel(): string
    {
        if ($this->hasUnlimitedJobs()) {
            return 'Unlimited Jobs';
        }
        return $this->allowed_jobs . ' Job' . ($this->allowed_jobs != 1 ? 's' : '');
    }

    /**
     * Get plan features array.
     */
    public function getFeatures(): array
    {
        $features = [
            $this->getJobsLabel(),
        ];

        if ($this->max_featured_jobs > 0) {
            $features[] = $this->max_featured_jobs . ' Featured Job' . ($this->max_featured_jobs != 1 ? 's' : '');
        }

        if ($this->hasPrioritySupport()) {
            $features[] = 'Priority Support';
        }

        if ($this->hasAnalyticsAccess()) {
            $features[] = 'Analytics Access';
        }

        return $features;
    }

    /**
     * Get subscription count.
     */
    public function getSubscriptionsCount(): int
    {
        return $this->subscriptions()->count();
    }

    /**
     * Get active subscription count.
     */
    public function getActiveSubscriptionsCount(): int
    {
        return $this->subscriptions()->where('status', 'active')->count();
    }

    /**
     * Calculate savings compared to monthly if yearly.
     */
    public function getSavingsPercentage(): float
    {
        if ($this->duration_days != 365) return 0;
        
        $monthlyPlan = static::where('name', str_replace('Yearly', 'Monthly', $this->name))
                            ->where('duration_days', 30)
                            ->first();
        
        if (!$monthlyPlan) return 0;
        
        $yearlyEquivalent = $monthlyPlan->amount * 12;
        if ($yearlyEquivalent <= $this->amount) return 0;
        
        return round((($yearlyEquivalent - $this->amount) / $yearlyEquivalent) * 100, 1);
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
            'plans.active',
            'plans.featured',
            'plans.free',
        ];

        // Clear popular cache variants
        for ($i = 3; $i <= 10; $i++) {
            $cacheKeys[] = "plans.popular.{$i}";
        }

        foreach ($cacheKeys as $key) {
            Cache::forget($key);
        }
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
            $model->is_active = $model->is_active ?? true;
            $model->is_featured = $model->is_featured ?? false;
            $model->is_trial_plan = $model->is_trial_plan ?? false;
            $model->priority_support = $model->priority_support ?? false;
            $model->analytics_access = $model->analytics_access ?? false;
            $model->max_featured_jobs = $model->max_featured_jobs ?? 0;
            $model->duration_days = $model->duration_days ?? 30;
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