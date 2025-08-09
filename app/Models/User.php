<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class User extends Authenticatable implements MustVerifyEmail, FilamentUser
{
    use HasFactory, Notifiable;

    // User type constants
    public const ADMIN = 'admin';
    public const EMPLOYER = 'employer';
    public const CANDIDATE = 'candidate';

    // Languages
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

    // Misc constants
    public const PROFILE = 'profile-pictures';
    public const DARK_MODE = 1;
    public const LIGHT_MODE = 0;
    public const ACTIVE = 1;

    protected $fillable = [
        'name',
        'first_name',
        'last_name',
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

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'user_type' => 'string',
        'is_active' => 'boolean',
        'is_verified' => 'boolean',
        'is_default' => 'boolean',
        'email_verified_at' => 'datetime',
        'dob' => 'date',
        'password' => 'hashed',
    ];

    public function getFullNameAttribute(): ?string
    {
        if ($this->first_name || $this->last_name) {
            return trim(($this->first_name ?? '').' '.($this->last_name ?? ''));
        }
        return null;
    }

    public function canAccessPanel(Panel $panel): bool
    {
        if ($panel->getId() === 'admin') {
            return $this->hasVerifiedEmail();
        }

        return true;
    }

    public function skills(): BelongsToMany
    {
        return $this->belongsToMany(Skill::class, 'candidate_skills', 'user_id', 'skill_id')->withTimestamps();
    }

    public function company(): HasOne
    {
        return $this->hasOne(Company::class);
    }

    // Legacy test relationship method stubs
    public function country() {}
    public function state() {}
    public function city() {}
    public function candidate() {}
    public function candidateSkill() {}
    public function candidateLanguage() {}
    public function followings() {}
}


