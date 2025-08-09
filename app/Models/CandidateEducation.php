<?php

namespace App\Models;

use Eloquent;
use Glorand\Model\Settings\Traits\HasSettingsField;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * App\Models\CandidateEducation.
 *
 * @property int $id
 * @property int $candidate_id
 * @property int $degree_level_id
 * @property string $degree_title
 * @property null|int $country_id
 * @property null|int $state_id
 * @property null|int $city_id
 * @property string $institute
 * @property string $result
 * @property int $year
 * @property null|float $grade_percentage
 * @property null|string $field_of_study
 * @property null|string $description
 * @property bool $is_verified
 * @property null|Carbon $created_at
 * @property null|Carbon $updated_at
 * @property Candidate $candidate
 * @property RequiredDegreeLevel $degreeLevel
 * @property null|Country $country
 * @property null|State $state
 * @property null|City $city
 * @property string $full_location
 * @property string $duration_description
 * @property string $grade_display
 * @property bool $is_recent
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
    use HasFactory;
    use HasSettingsField;
    use LogsActivity;

    /**
     * Validation rules with multilingual support.
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

    protected $table = 'candidate_educations';

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

    /**
     * Default settings for education model.
     */
    public $defaultSettings = [
        'display' => [
            'show_grade' => true,
            'show_field_of_study' => true,
            'show_description' => true,
            'show_institute_details' => true,
            'show_location' => true,
            'show_year' => true,
            'highlight_honors' => true,
            'priority_order' => 0, // for sorting in profile
        ],
        'privacy' => [
            'public_visibility' => true,
            'recruiter_access' => true,
            'hide_from_alumni' => false,
            'hide_from_current_institute' => false,
            'anonymous_institute' => false,
            'grade_visibility' => 'all', // all, recruiters_only, private
        ],
        'verification' => [
            'verification_required' => false,
            'auto_verify_threshold' => 80.0, // GPA/grade threshold for auto-verification
            'verification_documents' => [], // array of document types
            'verification_status' => 'pending', // pending, verified, rejected
            'verified_by' => null,
            'verified_at' => null,
        ],
        'achievements' => [
            'honors_and_awards' => [],
            'scholarships' => [],
            'academic_projects' => [],
            'publications' => [],
            'relevant_coursework' => [],
            'extracurricular_activities' => [],
        ],
        'matching' => [
            'skill_relevance_score' => 0, // calculated based on field of study
            'job_market_relevance' => 'high', // high, medium, low
            'industry_alignment' => [], // array of relevant industries
            'weight_in_profile' => 1.0, // multiplier for profile scoring
            'boost_recent_graduates' => true,
        ],
        'analytics' => [
            'profile_views_education' => 0,
            'recruiter_interest_score' => 0,
            'education_ranking' => null, // institute ranking if available
            'field_demand_score' => 0, // market demand for this field
            'completion_impact' => 0, // impact on profile completion %
        ],
        'formatting' => [
            'date_format' => 'year_only', // year_only, month_year, full_date
            'grade_format' => 'percentage', // percentage, gpa, letter, pass_fail
            'institute_display' => 'full_name', // full_name, abbreviation, custom
            'location_format' => 'city_country', // city_country, full, city_only
            'description_length' => 200, // max characters in summary
        ],
        'notifications' => [
            'verification_updates' => true,
            'institute_news' => false,
            'alumni_connections' => true,
            'career_opportunities' => true,
            'field_insights' => false,
        ],
    ];

    /**
     * Settings validation rules.
     */
    public $settingsRules = [
        'display.show_grade' => 'boolean',
        'display.show_field_of_study' => 'boolean',
        'display.show_description' => 'boolean',
        'display.show_institute_details' => 'boolean',
        'display.show_location' => 'boolean',
        'display.show_year' => 'boolean',
        'display.highlight_honors' => 'boolean',
        'display.priority_order' => 'integer|min:0|max:100',

        'privacy.public_visibility' => 'boolean',
        'privacy.recruiter_access' => 'boolean',
        'privacy.hide_from_alumni' => 'boolean',
        'privacy.hide_from_current_institute' => 'boolean',
        'privacy.anonymous_institute' => 'boolean',
        'privacy.grade_visibility' => 'string|in:all,recruiters_only,private',

        'verification.verification_required' => 'boolean',
        'verification.auto_verify_threshold' => 'numeric|min:0|max:100',
        'verification.verification_documents' => 'array',
        'verification.verification_status' => 'string|in:pending,verified,rejected',

        'achievements.honors_and_awards' => 'array',
        'achievements.scholarships' => 'array',
        'achievements.academic_projects' => 'array',
        'achievements.publications' => 'array',
        'achievements.relevant_coursework' => 'array',
        'achievements.extracurricular_activities' => 'array',

        'matching.skill_relevance_score' => 'numeric|min:0|max:100',
        'matching.job_market_relevance' => 'string|in:high,medium,low',
        'matching.industry_alignment' => 'array',
        'matching.weight_in_profile' => 'numeric|min:0|max:5',
        'matching.boost_recent_graduates' => 'boolean',

        'analytics.profile_views_education' => 'integer|min:0',
        'analytics.recruiter_interest_score' => 'numeric|min:0|max:100',
        'analytics.education_ranking' => 'nullable|integer|min:1',
        'analytics.field_demand_score' => 'numeric|min:0|max:100',
        'analytics.completion_impact' => 'numeric|min:0|max:100',

        'formatting.date_format' => 'string|in:year_only,month_year,full_date',
        'formatting.grade_format' => 'string|in:percentage,gpa,letter,pass_fail',
        'formatting.institute_display' => 'string|in:full_name,abbreviation,custom',
        'formatting.location_format' => 'string|in:city_country,full,city_only',
        'formatting.description_length' => 'integer|min:50|max:1000',

        'notifications.verification_updates' => 'boolean',
        'notifications.institute_news' => 'boolean',
        'notifications.alumni_connections' => 'boolean',
        'notifications.career_opportunities' => 'boolean',
        'notifications.field_insights' => 'boolean',
    ];

    /**
     * Scope a query to only include old records.
     */
    public function scopeOld(Builder $query): Builder
    {
        return $query->orderBy('created_at', 'asc');
    }

    /**
     * Activity log options.
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

        if ($this->city?->name) {
            $location[] = $this->city->name;
        }
        if ($this->state?->name) {
            $location[] = $this->state->name;
        }
        if ($this->country?->name) {
            $location[] = $this->country->name;
        }

        return implode(', ', $location) ?: __('common.location_not_specified');
    }

    public function getDurationDescriptionAttribute(): string
    {
        $currentYear = date('Y');
        $yearsAgo = $currentYear - $this->year;

        if ($yearsAgo === 0) {
            return __('education.current_year');
        }
        if ($yearsAgo === 1) {
            return __('education.one_year_ago');
        }

        return __('education.years_ago', ['years' => $yearsAgo]);
    }

    public function getGradeDisplayAttribute(): string
    {
        if ($this->grade_percentage) {
            return $this->grade_percentage.'%';
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
            $parts[] = 'in '.$this->field_of_study;
        }

        if ($this->institute) {
            $parts[] = 'from '.$this->institute;
        }

        if ($this->year) {
            $parts[] = '('.$this->year.')';
        }

        return implode(' ', $parts);
    }

    /**
     * Scope a query to only include active records.
     *
     * @param  Builder  $query
     * @return Builder
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include inactive records.
     *
     * @param  Builder  $query
     * @return Builder
     */
    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

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
     * Boot the model.
     */
    protected static function boot(): void
    {
        parent::boot();

        // Clear related caches when education is updated
        static::saved(function ($education) {
            cache()->forget("candidate.{$education->candidate_id}.profile_completion");
            try {
                cache()->tags(['candidate-education', 'candidate-'.$education->candidate_id])->flush();
            } catch (\Exception $e) {}
        });

        static::deleted(function ($education) {
            cache()->forget("candidate.{$education->candidate_id}.profile_completion");
            try {
                cache()->tags(['candidate-education', 'candidate-'.$education->candidate_id])->flush();
            } catch (\Exception $e) {}
        });
    }
}
