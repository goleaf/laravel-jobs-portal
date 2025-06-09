<?php

namespace App\Models;

use Barryvdh\LaravelIdeHelper\Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Carbon;

/**
 * Class Language
 *
 * @version July 3, 2020, 9:12 am UTC
 *
 * @property int $id
 * @property string $language
 * @property string $iso_code
 * @property bool $is_default
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\User[] $candidates
 *
 * @method static Builder|Language newModelQuery()
 * @method static Builder|Language newQuery()
 * @method static Builder|Language query()
 * @method static Builder|Language whereCreatedAt($value)
 * @method static Builder|Language whereId($value)
 * @method static Builder|Language whereIsoCode($value)
 * @method static Builder|Language whereLanguage($value)
 * @method static Builder|Language whereIsDefault($value)
 * @method static Builder|Language whereUpdatedAt($value)
 *
 * @mixin Eloquent
 */
class Language extends Model
{
    use HasFactory;
    public $table = 'languages';

    protected $fillable = [
        'language',
        'iso_code',
        'is_default',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
        protected function casts(): array
    {
        return [
            'is_featured' => 'boolean',

            'id' => 'integer',
            'is_default' => 'boolean',
            'is_active' => 'boolean',
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
        'language' => 'required|unique:languages,language|max:150',
        'iso_code' => 'required|unique:languages,iso_code|max:150',
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
        return $query->where('language', 'like', "%{$term}%")
                    ->orWhere('iso_code', 'like', "%{$term}%");
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
        return $query->whereIn('iso_code', ['en', 'fr', 'de', 'es', 'it', 'pt', 'nl', 'pl', 'ru']);
    }

    /**
     * Scope for major world languages.
     */
    public function scopeMajor($query)
    {
        return $query->whereIn('iso_code', ['en', 'zh', 'es', 'hi', 'ar', 'pt', 'ru', 'ja', 'de', 'fr']);
    }
}
