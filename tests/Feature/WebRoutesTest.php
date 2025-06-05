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
    public function homepage_loads_successfully()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    /** @test */
    public function login_page_loads_successfully()
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
    }

    /** @test */
    public function register_page_loads_successfully()
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    /** @test */
    public function about_page_loads_successfully()
    {
        $response = $this->get('/about-us');

        $response->assertStatus(200);
    }

    /** @test */
    public function contact_page_loads_successfully()
    {
        $response = $this->get('/contact');

        $response->assertStatus(200);
    }

    /** @test */
    public function jobs_page_loads_successfully()
    {
        $response = $this->get('/jobs');

        $response->assertStatus(200);
    }

    /** @test */
    public function companies_page_loads_successfully()
    {
        $response = $this->get('/companies');

        $response->assertStatus(200);
    }

    /** @test */
    public function job_details_page_loads_successfully()
    {
        $job = Job::factory()->create([
            'status' => Job::STATUS_OPEN,
            'is_suspended' => false,
        ]);

        $response = $this->get("/jobs/{$job->id}");

        $response->assertStatus(200);
    }

    /** @test */
    public function company_details_page_loads_successfully()
    {
        $company = Company::factory()->create([
            'is_active' => true,
        ]);

        $response = $this->get("/company/{$company->id}");

        $response->assertStatus(200);
    }

    /** @test */
    public function unauthenticated_users_are_redirected_from_dashboard()
    {
        $response = $this->get('/dashboard');

        $response->assertRedirect('/login');
    }

    /** @test */
    public function authenticated_users_can_access_dashboard()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->get('/dashboard');

        $response->assertStatus(200);
    }

    /** @test */
    public function candidate_can_access_profile_page()
    {
        $user = User::factory()->create(['user_type' => User::CANDIDATE]);
        Candidate::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)
            ->get('/candidate/profile');

        $response->assertStatus(200);
    }

    /** @test */
    public function company_can_access_profile_page()
    {
        $user = User::factory()->create(['user_type' => User::EMPLOYER]);
        Company::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)
            ->get('/employer/company');

        $response->assertStatus(200);
    }

    /** @test */
    public function employer_can_access_job_creation_page()
    {
        $user = User::factory()->create(['user_type' => User::EMPLOYER]);
        Company::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)
            ->get('/employer/jobs/create');

        $response->assertStatus(200);
    }

    /** @test */
    public function candidate_can_access_applied_jobs_page()
    {
        $user = User::factory()->create(['user_type' => User::CANDIDATE]);
        Candidate::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)
            ->get('/candidate/applied-jobs');

        $response->assertStatus(200);
    }

    /** @test */
    public function employer_can_access_job_applications_page()
    {
        $user = User::factory()->create(['user_type' => User::EMPLOYER]);
        Company::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)
            ->get('/employer/jobs/applications');

        $response->assertStatus(200);
    }

    /** @test */
    public function admin_can_access_admin_dashboard()
    {
        $admin = User::factory()->create([
            'user_type' => User::ADMIN,
            'is_active' => true,
        ]);

        $response = $this->actingAs($admin)
            ->get('/admin/dashboard');

        $response->assertStatus(200);
    }

    /** @test */
    public function non_admin_cannot_access_admin_dashboard()
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
