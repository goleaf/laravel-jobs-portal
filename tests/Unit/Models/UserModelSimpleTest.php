<?php

namespace Tests\Unit\Models;

use App\Models\User;
use PHPUnit\Framework\TestCase;

class UserModelSimpleTest extends TestCase
{
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
    public function it_generates_full_name_attribute()
    {
        $user = new User([
            'first_name' => 'John',
            'last_name' => 'Doe'
        ]);

        $this->assertEquals('John Doe', $user->full_name);
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

    /** @test */
    public function it_checks_online_profile_availability_when_no_social_urls()
    {
        $user = new User([
            'facebook_url' => null,
            'twitter_url' => null,
            'linkedin_url' => null,
            'google_plus_url' => null,
            'pinterest_url' => null,
        ]);

        $this->assertFalse($user->is_online_profile_availbal);
    }

    /** @test */
    public function it_checks_online_profile_availability_when_has_social_urls()
    {
        $user = new User([
            'linkedin_url' => 'https://linkedin.com/in/johndoe',
        ]);

        $this->assertTrue($user->is_online_profile_availbal);
    }

    /** @test */
    public function it_has_relationship_methods()
    {
        $user = new User();
        
        // Test that relationship methods exist by checking they are callable
        $this->assertTrue(method_exists($user, 'country'));
        $this->assertTrue(method_exists($user, 'state'));
        $this->assertTrue(method_exists($user, 'city'));
        $this->assertTrue(method_exists($user, 'candidate'));
        $this->assertTrue(method_exists($user, 'company'));
        $this->assertTrue(method_exists($user, 'candidateSkill'));
        $this->assertTrue(method_exists($user, 'candidateLanguage'));
        $this->assertTrue(method_exists($user, 'followings'));
    }
} 