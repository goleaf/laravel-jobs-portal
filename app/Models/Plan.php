<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
    use HasFactory;
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
}
