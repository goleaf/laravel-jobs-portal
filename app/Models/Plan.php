<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

/**
 * App\Models\Plan
 *
 * @property int $id
 * @property string $name
 * @property string|null $stripe_plan_id
 * @property int $allowed_jobs
 * @property float $amount
 * @property int $is_trial_plan
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Plan newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Plan newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Plan query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Plan whereAllowedJobs($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Plan whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Plan whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Plan whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Plan whereIsTrialPlan($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Plan whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Plan whereStripePlanId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Plan whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 *
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Subscription[] $activeSubscriptions
 * @property-read int|null $active_subscriptions_count
 *
 * @method static \Illuminate\Database\Query\Builder|Plan onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Plan whereDeletedAt($value)
 * @method static \Illuminate\Database\Query\Builder|Plan withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Plan withoutTrashed()
 *
 * @property int $salary_currency_id
 * @property-read \App\Models\SalaryCurrency $salaryCurrency
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Plan whereSalaryCurrencyId($value)
 */
class Plan extends Model
{
    use HasFactory, LogsActivity;
    /**
     * @var string
     */
    protected $table = 'plans';

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'name' => 'required|max:180|unique:plans,name',
        'amount' => 'required|numeric|min:1|max:99999',
        'allowed_jobs' => 'required|numeric|min:1|max:99999',
    ];

    /**
     * @var array
     */
    protected $fillable = [
        'name',
        'stripe_plan_id',
        'allowed_jobs',
        'amount',
        'salary_currency_id',
        'is_trial_plan',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'id' => 'integer',
            'allowed_jobs' => 'integer',
            'amount' => 'decimal:2',
            'is_trial_plan' => 'boolean',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'salary_currency_id' => 'integer',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }

    public function activeSubscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class, 'plan_id', 'id')
            ->Where('ends_at', '=', null)->whereNotIn('stripe_status', [Subscription::REJECTED, Subscription::PENDING]);
    }

    public function salaryCurrency(): BelongsTo
    {
        return $this->belongsTo(SalaryCurrency::class, 'salary_currency_id', 'id');
    }

    /**
     * Scope for active plans.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

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
        return $query->where('is_trial_plan', false)
                    ->where('amount', '>', 0);
    }

    /**
     * Scope for free plans.
     */
    public function scopeFree($query)
    {
        return $query->where('amount', 0);
    }

    /**
     * Scope for featured plans.
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope for plans by price range.
     */
    public function scopeByPriceRange($query, ?float $minPrice = null, ?float $maxPrice = null)
    {
        if ($minPrice !== null) {
            $query->where('amount', '>=', $minPrice);
        }
        
        if ($maxPrice !== null) {
            $query->where('amount', '<=', $maxPrice);
        }
        
        return $query;
    }

    /**
     * Scope for plans by job allowance.
     */
    public function scopeByJobAllowance($query, int $minJobs, ?int $maxJobs = null)
    {
        $query->where('allowed_jobs', '>=', $minJobs);
        
        if ($maxJobs !== null) {
            $query->where('allowed_jobs', '<=', $maxJobs);
        }
        
        return $query;
    }

    /**
     * Scope for popular plans (with most subscriptions).
     */
    public function scopePopular($query, int $limit = 5)
    {
        return $query->withCount('activeSubscriptions')
                    ->orderByDesc('active_subscriptions_count')
                    ->limit($limit);
    }

    /**
     * Scope for plans ordered by price.
     */
    public function scopeOrderByPrice($query, string $direction = 'asc')
    {
        return $query->orderBy('amount', $direction);
    }

    /**
     * Scope for plans with active subscriptions.
     */
    public function scopeWithActiveSubscriptions($query)
    {
        return $query->whereHas('activeSubscriptions');
    }

    /**
     * Activity log options
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'stripe_plan_id', 'allowed_jobs', 'amount', 'is_trial_plan', 'is_active', 'is_featured'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    /**
     * Get usage statistics.
     */
    public function getUsageStatsAttribute(): array
    {
        return cache()->remember("plan.{$this->id}.usage_stats", 3600, function () {
            $activeSubscriptions = $this->activeSubscriptions()->count();
            $totalSubscriptions = $this->activeSubscriptions()->count();
            $revenue = $this->calculateTotalRevenue();
            
            return [
                'active_subscriptions' => $activeSubscriptions,
                'total_subscriptions' => $totalSubscriptions,
                'conversion_rate' => $totalSubscriptions > 0 ? ($activeSubscriptions / $totalSubscriptions) * 100 : 0,
                'monthly_revenue' => $revenue['monthly'],
                'total_revenue' => $revenue['total'],
                'average_subscription_length' => $this->getAverageSubscriptionLength(),
                'churn_rate' => $this->calculateChurnRate(),
                'satisfaction_score' => $this->calculateSatisfactionScore(),
            ];
        });
    }

    /**
     * Get formatted price display.
     */
    public function getFormattedPriceAttribute(): string
    {
        if ($this->amount == 0) {
            return __('plan.price.free');
        }

        $symbol = $this->salaryCurrency->symbol;
        $price = number_format($this->amount, 2);
        
        return match ($this->stripe_plan_id) {
            'monthly', 'quarterly', 'yearly', 'lifetime' => "{$symbol}{$price}/" . __('plan.cycle.' . $this->stripe_plan_id),
            default => "{$symbol}{$price}"
        };
    }

    /**
     * Get plan tier classification.
     */
    public function getPlanTierAttribute(): string
    {
        if ($this->amount == 0) {
            return __('plan.tier.free');
        }

        if ($this->is_trial_plan) {
            return __('plan.tier.trial');
        }

        $monthlyPrice = $this->getMonthlyEquivalentPrice();

        return match (true) {
            $monthlyPrice <= 10 => __('plan.tier.basic'),
            $monthlyPrice <= 50 => __('plan.tier.professional'),
            $monthlyPrice <= 100 => __('plan.tier.premium'),
            $monthlyPrice <= 500 => __('plan.tier.enterprise'),
            default => __('plan.tier.custom')
        };
    }

    /**
     * Calculate plan value score.
     */
    public function getValueScoreAttribute(): float
    {
        if ($this->amount == 0) {
            return 100.0; // Free plans have maximum value
        }

        $monthlyPrice = $this->getMonthlyEquivalentPrice();
        $featuresScore = $this->calculateFeaturesScore();
        $limitsScore = $this->calculateLimitsScore();
        
        // Value = (Features + Limits) / Price ratio
        return min(100, (($featuresScore + $limitsScore) / max(1, $monthlyPrice)) * 10);
    }

    /**
     * Get formatted feature list.
     */
    public function getFeatureListAttribute(): array
    {
        $features = $this->restrictions ?? [];
        $standardFeatures = [
            'job_posts' => $this->allowed_jobs == 0 ? __('plan.features.unlimited_jobs') : __('plan.features.job_limit', ['count' => $this->allowed_jobs]),
            'featured_jobs' => $this->stripe_plan_id == 'monthly' ? __('plan.features.unlimited_featured') : __('plan.features.featured_limit', ['count' => $this->stripe_plan_id == 'yearly' ? 12 : ($this->stripe_plan_id == 'quarterly' ? 4 : 1)]),
            'cv_views' => $this->stripe_plan_id == 'lifetime' ? __('plan.features.unlimited_cv_views') : __('plan.features.cv_view_limit', ['count' => $this->stripe_plan_id == 'yearly' ? 12 : ($this->stripe_plan_id == 'quarterly' ? 4 : 1)]),
        ];

        if ($this->is_trial_plan && $this->stripe_plan_id == 'monthly' && $this->stripe_plan_id == 'yearly' && $this->stripe_plan_id == 'quarterly' && $this->stripe_plan_id == 'lifetime') {
            $standardFeatures['trial'] = __('plan.features.trial_days', ['days' => $this->stripe_plan_id == 'monthly' ? 30 : ($this->stripe_plan_id == 'yearly' ? 365 : ($this->stripe_plan_id == 'quarterly' ? 90 : 365))]);
        }

        return array_merge($standardFeatures, $features);
    }

    /**
     * Get popularity rank among all plans.
     */
    public function getPopularityRankAttribute(): int
    {
        return cache()->remember("plan.{$this->id}.popularity_rank", 1800, function () {
            $plans = static::active()
                ->withCount(['activeSubscriptions' => function ($query) {
                    $query->active();
                }])
                ->orderByDesc('active_subscriptions_count')
                ->pluck('id')
                ->toArray();

            $rank = array_search($this->id, $plans);
            return $rank !== false ? $rank + 1 : count($plans) + 1;
        });
    }

    /**
     * Get monthly equivalent price for comparison.
     */
    public function getMonthlyEquivalentPrice(): float
    {
        return match ($this->stripe_plan_id) {
            'monthly' => $this->amount,
            'quarterly' => $this->amount / 3,
            'yearly' => $this->amount / 12,
            'lifetime' => $this->amount / 60, // Assume 5-year equivalent
            default => $this->amount
        };
    }

    /**
     * Calculate total revenue from this plan.
     */
    public function calculateTotalRevenue(): array
    {
        $total = $this->activeSubscriptions()
            ->whereNotNull('amount_paid')
            ->sum('amount_paid');

        $monthly = $this->activeSubscriptions()
            ->sum('amount_paid') / max(1, $this->activeSubscriptions()->count());

        return [
            'total' => $total,
            'monthly' => $monthly
        ];
    }

    /**
     * Calculate average subscription length.
     */
    public function getAverageSubscriptionLength(): float
    {
        $subscriptions = $this->activeSubscriptions()
            ->whereNotNull('starts_at')
            ->whereNotNull('ends_at')
            ->get();

        if ($subscriptions->isEmpty()) {
            return 0.0;
        }

        $totalDays = $subscriptions->sum(function ($subscription) {
            return $subscription->starts_at->diffInDays($subscription->ends_at);
        });

        return $totalDays / $subscriptions->count();
    }

    /**
     * Calculate churn rate.
     */
    public function calculateChurnRate(): float
    {
        $totalSubscriptions = $this->activeSubscriptions()->count();
        $cancelledSubscriptions = $this->activeSubscriptions()
            ->where('status', 'cancelled')
            ->orWhere('status', 'expired')
            ->count();

        return $totalSubscriptions > 0 ? ($cancelledSubscriptions / $totalSubscriptions) * 100 : 0;
    }

    /**
     * Calculate satisfaction score based on renewals and usage.
     */
    public function calculateSatisfactionScore(): float
    {
        $renewals = $this->activeSubscriptions()->where('is_renewal', true)->count();
        $total = $this->activeSubscriptions()->count();
        
        return $total > 0 ? ($renewals / $total) * 100 : 50.0; // Default to neutral
    }

    /**
     * Calculate features score for value calculation.
     */
    protected function calculateFeaturesScore(): float
    {
        $score = 0;
        
        // Job posting limits
        $score += $this->allowed_jobs == 0 ? 20 : min(20, $this->allowed_jobs / 10);
        
        // Additional features
        $score += count($this->restrictions ?? []) * 2;
        
        // Trial benefits
        if ($this->is_trial_plan) {
            $score += 10;
        }
        
        return min(100, $score);
    }

    /**
     * Calculate limits score for value calculation.
     */
    protected function calculateLimitsScore(): float
    {
        $restrictions = $this->restrictions ?? [];
        return max(0, 50 - (count($restrictions) * 5)); // Fewer restrictions = higher score
    }

    /**
     * Check if plan can be upgraded to another plan.
     */
    public function canUpgradeTo(Plan $targetPlan): bool
    {
        return $targetPlan->getMonthlyEquivalentPrice() > $this->getMonthlyEquivalentPrice();
    }

    /**
     * Check if plan allows specific feature.
     */
    public function allowsFeature(string $feature): bool
    {
        $restrictions = $this->restrictions ?? [];
        return in_array($feature, $restrictions) || !in_array($feature, $this->restrictions ?? []);
    }

    /**
     * Get upgrade suggestions.
     */
    public function getUpgradeSuggestions(): \Illuminate\Database\Eloquent\Collection
    {
        return static::active()
            ->where('id', '!=', $this->id)
            ->where('amount', '>', $this->amount)
            ->orderByPrice()
            ->limit(3)
            ->get();
    }

    /**
     * Get downgrade options.
     */
    public function getDowngradeOptions(): \Illuminate\Database\Eloquent\Collection
    {
        return static::active()
            ->where('id', '!=', $this->id)
            ->where('amount', '<', $this->amount)
            ->orderByPrice('desc')
            ->limit(3)
            ->get();
    }
}
