<?php

namespace Tests\Feature;

use App\Models\Candidate;
use App\Models\CandidateEducation;
use App\Models\CandidateExperience;
use App\Models\CareerLevel;
use App\Models\City;
use App\Models\Company;
use App\Models\Country;
use App\Models\FunctionalArea;
use App\Models\Industry;
use App\Models\Job;
use App\Models\JobApplication;
use App\Models\Language;
use App\Models\MaritalStatus;
use App\Models\SalaryCurrency;
use App\Models\Skill;
use App\Models\State;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class CandidateTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    protected $adminUser;
    protected $candidateUser;
    protected $candidate;
    protected $employerUser;
    protected $company;

    protected function setUp(): void
    {
        parent::setUp();
        // Create users and associated models needed for tests
        $this->adminUser = User::factory()->create(['user_type' => User::ADMIN]);
        $this->candidateUser = User::factory()->create(['user_type' => User::CANDIDATE]);
        $this->candidate = Candidate::factory()->create(['user_id' => $this->candidateUser->id]);

        $this->employerUser = User::factory()->create(['user_type' => User::EMPLOYER]);
        $this->company = Company::factory()->create(['user_id' => $this->employerUser->id]);
        $this->employerUser->company()->save($this->company);
        $this->employerUser->load('company');
    }

    /** @test */
    public function candidateCanBeCreated()
    {
        $user = User::factory()->create();

        $candidateData = [
            'user_id' => $user->id,
            'expected_salary' => $this->faker->randomNumber(5),
            'experience' => $this->faker->randomNumber(1),
            'career_level_id' => 1,
            'industry_id' => 1,
            'functional_area_id' => 1,
            'current_salary' => $this->faker->randomNumber(5),
            'address' => $this->faker->address,
            'is_immediate_available' => true,
        ];

        $candidate = Candidate::create($candidateData);

        $this->assertInstanceOf(Candidate::class, $candidate);
        $this->assertEquals($candidateData['user_id'], $candidate->user_id);
        $this->assertEquals($candidateData['expected_salary'], $candidate->expected_salary);
        $this->assertEquals($candidateData['experience'], $candidate->experience);
        $this->assertTrue($candidate->is_immediate_available);
    }

    /** @test */
    public function candidateCanBeUpdated()
    {
        $candidate = Candidate::factory()->create();

        $updatedData = [
            'expected_salary' => $this->faker->randomNumber(5),
            'current_salary' => $this->faker->randomNumber(5),
            'is_immediate_available' => false,
        ];

        $candidate->update($updatedData);
        $candidate->refresh();

        $this->assertEquals($updatedData['expected_salary'], $candidate->expected_salary);
        $this->assertEquals($updatedData['current_salary'], $candidate->current_salary);
        $this->assertFalse($candidate->is_immediate_available);
    }

    /** @test */
    public function candidateBelongsToUser()
    {
        $user = User::factory()->create();
        $candidate = Candidate::factory()->create(['user_id' => $user->id]);

        $this->assertInstanceOf(User::class, $candidate->user);
        $this->assertEquals($user->id, $candidate->user_id);
    }

    /** @test */
    public function candidateCanHaveEducationRecords()
    {
        $candidate = Candidate::factory()->create();

        $education = CandidateEducation::factory()->create([
            'candidate_id' => $candidate->id,
            'degree_level' => $this->faker->word,
            'degree_title' => $this->faker->sentence,
            'year' => $this->faker->year,
            'institute' => $this->faker->company,
        ]);

        $this->assertInstanceOf(CandidateEducation::class, $candidate->educations->first());
        $this->assertCount(1, $candidate->educations);
        $this->assertEquals($education->id, $candidate->educations->first()->id);
    }

    /** @test */
    public function candidateCanHaveExperienceRecords()
    {
        $candidate = Candidate::factory()->create();

        $experience = CandidateExperience::factory()->create([
            'candidate_id' => $candidate->id,
            'company' => $this->faker->company,
            'job_title' => $this->faker->jobTitle,
            'start_date' => $this->faker->date(),
            'end_date' => $this->faker->date(),
            'is_current_job' => false,
        ]);

        $this->assertInstanceOf(CandidateExperience::class, $candidate->experiences->first());
        $this->assertCount(1, $candidate->experiences);
        $this->assertEquals($experience->id, $candidate->experiences->first()->id);
    }

    /** @test */
    public function candidateCanApplyForJobs()
    {
        $candidate = Candidate::factory()->create();
        $job = Job::factory()->create();

        $application = JobApplication::factory()->create([
            'candidate_id' => $candidate->id,
            'job_id' => $job->id,
            'status' => JobApplication::PENDING,
        ]);

        $this->assertInstanceOf(JobApplication::class, $candidate->jobApplications->first());
        $this->assertCount(1, $candidate->jobApplications);
        $this->assertEquals($application->id, $candidate->jobApplications->first()->id);
    }

    /** @test */
    public function candidatesCanBeFilteredByExperience()
    {
        Candidate::factory()->count(3)->create(['experience' => 5]);
        Candidate::factory()->count(2)->create(['experience' => 2]);

        $experiencedCandidates = Candidate::where('experience', '>=', 5)->get();
        $lesserExperiencedCandidates = Candidate::where('experience', '<', 5)->get();

        $this->assertCount(3, $experiencedCandidates);
        $this->assertCount(2, $lesserExperiencedCandidates);
    }

    /** @test */
    public function candidatesCanBeFilteredByAvailability()
    {
        Candidate::factory()->count(3)->create(['is_immediate_available' => true]);
        Candidate::factory()->count(2)->create(['is_immediate_available' => false]);

        $immediatelyAvailable = Candidate::where('is_immediate_available', true)->get();
        $notImmediatelyAvailable = Candidate::where('is_immediate_available', false)->get();

        $this->assertCount(3, $immediatelyAvailable);
        $this->assertCount(2, $notImmediatelyAvailable);
    }

    // =========================================
    // Admin Candidate Management Tests
    // =========================================

    /** @test */
    public function guestsCannotAccessAdminCandidatesSection()
    {
        $this->get('/admin/candidates')->assertRedirect('/login'); // Assuming /admin prefix
        $this->get('/admin/candidates/create')->assertRedirect('/login');
        $this->post('/admin/candidates')->assertRedirect('/login');
    }

    /** @test */
    public function nonAdminUsersCannotAccessAdminCandidatesSection()
    {
        // Test candidate access
        $this->actingAs($this->candidateUser)->get('/admin/candidates')->assertStatus(403);
        $this->actingAs($this->candidateUser)->get('/admin/candidates/create')->assertStatus(403);

        // Test employer access
        $this->actingAs($this->employerUser)->get('/admin/candidates')->assertStatus(403);
        $this->actingAs($this->employerUser)->get('/admin/candidates/create')->assertStatus(403);
    }

    /** @test */
    public function adminCanViewCandidatesList()
    {
        $response = $this->actingAs($this->adminUser)->get('/admin/candidates');
        $response->assertStatus(200);
        $response->assertViewIs('candidates.index');
    }

    /** @test */
    public function adminCanViewCandidateDetails()
    {
        $response = $this->actingAs($this->adminUser)->get("/admin/candidates/{$this->candidate->id}");
        $response->assertStatus(200);
        $response->assertViewIs('candidates.show');
        $response->assertSee($this->candidateUser->name);
    }

    /** @test */
    public function adminCanViewEditCandidateForm()
    {
        $response = $this->actingAs($this->adminUser)->get("/admin/candidates/{$this->candidate->id}/edit");
        $response->assertStatus(200);
        $response->assertViewIs('candidates.edit');
        $response->assertSee($this->candidateUser->name);
    }

    /** @test */
    public function adminCanUpdateCandidate()
    {
        $updateData = [
            // User fields
            'name' => 'Updated Candidate Name',
            'email' => $this->faker->unique()->safeEmail,
            'password' => 'password123', // Required by validation, but shouldn't change here usually
            'password_confirmation' => 'password123',
            'phone' => $this->faker->phoneNumber,
            'region_code' => 'GB', // Example
            'user_type' => User::CANDIDATE, // Required by validation
            'dob' => $this->faker->date(),
            'gender' => 1,
            'country_id' => Country::factory()->create()->id,
            'state_id' => State::factory()->create(['country_id' => 1])->id,
            'city_id' => City::factory()->create(['state_id' => 1])->id,
            'is_active' => true,
            'is_verified' => true,
            // Candidate fields
            'candidate_id' => $this->candidate->id, // Required by validation
            'father_name' => $this->faker->name('male'),
            'marital_status_id' => MaritalStatus::factory()->create()->id,
            'nationality' => $this->faker->country,
            'national_id_card' => $this->faker->uuid,
            'experience' => 5,
            'career_level_id' => CareerLevel::factory()->create()->id,
            'industry_id' => Industry::factory()->create()->id,
            'functional_area_id' => FunctionalArea::factory()->create()->id,
            'current_salary' => 60000,
            'expected_salary' => 70000,
            'salary_currency' => SalaryCurrency::factory()->create()->id,
            'address' => $this->faker->address,
            'immediate_available' => true,
            'available_at' => now()->addMonth()->toDateString(),
            // Skills and Languages (adjust based on actual form input names)
            'skill_ids' => Skill::factory()->count(2)->create()->pluck('id')->toArray(),
            'language_ids' => Language::factory()->count(2)->create()->pluck('id')->toArray(),
        ];

        $response = $this->actingAs($this->adminUser)->put("/admin/candidates/{$this->candidate->id}", $updateData);

        $response->assertRedirect('/admin/candidates');
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('users', [
            'id' => $this->candidateUser->id,
            'name' => 'Updated Candidate Name',
            'email' => $updateData['email'],
        ]);
        $this->assertDatabaseHas('candidates', [
            'id' => $this->candidate->id,
            'experience' => 5,
            'current_salary' => 60000,
            'expected_salary' => 70000,
        ]);
    }

    /** @test */
    public function adminCanDeleteCandidate()
    {
        // Create a new candidate specifically for this test to avoid FK issues
        $testCandidateUser = User::factory()->create(['user_type' => User::CANDIDATE]);
        $testCandidate = Candidate::factory()->create(['user_id' => $testCandidateUser->id]);

        $response = $this->actingAs($this->adminUser)->delete("/admin/candidates/{$testCandidate->id}");

        // Assuming JSON response based on controller
        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        $this->assertDatabaseMissing('candidates', ['id' => $testCandidate->id]);
        $this->assertDatabaseMissing('users', ['id' => $testCandidateUser->id]);
    }

    /** @test */
    public function adminCanChangeCandidateStatus()
    {
        $initialStatus = $this->candidateUser->is_active;

        $response = $this->actingAs($this->adminUser)->postJson("/admin/candidates/{$this->candidate->id}/change-status");

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        $this->candidateUser->refresh();
        $this->assertEquals(!$initialStatus, $this->candidateUser->is_active);
    }

    /** @test */
    public function adminCanChangeCandidateEmailVerifiedStatus()
    {
        $this->candidateUser->update(['email_verified_at' => null]); // Ensure initially not verified

        $response = $this->actingAs($this->adminUser)->postJson("/admin/candidates/{$this->candidate->id}/change-is-verified");

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        $this->candidateUser->refresh();
        $this->assertNotNull($this->candidateUser->email_verified_at);

        // Test un-verifying
        $response = $this->actingAs($this->adminUser)->postJson("/admin/candidates/{$this->candidate->id}/change-is-verified");
        $response->assertStatus(200);
        $this->candidateUser->refresh();
        $this->assertNull($this->candidateUser->email_verified_at);
    }

    // =========================================
    // Employer Candidate Interaction Tests
    // =========================================

    /** @test */
    public function employerCanReportACandidate()
    {
        $reportData = [
            'candidate_id' => $this->candidate->id,
            'user_id' => $this->employerUser->id,
            'note' => $this->faker->sentence,
        ];

        $response = $this->actingAs($this->employerUser)->postJson('/employer/report-candidate', $reportData);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        $this->assertDatabaseHas('reported_to_candidates', [
            'candidate_id' => $this->candidate->id,
            'user_id' => $this->employerUser->id,
            'note' => $reportData['note'],
        ]);
    }
}
