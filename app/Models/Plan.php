<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

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
        'is_active',
        'is_featured',
        'description',
        'salary_currency_id',
        'duration_days',
        'max_featured_jobs',
        'priority_support',
        'analytics_access'
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
            'duration_days' => 'integer',
            'max_featured_jobs' => 'integer',
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
     * Scope for active plans.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for inactive plans.
     */
    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
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
        return $query->where('is_trial_plan', false);
    }

    /**
     * Scope for featured plans.
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
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
     * Scope for plans with priority support.
     */
    public function scopeWithPrioritySupport($query)
    {
        return $query->where('priority_support', true);
    }

    /**
     * Scope for plans with analytics access.
     */
    public function scopeWithAnalytics($query)
    {
        return $query->where('analytics_access', true);
    }

    /**
     * Scope for searching plans.
     */
    public function scopeSearch($query, string $term)
    {
        return $query->where('name', 'like', "%{$term}%")
                    ->orWhere('description', 'like', "%{$term}%");
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

        return number_format($this->amount, 2);
    }
} 