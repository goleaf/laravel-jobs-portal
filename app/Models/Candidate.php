<?php

namespace App\Models;

use App\Services\FileService;
use App\Traits\HasTaxonomy;
use Glorand\Model\Settings\Traits\HasSettingsField;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * Class Candidate.
 *
 * @version July 20, 2020, 5:48 am UTC
 *
 * @property int $id
 * @property int $user_id
 * @property string $unique_id
 * @property null|string $father_name
 * @property null|int $marital_status_id
 * @property null|string $nationality
 * @property null|string $national_id_card
 * @property null|string $experience
 * @property null|int $career_level_id
 * @property null|int $industry_id
 * @property null|int $functional_area_id
 * @property null|string $current_salary
 * @property null|string $expected_salary
 * @property null|string $image_path
 * @property null|string $resume_path
 * @property null|Carbon $available_at
 * @property null|int $immediate_available
 * @property null|int $is_active
 * @property int $job_alert
 * @property null|Carbon $created_at
 * @property null|Carbon $updated_at
 * @property CandidateEducation[]|Collection $candidateEducation
 * @property CandidateExperience[]|Collection $candidateExperience
 * @property User $user
 * @property mixed $candidate_url
 * @property mixed $city_name
 * @property mixed $country_name
 * @property string $full_location
 * @property mixed $state_name
 * @property Collection|JobType[] $jobAlerts
 * @property null|int $job_alerts_count
 * @property Collection|JobApplication[] $jobApplications
 * @property null|int $job_applications_count
 * @property Collection|JobApplication[] $penddingJobApplications
 * @property null|int $pendding_job_applications_count
 * @property mixed $profile_completion_percentage
 * @property mixed $formatted_current_salary
 * @property mixed $formatted_expected_salary
 * @property mixed $experience_level
 *
 * @method static Builder|Candidate newModelQuery()
 * @method static Builder|Candidate newQuery()
 * @method static Builder|Candidate query()
 * @method static Builder|Candidate whereAvailableAt($value)
 * @method static Builder|Candidate whereCareerLevelId($value)
 * @method static Builder|Candidate whereCreatedAt($value)
 * @method static Builder|Candidate whereCurrentSalary($value)
 * @method static Builder|Candidate whereExpectedSalary($value)
 * @method static Builder|Candidate whereExperience($value)
 * @method static Builder|Candidate whereFatherName($value)
 * @method static Builder|Candidate whereFunctionalAreaId($value)
 * @method static Builder|Candidate whereId($value)
 * @method static Builder|Candidate whereImagePath($value)
 * @method static Builder|Candidate whereImmediateAvailable($value)
 * @method static Builder|Candidate whereIndustryId($value)
 * @method static Builder|Candidate whereIsActive($value)
 * @method static Builder|Candidate whereMaritalStatusId($value)
 * @method static Builder|Candidate whereNationalIdCard($value)
 * @method static Builder|Candidate whereNationality($value)
 * @method static Builder|Candidate whereResumePath($value)
 * @method static Builder|Candidate whereUpdatedAt($value)
 * @method static Builder|Candidate whereUserId($value)
 * @method static Builder|Candidate whereUniqueId($value)
 * @method static Builder|Candidate whereJobAlert($value)
 * @method static Builder|Candidate active()
 * @method static Builder|Candidate inactive()
 * @method static Builder|Candidate available()
 * @method static Builder|Candidate immediatelyAvailable()
 * @method static Builder|Candidate availableByDate(?Carbon $date = null)
 * @method static Builder|Candidate byExperience(int $minYears, ?int $maxYears = null)
 * @method static Builder|Candidate byCareerLevel(int $careerLevelId)
 * @method static Builder|Candidate byIndustry(int $industryId)
 * @method static Builder|Candidate byFunctionalArea(int $functionalAreaId)
 * @method static Builder|Candidate bySalaryRange(?float $minSalary = null, ?float $maxSalary = null, string $type = 'expected')
 * @method static Builder|Candidate byLocation(?int $countryId = null, ?int $stateId = null, ?int $cityId = null)
 * @method static Builder|Candidate withResume()
 * @method static Builder|Candidate withProfileImage()
 * @method static Builder|Candidate search(string $term)
 * @method static Builder|Candidate recent(int $days = 30)
 * @method static Builder|Candidate withJobAlerts()
 * @method static Builder|Candidate verified()
 * @method static Builder|Candidate profileComplete()
 * @method static Builder|Candidate popular()
 * @method static Builder|Candidate experienced(int $minYears = 5)
 * @method static Builder|Candidate freshGraduate()
 * @method static Builder|Candidate jobSeeking()
 * @method static Builder|Candidate alphabetical()
 *
 * @mixin \Eloquent
 */
