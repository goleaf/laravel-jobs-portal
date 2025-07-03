<?php

namespace App\Models;

use App\Traits\HasDeepJobPortalRelationships;
use App\Traits\HasFiles;
use Glorand\Model\Settings\Traits\HasSettingsField;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Laravel\Cashier\Billable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Traits\HasRoles;
use Staudenmeir\EloquentHasManyDeep\HasRelationships;
use Tymon\JWTAuth\Contracts\JWTSubject;

/**
 * App\Models\User.
 *
 * @property int $id
 * @property string $first_name
 * @property null|string $last_name
 * @property string $email
 * @property null|string $phone
 * @property null|Carbon $email_verified_at
 * @property string $password
 * @property null|string $dob
 * @property null|int $gender
 * @property null|string $country
 * @property null|string $state
 * @property null|string $city
 * @property int $is_active
 * @property int $is_verified
 * @property null|int $owner_id
 * @property null|string $owner_type
 * @property null|string $remember_token
 * @property null|Carbon $created_at
 * @property null|Carbon $updated_at
 * @property null|array $settings
 * @property null|Candidate $candidate
 * @property Collection|Skill[] $candidateSkill
 * @property null|int $candidate_skill_count
 * @property mixed $avatar
 * @property string $full_name
 * @property Collection|DatabaseNotification[] $notifications
 * @property null|int $notifications_count
 * @property Collection|Permission[] $permissions
 * @property null|int $permissions_count
 * @property Collection|Role[] $roles
 * @property null|int $roles_count
 *
 * // Deep Relationships Added
 * @property Collection|Job[] $locationJobs Jobs in user's location (country->state->city->jobs)
 * @property Collection|JobApplication[] $companyApplications Applications to user's company jobs
 * @property Collection|User[] $regionCandidates Other candidates in same region
 * @property Collection|Job[] $industryJobs Jobs in user's company industry
 */
class User extends Authenticatable implements HasMedia, JWTSubject
{
    use Billable;
    use HasApiTokens;
    use HasDeepJobPortalRelationships;
    use HasFactory;
    use HasFiles;
    use HasRelationships;
    use HasRoles;
    use HasSettingsField;
    use InteractsWithMedia;
    use Notifiable;
    use SoftDeletes;
    use \Staudenmeir\EloquentHasManyDeep\HasRelationships;

    public const DARK_MODE = 1;
    public const LIGHT_MODE = 0;
    public const PROFILE = 'profile-pictures';
    public const ACTIVE = 1;

    // User Types
    public const ADMIN = 'admin';
    public const EMPLOYER = 'employer';
    public const CANDIDATE = 'candidate';

    public const LANGUAGES = [
        'ar' => 'Arabic',
        'zh' => 'Chinese',
        'en' => 'English',
        'fr' => 'French',
        'de' => 'German',
        'pt' => 'Portuguese',
        'ru' => 'Russian',
        'es' => 'Spanish',
        'tr' => 'Turkish',
    ];

