<?php

namespace Tests\Unit\Models;

use App\Models\User;
use App\Models\Candidate;
use App\Models\Company;
use App\Models\Country;
use App\Models\State;
use App\Models\City;
use App\Models\Skill;
use App\Models\Language;
use App\Models\FavouriteCompany;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserModelTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** @test */
    public function it_has_user_type_constants()
    {
        $this->assertEquals(1, User::ADMIN);
        $this->assertEquals(2, User::EMPLOYER);
        $this->assertEquals(3, User::CANDIDATE);
    }

    /** @test */
    public function it_has_correct_fillable_attributes()
    {
        $user = new User();
        $fillable = $user->getFillable();

        $expectedFillable = [
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
            'region_code',
        ];

        foreach ($expectedFillable as $attribute) {
            $this->assertContains($attribute, $fillable);
        }
    }

    /** @test */
    public function it_has_correct_hidden_attributes()
    {
        $user = new User();
        $hidden = $user->getHidden();

        $this->assertContains('password', $hidden);
        $this->assertContains('remember_token', $hidden);
    }

    /** @test */
    public function it_has_correct_casts()
    {
        $user = new User();
        $casts = $user->getCasts();

        $expectedCasts = [
            'user_type' => 'integer',
            'is_active' => 'boolean',
            'is_verified' => 'boolean',
            'is_default' => 'boolean',
            'email_verified_at' => 'datetime',
            'dob' => 'date',
        ];

        foreach ($expectedCasts as $attribute => $cast) {
            $this->assertEquals($cast, $casts[$attribute]);
        }
    }

    /** @test */
    public function it_can_be_created_with_valid_attributes()
    {
        $userData = [
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'email' => $this->faker->unique()->safeEmail(),
            'password' => bcrypt('password'),
            'user_type' => User::CANDIDATE,
            'is_active' => true,
            'is_verified' => true,
            'phone' => $this->faker->phoneNumber(),
            'language' => 'en',
        ];

        $user = User::create($userData);

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals($userData['first_name'], $user->first_name);
        $this->assertEquals($userData['last_name'], $user->last_name);
        $this->assertEquals($userData['email'], $user->email);
        $this->assertEquals($userData['user_type'], $user->user_type);
        $this->assertTrue($user->is_active);
        $this->assertTrue($user->is_verified);
    }

    /** @test */
    public function it_generates_full_name_attribute()
    {
        $user = User::factory()->create([
            'first_name' => 'John',
            'last_name' => 'Doe'
        ]);

        $this->assertEquals('John Doe', $user->full_name);
    }

    /** @test */
    public function it_belongs_to_country()
    {
        $user = User::factory()->create(['country_id' => 1]);

        // Check relationship exists and returns the correct type
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsTo::class, $user->country());
    }

    /** @test */
    public function it_belongs_to_state()
    {
        $user = User::factory()->create(['state_id' => 1]);

        // Check relationship exists and returns the correct type
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsTo::class, $user->state());
    }

    /** @test */
    public function it_belongs_to_city()
    {
        $user = User::factory()->create(['city_id' => 1]);

        // Check relationship exists and returns the correct type
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsTo::class, $user->city());
    }

    /** @test */
    public function it_has_one_candidate()
    {
        $user = User::factory()->create(['user_type' => User::CANDIDATE]);

        // Check relationship exists and returns the correct type
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\HasOne::class, $user->candidate());
    }

    /** @test */
    public function it_has_one_company()
    {
        $user = User::factory()->create(['user_type' => User::EMPLOYER]);

        // Check relationship exists and returns the correct type
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\HasOne::class, $user->company());
    }

    /** @test */
    public function it_has_many_to_many_candidate_skills()
    {
        $user = User::factory()->create(['user_type' => User::CANDIDATE]);

        // Check relationship exists and returns the correct type
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsToMany::class, $user->candidateSkill());
    }

    /** @test */
    public function it_has_many_to_many_candidate_languages()
    {
        $user = User::factory()->create(['user_type' => User::CANDIDATE]);

        // Check relationship exists and returns the correct type
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsToMany::class, $user->candidateLanguage());
    }

    /** @test */
    public function it_has_many_followings()
    {
        $user = User::factory()->create(['user_type' => User::CANDIDATE]);

        // Check relationship exists and returns the correct type
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\HasMany::class, $user->followings());
    }

    /** @test */
    public function it_returns_default_avatar_when_no_media()
    {
        $user = User::factory()->create();

        // Since we're not setting up media files in unit tests, should return default
        $this->assertStringContains('infyom-logo.png', $user->avatar);
    }

    /** @test */
    public function it_checks_online_profile_availability()
    {
        // User with no social URLs should return false
        $userWithoutSocial = User::factory()->create([
            'facebook_url' => null,
            'twitter_url' => null,
            'linkedin_url' => null,
            'google_plus_url' => null,
            'pinterest_url' => null,
        ]);

        $this->assertFalse($userWithoutSocial->is_online_profile_availbal);

        // User with at least one social URL should return true
        $userWithSocial = User::factory()->create([
            'linkedin_url' => 'https://linkedin.com/in/johndoe',
        ]);

        $this->assertTrue($userWithSocial->is_online_profile_availbal);
    }

    /** @test */
    public function it_has_language_constants()
    {
        $expectedLanguages = [
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

        $this->assertEquals($expectedLanguages, User::LANGUAGES);
    }

    /** @test */
    public function it_can_be_created_as_admin()
    {
        $admin = User::factory()->create(['user_type' => User::ADMIN]);

        $this->assertEquals(User::ADMIN, $admin->user_type);
        $this->assertTrue($admin->user_type === User::ADMIN);
    }

    /** @test */
    public function it_can_be_created_as_employer()
    {
        $employer = User::factory()->create(['user_type' => User::EMPLOYER]);

        $this->assertEquals(User::EMPLOYER, $employer->user_type);
        $this->assertTrue($employer->user_type === User::EMPLOYER);
    }

    /** @test */
    public function it_can_be_created_as_candidate()
    {
        $candidate = User::factory()->create(['user_type' => User::CANDIDATE]);

        $this->assertEquals(User::CANDIDATE, $candidate->user_type);
        $this->assertTrue($candidate->user_type === User::CANDIDATE);
    }

    /** @test */
    public function it_properly_casts_boolean_attributes()
    {
        $user = User::factory()->create([
            'is_active' => 1,
            'is_verified' => 0,
            'is_default' => 1,
        ]);

        $this->assertTrue($user->is_active);
        $this->assertFalse($user->is_verified);
        $this->assertTrue($user->is_default);
    }

    /** @test */
    public function it_properly_casts_date_attributes()
    {
        $user = User::factory()->create([
            'dob' => '1990-01-01',
            'email_verified_at' => now(),
        ]);

        $this->assertInstanceOf(\Carbon\Carbon::class, $user->dob);
        $this->assertInstanceOf(\Carbon\Carbon::class, $user->email_verified_at);
    }

    /** @test */
    public function password_is_hidden_in_array_conversion()
    {
        $user = User::factory()->create();
        $userArray = $user->toArray();

        $this->assertArrayNotHasKey('password', $userArray);
        $this->assertArrayNotHasKey('remember_token', $userArray);
    }

    /** @test */
    public function it_has_profile_constant()
    {
        $this->assertEquals('profile-pictures', User::PROFILE);
    }

    /** @test */
    public function it_has_mode_constants()
    {
        $this->assertEquals(1, User::DARK_MODE);
        $this->assertEquals(0, User::LIGHT_MODE);
        $this->assertEquals(1, User::ACTIVE);
    }
} 