class Candidate extends Model
{
    use HasFactory;
    use HasSettingsField;
    use HasTaxonomy;
    use LogsActivity;

    public const RESUME_PATH = 'candidates/resumes';
    public const IMAGE_PATH = 'candidates/images';

    public const CANDIDATE_LOGIN_TYPE = 1;
    public const CANDIDATE_EMP_TYPE = 2;

    public const ALL = 2;
    public const ACTIVE = 1;
    public const DEACTIVE = 0;

    public const STATUS = [
        self::ALL => 'All',
        self::ACTIVE => 'Active',
        self::DEACTIVE => 'Deactive',
    ];

    public const IMMEDIATE_AVAILABLE = 1;
    public const Not_IMMEDIATE_AVAILABLE = 0;
    public const IMMEDIATE = [
        self::ALL => 'All',
        self::IMMEDIATE_AVAILABLE => 'Immediate Available',
        self::Not_IMMEDIATE_AVAILABLE => 'Not Immediate Available',
    ];

    public $table = 'candidates';

    public $fillable = [
        'user_id',
        'unique_id',
        'father_name',
        'marital_status_id',
        'nationality',
        'national_id_card',
        'experience',
        'career_level_id',
        'industry_id',
        'functional_area_id',
        'current_salary',
        'expected_salary',
        'image_path',
        'resume_path',
        'available_at',
        'immediate_available',
        'job_alert',
        'address',
    ];

    /**
     * Validation rules with multilingual support.
     *
     * @var array
     */
    public static $rules = [
        'first_name' => 'required|string|max:100',
        'last_name' => 'required|string|max:100',
        'email' => 'required|email:filter|unique:users,email',
        'password' => 'nullable|same:password_confirmation|min:6',
        'marital_status_id' => 'nullable|integer|exists:marital_statuses,id',
        'nationality' => 'nullable|string|max:100',
        'country_id' => 'required|integer|exists:countries,id',
        'state_id' => 'required|integer|exists:states,id',
        'city_id' => 'required|integer|exists:cities,id',
        'phone' => 'required|string|max:20',
        'experience' => 'nullable|integer|min:0|max:50',
        'career_level_id' => 'nullable|integer|exists:career_levels,id',
        'industry_id' => 'nullable|integer|exists:industries,id',
        'functional_area_id' => 'nullable|integer|exists:functional_areas,id',
        'current_salary' => 'nullable|numeric|min:0',
        'expected_salary' => 'nullable|numeric|min:0',
        'father_name' => 'nullable|string|max:100',
        'national_id_card' => 'nullable|string|max:50',
    ];

    /**
     * Default eager loading for performance.
     */
    protected $with = ['user', 'maritalStatus', 'careerLevel', 'industry', 'functionalArea'];

    protected $appends = [
        'country_name',
        'state_name',
        'city_name',
        'full_location',
        'candidate_url',
        'profile_completion_percentage',
        'formatted_current_salary',
        'formatted_expected_salary',
        'experience_level',
    ];

