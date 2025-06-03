<?php

namespace Tests\Unit\Models;

use App\Models\User;
use Tests\UnitTestCase;

class UserModelOptimizedTest extends UnitTestCase
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
    public function it_can_be_instantiated_with_attributes()
    {
        $user = new User([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@example.com',
            'password' => 'password',
            'user_type' => User::CANDIDATE,
            'is_active' => true,
            'is_verified' => true,
            'phone' => '+1234567890',
            'language' => 'en',
        ]);

        $this->assertEquals('John', $user->first_name);
        $this->assertEquals('Doe', $user->last_name);
        $this->assertEquals('john@example.com', $user->email);
        $this->assertEquals(User::CANDIDATE, $user->user_type);
        $this->assertTrue($user->is_active);
        $this->assertTrue($user->is_verified);
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
    }

    /** @test */
    public function it_has_language_constants()
    {
        $expectedLanguages = [
            1 => 'English',
            2 => 'Lithuanian',
        ];

        $this->assertEquals($expectedLanguages, User::LANGUAGES);
    }

    /** @test */
    public function it_has_profile_constant()
    {
        $this->assertEquals('profile/', User::PROFILE_PATH);
    }

    /** @test */
    public function it_has_mode_constants()
    {
        $expectedModes = [
            1 => 'Light',
            2 => 'Dark',
        ];

        $this->assertEquals($expectedModes, User::MODE);
    }
} 