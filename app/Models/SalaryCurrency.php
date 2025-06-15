<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Cache;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

/**
 * SalaryCurrency Model - Enhanced with Enhanced patterns
 *
 * @property int $id
 * @property string $currency_name
 * @property string $currency_code
 * @property string $currency_symbol
 * @property float|null $exchange_rate
 * @property string|null $base_currency
 * @property bool $is_active
 * @property bool $is_default
 * @property bool $is_crypto
 * @property array|null $supported_countries
 * @property int $decimal_places
 * @property string|null $number_format
 * @property \Illuminate\Support\Carbon|null $last_rate_update
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Job[] $jobs
 * @property-read int|null $jobs_count
 * @property-read mixed $usage_statistics
 * @property-read mixed $formatted_symbol
 * @property-read mixed $exchange_info
 * @property-read mixed $market_value
 * @property-read mixed $currency_trend
 * @property-read mixed $conversion_data
 * @property-read mixed $regional_info
 *
 * Enhanced Enhanced Scopes:
 * @method static Builder|SalaryCurrency active()
 * @method static Builder|SalaryCurrency inactive()
 * @method static Builder|SalaryCurrency default()
 * @method static Builder|SalaryCurrency crypto()
 * @method static Builder|SalaryCurrency fiat()
 * @method static Builder|SalaryCurrency major()
 * @method static Builder|SalaryCurrency minor()
 * @method static Builder|SalaryCurrency search(string $term)
 * @method static Builder|SalaryCurrency byCode(string $code)
 * @method static Builder|SalaryCurrency byCountry(string $country)
 * @method static Builder|SalaryCurrency popular()
 * @method static Builder|SalaryCurrency trending()
 * @method static Builder|SalaryCurrency stable()
 * @method static Builder|SalaryCurrency volatile()
 * @method static Builder|SalaryCurrency recentlyUpdated(int $hours = 24)
 * @method static Builder|SalaryCurrency needsUpdate(int $hours = 24)
 * @method static Builder|SalaryCurrency withExchangeRate()
 * @method static Builder|SalaryCurrency withoutExchangeRate()
 * @method static Builder|SalaryCurrency orderByRate(string $direction = 'asc')
 * @method static Builder|SalaryCurrency orderByUsage()
 * @method static Builder|SalaryCurrency orderByTrend()
 * @method static Builder|SalaryCurrency recent(int $days = 30)
 * @method static Builder|SalaryCurrency old(int $days = 365)
 *
 * @mixin \Eloquent
 */
class SalaryCurrency extends Model
{
    use HasFactory;
    use LogsActivity;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'currency_name',
        'currency_code',
        'currency_symbol',
        'exchange_rate',
        'base_currency',
        'is_active',
        'is_default',
        'is_crypto',
        'supported_countries',
        'decimal_places',
        'number_format',
        'last_rate_update',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'is_default' => 'boolean',
            'is_crypto' => 'boolean',
            'exchange_rate' => 'decimal:8',
            'decimal_places' => 'integer',
            'supported_countries' => 'array',
            'last_rate_update' => 'datetime',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /**
     * Get the activity log options for the model.
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'currency_name',
                'currency_code',
                'currency_symbol',
                'exchange_rate',
                'is_active',
                'is_default',
                'is_crypto',
                'supported_countries',
                'decimal_places',
            ])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    /**
     * Validation rules for creating salary currencies.
     *
     * @var array<string, string>
     */
    public static array $rules = [
        'currency_name' => 'required|string|max:100|unique:salary_currencies,currency_name',
        'currency_code' => 'required|string|size:3|unique:salary_currencies,currency_code',
        'currency_symbol' => 'required|string|max:10',
        'exchange_rate' => 'nullable|numeric|min:0',
        'base_currency' => 'nullable|string|size:3',
        'is_active' => 'boolean',
        'is_default' => 'boolean',
        'is_crypto' => 'boolean',
        'supported_countries' => 'nullable|array',
        'decimal_places' => 'integer|min:0|max:8',
        'number_format' => 'nullable|string|max:50',
    ];