    public const LANGUAGES_IMAGE = [
        'en' => 'assets/img/united-states.svg',
        'es' => 'assets/img/spain.svg',
        'fr' => 'assets/img/france.svg',
        'de' => 'assets/img/germany.svg',
        'ru' => 'assets/img/russia.svg',
        'pt' => 'assets/img/portugal.svg',
        'ar' => 'assets/img/iraq.svg',
        'zh' => 'assets/img/china.svg',
        'tr' => 'assets/img/turkey.svg',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'name', // Test compatibility field
        'email',
        'email_verified_at', // Added for mass assignment compatibility
        'password',
        'user_type',
        'dob',
        'gender',
        'country_id',
        'state_id',
        'city_id',
        'company_id', // Added for mass assignment compatibility
        'is_active',
        'is_verified',
        'phone',
        'country',
        'state',
        'city',
        'owner_id',
        'owner_type',
        'language',
        'profile_views',
        'facebook_url',
        'twitter_url',
        'linkedin_url',
        'google_plus_url',
        'pinterest_url',
        'is_default',
        'stripe_id',
        'region_code',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Scope a query to only include active users.
     *
     * @param  Builder  $query
     * @return Builder
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include inactive users.
     *
     * @param  Builder  $query
     * @return Builder
     */
    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    /**
     * Scope a query to only include admin users.
     *
     * @param  Builder  $query
     * @return Builder
     */
    public function scopeAdmin($query)
    {
        return $query->where('is_admin', true);
    }

    /**
     * Scope a query to only include users with a specific role.
     *
     * @param  Builder  $query
     * @param  string  $role
     * @return Builder
     */
    public function scopeWithRole($query, $role)
    {
        return $query->where('role', $role);
    }

    /**
     * Scope a query to only include users verified by email.
     *
     * @param  Builder  $query
     * @return Builder
     */
    public function scopeVerified($query)
    {
        return $query->whereNotNull('email_verified_at');
    }

    /**
     * Scope a query to only include unverified users.
     *
     * @param  Builder  $query
     * @return Builder
     */
    public function scopeUnverified($query)
    {
        return $query->whereNull('email_verified_at');
    }

    /**
     * Scope a query to only include users who logged in recently.
     *
     * @param  Builder  $query
     * @param  \Carbon\Carbon  $date
     * @return Builder
     */
    public function scopeRecentlyActive($query, $date)
    {
        return $query->where('last_login_at', '>=', $date);
    }

    /**
     * Scope a query to search users by name or email.
     *
     * @param  Builder  $query
     * @param  string  $search
     * @return Builder
     */
    public function scopeSearch($query, $search)
    {
        return $query->where('name', 'like', '%'.$search.'%')
            ->orWhere('email', 'like', '%'.$search.'%')
            ->orWhere('first_name', 'like', '%'.$search.'%')
            ->orWhere('last_name', 'like', '%'.$search.'%');
    }

    /**
     * Scope a query to order users by creation date.
     *
     * @param  Builder  $query
     * @param  string  $direction
     * @return Builder
     */
    public function scopeOrderByCreated($query, $direction = 'desc')
    {
        return $query->orderBy('created_at', $direction);
    }

    /**
     * Scope a query to order users by last login.
     *
     * @param  Builder  $query
     * @param  string  $direction
     * @return Builder
     */
    public function scopeOrderByLastLogin($query, $direction = 'desc')
    {
        return $query->orderBy('last_login_at', $direction);
    }

    /**
     * Scope a query to only include users created within a date range.
     *
     * @param  Builder  $query
     * @param  \Carbon\Carbon  $start
     * @param  \Carbon\Carbon  $end
     * @return Builder
     */
    public function scopeCreatedBetween($query, $start, $end)
    {
        return $query->whereBetween('created_at', [$start, $end]);
    }

    /**
     * Scope a query to only include users with a specific status.
     *
     * @param  Builder  $query
     * @param  string  $status
     * @return Builder
     */
    public function scopeWithStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope a query to only include featured users.
     *
     * @param  Builder  $query
     * @return Builder
     */
    public function scopeFeatured($query)
    {
        return $query->whereHas('candidate', function ($q) {
            $q->where('is_featured', true);
        });
    }

    /**
     * Scope a query to only include non-featured users.
     *
     * @param  Builder  $query
     * @return Builder
     */
    public function scopeNotFeatured($query)
    {
        return $query->whereHas('candidate', function ($q) {
            $q->where('is_featured', false);
        });
    }

    /**
     * Scope a query to only include users by role.
     *
     * @param  Builder  $query
     * @return Builder
     */
    public function scopeByRole($query, string $role)
    {
        return $query->where('user_type', $role);
    }

    /**
     * Scope a query to only include admin users.
     *
     * @param  Builder  $query
     * @return Builder
     */
    public function scopeAdmins($query)
    {
        return $query->where('user_type', self::ADMIN);
    }

    /**
     * Scope a query to only include candidate users.
     *
     * @param  Builder  $query
     * @return Builder
     */
    public function scopeCandidates($query)
    {
        return $query->where('user_type', self::CANDIDATE);
    }

    /**
     * Scope a query to only include employer users.
     *
     * @param  Builder  $query
     * @return Builder
     */
    public function scopeEmployers($query)
    {
        return $query->where('user_type', self::EMPLOYER);
    }

    /**
     * Scope a query to only include recent users.
     *
     * @param  Builder  $query
     * @return Builder
     */
    public function scopeRecent($query, int $days = 30)
    {
        return $query->where('created_at', '>=', Carbon::now()->subDays($days));
    }

    /**
     * Scope a query to only include old users.
     *
     * @param  Builder  $query
     * @return Builder
     */
    public function scopeOld($query, int $days = 365)
    {
        return $query->where('created_at', '<', now()->subDays($days));
    }

    // Relationships

    /**
     * Get the candidate associated with the user.
     */
    public function candidate()
    {
        return $this->hasOne(Candidate::class);
    }

    /**
     * Get the company associated with the user.
     */
    public function company()
    {
        return $this->hasOne(Company::class);
    }

    /**
     * Get the country that the user belongs to.
     */
    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    /**
     * Get the state that the user belongs to.
     */
    public function state()
    {
        return $this->belongsTo(State::class);
    }

    /**
     * Get the city that the user belongs to.
     */
    public function city()
    {
        return $this->belongsTo(City::class);
    }

    /**
     * Get the skills associated with the candidate user.
     */
    public function candidateSkill()
    {
        return $this->belongsToMany(Skill::class, 'candidate_skills', 'user_id', 'skill_id');
    }

    // =============================================
    // ACCESSOR METHODS
    // =============================================

    /**
     * Get the user's full name.
     */
    public function getFullNameAttribute(): string
    {
        return trim($this->first_name.' '.$this->last_name);
    }

    // =============================================
    // JWT IMPLEMENTATION
    // =============================================

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_featured' => 'boolean',

            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'is_active' => 'boolean',
            'is_admin' => 'boolean',
            'last_login_at' => 'datetime',
            'is_verified' => 'boolean',
            'is_default' => 'boolean',
            'dob' => 'date',
            'profile_views' => 'integer',
            'gender' => 'integer',
        ];
    }

    // Additional scopes and methods from the original file can be added here as needed for the job portal project

    /**
     * DEEP RELATIONSHIPS USING ELOQUENT HAS MANY DEEP
     *
     * Package: staudenmeir/eloquent-has-many-deep v1.21
     * Source: https://github.com/staudenmeir/eloquent-has-many-deep
     * Reference: https://madewithlaravel.com/eloquent-has-many-deep
     *
     * These methods provide complex multi-level relationships
     * for advanced querying in the job portal system.
     */

    /**
     * Get all jobs in the user's location (Country->State->City->Jobs).
     *
     * This allows finding all jobs available in a user's location
     * without complex joins or multiple queries.
     *
     * Usage: $user->locationJobs()->where('is_active', true)->get()
     *
     * @return \Staudenmeir\EloquentHasManyDeep\HasManyDeep
     */
    public function locationJobs()
    {
        return $this->hasManyDeep(
            \App\Models\Job::class,
            [\App\Models\Country::class, \App\Models\State::class, \App\Models\City::class],
            [
                'id',        // User.country_id = Country.id
                'country_id', // Country.id = State.country_id
                'state_id',  // State.id = City.state_id
                'city_id',    // City.id = Job.city_id
            ],
            [
                'country_id', // User.country_id
                'id',        // Country.id
                'id',        // State.id
                'id',         // City.id
            ]
        );
    }

    /**
     * Get all job applications for jobs in user's company.
     *
     * Path: User -> Company -> Jobs -> JobApplications
     * Perfect for employers to see all applications across all their jobs.
     *
     * Usage: $employer->companyJobApplications()->with('candidate')->get()
     *
     * @return \Staudenmeir\EloquentHasManyDeep\HasManyDeep
     */
    public function companyJobApplications()
    {
        return $this->hasManyDeep(
            \App\Models\JobApplication::class,
            [\App\Models\Company::class, \App\Models\Job::class],
            [
                'id',         // User.id = Company.user_id
                'company_id', // Company.id = Job.company_id
                'job_id',      // Job.id = JobApplication.job_id
            ],
            [
                'id',         // User.id
                'user_id',    // Company.user_id
                'id',          // Job.id
            ]
        );
    }

    /**
     * Get all candidates in the same region.
     *
     * Path: User -> Country -> State -> City -> Users (Candidates)
     * Useful for finding local talent or networking.
     *
     * Usage: $user->regionCandidates()->active()->get()
     *
     * @return \Staudenmeir\EloquentHasManyDeep\HasManyDeep
     */
    public function regionCandidates()
    {
        return $this->hasManyDeep(
            \App\Models\User::class,
            [\App\Models\Country::class, \App\Models\State::class, \App\Models\City::class],
            [
                'id',        // User.country_id = Country.id
                'country_id', // Country.id = State.country_id
                'state_id',  // State.id = City.state_id
                'city_id',    // City.id = User.city_id
            ],
            [
                'country_id', // User.country_id
                'id',        // Country.id
                'id',        // State.id
                'id',         // City.id
            ]
        )->where('users.id', '!=', $this->id)
            ->whereHas('candidate'); // Only candidates
    }

    /**
     * Get all skills through job applications.
     *
     * Path: User -> JobApplications -> Jobs -> JobSkills -> Skills
     * Shows all skills required for jobs a candidate has applied to.
     *
     * Usage: $candidate->appliedJobSkills()->distinct()->get()
     *
     * @return \Staudenmeir\EloquentHasManyDeep\HasManyDeep
     */
    public function appliedJobSkills()
    {
        return $this->hasManyDeep(
            \App\Models\Skill::class,
            [\App\Models\JobApplication::class, \App\Models\Job::class, 'job_skill'],
            [
                'id',      // User.id = JobApplication.candidate_id
                'job_id',  // JobApplication.job_id = Job.id
                'job_id',  // Job.id = job_skill.job_id
                'skill_id', // job_skill.skill_id = Skill.id
            ],
            [
                'id',          // User.id
                'candidate_id', // JobApplication.candidate_id
                'id',          // Job.id
                'id',           // Skill.id
            ]
        );
    }

    /**
     * Get similar candidates who applied to same jobs.
     *
     * Path: User -> JobApplications -> Jobs -> JobApplications -> Users
     * Great for networking with candidates who have similar interests.
     *
     * Usage: $candidate->similarCandidates()->limit(10)->get()
     *
     * @return \Staudenmeir\EloquentHasManyDeep\HasManyDeep
     */
    public function similarCandidates()
    {
        return $this->hasManyDeep(
            \App\Models\User::class,
            [\App\Models\JobApplication::class, \App\Models\Job::class, \App\Models\JobApplication::class],
            [
                'id',          // User.id = JobApplication.candidate_id
                'job_id',      // JobApplication.job_id = Job.id
                'id',          // Job.id = JobApplication.job_id
                'candidate_id', // JobApplication.candidate_id = User.id
            ],
            [
                'id',          // User.id
                'candidate_id', // JobApplication.candidate_id
                'id',          // Job.id
                'job_id',       // JobApplication.job_id
            ]
        )->where('users.id', '!=', $this->id) // Exclude self
            ->distinct(); // Avoid duplicates
    }

    /**
     * Default settings for User model.
     *
     * These settings provide user preferences and configuration options
     * that can be customized per user without database schema changes.
     *
     * @return array
     */
    public $defaultSettings = [
        'profile' => [
            'theme' => 'light',
            'language' => 'en',
            'timezone' => 'UTC',
            'notifications_enabled' => true,
            'email_notifications' => true,
            'sms_notifications' => false,
            'profile_visibility' => 'public',
            'show_email' => false,
            'show_phone' => false,
        ],
        'job_preferences' => [
            'job_alerts' => true,
            'preferred_job_types' => [],
            'preferred_locations' => [],
            'salary_range' => [
                'min' => 0,
                'max' => 999999,
                'currency' => 'USD',
            ],
            'remote_work' => false,
            'travel_willingness' => 0, // 0-100%
        ],
        'privacy' => [
            'profile_searchable' => true,
            'allow_recruiter_contact' => true,
            'show_activity_status' => true,
            'data_sharing_consent' => false,
        ],
        'dashboard' => [
            'widgets' => [
                'recent_jobs' => true,
                'applications_status' => true,
                'profile_views' => true,
                'recommendations' => true,
            ],
            'layout' => 'grid',
            'items_per_page' => 10,
        ],
    ];

    /**
     * Validation rules for settings data.
     *
     * These rules ensure data integrity when updating user settings
     * and provide clear validation messages for the frontend.
     *
     * @return array
     */
    public $settingsRules = [
        'profile' => 'array',
        'profile.theme' => 'string|in:light,dark,auto',
        'profile.language' => 'string|in:en,es,fr,de,pt,ru,ar,zh,tr',
        'profile.timezone' => 'string|timezone',
        'profile.notifications_enabled' => 'boolean',
        'profile.email_notifications' => 'boolean',
        'profile.sms_notifications' => 'boolean',
        'profile.profile_visibility' => 'string|in:public,private,contacts',
        'profile.show_email' => 'boolean',
        'profile.show_phone' => 'boolean',

        'job_preferences' => 'array',
        'job_preferences.job_alerts' => 'boolean',
        'job_preferences.preferred_job_types' => 'array',
        'job_preferences.preferred_locations' => 'array',
        'job_preferences.salary_range' => 'array',
        'job_preferences.salary_range.min' => 'integer|min:0',
        'job_preferences.salary_range.max' => 'integer|min:0',
        'job_preferences.salary_range.currency' => 'string|size:3',
        'job_preferences.remote_work' => 'boolean',
        'job_preferences.travel_willingness' => 'integer|min:0|max:100',

        'privacy' => 'array',
        'privacy.profile_searchable' => 'boolean',
        'privacy.allow_recruiter_contact' => 'boolean',
        'privacy.show_activity_status' => 'boolean',
        'privacy.data_sharing_consent' => 'boolean',

        'dashboard' => 'array',
        'dashboard.widgets' => 'array',
        'dashboard.layout' => 'string|in:grid,list',
        'dashboard.items_per_page' => 'integer|min:5|max:100',
    ];
}
