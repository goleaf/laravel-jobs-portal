<?php

namespace Tests\Feature;

use App\Models\Candidate;
use App\Models\CmsServices;
use App\Models\Company;
use App\Models\Job;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class WebRoutesTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    protected function setUp(): void
    {
        parent::setUp();

        // Create essential CMS services data required by home page
        CmsServices::create(['key' => 'home_title', 'value' => 'Find Your Dream Job']);
        CmsServices::create(['key' => 'home_description', 'value' => 'Discover opportunities with top companies.']);
        CmsServices::create(['key' => 'home_banner', 'value' => 'front_web/images/hero-img.png']);

        // Create essential settings
        Setting::create(['key' => 'slider_is_active', 'value' => '0']);
        Setting::create(['key' => 'is_full_slider', 'value' => '0']);
        Setting::create(['key' => 'is_slider_active', 'value' => '0']);
    }

    /** @test */
    public function homepageLoadsSuccessfully()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    /** @test */
    public function loginPageLoadsSuccessfully()
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
    }

    /** @test */
    public function registerPageLoadsSuccessfully()
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    /** @test */
    public function aboutPageLoadsSuccessfully()
    {
        $response = $this->get('/about-us');

        $response->assertStatus(200);
    }

    /** @test */
    public function contactPageLoadsSuccessfully()
    {
        $response = $this->get('/contact');

        $response->assertStatus(200);
    }

    /** @test */
    public function jobsPageLoadsSuccessfully()
    {
        $response = $this->get('/jobs');

        $response->assertStatus(200);
    }

    /** @test */
    public function companiesPageLoadsSuccessfully()
    {
        $response = $this->get('/companies');

        $response->assertStatus(200);
    }

    /** @test */
    public function jobDetailsPageLoadsSuccessfully()
    {
        $job = Job::factory()->create([
            'status' => Job::STATUS_OPEN,
            'is_suspended' => false,
        ]);

        $response = $this->get("/jobs/{$job->id}");

        $response->assertStatus(200);
    }

    /** @test */
    public function companyDetailsPageLoadsSuccessfully()
    {
        $company = Company::factory()->create([
            'is_active' => true,
        ]);

        $response = $this->get("/company/{$company->id}");

        $response->assertStatus(200);
    }

    /** @test */
    public function unauthenticatedUsersAreRedirectedFromDashboard()
    {
        $response = $this->get('/dashboard');

        $response->assertRedirect('/login');
    }

    /** @test */
    public function authenticatedUsersCanAccessDashboard()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->get('/dashboard')
        ;

        $response->assertStatus(200);
    }

    /** @test */
    public function candidateCanAccessProfilePage()
    {
        $user = User::factory()->create(['user_type' => User::CANDIDATE]);
        Candidate::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)
            ->get('/candidate/profile')
        ;

        $response->assertStatus(200);
    }

    /** @test */
    public function companyCanAccessProfilePage()
    {
        $user = User::factory()->create(['user_type' => User::EMPLOYER]);
        Company::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)
            ->get('/employer/company')
        ;

        $response->assertStatus(200);
    }

    /** @test */
    public function employerCanAccessJobCreationPage()
    {
        $user = User::factory()->create(['user_type' => User::EMPLOYER]);
        Company::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)
            ->get('/employer/jobs/create')
        ;

        $response->assertStatus(200);
    }

    /** @test */
    public function candidateCanAccessAppliedJobsPage()
    {
        $user = User::factory()->create(['user_type' => User::CANDIDATE]);
        Candidate::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)
            ->get('/candidate/applied-jobs')
        ;

        $response->assertStatus(200);
    }

    /** @test */
    public function employerCanAccessJobApplicationsPage()
    {
        $user = User::factory()->create(['user_type' => User::EMPLOYER]);
        Company::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)
            ->get('/employer/jobs/applications')
        ;

        $response->assertStatus(200);
    }

    /** @test */
    public function adminCanAccessAdminDashboard()
    {
        $admin = User::factory()->create([
            'user_type' => User::ADMIN,
            'is_active' => true,
        ]);

        $response = $this->actingAs($admin)
            ->get('/admin/dashboard')
        ;

        $response->assertStatus(200);
    }

    /** @test */
    public function nonAdminCannotAccessAdminDashboard()
    {
        $user = User::factory()->create([
            'user_type' => User::CANDIDATE,
            'is_active' => true,
        ]);

        $response = $this->actingAs($user)
            ->get('/admin/dashboard');

        $response->assertStatus(403);
    }
}