    /**
     * Update validation rules for salary currencies.
     *
     * @param int $id
     * @return array<string, string>
     */
    public static function updateRules(int $id): array
    {
        return [
            'currency_name' => 'required|string|max:100|unique:salary_currencies,currency_name,' . $id,
            'currency_code' => 'required|string|size:3|unique:salary_currencies,currency_code,' . $id,
            'currency_symbol' => 'required|string|max:10',
            'exchange_rate' => 'nullable|numeric|min:0',
            'base_currency' => 'nullable|string|size:3',
            'is_active' => 'boolean',
            'is_default' => 'boolean',
            'is_crypto' => 'boolean',
            'supported_countries' => 'nullable|array',
            'decimal_places' => 'integer|min:0|max:8',
            'number_format' => 'nullable|string|max:50',
        ];
    }

    // =============================================
    // RELATIONSHIPS
    // =============================================

    /**
     * Get the jobs that use this currency.
     */
    public function jobs(): HasMany
    {
        return $this->hasMany(Job::class, 'salary_currency_id');
    }

    // =============================================
    // SCOPES
    // =============================================

    /**
     * Scope for active currencies.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for inactive currencies.
     */
    public function scopeInactive(Builder $query): Builder
    {
        return $query->where('is_active', false);
    }

    /**
     * Scope for default currency.
     */
    public function scopeDefault(Builder $query): Builder
    {
        return $query->where('is_default', true);
    }

    /**
     * Scope for cryptocurrency.
     */
    public function scopeCrypto(Builder $query): Builder
    {
        return $query->where('is_crypto', true);
    }

    /**
     * Scope for fiat currency.
     */
    public function scopeFiat(Builder $query): Builder
    {
        return $query->where('is_crypto', false);
    }

    /**
     * Scope for major currencies (USD, EUR, GBP, JPY, etc.).
     */
    public function scopeMajor(Builder $query): Builder
    {
        $majorCurrencies = ['USD', 'EUR', 'GBP', 'JPY', 'CHF', 'CAD', 'AUD', 'NZD'];
        return $query->whereIn('currency_code', $majorCurrencies);
    }

    /**
     * Scope for minor currencies.
     */
    public function scopeMinor(Builder $query): Builder
    {
        $majorCurrencies = ['USD', 'EUR', 'GBP', 'JPY', 'CHF', 'CAD', 'AUD', 'NZD'];
        return $query->whereNotIn('currency_code', $majorCurrencies);
    }

    /**
     * Scope to search currencies by name, code, or symbol.
     */
    public function scopeSearch(Builder $query, string $term): Builder
    {
        return $query->where(function ($q) use ($term) {
            $q->where('currency_name', 'like', '%' . $term . '%')
              ->orWhere('currency_code', 'like', '%' . $term . '%')
              ->orWhere('currency_symbol', 'like', '%' . $term . '%');
        });
    }

    /**
     * Scope to filter by currency code.
     */
    public function scopeByCode(Builder $query, string $code): Builder
    {
        return $query->where('currency_code', strtoupper($code));
    }

    /**
     * Scope to filter by supported country.
     */
    public function scopeByCountry(Builder $query, string $country): Builder
    {
        return $query->whereJsonContains('supported_countries', $country);
    }

    /**
     * Scope for popular currencies (most used in jobs).
     */
    public function scopePopular(Builder $query): Builder
    {
        return $query->withCount(['jobs' => function ($q) {
            $q->active();
        }])->orderBy('jobs_count', 'desc');
    }

    /**
     * Scope for trending currencies (recently gaining popularity).
     */
    public function scopeTrending(Builder $query): Builder
    {
        return $query->withCount([
            'jobs as recent_jobs_count' => function ($q) {
                $q->active()->where('created_at', '>=', now()->subDays(30));
            }
        ])->orderBy('recent_jobs_count', 'desc');
    }

    /**
     * Scope for stable currencies (low volatility).
     */
    public function scopeStable(Builder $query): Builder
    {
        return $query->where('is_crypto', false)
                    ->whereIn('currency_code', ['USD', 'EUR', 'CHF', 'JPY']);
    }

    /**
     * Scope for volatile currencies (high volatility).
     */
    public function scopeVolatile(Builder $query): Builder
    {
        return $query->where('is_crypto', true)
                    ->orWhereNotIn('currency_code', ['USD', 'EUR', 'CHF', 'JPY']);
    }

