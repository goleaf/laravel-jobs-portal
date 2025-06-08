<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class SalaryCurrency
 *
 * @version July 7, 2020, 6:41 am UTC
 *
 * @property string $currency_name
 * @property int $id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SalaryCurrency newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SalaryCurrency newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SalaryCurrency query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SalaryCurrency whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SalaryCurrency whereCurrencyName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SalaryCurrency whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SalaryCurrency whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 *
 * @property string $currency_icon
 *
 * @method static \Illuminate\Database\Eloquent\Builder|SalaryCurrency whereCurrencyIcon($value)
 *
 * @property string $currency_code
 *
 * @method static \Illuminate\Database\Eloquent\Builder|SalaryCurrency whereCurrencyCode($value)
 */
class SalaryCurrency extends Model
{
    use HasFactory;
    public $table = 'salary_currencies';

    public $fillable = [
        'currency_name',
        'is_default',
        'currency_code',
        'currency_icon',
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
            'is_default' => 'boolean',
            'is_active' => 'boolean',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    public static $rules = [
        'currency_name' => 'required|string|unique:salary_currencies,currency_name',
        'currency_icon' => 'required|unique:salary_currencies,currency_icon',
        'currency_code' => 'required|min:3|max:3|unique:salary_currencies,currency_code',
    ];

    public function jobs()
    {
        return $this->hasMany(Job::class, 'salary_currency_id');
    }

    public function candidates()
    {
        return $this->hasMany(Candidate::class, 'salary_currency_id');
    }

    /**
     * Scope for active salary currencies.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for default salary currencies.
     */
    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    /**
     * Scope for custom salary currencies.
     */
    public function scopeCustom($query)
    {
        return $query->where('is_default', false);
    }

    /**
     * Scope for currencies with jobs.
     */
    public function scopeWithJobs($query)
    {
        return $query->whereHas('jobs');
    }

    /**
     * Scope for currencies with candidates.
     */
    public function scopeWithCandidates($query)
    {
        return $query->whereHas('candidates');
    }

    /**
     * Scope for searching currencies by name or code.
     */
    public function scopeSearch($query, string $term)
    {
        return $query->where('currency_name', 'like', "%{$term}%")
                    ->orWhere('currency_code', 'like', "%{$term}%");
    }

    /**
     * Scope for popular currencies (with most jobs).
     */
    public function scopePopular($query, int $limit = 10)
    {
        return $query->withCount(['jobs', 'candidates'])
                    ->orderByDesc('jobs_count')
                    ->orderByDesc('candidates_count')
                    ->limit($limit);
    }

    /**
     * Scope for alphabetically ordered currencies.
     */
    public function scopeAlphabetical($query)
    {
        return $query->orderBy('currency_name', 'asc');
    }

    /**
     * Scope for currencies by code.
     */
    public function scopeByCode($query, string $code)
    {
        return $query->where('currency_code', $code);
    }

    /**
     * Scope for major world currencies.
     */
    public function scopeMajor($query)
    {
        return $query->whereIn('currency_code', ['USD', 'EUR', 'GBP', 'JPY', 'AUD', 'CAD', 'CHF', 'CNY']);
    }

    /**
     * Scope for recent currencies (created in last 30 days).
     */
    public function scopeRecent($query, int $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Scope for old currencies (created more than specified days ago).
     */
    public function scopeOld($query, int $days = 365)
    {
        return $query->where('created_at', '<', now()->subDays($days));
    }

    /**
     * Scope for featured currencies.
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope for not featured currencies.
     */
    public function scopeNotFeatured($query)
    {
        return $query->where('is_featured', false);
    }

    /**
     * Scope for inactive currencies.
     */
    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    /**
     * Scope for currencies used in active jobs.
     */
    public function scopeWithActiveJobs($query)
    {
        return $query->whereHas('jobs', function ($q) {
            $q->where('status', 'open')->where('expire_date', '>', now());
        });
    }

    /**
     * Scope for currencies with available candidates.
     */
    public function scopeWithAvailableCandidates($query)
    {
        return $query->whereHas('candidates', function ($q) {
            $q->where('is_available', true);
        });
    }

    /**
     * Scope for currencies by continent.
     */
    public function scopeByContinent($query, string $continent)
    {
        $continentCurrencies = [
            'europe' => ['EUR', 'GBP', 'CHF', 'SEK', 'NOK', 'DKK', 'PLN', 'CZK'],
            'america' => ['USD', 'CAD', 'BRL', 'ARS', 'MXN', 'CLP', 'COP'],
            'asia' => ['JPY', 'CNY', 'KRW', 'INR', 'SGD', 'HKD', 'THB', 'MYR'],
            'africa' => ['ZAR', 'EGP', 'NGN', 'KES', 'MAD', 'TND'],
            'oceania' => ['AUD', 'NZD', 'FJD'],
        ];

        return $query->whereIn('currency_code', $continentCurrencies[strtolower($continent)] ?? []);
    }
}
