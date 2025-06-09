<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Laravel\Cashier\Subscription as CashierSubscription;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

/**
 * App\Models\Subscription
 *
 * @property int $id
 * @property int $user_id
 * @property string $name
 * @property string $stripe_id
 * @property string $stripe_status
 * @property string|null $stripe_plan
 * @property int|null $plan_id
 * @property int|null $quantity
 * @property string $type
 * @property string|null $paypal_payment_id
 * @property Carbon|null $trial_ends_at
 * @property Carbon|null $ends_at
 * @property Carbon|null $current_period_start
 * @property Carbon|null $current_period_end
 * @property string|null $cancellation_reason
 * @property float|null $amount
 * @property string|null $currency
 * @property bool $auto_renewal
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Plan|null $plan
 * @property-read Plan|null $planCurrency
 * @property-read User $user
 * @property-read User $owner
 * @property-read Collection|SubscriptionItem[] $items
 * @property-read int|null $items_count
 * @property-read string $status_label
 * @property-read string $type_label
 * @property-read string $formatted_amount
 * @property-read bool $is_active
 * @property-read bool $is_trial
 * @property-read bool $is_cancelled
 * @property-read bool $is_expired
 * @property-read int $days_remaining
 * @property-read string $renewal_status
 *
 * @method static Builder|Subscription newModelQuery()
 * @method static Builder|Subscription newQuery()
 * @method static Builder|Subscription query()
 * @method static Builder|Subscription whereId($value)
 * @method static Builder|Subscription whereUserId($value)
 * @method static Builder|Subscription whereName($value)
 * @method static Builder|Subscription whereStripeId($value)
 * @method static Builder|Subscription whereStripeStatus($value)
 * @method static Builder|Subscription whereStripePlan($value)
 * @method static Builder|Subscription wherePlanId($value)
 * @method static Builder|Subscription whereQuantity($value)
 * @method static Builder|Subscription whereType($value)
 * @method static Builder|Subscription wherePaypalPaymentId($value)
 * @method static Builder|Subscription whereTrialEndsAt($value)
 * @method static Builder|Subscription whereEndsAt($value)
 * @method static Builder|Subscription whereCurrentPeriodStart($value)
 * @method static Builder|Subscription whereCurrentPeriodEnd($value)
 * @method static Builder|Subscription whereCancellationReason($value)
 * @method static Builder|Subscription whereAmount($value)
 * @method static Builder|Subscription whereCurrency($value)
 * @method static Builder|Subscription whereAutoRenewal($value)
 * @method static Builder|Subscription whereCreatedAt($value)
 * @method static Builder|Subscription whereUpdatedAt($value)
 * @method static Builder|Subscription active()
 * @method static Builder|Subscription cancelled()
 * @method static Builder|Subscription ended()
 * @method static Builder|Subscription incomplete()
 * @method static Builder|Subscription notCancelled()
 * @method static Builder|Subscription notOnGracePeriod()
 * @method static Builder|Subscription notOnTrial()
 * @method static Builder|Subscription onGracePeriod()
 * @method static Builder|Subscription onTrial()
 * @method static Builder|Subscription pastDue()
 * @method static Builder|Subscription recurring()
 * @method static Builder|Subscription expired()
 * @method static Builder|Subscription expiring(int $days = 7)
 * @method static Builder|Subscription recent(int $days = 30)
 * @method static Builder|Subscription byType(string $type)
 * @method static Builder|Subscription byPlan(int $planId)
 * @method static Builder|Subscription byStatus(string $status)
 * @method static Builder|Subscription stripe()
 * @method static Builder|Subscription paypal()
 * @method static Builder|Subscription manual()
 * @method static Builder|Subscription withAutoRenewal()
 * @method static Builder|Subscription withoutAutoRenewal()
 * @method static Builder|Subscription byPeriod(Carbon $start, Carbon $end)
 * @method static Builder|Subscription monthly()
 * @method static Builder|Subscription yearly()
 * @method static Builder|Subscription lifetime()
 * @method static Builder|Subscription highValue(float $minAmount = 100)
 * @method static Builder|Subscription lowValue(float $maxAmount = 20)
 * @method static Builder|Subscription byCurrency(string $currency)
 * @method static Builder|Subscription search(string $term)
 * @method static Builder|Subscription alphabetical()
 * @method static Builder|Subscription newestFirst()
 * @method static Builder|Subscription oldestFirst()
 *
 * @mixin \Eloquent
 */