    /**
     * Scope for recently updated exchange rates.
     */
    public function scopeRecentlyUpdated(Builder $query, int $hours = 24): Builder
    {
        return $query->where('last_rate_update', '>=', now()->subHours($hours));
    }

    /**
     * Scope for currencies that need rate updates.
     */
    public function scopeNeedsUpdate(Builder $query, int $hours = 24): Builder
    {
        return $query->where(function ($q) use ($hours) {
            $q->whereNull('last_rate_update')
              ->orWhere('last_rate_update', '<', now()->subHours($hours));
        });
    }

    /**
     * Scope for currencies with exchange rates.
     */
    public function scopeWithExchangeRate(Builder $query): Builder
    {
        return $query->whereNotNull('exchange_rate');
    }

    /**
     * Scope for currencies without exchange rates.
     */
    public function scopeWithoutExchangeRate(Builder $query): Builder
    {
        return $query->whereNull('exchange_rate');
    }

    /**
     * Scope to order by exchange rate.
     */
    public function scopeOrderByRate(Builder $query, string $direction = 'asc'): Builder
    {
        return $query->orderBy('exchange_rate', $direction);
    }

    /**
     * Scope to order by usage (job count).
     */
    public function scopeOrderByUsage(Builder $query): Builder
    {
        return $query->withCount('jobs')->orderBy('jobs_count', 'desc');
    }

    /**
     * Scope to order by trend (recent usage).
     */
    public function scopeOrderByTrend(Builder $query): Builder
    {
        return $query->withCount([
            'jobs as recent_jobs_count' => function ($q) {
                $q->where('created_at', '>=', now()->subDays(30));
            }
        ])->orderBy('recent_jobs_count', 'desc');
    }

    /**
     * Scope a query to only include recent records.
     */
    public function scopeRecent(Builder $query, int $days = 30): Builder
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Scope a query to only include old records.
     */
    public function scopeOld(Builder $query, int $days = 365): Builder
    {
        return $query->where('created_at', '<=', now()->subDays($days));
    }

    // =============================================
    // ACCESSOR METHODS
    // =============================================

    /**
     * Get usage statistics.
     */
    public function getUsageStatisticsAttribute(): array
    {
        return Cache::remember("currency.{$this->id}.usage_stats", 3600, function () {
            $jobCount = $this->jobs()->count();
            $activeJobCount = $this->jobs()->active()->count();
            $totalSalaryPosted = $this->jobs()->sum('salary_from');
            
            return [
                'total_jobs' => $jobCount,
                'active_jobs' => $activeJobCount,
                'usage_percentage' => $this->calculateUsagePercentage(),
                'total_salary_posted' => $totalSalaryPosted,
                'average_salary' => $jobCount > 0 ? $totalSalaryPosted / $jobCount : 0,
                'popularity_rank' => $this->getPopularityRank(),
                'trend_direction' => $this->getTrendDirection(),
                'adoption_rate' => $this->calculateAdoptionRate(),
            ];
        });
    }

    /**
     * Get formatted symbol with currency code.
     */
    public function getFormattedSymbolAttribute(): string
    {
        return "{$this->currency_symbol} ({$this->currency_code})";
    }

    /**
     * Get exchange rate information.
     */
    public function getExchangeInfoAttribute(): array
    {
        return [
            'rate' => $this->exchange_rate,
            'base_currency' => $this->base_currency ?? 'USD',
            'last_updated' => $this->last_rate_update?->toISOString(),
            'is_stale' => $this->isExchangeRateStale(),
            'update_frequency' => $this->getUpdateFrequency(),
            'volatility_index' => $this->calculateVolatilityIndex(),
            'confidence_score' => $this->getConfidenceScore(),
        ];
    }

    /**
     * Get market value information.
     */
    public function getMarketValueAttribute(): array
    {
        return [
            'strength_index' => $this->calculateStrengthIndex(),
            'stability_rating' => $this->getStabilityRating(),
            'liquidity_score' => $this->getLiquidityScore(),
            'adoption_countries' => count($this->supported_countries ?? []),
            'market_cap_rank' => $this->is_crypto ? $this->getCryptoRank() : null,
            'inflation_rate' => $this->getInflationRate(),
        ];
    }

