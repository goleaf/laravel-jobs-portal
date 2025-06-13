<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

/**
 * Country Model - Enhanced with Context7 patterns
 *
 * @property int $id
 * @property string $name
 * @property string $short_code
 * @property string|null $phone_code
 * @property string|null $iso_code
 * @property string|null $currency
 * @property bool $is_active
 * @property bool $is_default
 * @property bool $is_featured
 * @property string|null $flag_url
 * @property string|null $region
 * @property string|null $continent
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 *
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\State[] $states
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\City[] $cities
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\User[] $users
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Company[] $companies
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Job[] $jobs
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Candidate[] $candidates
 * @property-read string $display_name
 * @property-read string $full_name
 * @property-read string $flag_emoji
 * @property-read int $states_count
 * @property-read int $cities_count
 * @property-read int $companies_count
 * @property-read int $active_companies_count
 * @property-read int $jobs_count
 * @property-read int $active_jobs_count
 * @property-read int $candidates_count
 * @property-read int $active_candidates_count
 * @property-read int $users_count
 *
 * Context7 Enhanced Scopes:
 * @method static \Illuminate\Database\Eloquent\Builder active()
 * @method static \Illuminate\Database\Eloquent\Builder inactive()
 * @method static \Illuminate\Database\Eloquent\Builder featured()
 * @method static \Illuminate\Database\Eloquent\Builder default()
 * @method static \Illuminate\Database\Eloquent\Builder custom()
 * @method static \Illuminate\Database\Eloquent\Builder search(string $term)
 * @method static \Illuminate\Database\Eloquent\Builder byCode(string $code)
 * @method static \Illuminate\Database\Eloquent\Builder byIsoCode(string $isoCode)
 * @method static \Illuminate\Database\Eloquent\Builder byPhoneCode(string $phoneCode)
 * @method static \Illuminate\Database\Eloquent\Builder byCurrency(string $currency)
 * @method static \Illuminate\Database\Eloquent\Builder byRegion(string $region)
 * @method static \Illuminate\Database\Eloquent\Builder byContinent(string $continent)
 * @method static \Illuminate\Database\Eloquent\Builder recent(int $days = 30)
 * @method static \Illuminate\Database\Eloquent\Builder popular(int $limit = 10)
 * @method static \Illuminate\Database\Eloquent\Builder alphabetical()
 * @method static \Illuminate\Database\Eloquent\Builder european()
 * @method static \Illuminate\Database\Eloquent\Builder northAmerican()
 * @method static \Illuminate\Database\Eloquent\Builder asian()
 * @method static \Illuminate\Database\Eloquent\Builder african()
 * @method static \Illuminate\Database\Eloquent\Builder southAmerican()
 * @method static \Illuminate\Database\Eloquent\Builder oceanian()
 *
 * @mixin \Eloquent
 */