class Subscription extends CashierSubscription
{
    use LogsActivity;

    protected $table = 'subscriptions';

    // Subscription types
    const TYPE_STRIPE = 1;
    const TYPE_PAYPAL = 2;
    const TYPE_MANUALLY = 3;

    const TYPE = [
        self::TYPE_STRIPE => 'stripe',
        self::TYPE_PAYPAL => 'paypal',
        self::TYPE_MANUALLY => 'Manually',
    ];

    // Subscription statuses
    const STATUS_ACTIVE = 'active';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_REJECTED = 'rejected';
    const STATUS_PENDING = 'pending';
    const STATUS_INCOMPLETE = 'incomplete';
    const STATUS_PAST_DUE = 'past_due';
    const STATUS_TRIALING = 'trialing';

    protected $fillable = [
        'user_id',
        'name',
        'stripe_id',
        'stripe_status',
        'stripe_plan',
        'plan_id',
        'quantity',
        'type',
        'paypal_payment_id',
        'trial_ends_at',
        'ends_at',
        'current_period_start',
        'current_period_end',
        'cancellation_reason',
        'amount',
        'currency',
        'auto_renewal',
    ];

    protected $appends = [
        'status_label',
        'type_label',
        'formatted_amount',
        'is_active',
        'is_trial',
        'is_cancelled',
        'is_expired',
        'days_remaining',
        'renewal_status',
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
            'user_id' => 'integer',
            'plan_id' => 'integer',
            'quantity' => 'integer',
            'amount' => 'decimal:2',
            'auto_renewal' => 'boolean',
            'trial_ends_at' => 'datetime',
            'ends_at' => 'datetime',
            'current_period_start' => 'datetime',
            'current_period_end' => 'datetime',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /**
     * Validation rules with multilingual support
     *
     * @var array
     */
    public static $rules = [
        'user_id' => 'required|integer|exists:users,id',
        'name' => 'required|string|max:255',
        'plan_id' => 'nullable|integer|exists:plans,id',
        'quantity' => 'nullable|integer|min:1',
        'type' => 'required|string|max:50',
        'amount' => 'nullable|numeric|min:0',
        'currency' => 'nullable|string|size:3',
        'trial_ends_at' => 'nullable|date|after:now',
        'ends_at' => 'nullable|date',
        'cancellation_reason' => 'nullable|string|max:500',
    ];

    /**
     * Boot the model.
     */
    protected static function boot(): void
    {
        parent::boot();

        // Clear related caches when subscription is updated
        static::saved(function ($subscription) {
            cache()->forget("user.{$subscription->user_id}.active_subscription");
            cache()->tags(['subscriptions', 'user-' . $subscription->user_id])->flush();
        });

        static::deleted(function ($subscription) {
            cache()->forget("user.{$subscription->user_id}.active_subscription");
            cache()->tags(['subscriptions', 'user-' . $subscription->user_id])->flush();
        });
    }

    /**
     * Activity log options
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['stripe_status', 'plan_id', 'ends_at', 'cancellation_reason', 'auto_renewal'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    // ==============================================
    // RELATIONSHIPS
    // ==============================================

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class, 'stripe_plan', 'stripe_plan_id');
    }

    public function planCurrency(): BelongsTo
    {
        return $this->belongsTo(Plan::class, 'plan_id', 'id');
    }

    // ==============================================
    // ACCESSORS
    // ==============================================

    public function getStatusLabelAttribute(): string
    {
        return match ($this->stripe_status) {
            self::STATUS_ACTIVE => __('subscription.status.active'),
            self::STATUS_CANCELLED => __('subscription.status.cancelled'),
            self::STATUS_REJECTED => __('subscription.status.rejected'),
            self::STATUS_PENDING => __('subscription.status.pending'),
            self::STATUS_INCOMPLETE => __('subscription.status.incomplete'),
            self::STATUS_PAST_DUE => __('subscription.status.past_due'),
            self::STATUS_TRIALING => __('subscription.status.trialing'),
            default => __('subscription.status.unknown')
        };
    }

    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            'stripe' => __('subscription.type.stripe'),
            'paypal' => __('subscription.type.paypal'),
            'Manually' => __('subscription.type.manual'),
            default => __('subscription.type.unknown')
        };
    }

    public function getFormattedAmountAttribute(): string
    {
        if (!$this->amount) {
            return __('common.free');
        }
        return ($this->currency ?? 'USD') . ' ' . number_format($this->amount, 2);
    }

    public function getIsActiveAttribute(): bool
    {
        return $this->stripe_status === self::STATUS_ACTIVE && 
               (!$this->ends_at || $this->ends_at->isFuture());
    }

    public function getIsTrialAttribute(): bool
    {
        return $this->stripe_status === self::STATUS_TRIALING || 
               ($this->trial_ends_at && $this->trial_ends_at->isFuture());
    }

    public function getIsCancelledAttribute(): bool
    {
        return $this->stripe_status === self::STATUS_CANCELLED || 
               ($this->ends_at && $this->ends_at->isPast());
    }

    public function getIsExpiredAttribute(): bool
    {
        return $this->ends_at && $this->ends_at->isPast();
    }

    public function getDaysRemainingAttribute(): int
    {
        if (!$this->ends_at) {
            return -1; // No end date (lifetime or ongoing)
        }
        
        return max(0, now()->diffInDays($this->ends_at, false));
    }

    public function getRenewalStatusAttribute(): string
    {
        if ($this->is_expired) {
            return __('subscription.renewal.expired');
        }
        
        if ($this->is_cancelled) {
            return __('subscription.renewal.cancelled');
        }
        
        if ($this->auto_renewal) {
            return __('subscription.renewal.auto');
        }
        
        return __('subscription.renewal.manual');
    }

    // ==============================================
    // QUERY SCOPES
    // ==============================================

    /**
     * Scope for expired subscriptions.
     */
    public function scopeExpired(Builder $query): Builder
    {
        return $query->where('ends_at', '<', now());
    }

    /**
     * Scope for expiring subscriptions.
     */
    public function scopeExpiring(Builder $query, int $days = 7): Builder
    {
        return $query->whereBetween('ends_at', [now(), now()->addDays($days)]);
    }

    /**
     * Scope for recent subscriptions.
     */
    public function scopeRecent(Builder $query, int $days = 30): Builder
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Scope by subscription type.
     */
    public function scopeByType(Builder $query, string $type): Builder
    {
        return $query->where('type', $type);
    }

    /**
     * Scope by plan.
     */
    public function scopeByPlan(Builder $query, int $planId): Builder
    {
        return $query->where('plan_id', $planId);
    }

    /**
     * Scope by status.
     */
    public function scopeByStatus(Builder $query, string $status): Builder
    {
        return $query->where('stripe_status', $status);
    }

    /**
     * Scope for Stripe subscriptions.
     */
    public function scopeStripe(Builder $query): Builder
    {
        return $query->where('type', 'stripe');
    }

    /**
     * Scope for PayPal subscriptions.
     */
    public function scopePaypal(Builder $query): Builder
    {
        return $query->where('type', 'paypal');
    }

    /**
     * Scope for manual subscriptions.
     */
    public function scopeManual(Builder $query): Builder
    {
        return $query->where('type', 'Manually');
    }

    /**
     * Scope for subscriptions with auto-renewal.
     */
    public function scopeWithAutoRenewal(Builder $query): Builder
    {
        return $query->where('auto_renewal', true);
    }

    /**
     * Scope for subscriptions without auto-renewal.
     */
    public function scopeWithoutAutoRenewal(Builder $query): Builder
    {
        return $query->where('auto_renewal', false);
    }

    /**
     * Scope by period.
     */
    public function scopeByPeriod(Builder $query, Carbon $start, Carbon $end): Builder
    {
        return $query->whereBetween('created_at', [$start, $end]);
    }

    /**
     * Scope for monthly subscriptions.
     */
    public function scopeMonthly(Builder $query): Builder
    {
        return $query->whereHas('planCurrency', function ($q) {
            $q->where('frequency', 'monthly');
        });
    }

    /**
     * Scope for yearly subscriptions.
     */
    public function scopeYearly(Builder $query): Builder
    {
        return $query->whereHas('planCurrency', function ($q) {
            $q->where('frequency', 'yearly');
        });
    }

    /**
     * Scope for lifetime subscriptions.
     */
    public function scopeLifetime(Builder $query): Builder
    {
        return $query->whereNull('ends_at');
    }

    /**
     * Scope for high-value subscriptions.
     */
    public function scopeHighValue(Builder $query, float $minAmount = 100): Builder
    {
        return $query->where('amount', '>=', $minAmount);
    }

    /**
     * Scope for low-value subscriptions.
     */
    public function scopeLowValue(Builder $query, float $maxAmount = 20): Builder
    {
        return $query->where('amount', '<=', $maxAmount);
    }

    /**
     * Scope by currency.
     */
    public function scopeByCurrency(Builder $query, string $currency): Builder
    {
        return $query->where('currency', $currency);
    }

    /**
     * Scope for searching subscriptions.
     */
    public function scopeSearch(Builder $query, string $term): Builder
    {
        return $query->where(function ($q) use ($term) {
            $q->where('name', 'LIKE', "%{$term}%")
              ->orWhere('stripe_id', 'LIKE', "%{$term}%")
              ->orWhere('paypal_payment_id', 'LIKE', "%{$term}%")
              ->orWhereHas('user', function ($userQuery) use ($term) {
                  $userQuery->where('name', 'LIKE', "%{$term}%")
                           ->orWhere('email', 'LIKE', "%{$term}%");
              })
              ->orWhereHas('planCurrency', function ($planQuery) use ($term) {
                  $planQuery->where('name', 'LIKE', "%{$term}%");
              });
        });
    }

    /**
     * Scope for alphabetical ordering.
     */
    public function scopeAlphabetical(Builder $query): Builder
    {
        return $query->orderBy('name');
    }

    /**
     * Scope for newest first ordering.
     */
    public function scopeNewestFirst(Builder $query): Builder
    {
        return $query->orderBy('created_at', 'desc');
    }

    /**
     * Scope for oldest first ordering.
     */
    public function scopeOldestFirst(Builder $query): Builder
    {
        return $query->orderBy('created_at', 'asc');
    }

    // ==============================================
    // UTILITY METHODS
    // ==============================================

    /**
     * Check if subscription is renewable.
     */
    public function isRenewable(): bool
    {
        return $this->is_active && 
               !$this->is_cancelled && 
               $this->auto_renewal;
    }

    /**
     * Check if subscription needs attention.
     */
    public function needsAttention(): bool
    {
        return $this->stripe_status === self::STATUS_PAST_DUE ||
               $this->stripe_status === self::STATUS_INCOMPLETE ||
               ($this->ends_at && $this->ends_at->isPast());
    }

    /**
     * Get subscription duration in days.
     */
    public function getDurationInDays(): int
    {
        if (!$this->current_period_start || !$this->current_period_end) {
            return 0;
        }
        
        return $this->current_period_start->diffInDays($this->current_period_end);
    }

    /**
     * Get total value of subscription.
     */
    public function getTotalValue(): float
    {
        return ($this->amount ?? 0) * ($this->quantity ?? 1);
    }

    /**
     * Get formatted subscription summary.
     */
    public function getSubscriptionSummary(): string
    {
        $parts = [];
        
        if ($this->planCurrency?->name) {
            $parts[] = $this->planCurrency->name;
        } elseif ($this->name) {
            $parts[] = $this->name;
        }
        
        if ($this->formatted_amount !== __('common.free')) {
            $parts[] = $this->formatted_amount;
        }
        
        $parts[] = $this->status_label;
        
        return implode(' - ', $parts);
    }

    /**
     * Check if subscription can be cancelled.
     */
    public function canBeCancelled(): bool
    {
        return $this->is_active && !$this->is_cancelled;
    }

    /**
     * Check if subscription can be resumed.
     */
    public function canBeResumed(): bool
    {
        return $this->is_cancelled && 
               $this->ends_at && 
               $this->ends_at->isFuture();
    }

    /**
     * Get renewal date.
     */
    public function getRenewalDate(): ?Carbon
    {
        if ($this->current_period_end && $this->auto_renewal && $this->is_active) {
            return $this->current_period_end;
        }
        
        return null;
    }

    /**
     * Calculate refund amount (prorated).
     */
    public function calculateRefundAmount(): float
    {
        if (!$this->amount || !$this->current_period_start || !$this->current_period_end) {
            return 0.0;
        }
        
        $totalDays = $this->current_period_start->diffInDays($this->current_period_end);
        $remainingDays = now()->diffInDays($this->current_period_end, false);
        
        if ($remainingDays <= 0 || $totalDays <= 0) {
            return 0.0;
        }
        
        return ($this->amount * $remainingDays) / $totalDays;
    }
}