    /**
     * Get currency trend data.
     */
    public function getCurrencyTrendAttribute(): array
    {
        return [
            'weekly_change' => $this->calculateWeeklyChange(),
            'monthly_change' => $this->calculateMonthlyChange(),
            'yearly_change' => $this->calculateYearlyChange(),
            'trend_direction' => $this->getTrendDirection(),
            'prediction' => $this->getPrediction(),
            'support_level' => $this->getSupportLevel(),
            'resistance_level' => $this->getResistanceLevel(),
        ];
    }

    /**
     * Get conversion data for common operations.
     */
    public function getConversionDataAttribute(): array
    {
        $baseCurrency = static::default()->first();
        
        return [
            'to_base_rate' => $baseCurrency ? ($this->exchange_rate / $baseCurrency->exchange_rate) : $this->exchange_rate,
            'from_base_rate' => $baseCurrency ? ($baseCurrency->exchange_rate / $this->exchange_rate) : (1 / $this->exchange_rate),
            'precision' => $this->decimal_places,
            'formatting' => $this->number_format,
            'conversion_fee' => $this->getConversionFee(),
        ];
    }

    /**
     * Get regional information.
     */
    public function getRegionalInfoAttribute(): array
    {
        return [
            'supported_countries' => $this->supported_countries ?? [],
            'primary_regions' => $this->getPrimaryRegions(),
            'timezone_coverage' => $this->getTimezoneCoverage(),
            'economic_bloc' => $this->getEconomicBloc(),
            'regulatory_status' => $this->getRegulatoryStatus(),
        ];
    }

    // =============================================
    // UTILITY METHODS
    // =============================================

    /**
     * Convert amount from another currency to this currency.
     */
    public function convertFrom(float $amount, SalaryCurrency $fromCurrency): float
    {
        if ($this->id === $fromCurrency->id) {
            return $amount;
        }

        if (!$this->exchange_rate || !$fromCurrency->exchange_rate) {
            throw new \InvalidArgumentException('Exchange rates not available for conversion');
        }

        // Convert through base currency (usually USD)
        $baseAmount = $amount / $fromCurrency->exchange_rate;
        return $baseAmount * $this->exchange_rate;
    }

    /**
     * Convert amount from this currency to another currency.
     */
    public function convertTo(float $amount, SalaryCurrency $toCurrency): float
    {
        return $toCurrency->convertFrom($amount, $this);
    }

    /**
     * Format amount with currency symbol and proper decimal places.
     */
    public function formatAmount(float $amount, bool $includeSymbol = true): string
    {
        $formatted = number_format($amount, $this->decimal_places);
        
        if ($includeSymbol) {
            return $this->currency_symbol . ' ' . $formatted;
        }
        
        return $formatted;
    }

    /**
     * Check if exchange rate is stale and needs updating.
     */
    public function isExchangeRateStale(): bool
    {
        if (!$this->last_rate_update) {
            return true;
        }

        $staleHours = $this->is_crypto ? 1 : 24; // Crypto rates update more frequently
        return $this->last_rate_update->lt(now()->subHours($staleHours));
    }

    /**
     * Calculate usage percentage compared to all currencies.
     */
    public function calculateUsagePercentage(): float
    {
        $totalJobs = Job::count();
        if ($totalJobs === 0) {
            return 0;
        }

        $currencyJobs = $this->jobs()->count();
        return ($currencyJobs / $totalJobs) * 100;
    }

    /**
     * Get popularity rank among all currencies.
     */
    public function getPopularityRank(): int
    {
        return static::withCount('jobs')
            ->orderBy('jobs_count', 'desc')
            ->pluck('id')
            ->search($this->id) + 1;
    }

    /**
     * Get trend direction based on recent usage.
     */
    public function getTrendDirection(): string
    {
        $recentJobs = $this->jobs()->where('created_at', '>=', now()->subDays(30))->count();
        $previousJobs = $this->jobs()
            ->whereBetween('created_at', [now()->subDays(60), now()->subDays(30)])
            ->count();

        if ($recentJobs > $previousJobs) {
            return 'up';
        } elseif ($recentJobs < $previousJobs) {
            return 'down';
        }

        return 'stable';
    }

