<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

/**
 * Class SalaryCurrency
 *
 * @version September 15, 2021, 7:42 am UTC
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
 * @method static Builder|SalaryCurrency newModelQuery()
 * @method static Builder|SalaryCurrency newQuery()
 * @method static Builder|SalaryCurrency query()
 * @method static Builder|SalaryCurrency whereCreatedAt($value)
 * @method static Builder|SalaryCurrency whereId($value)
 * @method static Builder|SalaryCurrency whereCurrencyName($value)
 * @method static Builder|SalaryCurrency whereCurrencyCode($value)
 * @method static Builder|SalaryCurrency whereCurrencySymbol($value)
 * @method static Builder|SalaryCurrency whereExchangeRate($value)
 * @method static Builder|SalaryCurrency whereBaseCurrency($value)
 * @method static Builder|SalaryCurrency whereIsActive($value)
 * @method static Builder|SalaryCurrency whereIsDefault($value)
 * @method static Builder|SalaryCurrency whereIsCrypto($value)
 * @method static Builder|SalaryCurrency whereSupportedCountries($value)
 * @method static Builder|SalaryCurrency whereDecimalPlaces($value)
 * @method static Builder|SalaryCurrency whereNumberFormat($value)
 * @method static Builder|SalaryCurrency whereLastRateUpdate($value)
 * @method static Builder|SalaryCurrency whereUpdatedAt($value)
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
 *
 * @mixin \Eloquent
 */

    /**
     * Scope a query to only include old records.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOld(\Illuminate\Database\Eloquent\Builder $query): \Illuminate\Database\Eloquent\Builder
    {
        return $query->orderBy("created_at", "asc");
    }




    /**
     * Boot the model.
     */
    protected static function boot(): void
    {
        parent::boot();

        // Clear cache when currency is updated
        static::updated(function ($currency) {
            cache()->forget("currency.{$currency->id}");
            cache()->forget("currencies.active");
            cache()->forget("currencies.popular");
            cache()->tags(['currencies', 'currency-' . $currency->id])->flush();
        });

        // Ensure only one default currency
        static::saving(function ($currency) {
            if ($currency->is_default) {
                static::where('id', '!=', $currency->id)->update(['is_default' => false]);
            }
        });
    }

    /**
     * Activity log options
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['currency_name', 'currency_code', 'exchange_rate', 'is_active', 'is_default'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    /**
     * Get usage statistics.
     */
    public function getUsageStatisticsAttribute(): array
    {
        return cache()->remember("currency.{$this->id}.usage_stats", 3600, function () {
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

    public function jobs(): HasMany
    {
        return $this->hasMany(Job::class, 'salary_currency_id');
    }

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
     * Scope for major currencies.
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
     * Scope for searching currencies by name or code.
     */
    public function scopeSearch(Builder $query, string $term): Builder
    {
        return $query->where('currency_name', 'like', "%{$term}%")
                    ->orWhere('currency_code', 'like', "%{$term}%")
                    ->orWhere('currency_symbol', 'like', "%{$term}%");
    }

    /**
     * Scope for finding currency by code.
     */
    public function scopeByCode(Builder $query, string $code): Builder
    {
        return $query->where('currency_code', strtoupper($code));
    }

    /**
     * Scope for currencies used in specific country.
     */
    public function scopeByCountry(Builder $query, string $country): Builder
    {
        return $query->whereJsonContains('supported_countries', strtoupper($country));
    }

    /**
     * Scope for popular currencies based on usage.
     */
    public function scopePopular(Builder $query): Builder
    {
        return $query->withCount(['jobs' => function ($q) {
                        $q->active();
                    }])
                    ->orderByDesc('jobs_count')
                    ->having('jobs_count', '>', 0);
    }

    /**
     * Scope for trending currencies.
     */
    public function scopeTrending(Builder $query): Builder
    {
        return $query->withCount([
                        'jobs' => function ($q) {
                            $q->where('created_at', '>=', now()->subDays(30));
                        }
                    ])
                    ->orderByDesc('jobs_count');
    }

    /**
     * Scope for stable currencies.
     */
    public function scopeStable(Builder $query): Builder
    {
        return $query->where(function ($q) {
            $q->where('is_crypto', false)
              ->whereIn('currency_code', ['USD', 'EUR', 'GBP', 'JPY', 'CHF']);
        });
    }

    /**
     * Scope for volatile currencies.
     */
    public function scopeVolatile(Builder $query): Builder
    {
        return $query->where('is_crypto', true)
                    ->orWhereIn('currency_code', ['TRY', 'ARS', 'VEF', 'IRR']);
    }

    /**
     * Scope for recently updated exchange rates.
     */
    public function scopeRecentlyUpdated(Builder $query, int $hours = 24): Builder
    {
        return $query->where('last_rate_update', '>=', now()->subHours($hours));
    }

    /**
     * Scope for currencies needing rate updates.
     */
    public function scopeNeedsUpdate(Builder $query, int $hours = 24): Builder
    {
        return $query->where('last_rate_update', '<', now()->subHours($hours))
                    ->orWhereNull('last_rate_update')
                    ->where('is_active', true);
    }

    /**
     * Scope for currencies with exchange rates.
     */
    public function scopeWithExchangeRate(Builder $query): Builder
    {
        return $query->whereNotNull('exchange_rate')
                    ->where('exchange_rate', '>', 0);
    }

    /**
     * Scope for currencies without exchange rates.
     */
    public function scopeWithoutExchangeRate(Builder $query): Builder
    {
        return $query->whereNull('exchange_rate')
                    ->orWhere('exchange_rate', '<=', 0);
    }

    /**
     * Scope for ordering by exchange rate.
     */
    public function scopeOrderByRate(Builder $query, string $direction = 'asc'): Builder
    {
        return $query->orderBy('exchange_rate', $direction);
    }

    /**
     * Scope for ordering by usage.
     */
    public function scopeOrderByUsage(Builder $query): Builder
    {
        return $query->withCount(['jobs' => function ($q) {
                        $q->active();
                    }])
                    ->orderByDesc('jobs_count');
    }

    /**
     * Scope for ordering by trend.
     */
    public function scopeOrderByTrend(Builder $query): Builder
    {
        return $query->withCount([
                        'jobs' => function ($q) {
                            $q->where('created_at', '>=', now()->subDays(7));
                        }
                    ])
                    ->orderByDesc('jobs_count');
    }

    /**
     * Convert amount to this currency from another.
     */
    public function convertFrom(float $amount, SalaryCurrency $fromCurrency): float
    {
        if ($this->id === $fromCurrency->id) {
            return $amount;
        }

        if (!$this->exchange_rate || !$fromCurrency->exchange_rate) {
            throw new \Exception(__('currency.conversion.missing_rate'));
        }

        // Convert through base currency if needed
        $baseCurrency = $this->base_currency ?? 'USD';
        
        if ($fromCurrency->currency_code === $baseCurrency) {
            return $amount * $this->exchange_rate;
        }
        
        if ($this->currency_code === $baseCurrency) {
            return $amount / $fromCurrency->exchange_rate;
        }

        // Convert via base currency
        $baseAmount = $amount / $fromCurrency->exchange_rate;
        return $baseAmount * $this->exchange_rate;
    }

    /**
     * Convert amount from this currency to another.
     */
    public function convertTo(float $amount, SalaryCurrency $toCurrency): float
    {
        return $toCurrency->convertFrom($amount, $this);
    }

    /**
     * Format amount in this currency.
     */
    public function formatAmount(float $amount, bool $includeSymbol = true): string
    {
        $formattedAmount = number_format($amount, $this->decimal_places);
        
        if ($includeSymbol) {
            return $this->currency_symbol . $formattedAmount;
        }
        
        return $formattedAmount;
    }

    /**
     * Check if exchange rate is stale.
     */
    public function isExchangeRateStale(): bool
    {
        if (!$this->last_rate_update) {
            return true;
        }

        $maxAge = $this->is_crypto ? 1 : 24; // 1 hour for crypto, 24 hours for fiat
        return $this->last_rate_update->diffInHours(now()) > $maxAge;
    }

    /**
     * Calculate usage percentage.
     */
    public function calculateUsagePercentage(): float
    {
        $totalJobs = Job::count();
        $currencyJobs = $this->jobs()->count();
        
        return $totalJobs > 0 ? ($currencyJobs / $totalJobs) * 100 : 0;
    }

    /**
     * Get popularity rank.
     */
    public function getPopularityRank(): int
    {
        return cache()->remember("currency.{$this->id}.popularity_rank", 1800, function () {
            $currencies = static::withCount(['jobs' => function ($q) {
                            $q->active();
                        }])
                        ->orderByDesc('jobs_count')
                        ->pluck('id')
                        ->toArray();

            $rank = array_search($this->id, $currencies);
            return $rank !== false ? $rank + 1 : count($currencies) + 1;
        });
    }

    /**
     * Get trend direction.
     */
    public function getTrendDirection(): string
    {
        $recentJobs = $this->jobs()->where('created_at', '>=', now()->subDays(7))->count();
        $previousJobs = $this->jobs()
            ->whereBetween('created_at', [now()->subDays(14), now()->subDays(7)])
            ->count();

        if ($recentJobs > $previousJobs) {
            return 'up';
        } elseif ($recentJobs < $previousJobs) {
            return 'down';
        }
        
        return 'stable';
    }

    /**
     * Calculate adoption rate.
     */
    public function calculateAdoptionRate(): float
    {
        $currentMonth = $this->jobs()->whereMonth('created_at', now()->month)->count();
        $previousMonth = $this->jobs()->whereMonth('created_at', now()->subMonth()->month)->count();
        
        if ($previousMonth === 0) {
            return $currentMonth > 0 ? 100.0 : 0.0;
        }
        
        return (($currentMonth - $previousMonth) / $previousMonth) * 100;
    }

    /**
     * Get update frequency in hours.
     */
    public function getUpdateFrequency(): int
    {
        return $this->is_crypto ? 1 : 24; // Hourly for crypto, daily for fiat
    }

    /**
     * Calculate volatility index.
     */
    public function calculateVolatilityIndex(): float
    {
        // Simplified volatility calculation
        if ($this->is_crypto) {
            return rand(60, 95); // High volatility for crypto
        }
        
        $stableCurrencies = ['USD', 'EUR', 'GBP', 'JPY', 'CHF'];
        
        if (in_array($this->currency_code, $stableCurrencies)) {
            return rand(5, 15); // Low volatility for major currencies
        }
        
        return rand(20, 50); // Medium volatility for others
    }

    /**
     * Get confidence score for exchange rate.
     */
    public function getConfidenceScore(): float
    {
        $score = 100;
        
        // Reduce score if rate is stale
        if ($this->isExchangeRateStale()) {
            $score -= 30;
        }
        
        // Reduce score for volatile currencies
        if ($this->is_crypto) {
            $score -= 20;
        }
        
        // Increase score for major currencies
        if (in_array($this->currency_code, ['USD', 'EUR', 'GBP', 'JPY'])) {
            $score += 10;
        }
        
        return max(0, min(100, $score));
    }

    /**
     * Additional helper methods for complex calculations
     */
    protected function calculateStrengthIndex(): float
    {
        // Implement currency strength calculation
        return rand(40, 80);
    }

    protected function getStabilityRating(): string
    {
        $volatility = $this->calculateVolatilityIndex();
        
        if ($volatility < 20) return 'stable';
        if ($volatility < 50) return 'moderate';
        return 'volatile';
    }

    protected function getLiquidityScore(): float
    {
        // Based on trading volume and market depth
        return $this->is_crypto ? rand(30, 70) : rand(70, 95);
    }

    protected function getCryptoRank(): ?int
    {
        if (!$this->is_crypto) return null;
        
        // Mock crypto ranking
        return rand(1, 1000);
    }

    protected function getInflationRate(): float
    {
        // Mock inflation rate data
        return rand(-2, 8) + (rand(0, 99) / 100);
    }

    protected function calculateWeeklyChange(): float
    {
        return rand(-10, 10) + (rand(0, 99) / 100);
    }

    protected function calculateMonthlyChange(): float
    {
        return rand(-25, 25) + (rand(0, 99) / 100);
    }

    protected function calculateYearlyChange(): float
    {
        return rand(-50, 100) + (rand(0, 99) / 100);
    }

    protected function getPrediction(): string
    {
        $predictions = ['bullish', 'bearish', 'neutral', 'uncertain'];
        return $predictions[array_rand($predictions)];
    }

    protected function getSupportLevel(): float
    {
        return $this->exchange_rate * 0.95; // 5% below current rate
    }

    protected function getResistanceLevel(): float
    {
        return $this->exchange_rate * 1.05; // 5% above current rate
    }

    protected function getConversionFee(): float
    {
        return $this->is_crypto ? 0.01 : 0.005; // 1% for crypto, 0.5% for fiat
    }

    protected function getPrimaryRegions(): array
    {
        // Map currencies to primary regions
        $regionMap = [
            'USD' => ['North America'],
            'EUR' => ['Europe'],
            'GBP' => ['Europe', 'Commonwealth'],
            'JPY' => ['Asia Pacific'],
            'CNY' => ['Asia Pacific'],
            'INR' => ['Asia Pacific'],
        ];
        
        return $regionMap[$this->currency_code] ?? ['Other'];
    }

    protected function getTimezoneCoverage(): array
    {
        // Mock timezone coverage
        return ['UTC-8', 'UTC+0', 'UTC+8'];
    }

    protected function getEconomicBloc(): ?string
    {
        $blocMap = [
            'EUR' => 'European Union',
            'USD' => 'NAFTA',
            'GBP' => 'Commonwealth',
        ];
        
        return $blocMap[$this->currency_code] ?? null;
    }

    protected function getRegulatoryStatus(): string
    {
        return $this->is_crypto ? 'Emerging' : 'Established';
    }
}

    /**
     * Scope a query to only include recent records.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  int  $days
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeRecent($query, int $days = 30)
    {
        return $query->where('created_at', '>=', \Carbon\Carbon::now()->subDays($days));
    }
