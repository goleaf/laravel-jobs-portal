<?php

namespace App\Models;

use App\Traits\HasFiles;
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
use Tymon\JWTAuth\Contracts\JWTSubject;

/**
 * App\Models\User.
 *
 * @property int                               $id
 * @property string                            $first_name
 * @property null|string                       $last_name
 * @property string                            $email
 * @property null|string                       $phone
 * @property null|Carbon                       $email_verified_at
 * @property string                            $password
 * @property null|string                       $dob
 * @property null|int                          $gender
 * @property null|string                       $country
 * @property null|string                       $state
 * @property null|string                       $city
 * @property int                               $is_active
 * @property int                               $is_verified
 * @property null|int                          $owner_id
 * @property null|string                       $owner_type
 * @property null|string                       $remember_token
 * @property null|Carbon                       $created_at
 * @property null|Carbon                       $updated_at
 * @property null|Candidate                    $candidate
 * @property Collection|Skill[]                $candidateSkill
 * @property null|int                          $candidate_skill_count
 * @property mixed                             $avatar
 * @property string                            $full_name
 * @property Collection|DatabaseNotification[] $notifications
 * @property null|int                          $notifications_count
 * @property Collection|Permission[]           $permissions
 * @property null|int                          $permissions_count
 * @property Collection|Role[]                 $roles
 * @property null|int                          $roles_count
 */
class User extends Authenticatable implements HasMedia, JWTSubject
{
    use HasApiTokens;
    use Billable;
    use HasFactory;
    use HasFiles;
    use HasRoles;
    use InteractsWithMedia;
    use Notifiable;
    use SoftDeletes;

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
        'password',
        'user_type',
        'dob',
        'gender',
        'country_id',
        'state_id',
        'city_id',
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
     * @param Builder $query
     *
     * @return Builder
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include inactive users.
     *
     * @param Builder $query
     *
     * @return Builder
     */
    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    /**
     * Scope a query to only include admin users.
     *
     * @param Builder $query
     *
     * @return Builder
     */
    public function scopeAdmin($query)
    {
        return $query->where('is_admin', true);
    }

    /**
     * Scope a query to only include users with a specific role.
     *
     * @param Builder $query
     * @param string  $role
     *
     * @return Builder
     */
    public function scopeWithRole($query, $role)
    {
        return $query->where('role', $role);
    }

    /**
     * Scope a query to only include users verified by email.
     *
     * @param Builder $query
     *
     * @return Builder
     */
    public function scopeVerified($query)
    {
        return $query->whereNotNull('email_verified_at');
    }

    /**
     * Scope a query to only include unverified users.
     *
     * @param Builder $query
     *
     * @return Builder
     */
    public function scopeUnverified($query)
    {
        return $query->whereNull('email_verified_at');
    }

    /**
     * Scope a query to only include users who logged in recently.
     *
     * @param Builder        $query
     * @param \Carbon\Carbon $date
     *
     * @return Builder
     */
    public function scopeRecentlyActive($query, $date)
    {
        return $query->where('last_login_at', '>=', $date);
    }

    /**
     * Scope a query to search users by name or email.
     *
     * @param Builder $query
     * @param string  $search
     *
     * @return Builder
     */
    public function scopeSearch($query, $search)
    {
        return $query->where('name', 'like', '%'.$search.'%')
            ->orWhere('email', 'like', '%'.$search.'%')
            ->orWhere('first_name', 'like', '%'.$search.'%')
            ->orWhere('last_name', 'like', '%'.$search.'%')
        ;
    }

    /**
     * Scope a query to order users by creation date.
     *
     * @param Builder $query
     * @param string  $direction
     *
     * @return Builder
     */
    public function scopeOrderByCreated($query, $direction = 'desc')
    {
        return $query->orderBy('created_at', $direction);
    }

    /**
     * Scope a query to order users by last login.
     *
     * @param Builder $query
     * @param string  $direction
     *
     * @return Builder
     */
    public function scopeOrderByLastLogin($query, $direction = 'desc')
    {
        return $query->orderBy('last_login_at', $direction);
    }

    /**
     * Scope a query to only include users created within a date range.
     *
     * @param Builder        $query
     * @param \Carbon\Carbon $start
     * @param \Carbon\Carbon $end
     *
     * @return Builder
     */
    public function scopeCreatedBetween($query, $start, $end)
    {
        return $query->whereBetween('created_at', [$start, $end]);
    }

    /**
     * Scope a query to only include users with a specific status.
     *
     * @param Builder $query
     * @param string  $status
     *
     * @return Builder
     */
    public function scopeWithStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope a query to only include featured users.
     *
     * @param Builder $query
     *
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
     * @param Builder $query
     *
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
     * @param Builder $query
     *
     * @return Builder
     */
    public function scopeByRole($query, string $role)
    {
        return $query->where('user_type', $role);
    }

    /**
     * Scope a query to only include admin users.
     *
     * @param Builder $query
     *
     * @return Builder
     */
    public function scopeAdmins($query)
    {
        return $query->where('user_type', self::ADMIN);
    }

    /**
     * Scope a query to only include candidate users.
     *
     * @param Builder $query
     *
     * @return Builder
     */
    public function scopeCandidates($query)
    {
        return $query->where('user_type', self::CANDIDATE);
    }

    /**
     * Scope a query to only include employer users.
     *
     * @param Builder $query
     *
     * @return Builder
     */
    public function scopeEmployers($query)
    {
        return $query->where('user_type', self::EMPLOYER);
    }

    /**
     * Scope a query to only include recent users.
     *
     * @param Builder $query
     *
     * @return Builder
     */
    public function scopeRecent($query, int $days = 30)
    {
        return $query->where('created_at', '>=', Carbon::now()->subDays($days));
    }

    /**
     * Scope a query to only include old users.
     *
     * @param Builder $query
     *
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
}