    /**
     * Calculate adoption rate (percentage of active jobs using this currency).
     */
    public function calculateAdoptionRate(): float
    {
        $activeJobs = Job::active()->count();
        if ($activeJobs === 0) {
            return 0;
        }

        $currencyActiveJobs = $this->jobs()->active()->count();
        return ($currencyActiveJobs / $activeJobs) * 100;
    }

    /**
     * Get update frequency in hours.
     */
    public function getUpdateFrequency(): int
    {
        return $this->is_crypto ? 1 : 24;
    }

    /**
     * Calculate volatility index (0-100, higher = more volatile).
     */
    public function calculateVolatilityIndex(): float
    {
        if ($this->is_crypto) {
            return 85.0; // High volatility for crypto
        }

        $stableCurrencies = ['USD', 'EUR', 'CHF', 'JPY'];
        if (in_array($this->currency_code, $stableCurrencies)) {
            return 15.0; // Low volatility for stable currencies
        }

        return 45.0; // Medium volatility for other fiat currencies
    }

    /**
     * Get confidence score for exchange rate accuracy (0-100).
     */
    public function getConfidenceScore(): float
    {
        if (!$this->last_rate_update) {
            return 0;
        }

        $hoursOld = $this->last_rate_update->diffInHours(now());
        $maxHours = $this->is_crypto ? 24 : 168; // 1 day for crypto, 1 week for fiat

        return max(0, 100 - ($hoursOld / $maxHours * 100));
    }

    // =============================================
    // PROTECTED HELPER METHODS
    // =============================================

    /**
     * Calculate strength index based on various factors.
     */
    protected function calculateStrengthIndex(): float
    {
        $factors = [
            'usage' => $this->calculateUsagePercentage(),
            'stability' => $this->getStabilityRating() === 'stable' ? 100 : 50,
            'liquidity' => $this->getLiquidityScore(),
            'adoption' => count($this->supported_countries ?? []) * 2,
        ];

        return array_sum($factors) / count($factors);
    }

    /**
     * Get stability rating.
     */
    protected function getStabilityRating(): string
    {
        $volatility = $this->calculateVolatilityIndex();
        
        if ($volatility < 25) {
            return 'stable';
        } elseif ($volatility < 60) {
            return 'moderate';
        }
        
        return 'volatile';
    }

    /**
     * Get liquidity score.
     */
    protected function getLiquidityScore(): float
    {
        $majorCurrencies = ['USD', 'EUR', 'GBP', 'JPY', 'CHF', 'CAD', 'AUD'];
        return in_array($this->currency_code, $majorCurrencies) ? 95.0 : 60.0;
    }

    /**
     * Get crypto market cap rank.
     */
    protected function getCryptoRank(): ?int
    {
        if (!$this->is_crypto) {
            return null;
        }

        $cryptoRanks = [
            'BTC' => 1, 'ETH' => 2, 'BNB' => 3, 'XRP' => 4, 'ADA' => 5,
            'SOL' => 6, 'DOGE' => 7, 'DOT' => 8, 'MATIC' => 9, 'LTC' => 10,
        ];

        return $cryptoRanks[$this->currency_code] ?? 999;
    }

    /**
     * Get estimated inflation rate.
     */
    protected function getInflationRate(): float
    {
        $inflationRates = [
            'USD' => 3.2, 'EUR' => 2.8, 'GBP' => 4.1, 'JPY' => 1.5,
            'CHF' => 1.8, 'CAD' => 3.5, 'AUD' => 3.8, 'NZD' => 4.2,
        ];

        return $inflationRates[$this->currency_code] ?? 5.0;
    }

    /**
     * Calculate weekly change percentage.
     */
    protected function calculateWeeklyChange(): float
    {
        // Placeholder - would integrate with real exchange rate API
        return rand(-5, 5) / 10;
    }

    /**
     * Calculate monthly change percentage.
     */
    protected function calculateMonthlyChange(): float
    {
        // Placeholder - would integrate with real exchange rate API
        return rand(-15, 15) / 10;
    }

    /**
     * Calculate yearly change percentage.
     */
    protected function calculateYearlyChange(): float
    {
        // Placeholder - would integrate with real exchange rate API
        return rand(-50, 50) / 10;
    }

