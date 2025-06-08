<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\User;
use App\Models\Company;
use App\Models\JobApplication;
use App\Models\Resume;
use App\Models\Country;
use App\Models\State;
use App\Models\City;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Database\Eloquent\Collection;
use Spatie\Permission\Models\Role;

class UserTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create roles
        Role::create(['name' => 'Admin']);
        Role::create(['name' => 'Candidate']);
        Role::create(['name' => 'Employer']);
        
        // Create test user
        $this->user = User::factory()->create();
    }

    /** @test */
    public function it_has_correct_fillable_attributes(): void
    {
        $fillable = [
            'first_name', 'last_name', 'name', 'email', 'phone', 'mobile_phone',
            'date_of_birth', 'gender', 'marital_status_id', 'nationality_id',
            'country_id', 'state_id', 'city_id', 'address', 'postal_code',
            'experience_level', 'current_salary', 'expected_salary', 'salary_currency_id',
            'is_active', 'is_verified', 'is_featured', 'email_verified_at',
            'password', 'remember_token', 'avatar', 'cover_image',
            'linkedin_url', 'github_url', 'portfolio_url', 'website_url',
            'bio', 'skills', 'languages', 'availability_status',
            'preferred_job_type', 'willing_to_relocate', 'remote_work_preference'
        ];

        $this->assertEquals($fillable, $this->user->getFillable());
    }

    /** @test */
    public function it_has_correct_casts(): void
    {
        $expectedCasts = [
            'id' => 'int',
            'marital_status_id' => 'integer',
            'nationality_id' => 'integer',
            'country_id' => 'integer',
            'state_id' => 'integer',
            'city_id' => 'integer',
            'salary_currency_id' => 'integer',
            'date_of_birth' => 'date',
            'current_salary' => 'decimal:2',
            'expected_salary' => 'decimal:2',
            'experience_level' => 'integer',
            'is_active' => 'boolean',
            'is_verified' => 'boolean',
            'is_featured' => 'boolean',
            'willing_to_relocate' => 'boolean',
            'email_verified_at' => 'datetime',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'skills' => 'array',
            'languages' => 'array',
        ];

        foreach ($expectedCasts as $attribute => $cast) {
            $this->assertEquals($cast, $this->user->getCasts()[$attribute] ?? null, 
                "Cast for {$attribute} should be {$cast}");
        }
    }

    /** @test */
    public function it_hides_sensitive_attributes(): void
    {
        $hidden = ['password', 'remember_token'];
        
        $this->assertEquals($hidden, $this->user->getHidden());
    }

    /** @test */
    public function it_belongs_to_location_models(): void
    {
        $country = Country::factory()->create();
        $state = State::factory()->create(['country_id' => $country->id]);
        $city = City::factory()->create(['state_id' => $state->id]);
        
        $user = User::factory()->create([
            'country_id' => $country->id,
            'state_id' => $state->id,
            'city_id' => $city->id,
        ]);

        $this->assertInstanceOf(Country::class, $user->country);
        $this->assertInstanceOf(State::class, $user->state);
        $this->assertInstanceOf(City::class, $user->city);
        
        $this->assertEquals($country->id, $user->country->id);
        $this->assertEquals($state->id, $user->state->id);
        $this->assertEquals($city->id, $user->city->id);
    }

    /** @test */
    public function it_has_one_company(): void
    {
        $company = Company::factory()->create(['user_id' => $this->user->id]);

        $this->assertInstanceOf(Company::class, $this->user->company);
        $this->assertEquals($company->id, $this->user->company->id);
    }

    /** @test */
    public function it_has_many_job_applications(): void
    {
        $applications = JobApplication::factory()->count(3)->create(['candidate_id' => $this->user->id]);

        $this->assertInstanceOf(Collection::class, $this->user->jobApplications);
        $this->assertCount(3, $this->user->jobApplications);
        
        foreach ($applications as $application) {
            $this->assertTrue($this->user->jobApplications->contains($application));
        }
    }

    /** @test */
    public function it_has_many_resumes(): void
    {
        $resumes = Resume::factory()->count(2)->create(['user_id' => $this->user->id]);

        $this->assertInstanceOf(Collection::class, $this->user->resumes);
        $this->assertCount(2, $this->user->resumes);
        
        foreach ($resumes as $resume) {
            $this->assertTrue($this->user->resumes->contains($resume));
        }
    }

    /** @test */
    public function active_scope_returns_only_active_users(): void
    {
        User::factory()->create(['is_active' => true]);
        User::factory()->create(['is_active' => false]);

        $activeUsers = User::active()->get();

        $activeUsers->each(function ($user) {
            $this->assertTrue($user->is_active);
        });
    }

    /** @test */
    public function inactive_scope_returns_only_inactive_users(): void
    {
        User::factory()->create(['is_active' => true]);
        User::factory()->create(['is_active' => false]);

        $inactiveUsers = User::inactive()->get();

        $inactiveUsers->each(function ($user) {
            $this->assertFalse($user->is_active);
        });
    }

    /** @test */
    public function verified_scope_returns_only_verified_users(): void
    {
        User::factory()->create(['email_verified_at' => now()]);
        User::factory()->create(['email_verified_at' => null]);

        $verifiedUsers = User::verified()->get();

        $verifiedUsers->each(function ($user) {
            $this->assertNotNull($user->email_verified_at);
        });
    }

    /** @test */
    public function unverified_scope_returns_only_unverified_users(): void
    {
        User::factory()->create(['email_verified_at' => now()]);
        User::factory()->create(['email_verified_at' => null]);

        $unverifiedUsers = User::unverified()->get();

        $unverifiedUsers->each(function ($user) {
            $this->assertNull($user->email_verified_at);
        });
    }

    /** @test */
    public function candidates_scope_returns_only_candidates(): void
    {
        $candidate = User::factory()->create();
        $candidate->assignRole('Candidate');
        
        $employer = User::factory()->create();
        $employer->assignRole('Employer');

        $candidates = User::candidates()->get();

        $this->assertTrue($candidates->contains($candidate));
        $this->assertFalse($candidates->contains($employer));
    }

    /** @test */
    public function employers_scope_returns_only_employers(): void
    {
        $candidate = User::factory()->create();
        $candidate->assignRole('Candidate');
        
        $employer = User::factory()->create();
        $employer->assignRole('Employer');

        $employers = User::employers()->get();

        $this->assertTrue($employers->contains($employer));
        $this->assertFalse($employers->contains($candidate));
    }

    /** @test */
    public function by_role_scope_filters_by_role(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('Admin');
        
        $candidate = User::factory()->create();
        $candidate->assignRole('Candidate');

        $admins = User::byRole('Admin')->get();

        $this->assertTrue($admins->contains($admin));
        $this->assertFalse($admins->contains($candidate));
    }

    /** @test */
    public function by_location_scope_filters_by_location(): void
    {
        $country = Country::factory()->create();
        $state = State::factory()->create(['country_id' => $country->id]);
        $city = City::factory()->create(['state_id' => $state->id]);
        
        User::factory()->create([
            'country_id' => $country->id,
            'state_id' => $state->id,
            'city_id' => $city->id,
        ]);

        $locationUsers = User::byLocation($country->id, $state->id, $city->id)->get();

        $locationUsers->each(function ($user) use ($country, $state, $city) {
            $this->assertEquals($country->id, $user->country_id);
            $this->assertEquals($state->id, $user->state_id);
            $this->assertEquals($city->id, $user->city_id);
        });
    }

    /** @test */
    public function search_scope_searches_in_name_and_email(): void
    {
        User::factory()->create(['first_name' => 'John', 'last_name' => 'Doe', 'email' => 'john@example.com']);
        User::factory()->create(['first_name' => 'Jane', 'last_name' => 'Smith', 'email' => 'jane@example.com']);

        $searchUsers = User::search('John')->get();

        $this->assertCount(1, $searchUsers);
        $this->assertEquals('John', $searchUsers->first()->first_name);
    }

    /** @test */
    public function recent_scope_returns_users_from_last_30_days(): void
    {
        User::factory()->create(['created_at' => now()->subDays(10)]);
        User::factory()->create(['created_at' => now()->subDays(40)]);

        $recentUsers = User::recent()->get();

        $recentUsers->each(function ($user) {
            $this->assertTrue($user->created_at->gte(now()->subDays(30)));
        });
    }

    /** @test */
    public function with_experience_scope_filters_by_experience_level(): void
    {
        User::factory()->create(['experience_level' => 3]);
        User::factory()->create(['experience_level' => 7]);

        $experiencedUsers = User::withExperience(5, 10)->get();

        $experiencedUsers->each(function ($user) {
            $this->assertTrue($user->experience_level >= 5 && $user->experience_level <= 10);
        });
    }

    /** @test */
    public function available_scope_returns_available_users(): void
    {
        User::factory()->create(['availability_status' => 'available']);
        User::factory()->create(['availability_status' => 'not_available']);

        $availableUsers = User::available()->get();

        $availableUsers->each(function ($user) {
            $this->assertEquals('available', $user->availability_status);
        });
    }

    /** @test */
    public function willing_to_relocate_scope_returns_users_willing_to_relocate(): void
    {
        User::factory()->create(['willing_to_relocate' => true]);
        User::factory()->create(['willing_to_relocate' => false]);

        $relocateUsers = User::willingToRelocate()->get();

        $relocateUsers->each(function ($user) {
            $this->assertTrue($user->willing_to_relocate);
        });
    }

    /** @test */
    public function it_can_get_full_name(): void
    {
        $user = User::factory()->create([
            'first_name' => 'John',
            'last_name' => 'Doe'
        ]);

        $this->assertEquals('John Doe', $user->getFullName());
    }

    /** @test */
    public function it_can_get_initials(): void
    {
        $user = User::factory()->create([
            'first_name' => 'John',
            'last_name' => 'Doe'
        ]);

        $this->assertEquals('JD', $user->getInitials());
    }

    /** @test */
    public function it_can_get_age(): void
    {
        $user = User::factory()->create([
            'date_of_birth' => now()->subYears(25)->format('Y-m-d')
        ]);

        $this->assertEquals(25, $user->getAge());
    }

    /** @test */
    public function it_returns_null_age_for_users_without_birth_date(): void
    {
        $user = User::factory()->create(['date_of_birth' => null]);

        $this->assertNull($user->getAge());
    }

    /** @test */
    public function it_can_check_if_user_is_candidate(): void
    {
        $candidate = User::factory()->create();
        $candidate->assignRole('Candidate');
        
        $employer = User::factory()->create();
        $employer->assignRole('Employer');

        $this->assertTrue($candidate->isCandidate());
        $this->assertFalse($employer->isCandidate());
    }

    /** @test */
    public function it_can_check_if_user_is_employer(): void
    {
        $candidate = User::factory()->create();
        $candidate->assignRole('Candidate');
        
        $employer = User::factory()->create();
        $employer->assignRole('Employer');

        $this->assertFalse($candidate->isEmployer());
        $this->assertTrue($employer->isEmployer());
    }

    /** @test */
    public function it_can_check_if_user_is_admin(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('Admin');
        
        $candidate = User::factory()->create();
        $candidate->assignRole('Candidate');

        $this->assertTrue($admin->isAdmin());
        $this->assertFalse($candidate->isAdmin());
    }

    /** @test */
    public function it_can_get_avatar_url(): void
    {
        $user = User::factory()->create(['avatar' => 'avatars/user.jpg']);

        $this->assertStringContainsString('avatars/user.jpg', $user->getAvatarUrl());
    }

    /** @test */
    public function it_returns_default_avatar_when_no_avatar_set(): void
    {
        $user = User::factory()->create(['avatar' => null]);

        $this->assertStringContainsString('default-avatar', $user->getAvatarUrl());
    }

    /** @test */
    public function it_can_get_full_location(): void
    {
        $country = Country::factory()->create(['name' => 'United States']);
        $state = State::factory()->create(['name' => 'California', 'country_id' => $country->id]);
        $city = City::factory()->create(['name' => 'San Francisco', 'state_id' => $state->id]);
        
        $user = User::factory()->create([
            'country_id' => $country->id,
            'state_id' => $state->id,
            'city_id' => $city->id,
        ]);

        $fullLocation = $user->getFullLocation();
        
        $this->assertStringContainsString('San Francisco', $fullLocation);
        $this->assertStringContainsString('California', $fullLocation);
        $this->assertStringContainsString('United States', $fullLocation);
    }

    /** @test */
    public function it_can_get_experience_level_text(): void
    {
        $user = User::factory()->create(['experience_level' => 3]);

        $this->assertEquals('3 years', $user->getExperienceLevelText());
    }

    /** @test */
    public function it_can_check_if_profile_is_complete(): void
    {
        $incompleteUser = User::factory()->create([
            'first_name' => 'John',
            'last_name' => null,
            'phone' => null,
        ]);

        $completeUser = User::factory()->create([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'phone' => '123-456-7890',
            'date_of_birth' => '1990-01-01',
            'country_id' => Country::factory()->create()->id,
        ]);

        $this->assertFalse($incompleteUser->isProfileComplete());
        $this->assertTrue($completeUser->isProfileComplete());
    }

    /** @test */
    public function it_can_get_profile_completion_percentage(): void
    {
        $user = User::factory()->create([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@example.com',
            'phone' => null,
            'date_of_birth' => null,
        ]);

        $percentage = $user->getProfileCompletionPercentage();
        
        $this->assertIsInt($percentage);
        $this->assertGreaterThan(0, $percentage);
        $this->assertLessThanOrEqual(100, $percentage);
    }
}