class Country extends Model
{
    use HasFactory;
    use SoftDeletes;
    use LogsActivity;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'short_code',
        'phone_code',
        'iso_code',
        'currency',
        'is_active',
        'is_default',
        'is_featured',
        'flag_url',
        'region',
        'continent'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'deleted_at',
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
            'is_featured' => 'boolean',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }

    /**
     * Get the activity log options for the model.
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'short_code', 'iso_code', 'is_active', 'is_default', 'is_featured'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    /**
     * Validation rules for creating countries.
     *
     * @var array<string, string>
     */
    public static array $rules = [
        'name' => 'required|string|max:180|unique:countries,name',
        'short_code' => 'required|string|size:2|unique:countries,short_code',
        'iso_code' => 'nullable|string|size:2|unique:countries,iso_code',
        'phone_code' => 'nullable|string|max:10',
        'currency' => 'nullable|string|max:10',
        'is_active' => 'boolean',
        'is_default' => 'boolean',
        'is_featured' => 'boolean',
        'flag_url' => 'nullable|url|max:255',
        'region' => 'nullable|string|max:100',
        'continent' => 'nullable|string|max:50',
    ];

    /**
     * Update validation rules for countries.
     *
     * @param int $id
     * @return array<string, string>
     */
    public static function updateRules(int $id): array
    {
        return [
            'name' => 'required|string|max:180|unique:countries,name,' . $id,
            'short_code' => 'required|string|size:2|unique:countries,short_code,' . $id,
            'iso_code' => 'nullable|string|size:2|unique:countries,iso_code,' . $id,
            'phone_code' => 'nullable|string|max:10',
            'currency' => 'nullable|string|max:10',
            'is_active' => 'boolean',
            'is_default' => 'boolean',
            'is_featured' => 'boolean',
            'flag_url' => 'nullable|url|max:255',
            'region' => 'nullable|string|max:100',
            'continent' => 'nullable|string|max:50',
        ];
    }

    // =============================================
    // RELATIONSHIPS
    // =============================================

    /**
     * Get the states for the country.
     */
    public function states(): HasMany
    {
        return $this->hasMany(State::class, 'country_id');
    }

    /**
     * Get the cities through states.
     */
    public function cities(): HasManyThrough
    {
        return $this->hasManyThrough(City::class, State::class, 'country_id', 'state_id');
    }

    /**
     * Get the users for the country.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'country_id');
    }

    /**
     * Get the companies for the country.
     */
    public function companies(): HasMany
    {
        return $this->hasMany(Company::class, 'country_id');
    }

    /**
     * Get the jobs for the country.
     */
    public function jobs(): HasMany
    {
        return $this->hasMany(Job::class, 'country_id');
    }

    /**
     * Get the candidates for the country.
     */
    public function candidates(): HasMany
    {
        return $this->hasMany(Candidate::class, 'country_id');
    }

    // =============================================
    // SCOPES - Basic Status
    // =============================================

    /**
     * Scope a query to only include active countries.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include inactive countries.
     */
    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    /**
     * Scope a query to only include featured countries.
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope a query to only include non-featured countries.
     */
    public function scopeNonFeatured($query)
    {
        return $query->where('is_featured', false);
    }

    /**
     * Scope a query to only include default countries.
     */
    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    /**
     * Scope a query to only include custom countries.
     */
    public function scopeCustom($query)
    {
        return $query->where('is_default', false);
    }

    // =============================================
    // SCOPES - Search & Filtering
    // =============================================

    /**
     * Scope a query to search countries by name, code, or ISO code.
     */
    public function scopeSearch($query, string $term)
    {
        return $query->where(function ($q) use ($term) {
            $q->where('name', 'like', "%{$term}%")
              ->orWhere('short_code', 'like', "%{$term}%")
              ->orWhere('iso_code', 'like', "%{$term}%")
              ->orWhere('phone_code', 'like', "%{$term}%");
        });
    }

    /**
     * Scope a query to filter countries by short code.
     */
    public function scopeByCode($query, string $code)
    {
        return $query->where('short_code', $code);
    }

    /**
     * Scope a query to filter countries by ISO code.
     */
    public function scopeByIsoCode($query, string $isoCode)
    {
        return $query->where('iso_code', $isoCode);
    }

    /**
     * Scope a query to filter countries by phone code.
     */
    public function scopeByPhoneCode($query, string $phoneCode)
    {
        return $query->where('phone_code', $phoneCode);
    }

    /**
     * Scope a query to filter countries by currency.
     */
    public function scopeByCurrency($query, string $currency)
    {
        return $query->where('currency', $currency);
    }

    /**
     * Scope a query to filter countries by region.
     */
    public function scopeByRegion($query, string $region)
    {
        return $query->where('region', $region);
    }

    /**
     * Scope a query to filter countries by continent.
     */
    public function scopeByContinent($query, string $continent)
    {
        return $query->where('continent', $continent);
    }

    /**
     * Scope a query to get recent countries.
     */
    public function scopeRecent($query, int $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Scope a query to get old countries.
     */
    public function scopeOld($query, int $days = 365)
    {
        return $query->where('created_at', '<', now()->subDays($days));
    }

    /**
     * Scope a query to order countries alphabetically.
     */
    public function scopeAlphabetical($query)
    {
        return $query->orderBy('name', 'asc');
    }

    // =============================================
    // SCOPES - Relationships
    // =============================================

    /**
     * Scope a query to include countries with states.
     */
    public function scopeWithStates($query)
    {
        return $query->has('states');
    }

    /**
     * Scope a query to include countries without states.
     */
    public function scopeWithoutStates($query)
    {
        return $query->doesntHave('states');
    }

    /**
     * Scope a query to include countries with active states.
     */
    public function scopeWithActiveStates($query)
    {
        return $query->whereHas('states', function ($q) {
            $q->where('is_active', true);
        });
    }

    /**
     * Scope a query to include countries with cities.
     */
    public function scopeWithCities($query)
    {
        return $query->has('cities');
    }

    /**
     * Scope a query to include countries with companies.
     */
    public function scopeWithCompanies($query)
    {
        return $query->has('companies');
    }

    /**
     * Scope a query to include countries with active companies.
     */
    public function scopeWithActiveCompanies($query)
    {
        return $query->whereHas('companies', function ($q) {
            $q->where('is_active', true);
        });
    }

    /**
     * Scope a query to include countries with jobs.
     */
    public function scopeWithJobs($query)
    {
        return $query->has('jobs');
    }

    /**
     * Scope a query to include countries with active jobs.
     */
    public function scopeWithActiveJobs($query)
    {
        return $query->whereHas('jobs', function ($q) {
            $q->where('status', 'active');
        });
    }

    /**
     * Scope a query to include countries with candidates.
     */
    public function scopeWithCandidates($query)
    {
        return $query->has('candidates');
    }

    /**
     * Scope a query to include countries with active candidates.
     */
    public function scopeWithActiveCandidates($query)
    {
        return $query->whereHas('candidates', function ($q) {
            $q->where('is_active', true);
        });
    }

    /**
     * Scope a query to include countries with users.
     */
    public function scopeWithUsers($query)
    {
        return $query->has('users');
    }

    /**
     * Scope a query to get popular countries (with most companies).
     */
    public function scopePopular($query, int $limit = 10)
    {
        return $query->withCount(['companies' => function ($q) {
            $q->where('is_active', true);
        }])
        ->orderBy('companies_count', 'desc')
        ->limit($limit);
    }

    /**
     * Scope a query to get popular countries by jobs.
     */
    public function scopePopularByJobs($query, int $limit = 10)
    {
        return $query->withCount(['jobs' => function ($q) {
            $q->where('status', 'active');
        }])
        ->orderBy('jobs_count', 'desc')
        ->limit($limit);
    }

    /**
     * Scope a query to get popular countries by candidates.
     */
    public function scopePopularByCandidates($query, int $limit = 10)
    {
        return $query->withCount(['candidates' => function ($q) {
            $q->where('is_active', true);
        }])
        ->orderBy('candidates_count', 'desc')
        ->limit($limit);
    }

    // =============================================
    // SCOPES - Geographic Regions
    // =============================================

    /**
     * Scope a query to get European countries.
     */
    public function scopeEuropean($query)
    {
        $europeanCodes = [
            'AT', 'BE', 'BG', 'HR', 'CY', 'CZ', 'DK', 'EE', 'FI', 'FR', 
            'DE', 'GR', 'HU', 'IE', 'IT', 'LV', 'LT', 'LU', 'MT', 'NL', 
            'PL', 'PT', 'RO', 'SK', 'SI', 'ES', 'SE', 'GB'
        ];
        return $query->whereIn('iso_code', $europeanCodes);
    }

    /**
     * Scope a query to get North American countries.
     */
    public function scopeNorthAmerican($query)
    {
        $northAmericanCodes = ['US', 'CA', 'MX', 'GT', 'BZ', 'SV', 'HN', 'NI', 'CR', 'PA'];
        return $query->whereIn('iso_code', $northAmericanCodes);
    }

    /**
     * Scope a query to get Asian countries.
     */
    public function scopeAsian($query)
    {
        $asianCodes = [
            'CN', 'IN', 'ID', 'JP', 'BD', 'PK', 'VN', 'TR', 'IR', 'TH',
            'MM', 'KR', 'IQ', 'AF', 'MY', 'SA', 'UZ', 'NP', 'YE', 'LK'
        ];
        return $query->whereIn('iso_code', $asianCodes);
    }

    /**
     * Scope a query to get African countries.
     */
    public function scopeAfrican($query)
    {
        $africanCodes = [
            'NG', 'ET', 'EG', 'ZA', 'KE', 'UG', 'DZ', 'SD', 'MA', 'AO',
            'GH', 'MZ', 'MG', 'CM', 'CI', 'NE', 'BF', 'ML', 'MW', 'ZM'
        ];
        return $query->whereIn('iso_code', $africanCodes);
    }

    /**
     * Scope a query to get South American countries.
     */
    public function scopeSouthAmerican($query)
    {
        $southAmericanCodes = ['BR', 'CO', 'AR', 'PE', 'VE', 'CL', 'EC', 'BO', 'PY', 'UY', 'GY', 'SR', 'FK'];
        return $query->whereIn('iso_code', $southAmericanCodes);
    }

    /**
     * Scope a query to get Oceanian countries.
     */
    public function scopeOceanian($query)
    {
        $oceanianCodes = ['AU', 'PG', 'NZ', 'FJ', 'SB', 'NC', 'PF', 'VU', 'WS', 'KI'];
        return $query->whereIn('iso_code', $oceanianCodes);
    }

    // =============================================
    // CACHED METHODS
    // =============================================

    /**
     * Get cached active countries.
     */
    public static function getCachedActive(): \Illuminate\Database\Eloquent\Collection
    {
        return Cache::remember(
            'countries_active',
            now()->addHours(24),
            fn() => static::active()->alphabetical()->get()
        );
    }

    /**
     * Get cached featured countries.
     */
    public static function getCachedFeatured(): \Illuminate\Database\Eloquent\Collection
    {
        return Cache::remember(
            'countries_featured',
            now()->addHours(12),
            fn() => static::active()->featured()->alphabetical()->get()
        );
    }

    /**
     * Get cached default countries.
     */
    public static function getCachedDefault(): \Illuminate\Database\Eloquent\Collection
    {
        return Cache::remember(
            'countries_default',
            now()->addHours(24),
            fn() => static::active()->default()->alphabetical()->get()
        );
    }

    /**
     * Get cached countries by continent.
     */
    public static function getCachedByContinent(string $continent): \Illuminate\Database\Eloquent\Collection
    {
        return Cache::remember(
            "countries_continent_{$continent}",
            now()->addHours(24),
            fn() => static::active()
                ->byContinent($continent)
                ->alphabetical()
                ->get()
        );
    }

    /**
     * Get cached countries by region.
     */
    public static function getCachedByRegion(string $region): \Illuminate\Database\Eloquent\Collection
    {
        return Cache::remember(
            "countries_region_{$region}",
            now()->addHours(24),
            fn() => static::active()
                ->byRegion($region)
                ->alphabetical()
                ->get()
        );
    }

    /**
     * Get cached countries with states.
     */
    public static function getCachedWithStates(): \Illuminate\Database\Eloquent\Collection
    {
        return Cache::remember(
            'countries_with_states',
            now()->addHours(12),
            fn() => static::active()
                ->withStates()
                ->with('states')
                ->alphabetical()
                ->get()
        );
    }

    /**
     * Get cached popular countries.
     */
    public static function getCachedPopular(int $limit = 10): \Illuminate\Database\Eloquent\Collection
    {
        return Cache::remember(
            "countries_popular_{$limit}",
            now()->addHours(6),
            fn() => static::active()
                ->popular($limit)
                ->get()
        );
    }

    // =============================================
    // HELPER METHODS & ATTRIBUTES
    // =============================================

    /**
     * Get display name with flag.
     */
    public function getDisplayNameAttribute(): string
    {
        return $this->flag_emoji . ' ' . $this->name;
    }

    /**
     * Get full name with code.
     */
    public function getFullNameAttribute(): string
    {
        return $this->name . ' (' . $this->short_code . ')';
    }

    /**
     * Get flag emoji based on ISO code.
     */
    public function getFlagEmojiAttribute(): string
    {
        if (!$this->iso_code || strlen($this->iso_code) !== 2) {
            return '🏳️';
        }

        $flagEmojis = [
            'US' => '🇺🇸', 'CA' => '🇨🇦', 'GB' => '🇬🇧', 'FR' => '🇫🇷', 'DE' => '🇩🇪',
            'IT' => '🇮🇹', 'ES' => '🇪🇸', 'PT' => '🇵🇹', 'BR' => '🇧🇷', 'MX' => '🇲🇽',
            'AR' => '🇦🇷', 'IN' => '🇮🇳', 'CN' => '🇨🇳', 'JP' => '🇯🇵', 'KR' => '🇰🇷',
            'AU' => '🇦🇺', 'NZ' => '🇳🇿', 'ZA' => '🇿🇦', 'EG' => '🇪🇬', 'NG' => '🇳🇬',
            'RU' => '🇷🇺', 'TR' => '🇹🇷', 'SA' => '🇸🇦', 'AE' => '🇦🇪', 'SG' => '🇸🇬'
        ];

        return $flagEmojis[$this->iso_code] ?? '🏳️';
    }

    /**
     * Get states count.
     */
    public function getStatesCountAttribute(): int
    {
        return Cache::remember(
            "country_{$this->id}_states_count",
            now()->addHours(6),
            fn() => $this->states()->count()
        );
    }

    /**
     * Get cities count.
     */
    public function getCitiesCountAttribute(): int
    {
        return Cache::remember(
            "country_{$this->id}_cities_count",
            now()->addHours(6),
            fn() => $this->cities()->count()
        );
    }

    /**
     * Get companies count.
     */
    public function getCompaniesCountAttribute(): int
    {
        return Cache::remember(
            "country_{$this->id}_companies_count",
            now()->addHours(6),
            fn() => $this->companies()->count()
        );
    }

    /**
     * Get active companies count.
     */
    public function getActiveCompaniesCountAttribute(): int
    {
        return Cache::remember(
            "country_{$this->id}_active_companies_count",
            now()->addHours(6),
            fn() => $this->companies()->where('is_active', true)->count()
        );
    }

    /**
     * Get jobs count.
     */
    public function getJobsCountAttribute(): int
    {
        return Cache::remember(
            "country_{$this->id}_jobs_count",
            now()->addHours(6),
            fn() => $this->jobs()->count()
        );
    }

    /**
     * Get active jobs count.
     */
    public function getActiveJobsCountAttribute(): int
    {
        return Cache::remember(
            "country_{$this->id}_active_jobs_count",
            now()->addHours(6),
            fn() => $this->jobs()->where('status', 'active')->count()
        );
    }

    /**
     * Get candidates count.
     */
    public function getCandidatesCountAttribute(): int
    {
        return Cache::remember(
            "country_{$this->id}_candidates_count",
            now()->addHours(6),
            fn() => $this->candidates()->count()
        );
    }

    /**
     * Get active candidates count.
     */
    public function getActiveCandidatesCountAttribute(): int
    {
        return Cache::remember(
            "country_{$this->id}_active_candidates_count",
            now()->addHours(6),
            fn() => $this->candidates()->where('is_active', true)->count()
        );
    }

    /**
     * Get users count.
     */
    public function getUsersCountAttribute(): int
    {
        return Cache::remember(
            "country_{$this->id}_users_count",
            now()->addHours(6),
            fn() => $this->users()->count()
        );
    }

    /**
     * Check if country is in Europe.
     */
    public function isEuropean(): bool
    {
        return in_array($this->iso_code, [
            'AT', 'BE', 'BG', 'HR', 'CY', 'CZ', 'DK', 'EE', 'FI', 'FR', 
            'DE', 'GR', 'HU', 'IE', 'IT', 'LV', 'LT', 'LU', 'MT', 'NL', 
            'PL', 'PT', 'RO', 'SK', 'SI', 'ES', 'SE', 'GB'
        ]);
    }

    /**
     * Check if country uses Euro currency.
     */
    public function usesEuro(): bool
    {
        return $this->currency === 'EUR';
    }

    /**
     * Get formatted phone code.
     */
    public function getFormattedPhoneCode(): string
    {
        return $this->phone_code ? '+' . $this->phone_code : '';
    }

    /**
     * Clear related caches.
     */
    public function clearCaches(): void
    {
        $patterns = [
            'countries_active',
            'countries_featured',
            'countries_default',
            'countries_with_states',
            "country_{$this->id}_*",
        ];

        foreach ($patterns as $pattern) {
            if (str_contains($pattern, '*')) {
                $this->clearCachePattern($pattern);
            } else {
                Cache::forget($pattern);
            }
        }

        if ($this->continent) {
            Cache::forget("countries_continent_{$this->continent}");
        }
        if ($this->region) {
            Cache::forget("countries_region_{$this->region}");
        }

        // Clear popular caches
        for ($i = 5; $i <= 20; $i += 5) {
            Cache::forget("countries_popular_{$i}");
        }
    }

    /**
     * Clear cache keys matching pattern.
     */
    private function clearCachePattern(string $pattern): void
    {
        $prefix = str_replace('*', '', $pattern);
        $keys = [
            $prefix . 'states_count',
            $prefix . 'cities_count',
            $prefix . 'companies_count',
            $prefix . 'active_companies_count',
            $prefix . 'jobs_count',
            $prefix . 'active_jobs_count',
            $prefix . 'candidates_count',
            $prefix . 'active_candidates_count',
            $prefix . 'users_count',
        ];

        foreach ($keys as $key) {
            Cache::forget($key);
        }
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::saved(function ($country) {
            $country->clearCaches();
        });

        static::deleted(function ($country) {
            $country->clearCaches();
        });

        static::restored(function ($country) {
            $country->clearCaches();
        });
    }
}
