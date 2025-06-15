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
 * App\Models\CandidateExperience.
 *
 * @property int          $id
 * @property int          $candidate_id
 * @property string       $experience_title
 * @property string       $company
 * @property null|int     $country_id
 * @property null|int     $state_id
 * @property null|int     $city_id
 * @property Carbon       $start_date
 * @property null|Carbon  $end_date
 * @property bool         $currently_working
 * @property null|string  $description
 * @property null|string  $job_level
 * @property null|string  $employment_type
 * @property null|float   $salary
 * @property bool         $is_verified
 * @property null|Carbon  $created_at
 * @property null|Carbon  $updated_at
 * @property Candidate    $candidate
 * @property null|Country $country
 * @property null|State   $state
 * @property null|City    $city
 * @property string       $full_location
 * @property string       $duration_description
 * @property int          $duration_in_months
 * @property string       $formatted_salary
 * @property bool         $is_current
 * @property bool         $is_recent
 * @property string       $experience_level
 *
 * @method static Builder|CandidateExperience newModelQuery()
 * @method static Builder|CandidateExperience newQuery()
 * @method static Builder|CandidateExperience query()
 * @method static Builder|CandidateExperience whereId($value)
 * @method static Builder|CandidateExperience whereCandidateId($value)
 * @method static Builder|CandidateExperience whereExperienceTitle($value)
 * @method static Builder|CandidateExperience whereCompany($value)
 * @method static Builder|CandidateExperience whereCountryId($value)
 * @method static Builder|CandidateExperience whereStateId($value)
 * @method static Builder|CandidateExperience whereCityId($value)
 * @method static Builder|CandidateExperience whereStartDate($value)
 * @method static Builder|CandidateExperience whereEndDate($value)
 * @method static Builder|CandidateExperience whereCurrentlyWorking($value)
 * @method static Builder|CandidateExperience whereDescription($value)
 * @method static Builder|CandidateExperience whereJobLevel($value)
 * @method static Builder|CandidateExperience whereEmploymentType($value)
 * @method static Builder|CandidateExperience whereSalary($value)
 * @method static Builder|CandidateExperience whereIsVerified($value)
 * @method static Builder|CandidateExperience whereCreatedAt($value)
 * @method static Builder|CandidateExperience whereUpdatedAt($value)
 * @method static Builder|CandidateExperience verified()
 * @method static Builder|CandidateExperience unverified()
 * @method static Builder|CandidateExperience current()
 * @method static Builder|CandidateExperience past()
 * @method static Builder|CandidateExperience recent(int $years = 3)
 * @method static Builder|CandidateExperience byCompany(string $company)
 * @method static Builder|CandidateExperience byTitle(string $title)
 * @method static Builder|CandidateExperience byCountry(int $countryId)
 * @method static Builder|CandidateExperience byState(int $stateId)
 * @method static Builder|CandidateExperience byCity(int $cityId)
 * @method static Builder|CandidateExperience byJobLevel(string $level)
 * @method static Builder|CandidateExperience byEmploymentType(string $type)
 * @method static Builder|CandidateExperience withSalaryRange(float $min, float $max)
 * @method static Builder|CandidateExperience byDuration(int $minMonths, ?int $maxMonths = null)
 * @method static Builder|CandidateExperience longTerm(int $minYears = 2)
 * @method static Builder|CandidateExperience shortTerm(int $maxMonths = 12)
 * @method static Builder|CandidateExperience senior()
 * @method static Builder|CandidateExperience management()
 * @method static Builder|CandidateExperience junior()
 * @method static Builder|CandidateExperience fullTime()
 * @method static Builder|CandidateExperience partTime()
 * @method static Builder|CandidateExperience contract()
 * @method static Builder|CandidateExperience internship()
 * @method static Builder|CandidateExperience alphabetical()
 * @method static Builder|CandidateExperience latestFirst()
 * @method static Builder|CandidateExperience oldestFirst()
 * @method static Builder|CandidateExperience search(string $term)
 *
 * @mixin Eloquent
 */
class CandidateExperience extends Model
{
    use HasFactory;
    use LogsActivity;
    use HasSettingsField;

