<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * Country Model - Enhanced with Context7 patterns.
 *
 * @property int                    $id
 * @property string                 $name
 * @property string                 $short_code
 * @property null|string            $phone_code
 * @property null|string            $iso_code
 * @property null|string            $currency
 * @property bool                   $is_active
 * @property bool                   $is_default
 * @property bool                   $is_featured
 * @property null|string            $flag_url
 * @property null|string            $region
 * @property null|string            $continent
 * @property null|int               $population
 * @property null|float             $area_km2
 * @property null|string            $capital
 * @property null|string            $timezone
 * @property null|array             $languages
 * @property null|Carbon            $created_at
 * @property null|Carbon            $updated_at
 * @property Collection|State[]     $states
 * @property City[]|Collection      $cities
 * @property Collection|User[]      $users
 * @property Collection|Company[]   $companies
 * @property Collection|Job[]       $jobs
 * @property Candidate[]|Collection $candidates
 * @property string                 $display_name
 * @property string                 $full_name
 * @property string                 $flag_emoji
 * @property string                 $population_formatted
 * @property string                 $area_formatted
 * @property int                    $states_count
 * @property int                    $cities_count
 * @property int                    $companies_count
 * @property int                    $active_companies_count
 * @property int                    $jobs_count
 * @property int                    $active_jobs_count
 * @property int                    $candidates_count
 * @property int                    $active_candidates_count
 * @property int                    $users_count
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Country newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Country newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Country query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Country active()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Country inactive()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Country featured()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Country nonFeatured()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Country default()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Country custom()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Country withStates()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Country withoutStates()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Country withCities()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Country withCompanies()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Country withCandidates()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Country search(string $term)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Country recent()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Country popular()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Country alphabetical()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Country byRegion(string $region)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Country byContinent(string $continent)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Country byCurrency(string $currency)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Country byLanguage(string $language)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Country populationGreaterThan(int $population)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Country populationLessThan(int $population)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Country populationBetween(int $min, int $max)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Country areaGreaterThan(float $area)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Country areaLessThan(float $area)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Country areaBetween(float $min, float $max)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Country withHighJobActivity()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Country withLowJobActivity()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Country trending()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Country emerging()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Country developed()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Country developing()
 *
 * @mixin \Eloquent
 */
class Country extends Model
{
    use HasFactory;
    use LogsActivity;

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
        'continent',
        'population',
        'area_km2',
        'capital',
        'timezone',
        'languages',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [];