    /**
     * Get prediction for currency trend.
     */
    protected function getPrediction(): string
    {
        $trend = $this->getTrendDirection();
        $predictions = [
            'up' => 'bullish',
            'down' => 'bearish',
            'stable' => 'neutral',
        ];

        return $predictions[$trend] ?? 'neutral';
    }

    /**
     * Get support level for technical analysis.
     */
    protected function getSupportLevel(): float
    {
        return $this->exchange_rate ? $this->exchange_rate * 0.95 : 0;
    }

    /**
     * Get resistance level for technical analysis.
     */
    protected function getResistanceLevel(): float
    {
        return $this->exchange_rate ? $this->exchange_rate * 1.05 : 0;
    }

    /**
     * Get conversion fee percentage.
     */
    protected function getConversionFee(): float
    {
        return $this->is_crypto ? 0.5 : 0.25; // Higher fees for crypto
    }

    /**
     * Get primary regions where currency is used.
     */
    protected function getPrimaryRegions(): array
    {
        $regionMap = [
            'USD' => ['North America', 'Global'],
            'EUR' => ['Europe'],
            'GBP' => ['Europe', 'Commonwealth'],
            'JPY' => ['Asia'],
            'CHF' => ['Europe'],
            'CAD' => ['North America'],
            'AUD' => ['Oceania'],
            'CNY' => ['Asia'],
        ];

        return $regionMap[$this->currency_code] ?? ['Global'];
    }

    /**
     * Get timezone coverage.
     */
    protected function getTimezoneCoverage(): array
    {
        return $this->supported_countries ?? [];
    }

    /**
     * Get economic bloc association.
     */
    protected function getEconomicBloc(): ?string
    {
        $blocMap = [
            'EUR' => 'European Union',
            'USD' => 'NAFTA',
            'GBP' => 'Commonwealth',
            'JPY' => 'ASEAN+3',
        ];

        return $blocMap[$this->currency_code] ?? null;
    }

    /**
     * Get regulatory status.
     */
    protected function getRegulatoryStatus(): string
    {
        if ($this->is_crypto) {
            return 'regulated'; // Simplified - would check actual regulations
        }

        return 'legal_tender';
    }

    // =============================================
    // STATIC METHODS & CACHING
    // =============================================

    /**
     * Get cached active currencies.
     */
    public static function getCachedActive(): \Illuminate\Database\Eloquent\Collection
    {
        return Cache::remember('salary_currencies_active', 3600, function () {
            return static::active()->orderBy('currency_name')->get();
        });
    }

    /**
     * Get cached default currency.
     */
    public static function getCachedDefault(): ?SalaryCurrency
    {
        return Cache::remember('salary_currency_default', 3600, function () {
            return static::default()->first();
        });
    }

    /**
     * Get cached popular currencies.
     */
    public static function getCachedPopular(int $limit = 10): \Illuminate\Database\Eloquent\Collection
    {
        return Cache::remember("salary_currencies_popular_{$limit}", 1800, function () use ($limit) {
            return static::popular()->active()->limit($limit)->get();
        });
    }

    /**
     * Clear related caches.
     */
    public function clearCaches(): void
    {
        Cache::forget('salary_currencies_active');
        Cache::forget('salary_currency_default');
        Cache::forget("currency.{$this->id}.usage_stats");
        
        // Clear pattern-based caches
        $this->clearCachePattern('salary_currencies_*');
        $this->clearCachePattern("currency.{$this->id}.*");
    }

    /**
     * Clear cache by pattern.
     */
    private function clearCachePattern(string $pattern): void
    {
        if (method_exists(Cache::getStore(), 'flush')) {
            // For stores that support pattern clearing
            $keys = Cache::getStore()->getRedis()->keys($pattern);
            if (!empty($keys)) {
                Cache::getStore()->getRedis()->del($keys);
            }
        }
    }

    // =============================================
    // MODEL EVENTS
    // =============================================

    /**
     * The "booted" method of the model.
     */
    protected static function boot(): void
    {
        parent::boot();

        // Clear cache when currency is updated
        static::updated(function ($currency) {
            $currency->clearCaches();
        });

        static::deleted(function ($currency) {
            $currency->clearCaches();
        });

        // Ensure only one default currency
        static::saving(function ($currency) {
            if ($currency->is_default) {
                static::where('id', '!=', $currency->id)->update(['is_default' => false]);
            }
        });
    }
}