    /**
     * Activity log options.
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['career_level_id', 'industry_id', 'functional_area_id', 'current_salary', 'expected_salary', 'is_active'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    public function getCountryNameAttribute()
    {
        return cache()->remember("candidate.{$this->id}.country_name", 3600, function () {
            return $this->user?->country?->name;
        });
    }

    public function getStateNameAttribute()
    {
        return cache()->remember("candidate.{$this->id}.state_name", 3600, function () {
            return $this->user?->state?->name;
        });
    }

    public function getCityNameAttribute()
    {
        return cache()->remember("candidate.{$this->id}.city_name", 3600, function () {
            return $this->user?->city?->name;
        });
    }

    public function getFullLocationAttribute(): string
    {
        $location = [];

        if ($this->city_name) {
            $location[] = $this->city_name;
        }
        if ($this->state_name) {
            $location[] = $this->state_name;
        }
        if ($this->country_name) {
            $location[] = $this->country_name;
        }

        return implode(', ', $location) ?: __('common.location_not_specified');
    }

    public function getCandidateUrlAttribute()
    {
        return cache()->remember("candidate.{$this->id}.candidate_url", 3600, function () {
            if ($this->image_path) {
                return asset('storage/'.$this->image_path);
            }

            return asset('assets/img/default-candidate-avatar.png');
        });
    }

    /**
     * Get profile completion percentage.
     */
    public function getProfileCompletionPercentageAttribute(): int
    {
        return cache()->remember("candidate.{$this->id}.profile_completion", 3600, function () {
            $fields = [
                'father_name', 'nationality', 'experience', 'career_level_id',
                'industry_id', 'functional_area_id', 'current_salary',
                'expected_salary', 'image_path', 'resume_path',
            ];

            $filledFields = 0;
            foreach ($fields as $field) {
                if (! empty($this->{$field})) {
                    $filledFields++;
                }
            }

            // Add user fields
            if ($this->user) {
                $userFields = ['phone', 'dob', 'gender'];
                foreach ($userFields as $field) {
                    if (! empty($this->user->{$field})) {
                        $filledFields++;
                    }
                }
            }

            return round(($filledFields / (count($fields) + 3)) * 100);
        });
    }

    /**
     * Get formatted current salary.
     */
    public function getFormattedCurrentSalaryAttribute(): string
    {
        if (! $this->current_salary) {
            return __('common.not_specified');
        }

        return number_format($this->current_salary, 2);
    }

    /**
     * Get formatted expected salary.
     */
    public function getFormattedExpectedSalaryAttribute(): string
    {
        if (! $this->expected_salary) {
            return __('common.not_specified');
        }

        return number_format($this->expected_salary, 2);
    }