    /**
     * Get the activity log options for the model.
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
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
                'continent',
                'population',
                'area_km2',
                'capital',
                'timezone',
                'languages',
            ])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
        ;
    }

    /**
     * Update validation rules for countries.
     *
     * @return array<string, string>
     */
    public static function updateRules(int $id): array
    {
        return [
            'name' => 'required|string|max:180|unique:countries,name,'.$id,
            'short_code' => 'required|string|size:2|unique:countries,short_code,'.$id,
            'iso_code' => 'nullable|string|size:2|unique:countries,iso_code,'.$id,
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
     *
     * @param mixed $query
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include inactive countries.
     *
     * @param mixed $query
     */
    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    /**
     * Scope a query to only include featured countries.
     *
     * @param mixed $query
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope a query to only include non-featured countries.
     *
     * @param mixed $query
     */
    public function scopeNonFeatured($query)
    {
        return $query->where('is_featured', false);
    }

    /**
     * Scope a query to only include default countries.
     *
     * @param mixed $query
     */
    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    /**
     * Scope a query to only include custom countries.
     *
     * @param mixed $query
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
     *
     * @param mixed $query
     */
    public function scopeSearch($query, string $term)
    {
        return $query->where(function ($q) use ($term) {
            $q->where('name', 'LIKE', "%{$term}%")
                ->orWhere('short_code', 'LIKE', "%{$term}%")
                ->orWhere('iso_code', 'LIKE', "%{$term}%")
                ->orWhere('capital', 'LIKE', "%{$term}%")
            ;
        });
    }

    /**
     * Scope a query to filter countries by short code.
     *
     * @param mixed $query
     */
    public function scopeByCode($query, string $code)
    {
        return $query->where('short_code', $code);
    }

    /**
     * Scope a query to filter countries by ISO code.
     *
     * @param mixed $query
     */
    public function scopeByIsoCode($query, string $isoCode)
    {
        return $query->where('iso_code', $isoCode);
    }

    /**
     * Scope a query to filter countries by phone code.
     *
     * @param mixed $query
     */
    public function scopeByPhoneCode($query, string $phoneCode)
    {
        return $query->where('phone_code', $phoneCode);
    }

    /**
     * Scope a query to filter countries by currency.
     *
     * @param mixed $query
     */
    public function scopeByCurrency($query, string $currency)
    {
        return $query->where('currency', $currency);
    }

    /**
     * Scope a query to filter countries by region.
     *
     * @param mixed $query
     */
    public function scopeByRegion($query, string $region)
    {
        return $query->where('region', $region);
    }

    /**
     * Scope a query to filter countries by continent.
     *
     * @param mixed $query
     */
    public function scopeByContinent($query, string $continent)
    {
        return $query->where('continent', $continent);
    }

    /**
     * Scope a query to get recent countries.
     *
     * @param mixed $query
     */
    public function scopeRecent($query)
    {
        return $query->orderBy('created_at', 'desc');
    }

    /**
     * Scope a query to get old countries.
     *
     * @param mixed $query
     */
    public function scopeOld($query, int $days = 365)
    {
        return $query->where('created_at', '<', now()->subDays($days));
    }

    /**
     * Scope a query to order countries alphabetically.
     *
     * @param mixed $query
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
     *
     * @param mixed $query
     */
    public function scopeWithStates($query)
    {
        return $query->has('states');
    }

    /**
     * Scope a query to include countries without states.
     *
     * @param mixed $query
     */
    public function scopeWithoutStates($query)
    {
        return $query->doesntHave('states');
    }

    /**
     * Scope a query to include countries with active states.
     *
     * @param mixed $query
     */
    public function scopeWithActiveStates($query)
    {
        return $query->whereHas('states', function ($q) {
            $q->where('is_active', true);
        });
    }

    /**
     * Scope a query to include countries with cities.
     *
     * @param mixed $query
     */
    public function scopeWithCities($query)
    {
        return $query->has('cities');
    }

    /**
     * Scope a query to include countries with companies.
     *
     * @param mixed $query
     */
    public function scopeWithCompanies($query)
    {
        return $query->has('companies');
    }

    /**
     * Scope a query to include countries with active companies.
     *
     * @param mixed $query
     */
    public function scopeWithActiveCompanies($query)
    {
        return $query->whereHas('companies', function ($q) {
            $q->where('is_active', true);
        });
    }

    /**
     * Scope a query to include countries with jobs.
     *
     * @param mixed $query
     */
    public function scopeWithJobs($query)
    {
        return $query->has('jobs');
    }

    /**
     * Scope a query to include countries with active jobs.
     *
     * @param mixed $query
     */
    public function scopeWithActiveJobs($query)
    {
        return $query->whereHas('jobs', function ($q) {
            $q->where('status', 'active');
        });
    }

    /**
     * Scope a query to include countries with candidates.
     *
     * @param mixed $query
     */
    public function scopeWithCandidates($query)
    {
        return $query->has('candidates');
    }

    /**
     * Scope a query to include countries with active candidates.
     *
     * @param mixed $query
     */
    public function scopeWithActiveCandidates($query)
    {
        return $query->whereHas('candidates', function ($q) {
            $q->where('is_active', true);
        });
    }

    /**
     * Scope a query to include countries with users.
     *
     * @param mixed $query
     */
    public function scopeWithUsers($query)
    {
        return $query->has('users');
    }

    /**
     * Scope a query to get popular countries (with most companies).
     *
     * @param mixed $query
     */
    public function scopePopular($query)
    {
        return $query->withCount(['companies', 'jobs'])
            ->orderBy('companies_count', 'desc')
            ->orderBy('jobs_count', 'desc')
        ;
    }

    /**
     * Scope a query to get popular countries by jobs.
     *
     * @param mixed $query
     */
    public function scopePopularByJobs($query, int $limit = 10)
    {
        return $query->withCount(['jobs' => function ($q) {
            $q->where('status', 'active');
        }])
            ->orderBy('jobs_count', 'desc')
            ->limit($limit)
        ;
    }

    /**
     * Scope a query to get popular countries by candidates.
     *
     * @param mixed $query
     */
    public function scopePopularByCandidates($query, int $limit = 10)
    {
        return $query->withCount(['candidates' => function ($q) {
            $q->where('is_active', true);
        }])
            ->orderBy('candidates_count', 'desc')
            ->limit($limit)
        ;
    }

    // =============================================
    // SCOPES - Geographic Regions
    // =============================================

    /**
     * Scope a query to get European countries.
     *
     * @param mixed $query
     */
    public function scopeEuropean($query)
    {
        $europeanCodes = [
            'AT', 'BE', 'BG', 'HR', 'CY', 'CZ', 'DK', 'EE', 'FI', 'FR',
            'DE', 'GR', 'HU', 'IE', 'IT', 'LV', 'LT', 'LU', 'MT', 'NL',
            'PL', 'PT', 'RO', 'SK', 'SI', 'ES', 'SE', 'GB',
        ];

        return $query->whereIn('iso_code', $europeanCodes);
    }

    /**
     * Scope a query to get North American countries.
     *
     * @param mixed $query
     */
    public function scopeNorthAmerican($query)
    {
        $northAmericanCodes = ['US', 'CA', 'MX', 'GT', 'BZ', 'SV', 'HN', 'NI', 'CR', 'PA'];

        return $query->whereIn('iso_code', $northAmericanCodes);
    }

    /**
     * Scope a query to get Asian countries.
     *
     * @param mixed $query
     */
    public function scopeAsian($query)
    {
        $asianCodes = [
            'CN', 'IN', 'ID', 'JP', 'BD', 'PK', 'VN', 'TR', 'IR', 'TH',
            'MM', 'KR', 'IQ', 'AF', 'MY', 'SA', 'UZ', 'NP', 'YE', 'LK',
        ];

        return $query->whereIn('iso_code', $asianCodes);
    }

    /**
     * Scope a query to get African countries.
     *
     * @param mixed $query
     */
    public function scopeAfrican($query)
    {
        $africanCodes = [
            'NG', 'ET', 'EG', 'ZA', 'KE', 'UG', 'DZ', 'SD', 'MA', 'AO',
            'GH', 'MZ', 'MG', 'CM', 'CI', 'NE', 'BF', 'ML', 'MW', 'ZM',
        ];

        return $query->whereIn('iso_code', $africanCodes);
    }

    /**
     * Scope a query to get South American countries.
     *
     * @param mixed $query
     */
    public function scopeSouthAmerican($query)
    {
        $southAmericanCodes = ['BR', 'CO', 'AR', 'PE', 'VE', 'CL', 'EC', 'BO', 'PY', 'UY', 'GY', 'SR', 'FK'];

        return $query->whereIn('iso_code', $southAmericanCodes);
    }

    /**
     * Scope a query to get Oceanian countries.
     *
     * @param mixed $query
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
    public static function getCachedActive(): Collection
    {
        return Cache::remember(
            'countries_active',
            now()->addHours(24),
            fn () => static::active()->alphabetical()->get()
        );
    }

    /**
     * Get cached featured countries.
     */
    public static function getCachedFeatured(): Collection
    {
        return Cache::remember(
            'countries_featured',
            now()->addHours(12),
            fn () => static::active()->featured()->alphabetical()->get()
        );
    }

    /**
     * Get cached default countries.
     */
    public static function getCachedDefault(): Collection
    {
        return Cache::remember(
            'countries_default',
            now()->addHours(24),
            fn () => static::active()->default()->alphabetical()->get()
        );
    }

    /**
     * Get cached countries by continent.
     */
    public static function getCachedByContinent(string $continent): Collection
    {
        return Cache::remember(
            "countries_continent_{$continent}",
            now()->addHours(24),
            fn () => static::active()
                ->byContinent($continent)
                ->alphabetical()
                ->get()
        );
    }

    /**
     * Get cached countries by region.
     */
    public static function getCachedByRegion(string $region): Collection
    {
        return Cache::remember(
            "countries_region_{$region}",
            now()->addHours(24),
            fn () => static::active()
                ->byRegion($region)
                ->alphabetical()
                ->get()
        );
    }

    /**
     * Get cached countries with states.
     */
    public static function getCachedWithStates(): Collection
    {
        return Cache::remember(
            'countries_with_states',
            now()->addHours(12),
            fn () => static::active()
                ->withStates()
                ->with('states')
                ->alphabetical()
                ->get()
        );
    }

    /**
     * Get cached popular countries.
     */
    public static function getCachedPopular(int $limit = 10): Collection
    {
        return Cache::remember(
            "countries_popular_{$limit}",
            now()->addHours(6),
            fn () => static::active()
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
        return $this->name.($this->short_code ? " ({$this->short_code})" : '');
    }

    /**
     * Get full name with code.
     */
    public function getFullNameAttribute(): string
    {
        $parts = [$this->name];

        if ($this->capital) {
            $parts[] = "Capital: {$this->capital}";
        }

        if ($this->region) {
            $parts[] = "Region: {$this->region}";
        }

        return implode(' | ', $parts);
    }

    /**
     * Get flag emoji based on ISO code.
     */
    public function getFlagEmojiAttribute(): string
    {
        if (!$this->short_code || 2 !== strlen($this->short_code)) {
            return '🏳️';
        }

        $flagEmojis = [
            'US' => '🇺🇸', 'CA' => '🇨🇦', 'GB' => '🇬🇧', 'DE' => '🇩🇪', 'FR' => '🇫🇷',
            'IT' => '🇮🇹', 'ES' => '🇪🇸', 'JP' => '🇯🇵', 'CN' => '🇨🇳', 'IN' => '🇮🇳',
            'AU' => '🇦🇺', 'BR' => '🇧🇷', 'MX' => '🇲🇽', 'RU' => '🇷🇺', 'ZA' => '🇿🇦',
        ];

        return $flagEmojis[$this->short_code] ?? '🏳️';
    }

    /**
     * Get formatted population attribute.
     */
    public function getPopulationFormattedAttribute(): string
    {
        if (!$this->population) {
            return 'N/A';
        }

        if ($this->population >= 1000000000) {
            return number_format($this->population / 1000000000, 1).'B';
        }
        if ($this->population >= 1000000) {
            return number_format($this->population / 1000000, 1).'M';
        }
        if ($this->population >= 1000) {
            return number_format($this->population / 1000, 1).'K';
        }

        return number_format($this->population);
    }

    /**
     * Get formatted area attribute.
     */
    public function getAreaFormattedAttribute(): string
    {
        if (!$this->area_km2) {
            return 'N/A';
        }

        return number_format($this->area_km2, 0).' km²';
    }

    /**
     * Get states count.
     */
    public function getStatesCountAttribute(): int
    {
        return Cache::remember(
            "country_{$this->id}_states_count",
            now()->addHours(6),
            fn () => $this->states()->count()
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
            fn () => $this->cities()->count()
        );
    }

    /**
     * Get companies count.
     */
    public function getCompaniesCountAttribute(): int
    {
        return Cache::remember(
            "country_{$this->id}_companies_count",
            now()->addHours(2),
            fn () => $this->companies()->count()
        );
    }

    /**
     * Get active companies count.
     */
    public function getActiveCompaniesCountAttribute(): int
    {
        return Cache::remember(
            "country_{$this->id}_active_companies_count",
            now()->addHours(2),
            fn () => $this->companies()->where('is_active', true)->count()
        );
    }

    /**
     * Get jobs count.
     */
    public function getJobsCountAttribute(): int
    {
        return Cache::remember(
            "country_{$this->id}_jobs_count",
            now()->addHours(1),
            fn () => $this->jobs()->count()
        );
    }

    /**
     * Get active jobs count.
     */
    public function getActiveJobsCountAttribute(): int
    {
        return Cache::remember(
            "country_{$this->id}_active_jobs_count",
            now()->addHours(1),
            fn () => $this->jobs()->where('status', 'active')->count()
        );
    }

    /**
     * Get candidates count.
     */
    public function getCandidatesCountAttribute(): int
    {
        return Cache::remember(
            "country_{$this->id}_candidates_count",
            now()->addHours(2),
            fn () => $this->candidates()->count()
        );
    }

    /**
     * Get active candidates count.
     */
    public function getActiveCandidatesCountAttribute(): int
    {
        return Cache::remember(
            "country_{$this->id}_active_candidates_count",
            now()->addHours(2),
            fn () => $this->candidates()->where('is_active', true)->count()
        );
    }

    /**
     * Get users count.
     */
    public function getUsersCountAttribute(): int
    {
        return Cache::remember(
            "country_{$this->id}_users_count",
            now()->addHours(4),
            fn () => $this->users()->count()
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
            'PL', 'PT', 'RO', 'SK', 'SI', 'ES', 'SE', 'GB',
        ]);
    }

    /**
     * Check if country uses Euro currency.
     */
    public function usesEuro(): bool
    {
        return 'EUR' === $this->currency;
    }

    /**
     * Get formatted phone code.
     */
    public function getFormattedPhoneCode(): string
    {
        return $this->phone_code ? '+'.$this->phone_code : '';
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
            'population' => 'integer',
            'area_km2' => 'float',
            'languages' => 'array',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
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
    }

    /**
     * Clear cache keys matching pattern.
     */
    private function clearCachePattern(string $pattern): void
    {
        $prefix = str_replace('*', '', $pattern);
        $keys = [
            $prefix.'states_count',
            $prefix.'cities_count',
            $prefix.'companies_count',
            $prefix.'active_companies_count',
            $prefix.'jobs_count',
            $prefix.'active_jobs_count',
            $prefix.'candidates_count',
            $prefix.'active_candidates_count',
            $prefix.'users_count',
        ];

        foreach ($keys as $key) {
            Cache::forget($key);
        }
    }
}
