<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

/**
 * App\Models\CandidateEducation
 *
 * @property int $id
 * @property int $candidate_id
 * @property int $degree_level_id
 * @property string $degree_title
 * @property int|null $country_id
 * @property int|null $state_id
 * @property int|null $city_id
 * @property string $institute
 * @property string $result
 * @property int $year
 * @property float|null $grade_percentage
 * @property string|null $field_of_study
 * @property string|null $description
 * @property bool $is_verified
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Candidate $candidate
 * @property-read RequiredDegreeLevel $degreeLevel
 * @property-read Country|null $country
 * @property-read State|null $state
 * @property-read City|null $city
 * @property-read string $full_location
 * @property-read string $duration_description
 * @property-read string $grade_display
 * @property-read bool $is_recent
 *
 * @method static Builder|CandidateEducation newModelQuery()
 * @method static Builder|CandidateEducation newQuery()
 * @method static Builder|CandidateEducation query()
 * @method static Builder|CandidateEducation whereId($value)
 * @method static Builder|CandidateEducation whereCandidateId($value)
 * @method static Builder|CandidateEducation whereDegreeLevelId($value)
 * @method static Builder|CandidateEducation whereDegreeTitle($value)
 * @method static Builder|CandidateEducation whereCountryId($value)
 * @method static Builder|CandidateEducation whereStateId($value)
 * @method static Builder|CandidateEducation whereCityId($value)
 * @method static Builder|CandidateEducation whereInstitute($value)
 * @method static Builder|CandidateEducation whereResult($value)
 * @method static Builder|CandidateEducation whereYear($value)
 * @method static Builder|CandidateEducation whereGradePercentage($value)
 * @method static Builder|CandidateEducation whereFieldOfStudy($value)
 * @method static Builder|CandidateEducation whereDescription($value)
 * @method static Builder|CandidateEducation whereIsVerified($value)
 * @method static Builder|CandidateEducation whereCreatedAt($value)
 * @method static Builder|CandidateEducation whereUpdatedAt($value)
 * @method static Builder|CandidateEducation verified()
 * @method static Builder|CandidateEducation unverified()
 * @method static Builder|CandidateEducation recent(int $years = 5)
 * @method static Builder|CandidateEducation byYear(int $year)
 * @method static Builder|CandidateEducation byYearRange(int $startYear, int $endYear)
 * @method static Builder|CandidateEducation byDegreeLevel(int $degreeLevelId)
 * @method static Builder|CandidateEducation byCountry(int $countryId)
 * @method static Builder|CandidateEducation byState(int $stateId)
 * @method static Builder|CandidateEducation byCity(int $cityId)
 * @method static Builder|CandidateEducation byInstitute(string $institute)
 * @method static Builder|CandidateEducation byFieldOfStudy(string $field)
 * @method static Builder|CandidateEducation withHighGrades(float $minPercentage = 70.0)
 * @method static Builder|CandidateEducation withGradeRange(float $min, float $max)
 * @method static Builder|CandidateEducation graduate()
 * @method static Builder|CandidateEducation postGraduate()
 * @method static Builder|CandidateEducation doctorate()
 * @method static Builder|CandidateEducation undergraduate()
 * @method static Builder|CandidateEducation engineering()
 * @method static Builder|CandidateEducation medical()
 * @method static Builder|CandidateEducation business()
 * @method static Builder|CandidateEducation technology()
 * @method static Builder|CandidateEducation science()
 * @method static Builder|CandidateEducation alphabetical()
 * @method static Builder|CandidateEducation latestFirst()
 * @method static Builder|CandidateEducation oldestFirst()
 * @method static Builder|CandidateEducation search(string $term)
 *
 * @mixin Eloquent
 */
class CandidateEducation extends Model
{
    use HasFactory, LogsActivity;

    protected $table = 'candidate_education';

    protected $fillable = [
        'candidate_id',
        'degree_level_id',
        'degree_title',
        'country_id',
        'state_id',
        'city_id',
        'institute',
        'result',
        'year',
        'grade_percentage',
        'field_of_study',
        'description',
        'is_verified',
    ];
 
