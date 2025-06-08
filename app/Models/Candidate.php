<?php

namespace App\Models;

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
    use HasFactory, LogsActivity;

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
        'is_active',
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
            'id' => 'integer',
            'user_id' => 'integer',
            'marital_status_id' => 'integer',
            'career_level_id' => 'integer',
            'industry_id' => 'integer',
            'functional_area_id' => 'integer',
            'current_salary' => 'decimal:2',
            'expected_salary' => 'decimal:2',
            'experience' => 'integer',
            'immediate_available' => 'boolean',
            'is_active' => 'boolean',
            'job_alert' => 'boolean',
            'available_at' => 'datetime',
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
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true)
                    ->whereHas('user', function ($q) {
                        $q->where('is_active', true);
                    });
    }

    /**
     * Scope for inactive candidates.
     */
    public function scopeInactive(Builder $query): Builder
    {
        return $query->where('is_active', false);
    }

    /**
     * Scope for available candidates.
     */
    public function scopeAvailable(Builder $query): Builder
    {
        return $query->where(function ($q) {
            $q->where('immediate_available', true)
              ->orWhere('available_at', '<=', now())
              ->orWhereNull('available_at');
        });
    }

    /**
     * Scope for immediately available candidates.
     */
    public function scopeImmediatelyAvailable(Builder $query): Builder
    {
        return $query->where('immediate_available', true);
    }

    /**
     * Scope for candidates available by specific date.
     */
    public function scopeAvailableByDate(Builder $query, ?Carbon $date = null): Builder
    {
        $date = $date ?: now();
        return $query->where(function ($q) use ($date) {
            $q->where('immediate_available', true)
              ->orWhere('available_at', '<=', $date);
        });
    }

    /**
     * Scope for candidates by experience range.
     */
    public function scopeByExperience(Builder $query, int $minYears, ?int $maxYears = null): Builder
    {
        $query->where('experience', '>=', $minYears);
        
        if ($maxYears !== null) {
            $query->where('experience', '<=', $maxYears);
        }
        
        return $query;
    }

    /**
     * Scope for candidates by career level.
     */
    public function scopeByCareerLevel(Builder $query, int $careerLevelId): Builder
    {
        return $query->where('career_level_id', $careerLevelId);
    }

    /**
     * Scope for candidates by industry.
     */
    public function scopeByIndustry(Builder $query, int $industryId): Builder
    {
        return $query->where('industry_id', $industryId);
    }

    /**
     * Scope for candidates by functional area.
     */
    public function scopeByFunctionalArea(Builder $query, int $functionalAreaId): Builder
    {
        return $query->where('functional_area_id', $functionalAreaId);
    }

    /**
     * Scope for candidates by salary range.
     */
    public function scopeBySalaryRange(Builder $query, ?float $minSalary = null, ?float $maxSalary = null, string $type = 'expected'): Builder
    {
        $column = $type === 'current' ? 'current_salary' : 'expected_salary';
        
        if ($minSalary !== null) {
            $query->where($column, '>=', $minSalary);
        }
        
        if ($maxSalary !== null) {
            $query->where($column, '<=', $maxSalary);
        }
        
        return $query;
    }

    /**
     * Scope for candidates by location.
     */
    public function scopeByLocation(Builder $query, ?int $countryId = null, ?int $stateId = null, ?int $cityId = null): Builder
    {
        return $query->whereHas('user', function ($q) use ($countryId, $stateId, $cityId) {
            if ($countryId) {
                $q->where('country_id', $countryId);
            }
            if ($stateId) {
                $q->where('state_id', $stateId);
            }
            if ($cityId) {
                $q->where('city_id', $cityId);
            }
        });
    }

    /**
     * Scope for candidates with resume.
     */
    public function scopeWithResume(Builder $query): Builder
    {
        return $query->whereNotNull('resume_path')
                    ->where('resume_path', '!=', '');
    }

    /**
     * Scope for candidates with profile image.
     */
    public function scopeWithProfileImage(Builder $query): Builder
    {
        return $query->whereNotNull('image_path')
                    ->where('image_path', '!=', '');
    }

    /**
     * Scope for searching candidates.
     */
    public function scopeSearch(Builder $query, string $term): Builder
    {
        return $query->whereHas('user', function ($q) use ($term) {
            $q->where('first_name', 'like', "%{$term}%")
              ->orWhere('last_name', 'like', "%{$term}%")
              ->orWhere('email', 'like', "%{$term}%");
        })->orWhere('father_name', 'like', "%{$term}%")
          ->orWhere('nationality', 'like', "%{$term}%")
          ->orWhere('national_id_card', 'like', "%{$term}%");
    }

    /**
     * Scope for recent candidates.
     */
    public function scopeRecent(Builder $query, int $days = 30): Builder
    {
        return $query->where('created_at', '>=', now()->subDays($days))
                    ->orderByDesc('created_at');
    }

    /**
     * Scope for candidates with job alerts enabled.
     */
    public function scopeWithJobAlerts(Builder $query): Builder
    {
        return $query->where('job_alert', true);
    }

    /**
     * Scope for verified candidates.
     */
    public function scopeVerified(Builder $query): Builder
    {
        return $query->whereHas('user', function ($q) {
            $q->where('is_verified', true);
        });
    }

    /**
     * Scope for candidates with complete profiles.
     */
    public function scopeProfileComplete(Builder $query): Builder
    {
        return $query->whereNotNull('resume_path')
                    ->whereNotNull('career_level_id')
                    ->whereNotNull('industry_id')
                    ->whereNotNull('functional_area_id');
    }

    /**
     * Scope for popular candidates (high profile views).
     */
    public function scopePopular(Builder $query): Builder
    {
        return $query->whereHas('user', function ($q) {
            $q->where('profile_views', '>', 10);
        })->orderByDesc(
            User::select('profile_views')
                ->whereColumn('users.id', 'candidates.user_id')
        );
    }

    /**
     * Scope for experienced candidates.
     */
    public function scopeExperienced(Builder $query, int $minYears = 5): Builder
    {
        return $query->where('experience', '>=', $minYears);
    }

    /**
     * Scope for fresh graduates.
     */
    public function scopeFreshGraduate(Builder $query): Builder
    {
        return $query->where('experience', '<=', 1);
    }

    /**
     * Scope for job seeking candidates.
     */
    public function scopeJobSeeking(Builder $query): Builder
    {
        return $query->active()
                    ->available()
                    ->where('job_alert', true);
    }

    /**
     * Scope for alphabetically ordered candidates.
     */
    public function scopeAlphabetical(Builder $query): Builder
    {
        return $query->join('users', 'candidates.user_id', '=', 'users.id')
                    ->orderBy('users.first_name', 'asc')
                    ->orderBy('users.last_name', 'asc')
                    ->select('candidates.*');
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
