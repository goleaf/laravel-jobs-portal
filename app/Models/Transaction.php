<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

/**
 * App\Models\Transaction
 *
 * @property int $id
 * @property int $user_id
 * @property int $subscription_id
 * @property string $invoice_id
 * @property float|null $amount
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Subscription $subscription
 * @property-read \App\Models\User $user
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Transaction newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Transaction newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Transaction query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Transaction whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Transaction whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Transaction whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Transaction whereInvoiceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Transaction whereOwnerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Transaction whereOwnerType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Transaction whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Transaction whereUserId($value)
 *
 * @mixin \Eloquent
 *
 * @property int $owner_id
 * @property string $owner_type
 * @property-read mixed $type_name
 * @property-read Model|\Eloquent $type
 */
class Transaction extends Model
{
    use HasFactory, LogsActivity;
    /**
     * @var string
     */
    public $table = 'transactions';

    /**
     * @var array
     */
    public $fillable = [
        'user_id',
        'owner_id',
        'owner_type',
        'amount',
        'invoice_id',
        'status',
        'is_approved',
        'approved_id',
        'plan_currency_id',
    ];

    const STRIPE_PAYMENT = 3;
    const PAYPAL_PAYMENT = 4;
    const PAYSTACK_PAYMENT = 5;
    const DIGITAL = 1;
    const MANUALLY = 2;

    const STATUS = [
        self::DIGITAL => 'digital',
        self::MANUALLY => 'Manually',
    ];

    const PENDING = 0;

    const APPROVED = 1;

