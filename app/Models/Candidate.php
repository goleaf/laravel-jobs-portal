<?php

namespace App\Models;
use App\Traits\HasTaxonomy;
use App\Services\FileService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

/**
 * Class Candidate
 *
 * @version July 20, 2020, 5:48 am UTC
 *
 * @property int $id
 * @property int $user_id
 * @property string $unique_id
 * @property string|null $father_name
 * @property int|null $marital_status_id
 * @property string|null $nationality
 * @property string|null $national_id_card
 * @property string|null $experience
 * @property int|null $career_level_id
 * @property int|null $industry_id
 * @property int|null $functional_area_id
 * @property string|null $current_salary
 * @property string|null $expected_salary
 * @property string|null $image_path
 * @property string|null $resume_path
 * @property Carbon|null $available_at
 * @property int|null $immediate_available
 * @property int|null $is_active
 * @property int $job_alert
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection|CandidateEducation[] $candidateEducation
 * @property-read Collection|CandidateExperience[] $candidateExperience
 * @property-read User $user
 * @property-read mixed $candidate_url
 * @property-read mixed $city_name
 * @property-read mixed $country_name
 * @property-read string $full_location
 * @property-read mixed $state_name
 * @property-read Collection|\App\Models\JobType[] $jobAlerts
 * @property-read int|null $job_alerts_count
 * @property-read Collection|\App\Models\JobApplication[] $jobApplications
 * @property-read int|null $job_applications_count
 * @property-read Collection|\App\Models\JobApplication[] $penddingJobApplications
 * @property-read int|null $pendding_job_applications_count
 * @property-read mixed $profile_completion_percentage
 * @property-read mixed $formatted_current_salary
 * @property-read mixed $formatted_expected_salary
 * @property-read mixed $experience_level
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
    use HasFactory, LogsActivity, HasTaxonomy;

    public $table = 'candidates';

    /**
     * Default eager loading for performance
     */
    protected $with = ['user', 'maritalStatus', 'careerLevel', 'industry', 'functionalArea'];

    const RESUME_PATH = 'candidates/resumes';
    const IMAGE_PATH = 'candidates/images';

    public const CANDIDATE_LOGIN_TYPE = 1;
    public const CANDIDATE_EMP_TYPE = 2;

    const ALL = 2;
    const ACTIVE = 1;
    const DEACTIVE = 0;

    const STATUS = [
        self::ALL => 'All',
        self::ACTIVE => 'Active',
        self::DEACTIVE => 'Deactive',
    ];

    const IMMEDIATE_AVAILABLE = 1;
    const Not_IMMEDIATE_AVAILABLE = 0;
    const IMMEDIATE = [
        self::ALL => 'All',
        self::IMMEDIATE_AVAILABLE => 'Immediate Available',
        self::Not_IMMEDIATE_AVAILABLE => 'Not Immediate Available',
    ];

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
    ];

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
            'immediate_available' => 'int',
            'is_verified' => 'boolean',
            'is_featured' => 'boolean',
            'is_available' => 'boolean',
            'is_immediate_available' => 'boolean',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }


    /**
     * Validation rules with multilingual support
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

    protected $appends = [
        'country_name', 
        'state_name', 
        'city_name', 
        'full_location', 
        'candidate_url',
        'profile_completion_percentage',
        'formatted_current_salary',
        'formatted_expected_salary',
        'experience_level'
    ];

    /**
     * Boot the model.
     */
    protected static function boot(): void
    {
        parent::boot();

        // Generate unique ID
        static::creating(function ($candidate) {
            if (!$candidate->unique_id) {
                $candidate->unique_id = 'CAND-' . strtoupper(uniqid());
            }
        });

        // Clear cache when candidate is updated
        static::updated(function ($candidate) {
            cache()->forget("candidate.{$candidate->id}");
            cache()->forget("candidate.{$candidate->id}.profile_completion");
            cache()->tags(['candidates', 'candidate-' . $candidate->id])->flush();
        });

        // Clear cache when candidate is deleted
        static::deleted(function ($candidate) {
            cache()->forget("candidate.{$candidate->id}");
            cache()->forget("candidate.{$candidate->id}.profile_completion");
            cache()->tags(['candidates', 'candidate-' . $candidate->id])->flush();
        });
    }

    /**
     * Activity log options
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
        
        if ($this->city_name) $location[] = $this->city_name;
        if ($this->state_name) $location[] = $this->state_name;
        if ($this->country_name) $location[] = $this->country_name;
        
        return implode(', ', $location) ?: __('common.location_not_specified');
    }

    public function getCandidateUrlAttribute()
    {
        return cache()->remember("candidate.{$this->id}.candidate_url", 3600, function () {
            if ($this->image_path) {
                return asset('storage/' . $this->image_path);
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
                'expected_salary', 'image_path', 'resume_path'
            ];
            
            $filledFields = 0;
            foreach ($fields as $field) {
                if (!empty($this->$field)) {
                    $filledFields++;
                }
            }
            
            // Add user fields
            if ($this->user) {
                $userFields = ['phone', 'dob', 'gender'];
                foreach ($userFields as $field) {
                    if (!empty($this->user->$field)) {
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
        if (!$this->current_salary) {
            return __('common.not_specified');
        }
        return number_format($this->current_salary, 2);
    }

    /**
     * Get formatted expected salary.
     */
    public function getFormattedExpectedSalaryAttribute(): string
    {
        if (!$this->expected_salary) {
            return __('common.not_specified');
        }
        return number_format($this->expected_salary, 2);
    }

    /**
     * Get experience level description.
     */
    public function getExperienceLevelAttribute(): string
    {
        if (!$this->experience) {
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
                return asset('storage/' . $this->resume_path);
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
            \Log::error('Failed to upload candidate profile image: ' . $e->getMessage());
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
            \Log::error('Failed to upload candidate resume: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Scope for active candidates.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for inactive candidates.
     */
    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    /**
     * Scope for verified candidates.
     */
    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    /**
     * Scope for unverified candidates.
     */
    public function scopeUnverified($query)
    {
        return $query->where('is_verified', false);
    }

    /**
     * Scope for featured candidates.
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope for non-featured candidates.
     */
    public function scopeNotFeatured($query)
    {
        return $query->where('is_featured', false);
    }

    /**
     * Scope for available candidates.
     */
    public function scopeAvailable($query)
    {
        return $query->where('is_available', true);
    }

    /**
     * Scope for unavailable candidates.
     */
    public function scopeUnavailable($query)
    {
        return $query->where('is_available', false);
    }

    /**
     * Scope for immediately available candidates.
     */
    public function scopeImmediatelyAvailable($query)
    {
        return $query->where('is_immediate_available', true);
    }

    /**
     * Scope for candidates by country.
     */
    public function scopeByCountry($query, int $countryId)
    {
        return $query->where('country_id', $countryId);
    }

    /**
     * Scope for candidates by state.
     */
    public function scopeByState($query, int $stateId)
    {
        return $query->where('state_id', $stateId);
    }

    /**
     * Scope for candidates by city.
     */
    public function scopeByCity($query, int $cityId)
    {
        return $query->where('city_id', $cityId);
    }

    /**
     * Scope for candidates by career level.
     */
    public function scopeByCareerLevel($query, int $careerLevelId)
    {
        return $query->where('career_level_id', $careerLevelId);
    }

    /**
     * Scope for candidates by functional area.
     */
    public function scopeByFunctionalArea($query, int $functionalAreaId)
    {
        return $query->where('functional_area_id', $functionalAreaId);
    }

    /**
     * Scope for candidates by marital status.
     */
    public function scopeByMaritalStatus($query, int $maritalStatusId)
    {
        return $query->where('marital_status_id', $maritalStatusId);
    }

    /**
     * Scope for searching candidates.
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
     */
    public function scopeRecent($query, int $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Scope for old candidates.
     */
    public function scopeOld($query, int $days = 365)
    {
        return $query->where('created_at', '<', now()->subDays($days));
    }

    /**
     * Scope for candidates by experience range.
     */
    public function scopeByExperienceRange($query, int $min, int $max)
    {
        return $query->whereBetween('experience_years', [$min, $max]);
    }

    /**
     * Scope for entry level candidates.
     */
    public function scopeEntryLevel($query)
    {
        return $query->where('experience_years', '<=', 2);
    }

    /**
     * Scope for experienced candidates.
     */
    public function scopeExperienced($query)
    {
        return $query->where('experience_years', '>=', 5);
    }

    /**
     * Scope for senior candidates.
     */
    public function scopeSenior($query)
    {
        return $query->where('experience_years', '>=', 10);
    }

    /**
     * Scope for candidates by salary range.
     */
    public function scopeBySalaryRange($query, float $min, float $max)
    {
        return $query->whereBetween('expected_salary', [$min, $max]);
    }

    /**
     * Scope for candidates by gender.
     */
    public function scopeByGender($query, string $gender)
    {
        return $query->where('gender', $gender);
    }

    /**
     * Scope for male candidates.
     */
    public function scopeMale($query)
    {
        return $query->where('gender', 'male');
    }

    /**
     * Scope for female candidates.
     */
    public function scopeFemale($query)
    {
        return $query->where('gender', 'female');
    }

    /**
     * Scope for candidates with resumes.
     */
    public function scopeWithResumes($query)
    {
        return $query->has('resumes');
    }

    /**
     * Scope for candidates without resumes.
     */
    public function scopeWithoutResumes($query)
    {
        return $query->doesntHave('resumes');
    }

    /**
     * Scope for candidates with job applications.
     */
    public function scopeWithApplications($query)
    {
        return $query->has('jobApplications');
    }

    /**
     * Scope for candidates with skills.
     */
    public function scopeWithSkills($query)
    {
        return $query->has('skills');
    }

    /**
     * Scope for candidates by age range.
     */
    public function scopeByAgeRange($query, int $minAge, int $maxAge)
    {
        $maxDate = now()->subYears($minAge)->format('Y-m-d');
        $minDate = now()->subYears($maxAge + 1)->format('Y-m-d');
        
        return $query->whereBetween('date_of_birth', [$minDate, $maxDate]);
    }

    /**
     * Scope for young candidates (under 30).
     */
    public function scopeYoung($query)
    {
        return $query->byAgeRange(18, 30);
    }

    /**
     * Scope for alphabetical ordering.
     */
    public function scopeAlphabetical($query)
    {
        return $query->orderBy('first_name', 'asc')
                    ->orderBy('last_name', 'asc');
    }

    /**
     * Scope for popular candidates (with most applications).
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
        return $this->is_active && 
               $this->resume_path && 
               $this->career_level_id && 
               $this->industry_id && 
               $this->functional_area_id;
    }
}
