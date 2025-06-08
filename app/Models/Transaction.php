<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
    use HasFactory;
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
}
