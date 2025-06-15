<?php

namespace App\Models;

use Barryvdh\LaravelIdeHelper\Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

/**
 * Language Model - Enhanced with Enhanced patterns
 *
 * @property int $id
 * @property string $language
 * @property string $iso_code
 * @property bool $is_default
 * @property bool $is_active
 * @property bool $is_featured
 * @property int|null $sort_order
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\User[] $candidates
 * @property-read string $display_name
 * @property-read string $native_name
 * @property-read int $candidates_count
 * @property-read bool $is_popular
 * @property-read bool $is_major
 * @property-read bool $is_european
 *
 * Enhanced Enhanced Scopes:
 * @method static \Illuminate\Database\Eloquent\Builder active()
 * @method static \Illuminate\Database\Eloquent\Builder inactive()
 * @method static \Illuminate\Database\Eloquent\Builder featured()
 * @method static \Illuminate\Database\Eloquent\Builder nonFeatured()
 * @method static \Illuminate\Database\Eloquent\Builder default()
 * @method static \Illuminate\Database\Eloquent\Builder custom()
 * @method static \Illuminate\Database\Eloquent\Builder search(string $term)
 * @method static \Illuminate\Database\Eloquent\Builder alphabetical()
 * @method static \Illuminate\Database\Eloquent\Builder withCandidates()
 * @method static \Illuminate\Database\Eloquent\Builder withoutCandidates()
 * @method static \Illuminate\Database\Eloquent\Builder recent(int $days = 30)
 * @method static \Illuminate\Database\Eloquent\Builder old(int $days = 365)
 * @method static \Illuminate\Database\Eloquent\Builder popular(int $limit = 10)
 * @method static \Illuminate\Database\Eloquent\Builder byIsoCode(string $code)
 * @method static \Illuminate\Database\Eloquent\Builder european()
 * @method static \Illuminate\Database\Eloquent\Builder major()
 * @method static \Illuminate\Database\Eloquent\Builder regional(string $region)
 * @method static \Illuminate\Database\Eloquent\Builder withCounts()
 * @method static \Illuminate\Database\Eloquent\Builder ordered()
 *
 * @mixin \Eloquent
 */
class Language extends Model
{
    public $table = 'languages';

    protected $fillable = [
        'language',
        'iso_code',
        'is_default',
        'is_active',
        'is_featured',
        'sort_order',
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
            'is_featured' => 'boolean',
            'sort_order' => 'integer',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /**
     * Validation rules
     *
     * @var array<string, string>
     */
    public static $rules = [
        'language' => 'required|string|max:150|unique:languages,language',
        'iso_code' => 'required|string|max:10|unique:languages,iso_code|regex:/^[a-z]{2}(-[A-Z]{2})?$/',
        'is_default' => 'boolean',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'sort_order' => 'nullable|integer|min:0',
    ];

    /**
     * Get all candidates that use this language
     */
    public function candidates(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'candidate_language');
    }

    /**
     * Scope for active languages.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for inactive languages.
     */
    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    /**
     * Scope for default languages.
     */
    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    /**
     * Scope for custom languages.
     */
    public function scopeCustom($query)
    {
        return $query->where('is_default', false);
    }

    /**
     * Scope for searching languages by name or ISO code.
     */
    public function scopeSearch($query, string $term)
    {
        return $query->where(function ($q) use ($term) {
            $q->where('language', 'like', '%' . $term . '%')
              ->orWhere('iso_code', 'like', '%' . $term . '%');
        });
    }

    /**
     * Scope for alphabetically ordered languages.
     */
    public function scopeAlphabetical($query)
    {
        return $query->orderBy('language', 'asc');
    }

    /**
     * Scope for languages with candidates.
     */
    public function scopeWithCandidates($query)
    {
        return $query->whereHas('candidates');
    }

    /**
     * Scope for languages without candidates.
     */
    public function scopeWithoutCandidates($query)
    {
        return $query->whereDoesntHave('candidates');
    }