    /**
     * Default settings for experience model.
     */
    public $defaultSettings = [
        'display' => [
            'show_salary' => false,
            'show_company_details' => true,
            'show_location' => true,
            'show_description' => true,
            'show_employment_type' => true,
            'show_job_level' => true,
            'show_duration' => true,
            'highlight_current_role' => true,
            'priority_order' => 0, // for sorting in profile
        ],
        'privacy' => [
            'public_visibility' => true,
            'recruiter_access' => true,
            'hide_from_competitors' => false,
            'hide_from_current_employer' => true,
            'anonymous_company' => false,
            'salary_visibility' => 'private', // all, recruiters_only, private
            'reference_contact_allowed' => false,
        ],
        'verification' => [
            'verification_required' => false,
            'employment_verification' => 'pending', // pending, verified, rejected
            'reference_check_status' => 'not_requested',
            'hr_verification' => false,
            'verification_documents' => [],
            'verified_by' => null,
            'verified_at' => null,
        ],
        'achievements' => [
            'key_accomplishments' => [],
            'awards_received' => [],
            'promotions' => [],
            'projects_led' => [],
            'team_size_managed' => 0,
            'budget_managed' => 0,
            'revenue_generated' => 0,
            'cost_savings' => 0,
        ],
        'skills' => [
            'technical_skills' => [],
            'soft_skills' => [],
            'tools_used' => [],
            'technologies' => [],
            'certifications_earned' => [],
            'training_completed' => [],
        ],
        'matching' => [
            'relevance_score' => 0, // calculated based on job market needs
            'industry_experience_weight' => 1.0,
            'role_level_progression' => 'stable', // ascending, stable, descending
            'leadership_experience' => false,
            'remote_work_experience' => false,
            'startup_experience' => false,
            'enterprise_experience' => false,
        ],
        'analytics' => [
            'profile_views_experience' => 0,
            'recruiter_interest_score' => 0,
            'company_reputation_score' => 0,
            'role_market_demand' => 0,
            'career_progression_score' => 0,
            'skill_relevance_score' => 0,
        ],
        'formatting' => [
            'date_format' => 'month_year', // month_year, year_only, full_date
            'description_length' => 300, // max characters in summary
            'company_display' => 'full_name', // full_name, abbreviation, anonymous
            'location_format' => 'city_country', // city_country, full, city_only
            'duration_format' => 'years_months', // years_months, months_only, custom
        ],
        'references' => [
            'reference_available' => false,
            'reference_contacts' => [],
            'reference_permission' => 'ask_first', // always_allow, ask_first, never
            'reference_notes' => '',
            'hr_contact_info' => [],
        ],
        'notifications' => [
            'verification_updates' => true,
            'reference_requests' => true,
            'industry_updates' => false,
            'company_news' => false,
            'role_opportunities' => true,
            'skill_endorsements' => true,
        ],
    ];

    /**
     * Settings validation rules.
     */
    public $settingsRules = [
        'display.show_salary' => 'boolean',
        'display.show_company_details' => 'boolean',
        'display.show_location' => 'boolean',
        'display.show_description' => 'boolean',
        'display.show_employment_type' => 'boolean',
        'display.show_job_level' => 'boolean',
        'display.show_duration' => 'boolean',
        'display.highlight_current_role' => 'boolean',
        'display.priority_order' => 'integer|min:0|max:100',
        
        'privacy.public_visibility' => 'boolean',
        'privacy.recruiter_access' => 'boolean',
        'privacy.hide_from_competitors' => 'boolean',
        'privacy.hide_from_current_employer' => 'boolean',
        'privacy.anonymous_company' => 'boolean',
        'privacy.salary_visibility' => 'string|in:all,recruiters_only,private',
        'privacy.reference_contact_allowed' => 'boolean',
        
        'verification.verification_required' => 'boolean',
        'verification.employment_verification' => 'string|in:pending,verified,rejected',
        'verification.reference_check_status' => 'string|in:not_requested,pending,completed,declined',
        'verification.hr_verification' => 'boolean',
        'verification.verification_documents' => 'array',
        
        'achievements.key_accomplishments' => 'array',
        'achievements.awards_received' => 'array',
        'achievements.promotions' => 'array',
        'achievements.projects_led' => 'array',
        'achievements.team_size_managed' => 'integer|min:0',
        'achievements.budget_managed' => 'numeric|min:0',
        'achievements.revenue_generated' => 'numeric|min:0',
        'achievements.cost_savings' => 'numeric|min:0',
        
        'skills.technical_skills' => 'array',
        'skills.soft_skills' => 'array',
        'skills.tools_used' => 'array',
        'skills.technologies' => 'array',
        'skills.certifications_earned' => 'array',
        'skills.training_completed' => 'array',
        
        'matching.relevance_score' => 'numeric|min:0|max:100',
        'matching.industry_experience_weight' => 'numeric|min:0|max:5',
        'matching.role_level_progression' => 'string|in:ascending,stable,descending',
        'matching.leadership_experience' => 'boolean',
        'matching.remote_work_experience' => 'boolean',
        'matching.startup_experience' => 'boolean',
        'matching.enterprise_experience' => 'boolean',
        
        'analytics.profile_views_experience' => 'integer|min:0',
        'analytics.recruiter_interest_score' => 'numeric|min:0|max:100',
        'analytics.company_reputation_score' => 'numeric|min:0|max:100',
        'analytics.role_market_demand' => 'numeric|min:0|max:100',
        'analytics.career_progression_score' => 'numeric|min:0|max:100',
        'analytics.skill_relevance_score' => 'numeric|min:0|max:100',
        
        'formatting.date_format' => 'string|in:month_year,year_only,full_date',
        'formatting.description_length' => 'integer|min:100|max:2000',
        'formatting.company_display' => 'string|in:full_name,abbreviation,anonymous',
        'formatting.location_format' => 'string|in:city_country,full,city_only',
        'formatting.duration_format' => 'string|in:years_months,months_only,custom',
        
        'references.reference_available' => 'boolean',
        'references.reference_contacts' => 'array',
        'references.reference_permission' => 'string|in:always_allow,ask_first,never',
        'references.reference_notes' => 'string|max:500',
        'references.hr_contact_info' => 'array',
        
        'notifications.verification_updates' => 'boolean',
        'notifications.reference_requests' => 'boolean',
        'notifications.industry_updates' => 'boolean',
        'notifications.company_news' => 'boolean',
        'notifications.role_opportunities' => 'boolean',
        'notifications.skill_endorsements' => 'boolean',
    ];

