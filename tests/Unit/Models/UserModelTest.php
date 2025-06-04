<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\User;
use App\Models\Job;
use App\Models\Company;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_has_correct_fillable_attributes(): void
    {
        $user = new User();
        $fillable = $user->getFillable();
        
        $expectedFillable = [
            'first_name', 'last_name', 'email', 'phone', 'dob', 'gender',
            'marital_status_id', 'nationality_id', 'national_id_card',
            'country_id', 'state_id', 'city_id', 'postal_code', 'address',
            'career_level_id', 'industry_id', 'functional_area_id',
            'current_salary', 'expected_salary', 'salary_currency_id',
            'salary_period_id', 'available_at', 'experience',
            'facebook_url', 'twitter_url', 'linkedin_url', 'google_plus_url',
            'pinterest_url', 'website', 'is_active', 'is_verified',
            'verification_token', 'stripe_id', 'pm_type', 'pm_last_four',
            'trial_ends_at', 'password', 'region_code', 'phone_verified_at',
            'email_verified_at', 'remember_token', 'is_subscribed'
        ];
        
        foreach ($expectedFillable as $field) {
            $this->assertContains($field, $fillable, "Field {$field} should be fillable");
        }
    }

    public function test_user_has_correct_hidden_attributes(): void
    {
        $user = new User();
        $hidden = $user->getHidden();
        
        $this->assertContains('password', $hidden);
        $this->assertContains('remember_token', $hidden);
    }

    public function test_user_has_correct_casts(): void
    {
        $user = new User();
        $casts = $user->getCasts();
        
        $this->assertEquals('datetime', $casts['email_verified_at']);
        $this->assertEquals('datetime', $casts['phone_verified_at']);
        $this->assertEquals('datetime', $casts['trial_ends_at']);
        $this->assertEquals('boolean', $casts['is_active']);
        $this->assertEquals('boolean', $casts['is_verified']);
        $this->assertEquals('boolean', $casts['is_subscribed']);
    }

    public function test_user_can_have_jobs(): void
    {
        $user = $this->createTestUser();
        $job = $this->createTestJob(['user_id' => $user->id]);
        
        $this->assertTrue($user->jobs()->exists());
        $this->assertEquals($job->id, $user->jobs->first()->id);
    }

    public function test_user_can_have_company(): void
    {
        $user = $this->createTestUser();
        
        // Create company for user
        $company = Company::factory()->create(['user_id' => $user->id]);
        
        $this->assertTrue($user->company()->exists());
        $this->assertEquals($company->id, $user->company->id);
    }

    public function test_password_is_hashed_when_set(): void
    {
        $user = User::factory()->make(['password' => 'plaintext']);
        
        $this->assertNotEquals('plaintext', $user->password);
        $this->assertTrue(\Hash::check('plaintext', $user->password));
    }
}