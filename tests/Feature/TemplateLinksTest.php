<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Job;
use App\Models\Company;
use App\Models\JobCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TemplateLinksTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** @test */
    public function home_page_contains_expected_links()
    {
        $response = $this->get('/');
        
        $response->assertStatus(200);
        
        // Check common navigation links
        $response->assertSee(route('contact'));
        $response->assertSee(route('front'));
        $response->assertSee(route('login'));
        $response->assertSee(route('jobs.index'));
    }

    /** @test */
    public function forms_page_contains_expected_links()
    {
        $response = $this->get(route('forms.validation'));
        
        $response->assertStatus(200);
        
        // Check for links to other form examples
        $response->assertSee(route('forms.alpine'));
        $response->assertSee(route('forms.binding'));
        $response->assertSee(route('forms.errors'));
        $response->assertSee(route('forms.methods'));
        $response->assertSee(route('contact'));
    }

    /** @test */
    public function contact_page_contains_expected_form_action()
    {
        $response = $this->get(route('contact'));
        
        $response->assertStatus(200);
        
        // Form should post to the contact submit route
        $response->assertSee('action="' . route('contact.submit') . '"', false);
    }

    /** @test */
    public function login_page_contains_expected_form_action()
    {
        $response = $this->get('/login');
        
        $response->assertStatus(200);
        
        // Form should post to front login route
        $response->assertSee('action="' . route('front.login') . '"', false);
        
        // Should link to password reset page
        $response->assertSee(route('password.request'));
    }

    /** @test */
    public function admin_dashboard_contains_expected_links()
    {
        $admin = User::factory()->create([
            'is_active' => true,
        ]);
        
        // Simulate admin role
        $admin->is_admin = true;
        $admin->save();
        
        $response = $this->actingAs($admin)->get(route('admin.dashboard'));
        
        $response->assertStatus(200);
        
        // Check admin section links
        $response->assertSee(route('job-categories.index'));
        $response->assertSee(route('subscribers.index'));
        $response->assertSee(route('settings.index'));
        $response->assertSee(route('companySize.index'));
        $response->assertSee(route('skills.index'));
    }

    /** @test */
    public function candidate_dashboard_contains_expected_links()
    {
        $candidate = User::factory()->create([
            'is_active' => true,
        ]);
        
        // Create a candidate profile for this user
        $candidateProfile = Candidate::factory()->create([
            'user_id' => $candidate->id,
        ]);
        
        $response = $this->actingAs($candidate)->get('/candidate/dashboard');
        
        $response->assertStatus(200);
        
        // Check candidate dashboard links
        $response->assertSee('/candidate/profile/edit');
        $response->assertSee('/candidate/jobs');
        $response->assertSee('/candidate/favourite-jobs');
        $response->assertSee('/candidate/job-applications');
    }

    /** @test */
    public function job_listing_page_contains_pagination_and_filter_links()
    {
        // Create job category
        $category = JobCategory::factory()->create();
        
        // Create company
        $company = Company::factory()->create(['status' => 'active']);
        
        // Create multiple jobs to force pagination
        Job::factory()->count(15)->create([
            'company_id' => $company->id,
            'job_category_id' => $category->id,
            'status' => 'active',
        ]);
        
        $response = $this->get(route('jobs.index'));
        
        $response->assertStatus(200);
        
        // Should see pagination links if there are enough jobs
        $response->assertSee('page=2');
        
        // Filter form should post to jobs index
        $response->assertSee('action="' . route('jobs.index') . '"', false);
    }

    /** @test */
    public function job_detail_page_contains_expected_links()
    {
        // Create job category
        $category = JobCategory::factory()->create();
        
        // Create company
        $company = Company::factory()->create(['status' => 'active']);
        
        // Create job
        $job = Job::factory()->create([
            'company_id' => $company->id,
            'job_category_id' => $category->id,
            'status' => 'active',
            'title' => 'Featured Job Position',
        ]);
        
        $response = $this->get(route('jobs.show', $job->id));
        
        $response->assertStatus(200);
        
        // Should link to company profile
        $response->assertSee(route('companies.show', $company->id));
        
        // Should link to job application
        $response->assertSee(route('jobs.apply', $job->id));
        
        // Should link to related jobs by category
        $response->assertSee(route('jobs.index', ['category' => $category->id]));
    }

    /** @test */
    public function blade_templates_escape_output_properly()
    {
        // Create job with title containing HTML
        $category = JobCategory::factory()->create();
        $company = Company::factory()->create(['status' => 'active']);
        
        $job = Job::factory()->create([
            'company_id' => $company->id,
            'job_category_id' => $category->id,
            'status' => 'active',
            'title' => 'Job with <script>alert("XSS")</script>',
        ]);
        
        $response = $this->get(route('jobs.index'));
        
        $response->assertStatus(200);
        
        // Should see escaped HTML, not rendered script
        $response->assertSee('Job with &lt;script&gt;alert(&quot;XSS&quot;)&lt;/script&gt;', false);
        $response->assertDontSee('Job with <script>alert("XSS")</script>', false);
    }
} 