    /**
     * Validation rules with multilingual support.
     *
     * @var array
     */
    public static $rules = [
        'experience_title' => 'required|string|max:150',
        'company' => 'required|string|max:150',
        'country_id' => 'required|integer|exists:countries,id',
        'state_id' => 'nullable|integer|exists:states,id',
        'city_id' => 'nullable|integer|exists:cities,id',
        'start_date' => 'required|date|before_or_equal:today',
        'end_date' => 'nullable|date|after:start_date|before_or_equal:today',
        'currently_working' => 'boolean',
        'description' => 'nullable|string|max:1000',
        'job_level' => 'nullable|string|max:50',
        'employment_type' => 'nullable|string|max:50',
        'salary' => 'nullable|numeric|min:0',
    ];

    protected $table = 'candidate_experiences';

    protected $fillable = [
        'candidate_id',
        'experience_title',
        'company',
        'country_id',
        'state_id',
        'city_id',
        'start_date',
        'end_date',
        'currently_working',
        'description',
        'job_level',
        'employment_type',
        'salary',
        'is_verified',
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
            ->logOnly(['experience_title', 'company', 'start_date', 'end_date', 'currently_working', 'is_verified'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
        ;
    }

    // ==============================================
    // RELATIONSHIPS
    // ==============================================

    public function candidate(): BelongsTo
    {
        return $this->belongsTo(Candidate::class, 'candidate_id');
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
        $start = $this->start_date;
        $end = $this->end_date ?? now();

        $months = $start->diffInMonths($end);
        $years = floor($months / 12);
        $remainingMonths = $months % 12;

        if (0 === $years) {
            return 1 === $months ? __('experience.one_month') : __('experience.months', ['count' => $months]);
        }
        if (0 === $remainingMonths) {
            return 1 === $years ? __('experience.one_year') : __('experience.years', ['count' => $years]);
        }

        return __('experience.years_months', ['years' => $years, 'months' => $remainingMonths]);
    }

    public function getDurationInMonthsAttribute(): int
    {
        $start = $this->start_date;
        $end = $this->end_date ?? now();

        return $start->diffInMonths($end);
    }

    public function getFormattedSalaryAttribute(): string
    {
        if (!$this->salary) {
            return __('common.not_specified');
        }

        return number_format($this->salary, 2);
    }

    public function getIsCurrentAttribute(): bool
    {
        return $this->currently_working;
    }

    public function getIsRecentAttribute(): bool
    {
        return $this->start_date >= now()->subYears(3);
    }

    public function getExperienceLevelAttribute(): string
    {
        $months = $this->duration_in_months;

        return match (true) {
            $months < 6 => __('experience.entry_level'),
            $months < 24 => __('experience.junior_level'),
            $months < 60 => __('experience.mid_level'),
            $months < 120 => __('experience.senior_level'),
            default => __('experience.executive_level')
        };
    }

    // ==============================================
    // QUERY SCOPES
    // ==============================================

    /**
     * Scope for verified experience records.
     */
    public function scopeVerified(Builder $query): Builder
    {
        return $query->where('is_verified', true);
    }

    /**
     * Scope for unverified experience records.
     */
    public function scopeUnverified(Builder $query): Builder
    {
        return $query->where('is_verified', false);
    }

    /**
     * Scope for current positions.
     */
    public function scopeCurrent(Builder $query): Builder
    {
        return $query->where('currently_working', true);
    }

    /**
     * Scope for past positions.
     */
    public function scopePast(Builder $query): Builder
    {
        return $query->where('currently_working', false);
    }

    /**
     * Scope for recent experience.
     */
    public function scopeRecent(Builder $query, int $years = 3): Builder
    {
        return $query->where('start_date', '>=', now()->subYears($years));
    }

    /**
     * Scope by company.
     */
    public function scopeByCompany(Builder $query, string $company): Builder
    {
        return $query->where('company', 'LIKE', "%{$company}%");
    }

    /**
     * Scope by job title.
     */
    public function scopeByTitle(Builder $query, string $title): Builder
    {
        return $query->where('experience_title', 'LIKE', "%{$title}%");
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
     * Scope by job level.
     */
    public function scopeByJobLevel(Builder $query, string $level): Builder
    {
        return $query->where('job_level', 'LIKE', "%{$level}%");
    }

    /**
     * Scope by employment type.
     */
    public function scopeByEmploymentType(Builder $query, string $type): Builder
    {
        return $query->where('employment_type', 'LIKE', "%{$type}%");
    }

    /**
     * Scope by salary range.
     */
    public function scopeWithSalaryRange(Builder $query, float $min, float $max): Builder
    {
        return $query->whereBetween('salary', [$min, $max]);
    }

    /**
     * Scope by duration in months.
     */
    public function scopeByDuration(Builder $query, int $minMonths, ?int $maxMonths = null): Builder
    {
        return $query->whereRaw('DATEDIFF(COALESCE(end_date, NOW()), start_date) / 30 >= ?', [$minMonths])
            ->when($maxMonths, function ($q) use ($maxMonths) {
                return $q->whereRaw('DATEDIFF(COALESCE(end_date, NOW()), start_date) / 30 <= ?', [$maxMonths]);
            })
        ;
    }

    /**
     * Scope for long-term positions.
     */
    public function scopeLongTerm(Builder $query, int $minYears = 2): Builder
    {
        return $query->whereRaw('DATEDIFF(COALESCE(end_date, NOW()), start_date) >= ?', [$minYears * 365]);
    }

    /**
     * Scope for short-term positions.
     */
    public function scopeShortTerm(Builder $query, int $maxMonths = 12): Builder
    {
        return $query->whereRaw('DATEDIFF(COALESCE(end_date, NOW()), start_date) / 30 <= ?', [$maxMonths]);
    }

    /**
     * Scope for senior positions.
     */
    public function scopeSenior(Builder $query): Builder
    {
        return $query->where(function ($q) {
            $q->where('job_level', 'LIKE', '%senior%')
                ->orWhere('job_level', 'LIKE', '%lead%')
                ->orWhere('experience_title', 'LIKE', '%senior%')
                ->orWhere('experience_title', 'LIKE', '%lead%')
            ;
        });
    }

    /**
     * Scope for management positions.
     */
    public function scopeManagement(Builder $query): Builder
    {
        return $query->where(function ($q) {
            $q->where('job_level', 'LIKE', '%manager%')
                ->orWhere('job_level', 'LIKE', '%director%')
                ->orWhere('job_level', 'LIKE', '%head%')
                ->orWhere('experience_title', 'LIKE', '%manager%')
                ->orWhere('experience_title', 'LIKE', '%director%')
            ;
        });
    }

    /**
     * Scope for junior positions.
     */
    public function scopeJunior(Builder $query): Builder
    {
        return $query->where(function ($q) {
            $q->where('job_level', 'LIKE', '%junior%')
                ->orWhere('job_level', 'LIKE', '%entry%')
                ->orWhere('experience_title', 'LIKE', '%junior%')
                ->orWhere('experience_title', 'LIKE', '%entry%')
            ;
        });
    }

    /**
     * Scope for full-time positions.
     */
    public function scopeFullTime(Builder $query): Builder
    {
        return $query->where('employment_type', 'LIKE', '%full%');
    }

    /**
     * Scope for part-time positions.
     */
    public function scopePartTime(Builder $query): Builder
    {
        return $query->where('employment_type', 'LIKE', '%part%');
    }

    /**
     * Scope for contract positions.
     */
    public function scopeContract(Builder $query): Builder
    {
        return $query->where('employment_type', 'LIKE', '%contract%');
    }

    /**
     * Scope for internship positions.
     */
    public function scopeInternship(Builder $query): Builder
    {
        return $query->where('employment_type', 'LIKE', '%intern%');
    }

    /**
     * Scope for alphabetical ordering.
     */
    public function scopeAlphabetical(Builder $query): Builder
    {
        return $query->orderBy('experience_title');
    }

    /**
     * Scope for latest first ordering.
     */
    public function scopeLatestFirst(Builder $query): Builder
    {
        return $query->orderBy('start_date', 'desc');
    }

    /**
     * Scope for oldest first ordering.
     */
    public function scopeOldestFirst(Builder $query): Builder
    {
        return $query->orderBy('start_date', 'asc');
    }

    /**
     * Scope for searching experience records.
     */
    public function scopeSearch(Builder $query, string $term): Builder
    {
        return $query->where(function ($q) use ($term) {
            $q->where('experience_title', 'LIKE', "%{$term}%")
                ->orWhere('company', 'LIKE', "%{$term}%")
                ->orWhere('job_level', 'LIKE', "%{$term}%")
                ->orWhere('employment_type', 'LIKE', "%{$term}%")
                ->orWhere('description', 'LIKE', "%{$term}%")
            ;
        });
    }

    // ==============================================
    // UTILITY METHODS
    // ==============================================

    /**
     * Check if this is a high-level position.
     */
    public function isHighLevel(): bool
    {
        $title = strtolower($this->experience_title);
        $level = strtolower($this->job_level ?? '');

        $highLevelKeywords = ['manager', 'director', 'head', 'lead', 'senior', 'principal', 'chief'];

        foreach ($highLevelKeywords as $keyword) {
            if (str_contains($title, $keyword) || str_contains($level, $keyword)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if this is a recent experience.
     */
    public function isRecentExperience(int $years = 5): bool
    {
        return $this->start_date >= now()->subYears($years);
    }

    /**
     * Get total experience duration.
     */
    public function getTotalDuration(): int
    {
        return $this->duration_in_months;
    }

    /**
     * Get formatted experience summary.
     */
    public function getExperienceSummary(): string
    {
        $parts = [];

        if ($this->experience_title) {
            $parts[] = $this->experience_title;
        }

        if ($this->company) {
            $parts[] = 'at '.$this->company;
        }

        if ($this->duration_description) {
            $parts[] = '('.$this->duration_description.')';
        }

        return implode(' ', $parts);
    }

    /**
     * Check if overlaps with another experience.
     */
    public function overlapsWith(CandidateExperience $other): bool
    {
        $thisStart = $this->start_date;
        $thisEnd = $this->end_date ?? now();
        $otherStart = $other->start_date;
        $otherEnd = $other->end_date ?? now();

        return $thisStart <= $otherEnd && $otherStart <= $thisEnd;
    }

    /**
     * Scope a query to only include active records.
     *
     * @param Builder $query
     *
     * @return Builder
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include inactive records.
     *
     * @param Builder $query
     *
     * @return Builder
     */
    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'currently_working' => 'boolean',
            'is_verified' => 'boolean',
            'salary' => 'float',
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

        // Auto-set end_date to null if currently working
        static::saving(function ($experience) {
            if ($experience->currently_working) {
                $experience->end_date = null;
            }
        });

        // Clear related caches when experience is updated
        static::saved(function ($experience) {
            cache()->forget("candidate.{$experience->candidate_id}.profile_completion");
            cache()->tags(['candidate-experience', 'candidate-'.$experience->candidate_id])->flush();
        });

        static::deleted(function ($experience) {
            cache()->forget("candidate.{$experience->candidate_id}.profile_completion");
            cache()->tags(['candidate-experience', 'candidate-'.$experience->candidate_id])->flush();
        });
    }
}