    const REJECTED = 2;

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
            'owner_id' => 'integer',
            'amount' => 'decimal:2',
            'is_approved' => 'boolean',
            'status' => 'integer',
            'approved_id' => 'integer',
            'plan_currency_id' => 'integer',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    protected $appends = ['type_name'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function owner()
    {
        return $this->belongsTo(Subscription::class, 'owner_id');
    }

    public function type()
    {
        return $this->morphTo('owner');
    }

    public function admin(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(User::class, 'id', 'approved_id');
    }

    public function getTypeNameAttribute()
    {
        switch ($this->owner_type) {
            case Company::class:
                return 'Featured Company';
                break;
            case Job::class:
                return 'Featured Job';
                break;
            case Subscription::class:
                return 'Company Subscription';
                break;
            default:
                return 'N/A';
        }
    }

    public function salaryCurrency(): BelongsTo
    {
        return $this->belongsTo(SalaryCurrency::class, 'plan_currency_id', 'id');
    }

    /**
     * Scope for approved transactions.
     */
    public function scopeApproved($query)
    {
        return $query->where('is_approved', true);
    }

    /**
     * Scope for pending transactions.
     */
    public function scopePending($query)
    {
        return $query->where('is_approved', false);
    }

    /**
     * Scope for transactions by status.
     */
    public function scopeByStatus($query, int $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope for transactions by user.
     */
    public function scopeByUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope for transactions by amount range.
     */
    public function scopeByAmountRange($query, float $min, float $max)
    {
        return $query->whereBetween('amount', [$min, $max]);
    }

    /**
     * Scope for recent transactions.
     */
    public function scopeRecent($query, int $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Scope for today's transactions.
     */
    public function scopeToday($query)
    {
        return $query->whereDate('created_at', today());
    }

    /**
     * Scope for this week's transactions.
     */
    public function scopeThisWeek($query)
    {
        return $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
    }

    /**
     * Scope for this month's transactions.
     */
    public function scopeThisMonth($query)
    {
        return $query->whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year);
    }

    /**
     * Scope for featured company transactions.
     */
    public function scopeFeaturedCompany($query)
    {
        return $query->where('owner_type', Company::class);
    }

    /**
     * Scope for featured job transactions.
     */
    public function scopeFeaturedJob($query)
    {
        return $query->where('owner_type', Job::class);
    }

    /**
     * Scope for subscription transactions.
     */
    public function scopeSubscription($query)
    {
        return $query->where('owner_type', Subscription::class);
    }

    /**
     * Scope for high value transactions.
     */
    public function scopeHighValue($query, float $threshold = 1000)
    {
        return $query->where('amount', '>=', $threshold);
    }

    /**
     * Scope for transactions by payment method.
     */
    public function scopeByPaymentMethod($query, int $method)
    {
        return $query->where('status', $method);
    }

    /**
     * Scope for revenue summary.
     */
    public function scopeRevenue($query)
    {
        return $query->approved()->sum('amount');
    }

    /**
     * Activity log options
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['status', 'amount', 'payment_method', 'payment_gateway', 'failure_reason'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    /**
     * Get financial summary.
     */
    public function getFinancialSummaryAttribute(): array
    {
        return [
            'gross_amount' => $this->amount,
            'gateway_fee' => $this->gateway_fee,
            'net_amount' => $this->net_amount,
            'currency' => $this->currency,
            'formatted_gross' => $this->formatted_amount,
            'formatted_net' => $this->formatCurrency($this->net_amount),
            'tax_amount' => $this->calculateTax(),
            'commission_rate' => $this->getCommissionRate(),
        ];
    }

    /**
     * Get formatted amount with currency.
     */
    public function getFormattedAmountAttribute(): string
    {
        return $this->formatCurrency($this->amount);
    }

    /**
     * Get payment status badge information.
     */
    public function getPaymentStatusBadgeAttribute(): array
    {
        return match ($this->status) {
            'pending' => ['color' => 'yellow', 'text' => __('transaction.status.pending')],
            'processing' => ['color' => 'blue', 'text' => __('transaction.status.processing')],
            'completed' => ['color' => 'green', 'text' => __('transaction.status.completed')],
            'failed' => ['color' => 'red', 'text' => __('transaction.status.failed')],
            'cancelled' => ['color' => 'gray', 'text' => __('transaction.status.cancelled')],
            'refunded' => ['color' => 'orange', 'text' => __('transaction.status.refunded')],
            'partial_refund' => ['color' => 'orange', 'text' => __('transaction.status.partial_refund')],
            default => ['color' => 'gray', 'text' => __('transaction.status.unknown')]
        };
    }

    /**
     * Get processing time in seconds.
     */
    public function getProcessingTimeAttribute(): ?int
    {
        if (!$this->processed_at) {
            return null;
        }

        return $this->created_at->diffInSeconds($this->processed_at);
    }

    /**
     * Calculate gateway fee.
     */
    public function getGatewayFeeAttribute(): float
    {
        $feeRates = [
            'paypal' => 0.029, // 2.9%
            'stripe' => 0.029, // 2.9%
            'razorpay' => 0.025, // 2.5%
            'paystack' => 0.015, // 1.5%
            'mollie' => 0.025, // 2.5%
            'flutterwave' => 0.014, // 1.4%
        ];

        $rate = $feeRates[$this->payment_gateway] ?? 0.03;
        $fixedFee = 0.30; // Fixed fee in USD equivalent

        return ($this->amount * $rate) + $fixedFee;
    }

    /**
     * Calculate net amount after fees.
     */
    public function getNetAmountAttribute(): float
    {
        return max(0, $this->amount - $this->gateway_fee);
    }

    /**
     * Get transaction category.
     */
    public function getTransactionCategoryAttribute(): string
    {
        if ($this->type === 'refund') {
            return __('transaction.category.refund');
        }

        if ($this->plan_id) {
            return __('transaction.category.subscription');
        }

        if ($this->amount >= 1000) {
            return __('transaction.category.enterprise');
        }

        if ($this->amount >= 100) {
            return __('transaction.category.premium');
        }

        return __('transaction.category.standard');
    }

    /**
     * Format currency amount.
     */
    public function formatCurrency(float $amount): string
    {
        $symbol = match (strtoupper($this->currency)) {
            'USD' => '$',
            'EUR' => '€',
            'GBP' => '£',
            'JPY' => '¥',
            'CNY' => '¥',
            'INR' => '₹',
            'CAD' => 'C$',
            'AUD' => 'A$',
            default => $this->currency . ' '
        };

        return $symbol . number_format($amount, 2);
    }

    /**
     * Calculate tax amount.
     */
    public function calculateTax(): float
    {
        $taxRates = [
            'USD' => 0.08, // 8% average US tax
            'EUR' => 0.21, // 21% VAT
            'GBP' => 0.20, // 20% VAT
            'CAD' => 0.13, // 13% HST
        ];

        $rate = $taxRates[strtoupper($this->currency)] ?? 0.0;
        return $this->amount * $rate;
    }

    /**
     * Get commission rate for this transaction.
     */
    public function getCommissionRate(): float
    {
        if ($this->plan_id) {
            return 0.05; // 5% for subscriptions
        }

        return 0.03; // 3% for one-time payments
    }

    /**
     * Check if transaction can be refunded.
     */
    public function canBeRefunded(): bool
    {
        return $this->status === 'completed' &&
               $this->refunded_at === null &&
               $this->created_at->diffInDays(now()) <= 30; // 30-day refund window
    }

    /**
     * Process refund.
     */
    public function processRefund(float $amount = null): bool
    {
        if (!$this->canBeRefunded()) {
            return false;
        }

        $refundAmount = $amount ?? $this->amount;
        
        if ($refundAmount > $this->amount) {
            return false;
        }

        $this->update([
            'refund_amount' => $refundAmount,
            'refunded_at' => now(),
            'status' => $refundAmount == $this->amount ? 'refunded' : 'partial_refund'
        ]);

        return true;
    }

    /**
     * Get transaction analytics.
     */
    public static function getAnalytics(array $filters = []): array
    {
        $query = static::query();

        // Apply filters
        if (isset($filters['date_from'])) {
            $query->where('created_at', '>=', $filters['date_from']);
        }
        
        if (isset($filters['date_to'])) {
            $query->where('created_at', '<=', $filters['date_to']);
        }

        return [
            'total_revenue' => $query->completed()->sum('amount'),
            'total_transactions' => $query->count(),
            'average_transaction' => $query->completed()->avg('amount') ?? 0,
            'success_rate' => $query->count() > 0 ? ($query->completed()->count() / $query->count()) * 100 : 0,
            'refund_rate' => $query->completed()->count() > 0 ? ($query->refunded()->count() / $query->completed()->count()) * 100 : 0,
            'top_gateways' => $query->completed()
                ->select('payment_gateway', \DB::raw('COUNT(*) as count, SUM(amount) as revenue'))
                ->groupBy('payment_gateway')
                ->orderByDesc('revenue')
                ->limit(5)
                ->get(),
            'monthly_trend' => $query->completed()
                ->selectRaw('YEAR(created_at) as year, MONTH(created_at) as month, SUM(amount) as revenue, COUNT(*) as count')
                ->groupByRaw('YEAR(created_at), MONTH(created_at)')
                ->orderByRaw('YEAR(created_at), MONTH(created_at)')
                ->get()
        ];
    }

    /**
     * Get fraud score based on transaction patterns.
     */
    public function getFraudScore(): float
    {
        $score = 0;

        // High amount transactions
        if ($this->amount > 5000) {
            $score += 30;
        }

        // Multiple failed attempts from same user recently
        $recentFailures = static::byUser($this->user_id)
            ->failed()
            ->where('created_at', '>=', now()->subHours(24))
            ->count();
        
        $score += min(40, $recentFailures * 10);

        // Unusual payment method for user
        $userUsualMethod = static::byUser($this->user_id)
            ->completed()
            ->select('payment_method', \DB::raw('COUNT(*) as count'))
            ->groupBy('payment_method')
            ->orderByDesc('count')
            ->first();
        
        if ($userUsualMethod && $userUsualMethod->payment_method !== $this->payment_method) {
            $score += 20;
        }

        // Geographic anomalies could be added here with IP tracking

        return min(100, $score);
    }
}