    protected function casts(): array
    {
        return [
            'is_verified' => 'boolean',
            'grade_percentage' => 'float',
            'year' => 'integer',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

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

    /**
     * Validation rules with multilingual support
     *
     * @var array
     */
    public static $rules = [
        'degree_title' => 'required|string|max:150',
        'country_id' => 'required|integer|exists:countries,id',
        'state_id' => 'nullable|integer|exists:states,id',
        'city_id' => 'nullable|integer|exists:cities,id',
        'institute' => 'required|string|max:150',
        'result' => 'required|string|max:150',
        'year' => 'required|integer|min:1950|max:2035',
        'degree_level_id' => 'required|integer|exists:required_degree_levels,id',
        'grade_percentage' => 'nullable|numeric|min:0|max:100',
        'field_of_study' => 'nullable|string|max:100',
        'description' => 'nullable|string|max:500',
    ];

    /**
     * Boot the model.
     */
    protected static function boot(): void
    {
        parent::boot();

        // Clear related caches when education is updated
        static::saved(function ($education) {
            cache()->forget("candidate.{$education->candidate_id}.profile_completion");
            cache()->tags(['candidate-education', 'candidate-' . $education->candidate_id])->flush();
        });

        static::deleted(function ($education) {
            cache()->forget("candidate.{$education->candidate_id}.profile_completion");
            cache()->tags(['candidate-education', 'candidate-' . $education->candidate_id])->flush();
        });
    }

    /**
     * Activity log options
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['degree_level_id', 'institute', 'result', 'year', 'is_verified'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    // ==============================================
    // RELATIONSHIPS
    // ==============================================

    public function candidate(): BelongsTo
    {
        return $this->belongsTo(Candidate::class, 'candidate_id');
    }

    public function degreeLevel(): BelongsTo
    {
        return $this->belongsTo(RequiredDegreeLevel::class, 'degree_level_id');
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class, 'country_id');
    }

    public function state(): BelongsTo
    {
        return $this->belongsTo(State::class, 'state_id');
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class, 'city_id');
    }

    // ==============================================
    // ACCESSORS
    // ==============================================

    public function getFullLocationAttribute(): string
    {
        $location = [];
        
        if ($this->city?->name) $location[] = $this->city->name;
        if ($this->state?->name) $location[] = $this->state->name;
        if ($this->country?->name) $location[] = $this->country->name;
        
        return implode(', ', $location) ?: __('common.location_not_specified');
    }

    public function getDurationDescriptionAttribute(): string
    {
        $currentYear = date('Y');
        $yearsAgo = $currentYear - $this->year;
        
        if ($yearsAgo === 0) {
            return __('education.current_year');
        } elseif ($yearsAgo === 1) {
            return __('education.one_year_ago');
        } else {
            return __('education.years_ago', ['years' => $yearsAgo]);
        }
    }

    public function getGradeDisplayAttribute(): string
    {
        if ($this->grade_percentage) {
            return $this->grade_percentage . '%';
        }
        return $this->result ?: __('education.grade_not_specified');
    }

    public function getIsRecentAttribute(): bool
    {
        return $this->year >= (date('Y') - 5);
    }

    // ==============================================
    // QUERY SCOPES
    // ==============================================

    /**
     * Scope for verified education records.
     */
    public function scopeVerified(Builder $query): Builder
    {
        return $query->where('is_verified', true);
    }

    /**
     * Scope for unverified education records.
     */
    public function scopeUnverified(Builder $query): Builder
    {
        return $query->where('is_verified', false);
    }

    /**
     * Scope for recent education records.
     */
    public function scopeRecent(Builder $query, int $years = 5): Builder
    {
        return $query->where('year', '>=', date('Y') - $years);
    }

    /**
     * Scope by specific year.
     */
    public function scopeByYear(Builder $query, int $year): Builder
    {
        return $query->where('year', $year);
    }

    /**
     * Scope by year range.
     */
    public function scopeByYearRange(Builder $query, int $startYear, int $endYear): Builder
    {
        return $query->whereBetween('year', [$startYear, $endYear]);
    }

    /**
     * Scope by degree level.
     */
    public function scopeByDegreeLevel(Builder $query, int $degreeLevelId): Builder
    {
        return $query->where('degree_level_id', $degreeLevelId);
    }

    /**
     * Scope by country.
     */
    public function scopeByCountry(Builder $query, int $countryId): Builder
    {
        return $query->where('country_id', $countryId);
    }

    /**
     * Scope by state.
     */
    public function scopeByState(Builder $query, int $stateId): Builder
    {
        return $query->where('state_id', $stateId);
    }

    /**
     * Scope by city.
     */
    public function scopeByCity(Builder $query, int $cityId): Builder
    {
        return $query->where('city_id', $cityId);
    }

    /**
     * Scope by institute.
     */
    public function scopeByInstitute(Builder $query, string $institute): Builder
    {
        return $query->where('institute', 'LIKE', "%{$institute}%");
    }

    /**
     * Scope by field of study.
     */
    public function scopeByFieldOfStudy(Builder $query, string $field): Builder
    {
        return $query->where('field_of_study', 'LIKE', "%{$field}%");
    }

    /**
     * Scope for high grades.
     */
    public function scopeWithHighGrades(Builder $query, float $minPercentage = 70.0): Builder
    {
        return $query->where('grade_percentage', '>=', $minPercentage);
    }

    /**
     * Scope by grade range.
     */
    public function scopeWithGradeRange(Builder $query, float $min, float $max): Builder
    {
        return $query->whereBetween('grade_percentage', [$min, $max]);
    }

    /**
     * Scope for graduate degrees.
     */
    public function scopeGraduate(Builder $query): Builder
    {
        return $query->whereHas('degreeLevel', function ($q) {
            $q->where('name', 'LIKE', '%bachelor%')
              ->orWhere('name', 'LIKE', '%graduate%')
              ->orWhere('name', 'LIKE', '%degree%');
        });
    }

    /**
     * Scope for post-graduate degrees.
     */
    public function scopePostGraduate(Builder $query): Builder
    {
        return $query->whereHas('degreeLevel', function ($q) {
            $q->where('name', 'LIKE', '%master%')
              ->orWhere('name', 'LIKE', '%post%')
              ->orWhere('name', 'LIKE', '%mba%');
        });
    }

    /**
     * Scope for doctorate degrees.
     */
    public function scopeDoctorate(Builder $query): Builder
    {
        return $query->whereHas('degreeLevel', function ($q) {
            $q->where('name', 'LIKE', '%phd%')
              ->orWhere('name', 'LIKE', '%doctor%')
              ->orWhere('name', 'LIKE', '%doctorate%');
        });
    }

    /**
     * Scope for undergraduate degrees.
     */
    public function scopeUndergraduate(Builder $query): Builder
    {
        return $query->whereHas('degreeLevel', function ($q) {
            $q->where('name', 'LIKE', '%diploma%')
              ->orWhere('name', 'LIKE', '%certificate%')
              ->orWhere('name', 'LIKE', '%associate%');
        });
    }

    /**
     * Scope for engineering fields.
     */
    public function scopeEngineering(Builder $query): Builder
    {
        return $query->where(function ($q) {
            $q->where('field_of_study', 'LIKE', '%engineering%')
              ->orWhere('field_of_study', 'LIKE', '%technical%')
              ->orWhere('degree_title', 'LIKE', '%engineering%');
        });
    }

    /**
     * Scope for medical fields.
     */
    public function scopeMedical(Builder $query): Builder
    {
        return $query->where(function ($q) {
            $q->where('field_of_study', 'LIKE', '%medical%')
              ->orWhere('field_of_study', 'LIKE', '%medicine%')
              ->orWhere('field_of_study', 'LIKE', '%health%')
              ->orWhere('degree_title', 'LIKE', '%medical%');
        });
    }

    /**
     * Scope for business fields.
     */
    public function scopeBusiness(Builder $query): Builder
    {
        return $query->where(function ($q) {
            $q->where('field_of_study', 'LIKE', '%business%')
              ->orWhere('field_of_study', 'LIKE', '%management%')
              ->orWhere('field_of_study', 'LIKE', '%mba%')
              ->orWhere('degree_title', 'LIKE', '%business%');
        });
    }

    /**
     * Scope for technology fields.
     */
    public function scopeTechnology(Builder $query): Builder
    {
        return $query->where(function ($q) {
            $q->where('field_of_study', 'LIKE', '%computer%')
              ->orWhere('field_of_study', 'LIKE', '%technology%')
              ->orWhere('field_of_study', 'LIKE', '%software%')
              ->orWhere('field_of_study', 'LIKE', '%it%')
              ->orWhere('degree_title', 'LIKE', '%computer%');
        });
    }

    /**
     * Scope for science fields.
     */
    public function scopeScience(Builder $query): Builder
    {
        return $query->where(function ($q) {
            $q->where('field_of_study', 'LIKE', '%science%')
              ->orWhere('field_of_study', 'LIKE', '%physics%')
              ->orWhere('field_of_study', 'LIKE', '%chemistry%')
              ->orWhere('field_of_study', 'LIKE', '%biology%')
              ->orWhere('degree_title', 'LIKE', '%science%');
        });
    }

    /**
     * Scope for alphabetical ordering.
     */
    public function scopeAlphabetical(Builder $query): Builder
    {
        return $query->orderBy('degree_title');
    }

    /**
     * Scope for latest first ordering.
     */
    public function scopeLatestFirst(Builder $query): Builder
    {
        return $query->orderBy('year', 'desc');
    }

    /**
     * Scope for oldest first ordering.
     */
    public function scopeOldestFirst(Builder $query): Builder
    {
        return $query->orderBy('year', 'asc');
    }

    /**
     * Scope for searching education records.
     */
    public function scopeSearch(Builder $query, string $term): Builder
    {
        return $query->where(function ($q) use ($term) {
            $q->where('degree_title', 'LIKE', "%{$term}%")
              ->orWhere('institute', 'LIKE', "%{$term}%")
              ->orWhere('field_of_study', 'LIKE', "%{$term}%")
              ->orWhere('result', 'LIKE', "%{$term}%")
              ->orWhereHas('degreeLevel', function ($subQuery) use ($term) {
                  $subQuery->where('name', 'LIKE', "%{$term}%");
              });
        });
    }

    // ==============================================
    // UTILITY METHODS
    // ==============================================

    /**
     * Check if this education is high-achieving.
     */
    public function isHighAchieving(): bool
    {
        return $this->grade_percentage && $this->grade_percentage >= 85.0;
    }

    /**
     * Check if this education is from a recent year.
     */
    public function isFromRecentYear(int $years = 10): bool
    {
        return $this->year >= (date('Y') - $years);
    }

    /**
     * Get the academic level category.
     */
    public function getAcademicLevel(): string
    {
        $degreeName = strtolower($this->degreeLevel?->name ?? '');
        
        if (str_contains($degreeName, 'phd') || str_contains($degreeName, 'doctorate')) {
            return 'doctorate';
        }
        if (str_contains($degreeName, 'master') || str_contains($degreeName, 'mba')) {
            return 'masters';
        }
        if (str_contains($degreeName, 'bachelor') || str_contains($degreeName, 'degree')) {
            return 'bachelors';
        }
        
        return 'other';
    }

    /**
     * Get formatted education summary.
     */
    public function getEducationSummary(): string
    {
        $parts = [];
        
        if ($this->degree_title) {
            $parts[] = $this->degree_title;
        }
        
        if ($this->field_of_study) {
            $parts[] = 'in ' . $this->field_of_study;
        }
        
        if ($this->institute) {
            $parts[] = 'from ' . $this->institute;
        }
        
        if ($this->year) {
            $parts[] = '(' . $this->year . ')';
        }
        
        return implode(' ', $parts);
    }

    /**
     * Scope a query to only include active records.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include inactive records.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }
}
