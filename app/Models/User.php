<?php

namespace App\Models;

use App\Notifications\PasswordReset;
use App\Notifications\UserVerifyNotification;
use App\Traits\HasFiles;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Laravel\Cashier\Billable;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Permission\Traits\HasRoles;

/**
 * App\Models\User
 *
 * @property int $id
 * @property string $first_name
 * @property string|null $last_name
 * @property string $email
 * @property string|null $phone
 * @property Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $dob
 * @property int|null $gender
 * @property string|null $country
 * @property string|null $state
 * @property string|null $city
 * @property int $is_active
 * @property int $is_verified
 * @property int|null $owner_id
 * @property string|null $owner_type
 * @property string|null $remember_token
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read \App\Models\Candidate|null $candidate
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Skill[] $candidateSkill
 * @property-read int|null $candidate_skill_count
 * @property-read mixed $avatar
 * @property-read string $full_name
 * @property-read \Illuminate\Database\Eloquent\Collection|\Illuminate\Notifications\DatabaseNotification[]
 *     $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\Spatie\Permission\Models\Permission[] $permissions
 * @property-read int|null $permissions_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\Spatie\Permission\Models\Role[] $roles
 * @property-read int|null $roles_count
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User permission($permissions)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User role($roles, $guard = null)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereCountry($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereDob($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereGender($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereIsVerified($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereLastName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereOwnerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereOwnerType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereState($value)
 *
 * @property-read \App\Models\Company|null $company
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 *
 * @property string $language
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Language[] $candidateLanguage
 * @property-read int|null $candidate_language_count
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereLanguage($value)
 *
 * @property int|null $country_id
 * @property int|null $state_id
 * @property int|null $city_id
 * @property int $profile_views
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\FavouriteCompany[] $followings
 * @property-read int|null $followings_count
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereCityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereCountryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereProfileViews($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereStateId($value)
 *
 * @property-read mixed $city_name
 * @property-read mixed $country_name
 * @property-read mixed $state_name
 * @property string|null $facebook_url
 * @property string|null $twitter_url
 * @property string|null $linkedin_url
 * @property string|null $google_plus_url
 * @property string|null $pinterest_url
 * @property int $is_default
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereFacebookUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereGooglePlusUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereIsDefault($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereLinkedinUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User wherePinterestUrl($value)
 *
 * @property string|null $stripe_id
 * @property-read \Illuminate\Database\Eloquent\Collection|\Laravel\Cashier\Subscription[] $subscriptions
 * @property-read int|null $subscriptions_count
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereStripeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereTwitterUrl($value)
 *
 * @property string|null $region_code
 * @property-read bool $is_online_profile_availbal
 *
 * @method static \Illuminate\Database\Eloquent\Builder|User whereRegionCode($value)
 */
class User extends Authenticatable implements HasMedia
{
    use HasApiTokens;
    use Billable;
    use HasFactory;
    use HasFiles;
    use HasRoles;
    use InteractsWithMedia;
    use Notifiable;

    const DARK_MODE = 1;

    const LIGHT_MODE = 0;

    const PROFILE = 'profile-pictures';

    const ACTIVE = 1;

    // User Types
    const ADMIN = 'admin';
    const EMPLOYER = 'employer';
    const CANDIDATE = 'candidate';

    const LANGUAGES = [
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

    const LANGUAGES_IMAGE = [
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
     * @var array
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
        'email_verified_at',
        'owner_id',
        'owner_type',
        'language',
        'facebook_url',
        'twitter_url',
        'linkedin_url',
        'google_plus_url',
        'pinterest_url',
        'is_default',
        'profile_views',
        'region_code',
    ];

    /**
     * @var array
     */
    protected $appends = ['full_name', 'avatar', 'country_name', 'state_name', 'city_name'];

    protected $with = [];

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
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'id' => 'integer',
            'first_name' => 'string',
            'last_name' => 'string',
            'name' => 'string',
            'email' => 'string',
            'phone' => 'string',
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'is_verified' => 'boolean',
            'is_featured' => 'boolean',
            'last_login_at' => 'datetime',
            'last_seen_at' => 'datetime',
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

        // Clear cache when user is updated (only if cache is available)
        static::updated(function ($user) {
            if (app()->bound('cache')) {
                cache()->forget("user.{$user->id}");
                cache()->forget("user.profile.{$user->id}");
            }
        });
    }

