<?php

namespace Tests\Unit\Models;

use App\Models\User;
use App\Models\Country;
use App\Models\State;
use App\Models\City;
use App\Models\Candidate;
use App\Models\Company;
use App\Models\Skill;
use App\Models\Language;
use App\Models\FavouriteCompany;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class UserModelTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::factory()->create([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john.doe@example.com',
            'is_active' => true,
            'is_verified' => true,
        ]);
    }

    /** @test */
    public function it_has_user_type_constants()
    {
        $this->assertEquals(1, User::ADMIN);
        $this->assertEquals(2, User::EMPLOYER);
        $this->assertEquals(3, User::CANDIDATE);
    }

    /** @test */
    public function it_has_proper_fillable_attributes()
    {
        $fillable = [
            'first_name', 'last_name', 'email', 'password', 'user_type',
            'dob', 'gender', 'country_id', 'state_id', 'city_id',
            'is_active', 'is_verified', 'phone', 'email_verified_at',
            'owner_id', 'owner_type', 'language', 'facebook_url',
            'twitter_url', 'linkedin_url', 'google_plus_url',
            'pinterest_url', 'is_default', 'profile_views', 'region_code',
        ];

        $this->assertEquals($fillable, $this->user->getFillable());
    }

    /** @test */
    public function it_has_proper_hidden_attributes()
    {
        $hidden = ['password', 'remember_token'];
        $this->assertEquals($hidden, $this->user->getHidden());
    }

    /** @test */
    public function it_casts_attributes_correctly()
    {
        $casts = [
            'id' => 'integer',
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'dob' => 'date',
            'gender' => 'integer',
            'is_active' => 'boolean',
            'is_verified' => 'boolean',
            'is_default' => 'boolean',
            'profile_views' => 'integer',
            'country_id' => 'integer',
            'state_id' => 'integer',
            'city_id' => 'integer',
            'owner_id' => 'integer',
        ];

        foreach ($casts as $attribute => $cast) {
            $this->assertEquals($cast, $this->user->getCasts()[$attribute] ?? null);
        }
    }

    /** @test */
    public function it_belongs_to_country()
    {
        $country = Country::factory()->create();
        $this->user->update(['country_id' => $country->id]);

        $this->assertInstanceOf(Country::class, $this->user->country);
        $this->assertEquals($country->id, $this->user->country->id);
    }

    /** @test */
    public function it_belongs_to_state()
    {
        $state = State::factory()->create();
        $this->user->update(['state_id' => $state->id]);

        $this->assertInstanceOf(State::class, $this->user->state);
        $this->assertEquals($state->id, $this->user->state->id);
    }

    /** @test */
    public function it_belongs_to_city()
    {
        $city = City::factory()->create();
        $this->user->update(['city_id' => $city->id]);

        $this->assertInstanceOf(City::class, $this->user->city);
        $this->assertEquals($city->id, $this->user->city->id);
    }

    /** @test */
    public function it_has_one_candidate()
    {
        $candidate = Candidate::factory()->create(['user_id' => $this->user->id]);

        $this->assertInstanceOf(Candidate::class, $this->user->candidate);
        $this->assertEquals($candidate->id, $this->user->candidate->id);
    }

    /** @test */
    public function it_has_one_company()
    {
        $company = Company::factory()->create(['user_id' => $this->user->id]);

        $this->assertInstanceOf(Company::class, $this->user->company);
        $this->assertEquals($company->id, $this->user->company->id);
    }

    /** @test */
    public function it_has_many_to_many_skills()
    {
        $skills = Skill::factory()->count(3)->create();
        $this->user->candidateSkill()->attach($skills->pluck('id'));

        $this->assertCount(3, $this->user->candidateSkill);
        $this->assertInstanceOf(Skill::class, $this->user->candidateSkill->first());
    }

    /** @test */
    public function it_has_many_to_many_languages()
    {
        $languages = Language::factory()->count(2)->create();
        $this->user->candidateLanguage()->attach($languages->pluck('id'));

        $this->assertCount(2, $this->user->candidateLanguage);
        $this->assertInstanceOf(Language::class, $this->user->candidateLanguage->first());
    }

    /** @test */
    public function it_has_many_followings()
    {
        $followings = FavouriteCompany::factory()->count(2)->create(['user_id' => $this->user->id]);

        $this->assertCount(2, $this->user->followings);
        $this->assertInstanceOf(FavouriteCompany::class, $this->user->followings->first());
    }

    /** @test */
    public function it_returns_full_name_attribute()
    {
        $this->assertEquals('John Doe', $this->user->full_name);
    }

    /** @test */
    public function it_returns_cached_country_name()
    {
        $country = Country::factory()->create(['name' => 'United States']);
        $this->user->update(['country_id' => $country->id]);

        // Clear cache first
        Cache::forget("user.{$this->user->id}.country_name");

        $countryName = $this->user->country_name;
        $this->assertEquals('United States', $countryName);

        // Verify it's cached
        $this->assertTrue(Cache::has("user.{$this->user->id}.country_name"));
    }

    /** @test */
    public function it_returns_cached_state_name()
    {
        $state = State::factory()->create(['name' => 'California']);
        $this->user->update(['state_id' => $state->id]);

        Cache::forget("user.{$this->user->id}.state_name");

        $stateName = $this->user->state_name;
        $this->assertEquals('California', $stateName);

        $this->assertTrue(Cache::has("user.{$this->user->id}.state_name"));
    }

    /** @test */
    public function it_returns_cached_city_name()
    {
        $city = City::factory()->create(['name' => 'Los Angeles']);
        $this->user->update(['city_id' => $city->id]);

        Cache::forget("user.{$this->user->id}.city_name");

        $cityName = $this->user->city_name;
        $this->assertEquals('Los Angeles', $cityName);

        $this->assertTrue(Cache::has("user.{$this->user->id}.city_name"));
    }

    /** @test */
    public function it_scopes_active_users()
    {
        User::factory()->create(['is_active' => false]);
        User::factory()->create(['is_active' => true]);

        $activeUsers = User::active()->get();
        
        $this->assertTrue($activeUsers->every(fn($user) => $user->is_active));
    }

    /** @test */
    public function it_scopes_verified_users()
    {
        User::factory()->create(['is_verified' => false]);
        User::factory()->create(['is_verified' => true]);

        $verifiedUsers = User::verified()->get();
        
        $this->assertTrue($verifiedUsers->every(fn($user) => $user->is_verified));
    }

    /** @test */
    public function it_clears_cache_when_updated()
    {
        // Set some cache values
        Cache::put("user.{$this->user->id}", 'test_data');
        Cache::put("user.profile.{$this->user->id}", 'profile_data');

        // Update the user
        $this->user->update(['first_name' => 'Jane']);

        // Verify cache is cleared
        $this->assertFalse(Cache::has("user.{$this->user->id}"));
        $this->assertFalse(Cache::has("user.profile.{$this->user->id}"));
    }

    /** @test */
    public function it_can_check_user_permissions()
    {
        // This would require setting up roles, but testing the method structure
        $this->assertTrue(method_exists($this->user, 'canPerformAction'));
    }

    /** @test */
    public function it_returns_default_avatar_when_no_media()
    {
        $avatar = $this->user->avatar;
        $this->assertStringContains('infyom-logo.png', $avatar);
    }

    /** @test */
    public function it_checks_online_profile_availability()
    {
        // Create candidate with some data
        $candidate = Candidate::factory()->create([
            'user_id' => $this->user->id,
            'career_level_id' => 1,
        ]);

        Cache::forget("user.{$this->user->id}.online_profile");

        $isAvailable = $this->user->is_online_profile_availbal;
        $this->assertTrue($isAvailable);
    }

    protected function tearDown(): void
    {
        Cache::flush();
        parent::tearDown();
    }
} 