    /**
     * Scope for recent languages.
     */
    public function scopeRecent($query, int $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Scope for old languages.
     */
    public function scopeOld($query, int $days = 365)
    {
        return $query->where('created_at', '<', now()->subDays($days));
    }

    /**
     * Scope for popular languages (most used by candidates).
     */
    public function scopePopular($query, int $limit = 10)
    {
        return $query->withCount('candidates')
                    ->orderByDesc('candidates_count')
                    ->limit($limit);
    }

    /**
     * Scope for featured languages.
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope for non-featured languages.
     */
    public function scopeNonFeatured($query)
    {
        return $query->where('is_featured', false);
    }

    /**
     * Scope for languages by ISO code.
     */
    public function scopeByIsoCode($query, string $code)
    {
        return $query->where('iso_code', $code);
    }

    /**
     * Scope for European languages.
     */
    public function scopeEuropean($query)
    {
        $europeanCodes = ['en', 'fr', 'de', 'es', 'it', 'pt', 'nl', 'pl', 'ru', 'sv', 'da', 'no', 'fi'];
        return $query->whereIn('iso_code', $europeanCodes);
    }

    /**
     * Scope for major world languages.
     */
    public function scopeMajor($query)
    {
        $majorCodes = ['en', 'zh', 'es', 'hi', 'ar', 'pt', 'ru', 'ja', 'de', 'fr'];
        return $query->whereIn('iso_code', $majorCodes);
    }

    /**
     * Scope for regional languages.
     */
    public function scopeRegional($query, string $region)
    {
        $regionalCodes = [
            'europe' => ['en', 'fr', 'de', 'es', 'it', 'pt', 'nl', 'pl', 'ru', 'sv', 'da', 'no', 'fi'],
            'asia' => ['zh', 'ja', 'ko', 'hi', 'th', 'vi', 'id', 'ms', 'tl'],
            'america' => ['en', 'es', 'pt', 'fr'],
            'africa' => ['ar', 'sw', 'am', 'ha', 'yo', 'ig'],
            'middle_east' => ['ar', 'fa', 'he', 'tr', 'ku'],
        ];

        $codes = $regionalCodes[strtolower($region)] ?? [];
        return $query->whereIn('iso_code', $codes);
    }

    /**
     * Scope for languages with counts loaded.
     */
    public function scopeWithCounts($query)
    {
        return $query->withCount(['candidates']);
    }

    /**
     * Scope for ordered languages (by sort_order, then alphabetical).
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order', 'asc')
                    ->orderBy('language', 'asc');
    }

    /**
     * Get the activity log options for the model.
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['language', 'iso_code', 'is_default', 'is_active', 'is_featured'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    /**
     * Update validation rules for languages.
     *
     * @param int $id
     * @return array<string, string>
     */
    public static function updateRules(int $id): array
    {
        return [
            'language' => 'required|string|max:150|unique:languages,language,' . $id,
            'iso_code' => 'required|string|max:10|unique:languages,iso_code,' . $id . '|regex:/^[a-z]{2}(-[A-Z]{2})?$/',
            'is_default' => 'boolean',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'sort_order' => 'nullable|integer|min:0',
        ];
    }

    /**
     * Get the display name for the language.
     */
    public function getDisplayNameAttribute(): string
    {
        $name = $this->language;
        if (isset($this->candidates_count)) {
            $name .= " ({$this->candidates_count})";
        }
        return $name;
    }

    /**
     * Get the native name for the language.
     */
    public function getNativeNameAttribute(): string
    {
        $nativeNames = [
            'en' => 'English',
            'es' => 'Español',
            'fr' => 'Français',
            'de' => 'Deutsch',
            'it' => 'Italiano',
            'pt' => 'Português',
            'ru' => 'Русский',
            'zh' => '中文',
            'ja' => '日本語',
            'ko' => '한국어',
            'ar' => 'العربية',
            'hi' => 'हिन्दी',
            'th' => 'ไทย',
            'vi' => 'Tiếng Việt',
            'tr' => 'Türkçe',
            'pl' => 'Polski',
            'nl' => 'Nederlands',
            'sv' => 'Svenska',
            'da' => 'Dansk',
            'no' => 'Norsk',
            'fi' => 'Suomi',
        ];

        return $nativeNames[$this->iso_code] ?? $this->language;
    }

    /**
     * Get candidates count attribute.
     */
    public function getCandidatesCountAttribute(): int
    {
        return $this->candidates()->count();
    }

    /**
     * Get active candidates count attribute.
     */
    public function getActiveCandidatesCountAttribute(): int
    {
        return $this->activeCandidates()->count();
    }

    /**
     * Check if language is popular.
     */
    public function getIsPopularAttribute(): bool
    {
        return $this->candidates()->count() >= 10;
    }

    /**
     * Check if language is major.
     */
    public function getIsMajorAttribute(): bool
    {
        return in_array($this->iso_code, [
            'en', 'zh', 'es', 'hi', 'ar', 'pt', 'ru', 'ja', 'de', 'fr'
        ]);
    }

    /**
     * Check if language is European.
     */
    public function getIsEuropeanAttribute(): bool
    {
        return in_array($this->iso_code, [
            'en', 'fr', 'de', 'es', 'it', 'pt', 'nl', 'pl', 'ru', 'sv', 'da', 'no', 'fi'
        ]);
    }

    /**
     * Check if language is active.
     */
    public function isActive(): bool
    {
        return $this->is_active;
    }

    /**
     * Check if language is featured.
     */
    public function isFeatured(): bool
    {
        return $this->is_featured;
    }

    /**
     * Check if language is default.
     */
    public function isDefault(): bool
    {
        return $this->is_default;
    }

    /**
     * Check if language is RTL.
     */
    public function isRtl(): bool
    {
        return in_array($this->iso_code, ['ar', 'he', 'fa', 'ur']);
    }

    /**
     * Check if language has candidates.
     */
    public function hasCandidates(): bool
    {
        return $this->candidates()->count() > 0;
    }

    /**
     * Check if language has active candidates.
     */
    public function hasActiveCandidates(): bool
    {
        return $this->activeCandidates()->count() > 0;
    }

    /**
     * Get the flag emoji for the language.
     */
    public function getFlagEmoji(): string
    {
        $flags = [
            'en' => '🇺🇸', 'es' => '🇪🇸', 'fr' => '🇫🇷', 'de' => '🇩🇪',
            'it' => '🇮🇹', 'pt' => '🇵🇹', 'ru' => '🇷🇺', 'zh' => '🇨🇳',
            'ja' => '🇯🇵', 'ko' => '🇰🇷', 'ar' => '🇸🇦', 'hi' => '🇮🇳',
            'th' => '🇹🇭', 'vi' => '🇻🇳', 'tr' => '🇹🇷', 'pl' => '🇵🇱',
            'nl' => '🇳🇱', 'sv' => '🇸🇪', 'da' => '🇩🇰', 'no' => '🇳🇴',
            'fi' => '🇫🇮',
        ];

        return $flags[$this->iso_code] ?? '🌐';
    }

    /**
     * Clear all related caches.
     */
    public function clearCaches(): void
    {
        $cacheKeys = [
            'languages.active',
            'languages.featured',
            'languages.major',
        ];

        // Clear popular cache variants
        for ($i = 5; $i <= 20; $i += 5) {
            $cacheKeys[] = "languages.popular.{$i}";
        }

        // Clear regional cache variants
        $regions = ['europe', 'asia', 'america', 'africa', 'middle_east'];
        foreach ($regions as $region) {
            $cacheKeys[] = "languages.region.{$region}";
        }

        foreach ($cacheKeys as $key) {
            Cache::forget($key);
        }
    }

    /**
     * Boot the model and register model events.
     */
    protected static function boot()
    {
        parent::boot();

        // Clear caches when model is modified
        static::saved(function ($model) {
            $model->clearCaches();
        });

        static::deleted(function ($model) {
            $model->clearCaches();
        });

    }
}