    /**
     * Get the user's country with caching.
     */
    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class)->withDefault();
    }

    /**
     * Get the user's state with caching.
     */
    public function state(): BelongsTo
    {
        return $this->belongsTo(State::class)->withDefault();
    }

    /**
     * Get the user's city with caching.
     */
    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class)->withDefault();
    }

    /**
     * Get cached country name.
     */
    public function getCountryNameAttribute(): ?string
    {
        if (!app()->bound('cache') || app()->environment('testing')) {
            return $this->country?->name;
        }
        
        return cache()->remember("user.{$this->id}.country_name", 3600, function () {
            return $this->country?->name;
        });
    }

    /**
     * Get cached state name.
     */
    public function getStateNameAttribute(): ?string
    {
        return cache()->remember("user.{$this->id}.state_name", 3600, function () {
            return $this->state?->name;
        });
    }

    /**
     * Get cached city name.
     */
    public function getCityNameAttribute(): ?string
    {
        return cache()->remember("user.{$this->id}.city_name", 3600, function () {
            return $this->city?->name;
        });
    }

    /**
     * Get the user's avatar with optimized file handling.
     */
    public function getAvatarAttribute(): string
    {
        return cache()->remember("user.{$this->id}.avatar", 3600, function () {
            if ($this->hasMedia(self::PROFILE)) {
                return $this->getFirstMediaUrl(self::PROFILE);
            }
            
            return asset('assets/img/infyom-logo.png');
        });
    }

    /**
     * Get the user's full name.
     */
    public function getFullNameAttribute(): string
    {
        return trim("{$this->first_name} {$this->last_name}");
    }

    /**
     * Get the user's candidate profile.
     */
    public function candidate(): HasOne
    {
        return $this->hasOne(Candidate::class)->withDefault();
    }

    /**
     * Get the user's company profile.
     */
    public function company(): HasOne
    {
        return $this->hasOne(Company::class)->withDefault();
    }

    /**
     * Get the user's skills with efficient loading.
     */
    public function candidateSkill(): BelongsToMany
    {
        return $this->belongsToMany(Skill::class, 'candidate_skills', 'user_id', 'skill_id');
    }

    /**
     * Get the user's languages with efficient loading.
     */
    public function candidateLanguage(): BelongsToMany
    {
        return $this->belongsToMany(Language::class, 'candidate_languages', 'user_id', 'language_id');
    }

    /**
     * Get the companies this user follows.
     */
    public function followings(): HasMany
    {
        return $this->hasMany(FavouriteCompany::class);
    }

    /**
     * Check if user has online profile available.
     */
    public function getIsOnlineProfileAvailbalAttribute(): bool
    {
        return cache()->remember("user.{$this->id}.online_profile", 3600, function () {
            return $this->candidate && 
                   ($this->candidate->career_level_id || 
                    $this->candidate->functional_area_id || 
                    $this->candidateSkill()->exists());
        });
    }

    /**
     * Scope for active users.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for inactive users.
     */
    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    /**
     * Scope for verified users.
     */
    public function scopeVerified($query)
    {
        return $query->whereNotNull('email_verified_at');
    }

    /**
     * Scope for unverified users.
     */
    public function scopeUnverified($query)
    {
        return $query->whereNull('email_verified_at');
    }

    /**
     * Scope for featured users.
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope for non-featured users.
     */
    public function scopeNotFeatured($query)
    {
        return $query->where('is_featured', false);
    }

    /**
     * Scope for users by role.
     */
    public function scopeByRole($query, string $role)
    {
        return $query->whereHas('roles', function ($q) use ($role) {
            $q->where('name', $role);
        });
    }

    /**
     * Scope for admin users.
     */
    public function scopeAdmins($query)
    {
        return $query->byRole('admin');
    }

    /**
     * Scope for candidate users.
     */
    public function scopeCandidates($query)
    {
        return $query->byRole('candidate');
    }

    /**
     * Scope for employer users.
     */
    public function scopeEmployers($query)
    {
        return $query->byRole('employer');
    }

    /**
     * Scope for searching users.
     */
    public function scopeSearch($query, string $term)
    {
        return $query->where('first_name', 'like', "%{$term}%")
                    ->orWhere('last_name', 'like', "%{$term}%")
                    ->orWhere('name', 'like', "%{$term}%")
                    ->orWhere('email', 'like', "%{$term}%")
                    ->orWhere('phone', 'like', "%{$term}%");
    }

    /**
     * Scope for recent users.
     */
    public function scopeRecent($query, int $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Scope for old users.
     */
    public function scopeOld($query, int $days = 365)
    {
        return $query->where('created_at', '<', now()->subDays($days));
    }

    /**
     * Scope for online users (last seen within minutes).
     */
    public function scopeOnline($query, int $minutes = 5)
    {
        return $query->where('last_seen_at', '>=', now()->subMinutes($minutes));
    }

    /**
     * Scope for offline users.
     */
    public function scopeOffline($query, int $minutes = 5)
    {
        return $query->where('last_seen_at', '<', now()->subMinutes($minutes))
                    ->orWhereNull('last_seen_at');
    }

    /**
     * Scope for users who logged in recently.
     */
    public function scopeRecentlyLoggedIn($query, int $days = 7)
    {
        return $query->where('last_login_at', '>=', now()->subDays($days));
    }

    /**
     * Scope for users who haven't logged in recently.
     */
    public function scopeInactive($query, int $days = 30)
    {
        return $query->where('last_login_at', '<', now()->subDays($days))
                    ->orWhereNull('last_login_at');
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
     * Scope for users with profile.
     */
    public function scopeWithProfile($query)
    {
        return $query->whereHas('candidate')
                    ->orWhereHas('company');
    }

    /**
     * Scope for users without profile.
     */
    public function scopeWithoutProfile($query)
    {
        return $query->whereDoesntHave('candidate')
                    ->whereDoesntHave('company');
    }

    /**
     * Scope for users with subscriptions.
     */
    public function scopeWithSubscriptions($query)
    {
        return $query->has('subscriptions');
    }

    /**
     * Scope for users with active subscriptions.
     */
    public function scopeWithActiveSubscriptions($query)
    {
        return $query->whereHas('subscriptions', function ($q) {
            $q->where('status', 'active')
              ->where('expires_at', '>', now());
        });
    }

    /**
     * Scope for users by country.
     */
    public function scopeByCountry($query, int $countryId)
    {
        return $query->whereHas('candidate', function ($q) use ($countryId) {
            $q->where('country_id', $countryId);
        })->orWhereHas('company', function ($q) use ($countryId) {
            $q->where('country_id', $countryId);
        });
    }

    /**
     * Scope for users by state.
     */
    public function scopeByState($query, int $stateId)
    {
        return $query->whereHas('candidate', function ($q) use ($stateId) {
            $q->where('state_id', $stateId);
        })->orWhereHas('company', function ($q) use ($stateId) {
            $q->where('state_id', $stateId);
        });
    }

    /**
     * Scope for users by city.
     */
    public function scopeByCity($query, int $cityId)
    {
        return $query->whereHas('candidate', function ($q) use ($cityId) {
            $q->where('city_id', $cityId);
        })->orWhereHas('company', function ($q) use ($cityId) {
            $q->where('city_id', $cityId);
        });
    }

    /**
     * Scope for premium users.
     */
    public function scopePremium($query)
    {
        return $query->withActiveSubscriptions();
    }

    /**
     * Scope for free users.
     */
    public function scopeFree($query)
    {
        return $query->whereDoesntHave('subscriptions', function ($q) {
            $q->where('status', 'active')
              ->where('expires_at', '>', now());
        });
    }

    /**
     * Send email verification notification.
     */
    public function sendEmailVerificationNotification(): void
    {
        $this->notify(new UserVerifyNotification());
    }

    /**
     * Send password reset notification.
     */
    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new PasswordReset($token));
    }

    /**
     * Check if user can perform action.
     */
    public function canPerformAction(string $action): bool
    {
        return match($action) {
            'create_job' => $this->hasRole('employer') && $this->is_active,
            'apply_job' => $this->hasRole('candidate') && $this->is_active,
            'manage_users' => $this->hasRole('admin'),
            default => false
        };
    }
}
