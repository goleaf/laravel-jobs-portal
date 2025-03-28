<?php

namespace App\Models;

use App\Services\FileService;
use Auth;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use Spatie\Translatable\HasTranslations;

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
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection|CandidateEducation[] $candidateEducation
 * @property-read Collection|CandidateExperience[] $candidateExperience
 * @property-read User $user
 * @property-read mixed $candidate_url
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
 *
 * @mixin \Eloquent
 *
 * @property int $job_alert
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
 *
 * @method static Builder|Candidate whereJobAlert($value)
 */
class Candidate extends Model
{
    use HasSlug, HasTranslations;

    public $table = 'candidates';

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
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'user_id' => 'integer',
        'unique_id' => 'string',
        'father_name' => 'string',
        'marital_status_id' => 'integer',
        'nationality' => 'string',
        'national_id_card' => 'string',
        'experience' => 'string',
        'career_level_id' => 'integer',
        'industry_id' => 'integer',
        'functional_area_id' => 'integer',
        'current_salary' => 'string',
        'expected_salary' => 'string',
        'image_path' => 'string',
        'resume_path' => 'string',
        'immediate_available' => 'integer',
        'is_active' => 'integer',
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'first_name' => 'required|string',
        'last_name' => 'required|string',
        'email' => 'required|email:filter|unique:users,email',
        'password' => 'nullable|same:password_confirmation|min:6',
        'marital_status_id' => 'nullable',
        'nationality' => 'nullable',
        'country_id' => 'required',
        'state_id' => 'required',
        'city_id' => 'required',
        'phone' => 'required|numeric',
        'experience' => 'nullable',
        'career_level_id' => 'nullable',
        'industry_id' => 'nullable',
        'functional_area_id' => 'nullable',
        'current_salary' => 'nullable|numeric',
        'expected_salary' => 'nullable|numeric',
    ];

    /**
     * @var array
     */
    public $translatable = ['father_name', 'nationality', 'national_id_card', 'experience', 'current_salary', 'expected_salary'];

    protected $appends = ['country_name', 'state_name', 'city_name', 'full_location', 'candidate_url'];

    protected $with = ['user'];

    public function getCountryNameAttribute()
    {
        if (! empty($this->user->country)) {
            return $this->user->country->name;
        }
    }

    public function getStateNameAttribute()
    {
        if (! empty($this->user->state)) {
            return $this->user->state->name;
        }
    }

    public function getCityNameAttribute()
    {
        if (! empty($this->user->city)) {
            return $this->user->city->name;
        }
    }

    public function getFullLocationAttribute(): string
    {
        $location = '';
        if (! empty($this->user->country)) {
            $location = $this->user->country->name;
        }
        if (! empty($this->user->state)) {
            $location = $location.','.$this->user->state->name;
        }
        if (! empty($this->user->city)) {
            $location = $location.','.$this->user->city->name;
        }

        return (! empty($location)) ? $location : '' ;
    }

    /**
     * @return mixed
     */
    public function getCandidateUrlAttribute()
    {
        $fileService = new FileService();
        
        if (!empty($this->image_path)) {
            return $fileService->getFileUrl($this->image_path);
        }
        
        return asset('assets/img/candidate-default-profile.png');
    }

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * @return HasMany
     */
    public function candidateEducation(): HasMany
    {
        return $this->hasMany(CandidateEducation::class, 'candidate_id');
    }

    /**
     * @return HasMany
     */
    public function candidateExperience(): HasMany
    {
        return $this->hasMany(CandidateExperience::class, 'candidate_id');
    }

    /**
     * @return BelongsToMany
     */
    public function jobAlerts(): BelongsToMany
    {
        return $this->belongsToMany(JobType::class, 'candidate_job_alerts', 'candidate_id', 'job_type_id');
    }

    /**
     * Get the resume URL
     * 
     * @return string|null
     */
    public function getResumeUrl(): ?string
    {
        $fileService = new FileService();
        
        if (!empty($this->resume_path)) {
            return $fileService->getFileUrl($this->resume_path);
        }
        
        return null;
    }
    
    /**
     * Upload a profile image
     * 
     * @param UploadedFile $file
     * @return bool
     */
    public function uploadProfileImage(UploadedFile $file): bool
    {
        $fileService = new FileService();
        
        // Delete old image if exists
        if (!empty($this->image_path)) {
            $fileService->deleteFile($this->image_path);
        }
        
        // Upload new image
        $imagePath = $fileService->uploadFile(
            $file, 
            self::IMAGE_PATH,
            'public'
        );
        
        $this->image_path = $imagePath;
        
        return $this->save();
    }
    
    /**
     * Upload a resume
     * 
     * @param UploadedFile $file
     * @param string $title
     * @return bool
     */
    public function uploadResume(UploadedFile $file, string $title = 'resume'): bool
    {
        $fileService = new FileService();
        
        // Delete old resume if exists
        if (!empty($this->resume_path)) {
            $fileService->deleteFile($this->resume_path);
        }
        
        // Upload new resume
        $resumePath = $fileService->uploadFile(
            $file, 
            self::RESUME_PATH,
            'public',
            uniqid() . '_' . $title . '.' . $file->getClientOriginalExtension()
        );
        
        $this->resume_path = $resumePath;
        
        return $this->save();
    }
}
