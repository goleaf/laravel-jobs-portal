<?php

namespace Tests\Unit\Models;

use App\Models\City;
use App\Models\Company;
use App\Models\Country;
use App\Models\JobApplication;
use App\Models\Resume;
use App\Models\State;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
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
    public function itHasCorrectFillableAttributes(): void
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
            'preferred_job_type', 'willing_to_relocate', 'remote_work_preference',
        ];

        $this->assertEquals($fillable, $this->user->getFillable());
    }

    /** @test */
    public function itHasCorrectCasts(): void
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
            $this->assertEquals(
                $cast,
                $this->user->getCasts()[$attribute] ?? null,
                "Cast for {$attribute} should be {$cast}"
            );
        }
    }

    /** @test */
    public function itHidesSensitiveAttributes(): void
    {
        $hidden = ['password', 'remember_token'];

        $this->assertEquals($hidden, $this->user->getHidden());
    }

    /** @test */
    public function itBelongsToLocationModels(): void
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
    public function itHasOneCompany(): void
    {
        $company = Company::factory()->create(['user_id' => $this->user->id]);

        $this->assertInstanceOf(Company::class, $this->user->company);
        $this->assertEquals($company->id, $this->user->company->id);
    }

    /** @test */
    public function itHasManyJobApplications(): void
    {
        $applications = JobApplication::factory()->count(3)->create(['candidate_id' => $this->user->id]);

        $this->assertInstanceOf(Collection::class, $this->user->jobApplications);
        $this->assertCount(3, $this->user->jobApplications);

        foreach ($applications as $application) {
            $this->assertTrue($this->user->jobApplications->contains($application));
        }
    }

    /** @test */
    public function itHasManyResumes(): void
    {
        $resumes = Resume::factory()->count(2)->create(['user_id' => $this->user->id]);

        $this->assertInstanceOf(Collection::class, $this->user->resumes);
        $this->assertCount(2, $this->user->resumes);

        foreach ($resumes as $resume) {
            $this->assertTrue($this->user->resumes->contains($resume));
        }
    }

    /** @test */
    public function activeScopeReturnsOnlyActiveUsers(): void
    {
        User::factory()->create(['is_active' => true]);
        User::factory()->create(['is_active' => false]);

        $activeUsers = User::active()->get();

        $activeUsers->each(function ($user) {
            $this->assertTrue($user->is_active);
        });
    }

    /** @test */
    public function inactiveScopeReturnsOnlyInactiveUsers(): void
    {
        User::factory()->create(['is_active' => true]);
        User::factory()->create(['is_active' => false]);

        $inactiveUsers = User::inactive()->get();

        $inactiveUsers->each(function ($user) {
            $this->assertFalse($user->is_active);
        });
    }

    /** @test */
    public function verifiedScopeReturnsOnlyVerifiedUsers(): void
    {
        User::factory()->create(['email_verified_at' => now()]);
        User::factory()->create(['email_verified_at' => null]);

        $verifiedUsers = User::verified()->get();

        $verifiedUsers->each(function ($user) {
            $this->assertNotNull($user->email_verified_at);
        });
    }

    /** @test */
    public function unverifiedScopeReturnsOnlyUnverifiedUsers(): void
    {
        User::factory()->create(['email_verified_at' => now()]);
        User::factory()->create(['email_verified_at' => null]);

        $unverifiedUsers = User::unverified()->get();

        $unverifiedUsers->each(function ($user) {
            $this->assertNull($user->email_verified_at);
        });
    }

    /** @test */
    public function candidatesScopeReturnsOnlyCandidates(): void
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
    public function employersScopeReturnsOnlyEmployers(): void
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
    public function byRoleScopeFiltersByRole(): void
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
    public function byLocationScopeFiltersByLocation(): void
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
    public function searchScopeSearchesInNameAndEmail(): void
    {
        User::factory()->create(['first_name' => 'John', 'last_name' => 'Doe', 'email' => 'john@example.com']);
        User::factory()->create(['first_name' => 'Jane', 'last_name' => 'Smith', 'email' => 'jane@example.com']);

        $searchUsers = User::search('John')->get();

        $this->assertCount(1, $searchUsers);
        $this->assertEquals('John', $searchUsers->first()->first_name);
    }

    /** @test */
    public function recentScopeReturnsUsersFromLast30Days(): void
    {
        User::factory()->create(['created_at' => now()->subDays(10)]);
        User::factory()->create(['created_at' => now()->subDays(40)]);

        $recentUsers = User::recent()->get();

        $recentUsers->each(function ($user) {
            $this->assertTrue($user->created_at->gte(now()->subDays(30)));
        });
    }

    /** @test */
    public function withExperienceScopeFiltersByExperienceLevel(): void
    {
        User::factory()->create(['experience_level' => 3]);
        User::factory()->create(['experience_level' => 7]);

        $experiencedUsers = User::withExperience(5, 10)->get();

        $experiencedUsers->each(function ($user) {
            $this->assertTrue($user->experience_level >= 5 && $user->experience_level <= 10);
        });
    }

    /** @test */
    public function availableScopeReturnsAvailableUsers(): void
    {
        User::factory()->create(['availability_status' => 'available']);
        User::factory()->create(['availability_status' => 'not_available']);

        $availableUsers = User::available()->get();

        $availableUsers->each(function ($user) {
            $this->assertEquals('available', $user->availability_status);
        });
    }

    /** @test */
    public function willingToRelocateScopeReturnsUsersWillingToRelocate(): void
    {
        User::factory()->create(['willing_to_relocate' => true]);
        User::factory()->create(['willing_to_relocate' => false]);

        $relocateUsers = User::willingToRelocate()->get();

        $relocateUsers->each(function ($user) {
            $this->assertTrue($user->willing_to_relocate);
        });
    }

    /** @test */
    public function itCanGetFullName(): void
    {
        $user = User::factory()->create([
            'first_name' => 'John',
            'last_name' => 'Doe',
        ]);

        $this->assertEquals('John Doe', $user->getFullName());
    }

    /** @test */
    public function itCanGetInitials(): void
    {
        $user = User::factory()->create([
            'first_name' => 'John',
            'last_name' => 'Doe',
        ]);

        $this->assertEquals('JD', $user->getInitials());
    }

    /** @test */
    public function itCanGetAge(): void
    {
        $user = User::factory()->create([
            'date_of_birth' => now()->subYears(25)->format('Y-m-d'),
        ]);

        $this->assertEquals(25, $user->getAge());
    }

    /** @test */
    public function itReturnsNullAgeForUsersWithoutBirthDate(): void
    {
        $user = User::factory()->create(['date_of_birth' => null]);

        $this->assertNull($user->getAge());
    }

    /** @test */
    public function itCanCheckIfUserIsCandidate(): void
    {
        $candidate = User::factory()->create();
        $candidate->assignRole('Candidate');

        $employer = User::factory()->create();
        $employer->assignRole('Employer');

        $this->assertTrue($candidate->isCandidate());
        $this->assertFalse($employer->isCandidate());
    }

    /** @test */
    public function itCanCheckIfUserIsEmployer(): void
    {
        $candidate = User::factory()->create();
        $candidate->assignRole('Candidate');

        $employer = User::factory()->create();
        $employer->assignRole('Employer');

        $this->assertFalse($candidate->isEmployer());
        $this->assertTrue($employer->isEmployer());
    }

    /** @test */
    public function itCanCheckIfUserIsAdmin(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('Admin');

        $candidate = User::factory()->create();
        $candidate->assignRole('Candidate');

        $this->assertTrue($admin->isAdmin());
        $this->assertFalse($candidate->isAdmin());
    }

    /** @test */
    public function itCanGetAvatarUrl(): void
    {
        $user = User::factory()->create(['avatar' => 'avatars/user.jpg']);

        $this->assertStringContainsString('avatars/user.jpg', $user->getAvatarUrl());
    }

    /** @test */
    public function itReturnsDefaultAvatarWhenNoAvatarSet(): void
    {
        $user = User::factory()->create(['avatar' => null]);

        $this->assertStringContainsString('default-avatar', $user->getAvatarUrl());
    }

    /** @test */
    public function itCanGetFullLocation(): void
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
    public function itCanGetExperienceLevelText(): void
    {
        $user = User::factory()->create(['experience_level' => 3]);

        $this->assertEquals('3 years', $user->getExperienceLevelText());
    }

    /** @test */
    public function itCanCheckIfProfileIsComplete(): void
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
    public function itCanGetProfileCompletionPercentage(): void
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