    /**
     * Get experience level description.
     */
    public function getExperienceLevelAttribute(): string
    {
        if (! $this->experience) {
            return __('candidate.fresh_graduate');
        }

        return match (true) {
            $this->experience < 1 => __('candidate.entry_level'),
            $this->experience < 3 => __('candidate.junior_level'),
            $this->experience < 7 => __('candidate.mid_level'),
            $this->experience < 12 => __('candidate.senior_level'),
            default => __('candidate.executive_level')
        };
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class)->withDefault();
    }

    public function maritalStatus(): BelongsTo
    {
        return $this->belongsTo(MaritalStatus::class)->withDefault();
    }

    public function careerLevel(): BelongsTo
    {
        return $this->belongsTo(CareerLevel::class)->withDefault();
    }

    public function industry(): BelongsTo
    {
        return $this->belongsTo(Industry::class)->withDefault();
    }

    public function functionalArea(): BelongsTo
    {
        return $this->belongsTo(FunctionalArea::class)->withDefault();
    }

    public function candidateEducation(): HasMany
    {
        return $this->hasMany(CandidateEducation::class, 'candidate_id');
    }

    public function candidateExperience(): HasMany
    {
        return $this->hasMany(CandidateExperience::class, 'candidate_id');
    }

    public function jobAlerts(): BelongsToMany
    {
        return $this->belongsToMany(JobType::class, 'job_alerts', 'candidate_id', 'job_type_id');
    }

    public function jobApplications(): HasMany
    {
        return $this->hasMany(JobApplication::class, 'candidate_id');
    }

    public function penddingJobApplications(): HasMany
    {
        return $this->hasMany(JobApplication::class, 'candidate_id')
            ->where('status', JobApplication::STATUS_APPLIED);
    }

    public function getResumeUrl(): ?string
    {
        return cache()->remember("candidate.{$this->id}.resume_url", 3600, function () {
            if ($this->resume_path) {
                return asset('storage/'.$this->resume_path);
            }

            return null;
        });
    }

    public function uploadProfileImage(UploadedFile $file): bool
    {
        try {
            $fileService = app(FileService::class);

            // Delete old image if exists
            if ($this->image_path) {
                $fileService->deleteFile($this->image_path);
            }

            $this->image_path = $fileService->uploadFile($file, self::IMAGE_PATH);
            $this->save();

            return true;
        } catch (\Exception $e) {
            \Log::error('Failed to upload candidate profile image: '.$e->getMessage());

            return false;
        }
    }

    public function uploadResume(UploadedFile $file, string $title = 'resume'): bool
    {
        try {
            $fileService = app(FileService::class);

            // Delete old resume if exists
            if ($this->resume_path) {
                $fileService->deleteFile($this->resume_path);
            }

            $this->resume_path = $fileService->uploadFile($file, self::RESUME_PATH);
            $this->save();

            return true;
        } catch (\Exception $e) {
            \Log::error('Failed to upload candidate resume: '.$e->getMessage());

            return false;
        }
    }

    /**
     * Scope for active candidates.
     *
     * @param  mixed  $query
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for inactive candidates.
     *
     * @param  mixed  $query
     */
    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    /**
     * Scope for verified candidates.
     *
     * @param  mixed  $query
     */
    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    /**
     * Scope for unverified candidates.
     *
     * @param  mixed  $query
     */
    public function scopeUnverified($query)
    {
        return $query->where('is_verified', false);
    }

    /**
     * Scope for featured candidates.
     *
     * @param  mixed  $query
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope for non-featured candidates.
     *
     * @param  mixed  $query
     */
    public function scopeNotFeatured($query)
    {
        return $query->where('is_featured', false);
    }

    /**
     * Scope for available candidates.
     *
     * @param  mixed  $query
     */
    public function scopeAvailable($query)
    {
        return $query->where('is_available', true);
    }

    /**
     * Scope for unavailable candidates.
     *
     * @param  mixed  $query
     */
    public function scopeUnavailable($query)
    {
        return $query->where('is_available', false);
    }

    /**
     * Scope for immediately available candidates.
     *
     * @param  mixed  $query
     */
    public function scopeImmediatelyAvailable($query)
    {
        return $query->where('is_immediate_available', true);
    }

    /**
     * Scope for candidates by country.
     *
     * @param  mixed  $query
     */
    public function scopeByCountry($query, int $countryId)
    {
        return $query->where('country_id', $countryId);
    }

    /**
     * Scope for candidates by state.
     *
     * @param  mixed  $query
     */
    public function scopeByState($query, int $stateId)
    {
        return $query->where('state_id', $stateId);
    }

    /**
     * Scope for candidates by city.
     *
     * @param  mixed  $query
     */
    public function scopeByCity($query, int $cityId)
    {
        return $query->where('city_id', $cityId);
    }

    /**
     * Scope for candidates by career level.
     *
     * @param  mixed  $query
     */
    public function scopeByCareerLevel($query, int $careerLevelId)
    {
        return $query->where('career_level_id', $careerLevelId);
    }

    /**
     * Scope for candidates by functional area.
     *
     * @param  mixed  $query
     */
    public function scopeByFunctionalArea($query, int $functionalAreaId)
    {
        return $query->where('functional_area_id', $functionalAreaId);
    }

    /**
     * Scope for candidates by marital status.
     *
     * @param  mixed  $query
     */
    public function scopeByMaritalStatus($query, int $maritalStatusId)
    {
        return $query->where('marital_status_id', $maritalStatusId);
    }

    /**
     * Scope for searching candidates.
     *
     * @param  mixed  $query
     */
    public function scopeSearch($query, string $term)
    {
        return $query->where('first_name', 'like', "%{$term}%")
            ->orWhere('last_name', 'like', "%{$term}%")
            ->orWhere('email', 'like', "%{$term}%")
            ->orWhere('phone', 'like', "%{$term}%");
    }

    /**
     * Scope for recent candidates.
     *
     * @param  mixed  $query
     */
    public function scopeRecent($query, int $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Scope for old candidates.
     *
     * @param  mixed  $query
     */
    public function scopeOld($query, int $days = 365)
    {
        return $query->where('created_at', '<', now()->subDays($days));
    }

    /**
     * Scope for candidates by experience range.
     *
     * @param  mixed  $query
     */
    public function scopeByExperienceRange($query, int $min, int $max)
    {
        return $query->whereBetween('experience_years', [$min, $max]);
    }

    /**
     * Scope for entry level candidates.
     *
     * @param  mixed  $query
     */
    public function scopeEntryLevel($query)
    {
        return $query->where('experience_years', '<=', 2);
    }

    /**
     * Scope for experienced candidates.
     *
     * @param  mixed  $query
     */
    public function scopeExperienced($query)
    {
        return $query->where('experience_years', '>=', 5);
    }

    /**
     * Scope for senior candidates.
     *
     * @param  mixed  $query
     */
    public function scopeSenior($query)
    {
        return $query->where('experience_years', '>=', 10);
    }

    /**
     * Scope for candidates by salary range.
     *
     * @param  mixed  $query
     */
    public function scopeBySalaryRange($query, float $min, float $max)
    {
        return $query->whereBetween('expected_salary', [$min, $max]);
    }

    /**
     * Scope for candidates by gender.
     *
     * @param  mixed  $query
     */
    public function scopeByGender($query, string $gender)
    {
        return $query->where('gender', $gender);
    }

    /**
     * Scope for male candidates.
     *
     * @param  mixed  $query
     */
    public function scopeMale($query)
    {
        return $query->where('gender', 'male');
    }

    /**
     * Scope for female candidates.
     *
     * @param  mixed  $query
     */
    public function scopeFemale($query)
    {
        return $query->where('gender', 'female');
    }

    /**
     * Scope for candidates with resumes.
     *
     * @param  mixed  $query
     */
    public function scopeWithResumes($query)
    {
        return $query->has('resumes');
    }

    /**
     * Scope for candidates without resumes.
     *
     * @param  mixed  $query
     */
    public function scopeWithoutResumes($query)
    {
        return $query->doesntHave('resumes');
    }

    /**
     * Scope for candidates with job applications.
     *
     * @param  mixed  $query
     */
    public function scopeWithApplications($query)
    {
        return $query->has('jobApplications');
    }

    /**
     * Scope for candidates with skills.
     *
     * @param  mixed  $query
     */
    public function scopeWithSkills($query)
    {
        return $query->has('skills');
    }

    /**
     * Scope for candidates by age range.
     *
     * @param  mixed  $query
     */
    public function scopeByAgeRange($query, int $minAge, int $maxAge)
    {
        $maxDate = now()->subYears($minAge)->format('Y-m-d');
        $minDate = now()->subYears($maxAge + 1)->format('Y-m-d');

        return $query->whereBetween('date_of_birth', [$minDate, $maxDate]);
    }

    /**
     * Scope for young candidates (under 30).
     *
     * @param  mixed  $query
     */
    public function scopeYoung($query)
    {
        return $query->byAgeRange(18, 30);
    }

    /**
     * Scope for alphabetical ordering.
     *
     * @param  mixed  $query
     */
    public function scopeAlphabetical($query)
    {
        return $query->orderBy('first_name', 'asc')
            ->orderBy('last_name', 'asc');
    }

    /**
     * Scope for popular candidates (with most applications).
     *
     * @param  mixed  $query
     */
    public function scopePopular($query, int $limit = 10)
    {
        return $query->withCount('jobApplications')
            ->orderBy('job_applications_count', 'desc')
            ->limit($limit);
    }

    /**
     * Check if candidate profile is complete.
     */
    public function isProfileComplete(): bool
    {
        return $this->profile_completion_percentage >= 80;
    }

    /**
     * Check if candidate is job ready.
     */
    public function isJobReady(): bool
    {
        return $this->is_active
               && $this->resume_path
               && $this->career_level_id
               && $this->industry_id
               && $this->functional_area_id;
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'id' => 'int',
            'user_id' => 'int',
            'country_id' => 'int',
            'state_id' => 'int',
            'city_id' => 'int',
            'marital_status_id' => 'int',
            'career_level_id' => 'int',
            'industry_id' => 'int',
            'functional_area_id' => 'int',
            'first_name' => 'string',
            'last_name' => 'string',
            'email' => 'string',
            'phone' => 'string',
            'date_of_birth' => 'date',
            'gender' => 'string',
            'experience_years' => 'int',
            'current_salary' => 'decimal:2',
            'expected_salary' => 'decimal:2',
            'immediate_available' => 'boolean',
            'is_verified' => 'boolean',
            'is_featured' => 'boolean',
            'is_available' => 'boolean',
            'is_immediate_available' => 'boolean',
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

        // Generate unique ID
        static::creating(function ($candidate) {
            if (! $candidate->unique_id) {
                $candidate->unique_id = 'CAND-'.strtoupper(uniqid());
            }
        });

        // Clear cache when candidate is updated
        static::updated(function ($candidate) {
            cache()->forget("candidate.{$candidate->id}");
            cache()->forget("candidate.{$candidate->id}.profile_completion");
            // Some cache stores (array/file) do not support tags
            try {
                cache()->tags(['candidates', 'candidate-'.$candidate->id])->flush();
            } catch (\Exception $e) {
                // Ignore if tags not supported
            }
        });

        // Clear cache when candidate is deleted
        static::deleted(function ($candidate) {
            cache()->forget("candidate.{$candidate->id}");
            cache()->forget("candidate.{$candidate->id}.profile_completion");
            try {
                cache()->tags(['candidates', 'candidate-'.$candidate->id])->flush();
            } catch (\Exception $e) {
                // Ignore if tags not supported
            }
        });
    }

    /**
     * Default settings for candidate model.
     */
    public $defaultSettings = [
        'profile' => [
            'visibility' => 'public', // public, private, recruiters_only
            'show_contact_info' => true,
            'show_salary_expectations' => true,
            'show_experience_details' => true,
            'show_education_details' => true,
            'show_skills' => true,
            'show_resume' => true,
            'show_profile_image' => true,
            'searchable' => true,
        ],
        'privacy' => [
            'allow_recruiter_contact' => true,
            'allow_company_contact' => true,
            'show_current_company' => false,
            'anonymous_profile' => false,
            'hide_from_current_employer' => true,
            'block_specific_companies' => [],
            'allow_profile_download' => false,
        ],
        'job_preferences' => [
            'job_alerts_enabled' => true,
            'preferred_job_types' => [], // remote, onsite, hybrid
            'preferred_industries' => [],
            'preferred_locations' => [],
            'salary_range_min' => 0,
            'salary_range_max' => 0,
            'willing_to_relocate' => false,
            'travel_percentage' => 0, // 0-100
            'notice_period_days' => 30,
            'immediate_availability' => false,
        ],
        'notifications' => [
            'job_matches' => true,
            'application_updates' => true,
            'recruiter_messages' => true,
            'profile_views' => false,
            'weekly_job_digest' => true,
            'monthly_market_insights' => true,
            'email_notifications' => true,
            'sms_notifications' => false,
            'push_notifications' => true,
        ],
        'dashboard' => [
            'default_view' => 'overview', // overview, jobs, applications, profile
            'show_profile_completion' => true,
            'show_recent_applications' => true,
            'show_job_recommendations' => true,
            'show_profile_views' => true,
            'show_saved_jobs' => true,
            'items_per_page' => 10,
            'auto_refresh' => false,
        ],
        'search' => [
            'save_search_history' => true,
            'default_sort' => 'relevance', // relevance, date, salary
            'results_per_page' => 20,
            'location_radius' => 50, // km
            'include_remote_jobs' => true,
            'auto_apply_filters' => false,
        ],
        'career' => [
            'career_goals' => '',
            'preferred_work_culture' => '', // startup, corporate, remote, etc.
            'skills_to_develop' => [],
            'certifications_pursuing' => [],
            'languages_spoken' => [],
            'availability_status' => 'open', // open, passive, not_looking
        ],
        'social' => [
            'linkedin_profile' => '',
            'github_profile' => '',
            'portfolio_website' => '',
            'twitter_handle' => '',
            'show_social_links' => true,
            'allow_social_login' => true,
        ],
    ];

    /**
     * Settings validation rules.
     */
    public $settingsRules = [
        'profile.visibility' => 'string|in:public,private,recruiters_only',
        'profile.show_contact_info' => 'boolean',
        'profile.show_salary_expectations' => 'boolean',
        'profile.show_experience_details' => 'boolean',
        'profile.show_education_details' => 'boolean',
        'profile.show_skills' => 'boolean',
        'profile.show_resume' => 'boolean',
        'profile.show_profile_image' => 'boolean',
        'profile.searchable' => 'boolean',

        'privacy.allow_recruiter_contact' => 'boolean',
        'privacy.allow_company_contact' => 'boolean',
        'privacy.show_current_company' => 'boolean',
        'privacy.anonymous_profile' => 'boolean',
        'privacy.hide_from_current_employer' => 'boolean',
        'privacy.block_specific_companies' => 'array',
        'privacy.allow_profile_download' => 'boolean',

        'job_preferences.job_alerts_enabled' => 'boolean',
        'job_preferences.preferred_job_types' => 'array',
        'job_preferences.preferred_industries' => 'array',
        'job_preferences.preferred_locations' => 'array',
        'job_preferences.salary_range_min' => 'numeric|min:0',
        'job_preferences.salary_range_max' => 'numeric|min:0',
        'job_preferences.willing_to_relocate' => 'boolean',
        'job_preferences.travel_percentage' => 'integer|min:0|max:100',
        'job_preferences.notice_period_days' => 'integer|min:0|max:365',
        'job_preferences.immediate_availability' => 'boolean',

        'notifications.job_matches' => 'boolean',
        'notifications.application_updates' => 'boolean',
        'notifications.recruiter_messages' => 'boolean',
        'notifications.profile_views' => 'boolean',
        'notifications.weekly_job_digest' => 'boolean',
        'notifications.monthly_market_insights' => 'boolean',
        'notifications.email_notifications' => 'boolean',
        'notifications.sms_notifications' => 'boolean',
        'notifications.push_notifications' => 'boolean',

        'dashboard.default_view' => 'string|in:overview,jobs,applications,profile',
        'dashboard.show_profile_completion' => 'boolean',
        'dashboard.show_recent_applications' => 'boolean',
        'dashboard.show_job_recommendations' => 'boolean',
        'dashboard.show_profile_views' => 'boolean',
        'dashboard.show_saved_jobs' => 'boolean',
        'dashboard.items_per_page' => 'integer|min:5|max:100',
        'dashboard.auto_refresh' => 'boolean',

        'search.save_search_history' => 'boolean',
        'search.default_sort' => 'string|in:relevance,date,salary',
        'search.results_per_page' => 'integer|min:10|max:100',
        'search.location_radius' => 'integer|min:1|max:500',
        'search.include_remote_jobs' => 'boolean',
        'search.auto_apply_filters' => 'boolean',

        'career.career_goals' => 'string|max:1000',
        'career.preferred_work_culture' => 'string|max:255',
        'career.skills_to_develop' => 'array',
        'career.certifications_pursuing' => 'array',
        'career.languages_spoken' => 'array',
        'career.availability_status' => 'string|in:open,passive,not_looking',

        'social.linkedin_profile' => 'url|nullable',
        'social.github_profile' => 'url|nullable',
        'social.portfolio_website' => 'url|nullable',
        'social.twitter_handle' => 'string|max:50|nullable',
        'social.show_social_links' => 'boolean',
        'social.allow_social_login' => 'boolean',
    ];
}
