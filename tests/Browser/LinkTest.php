<?php

namespace Tests\Browser;

use App\Models\User;
use App\Models\Job;
use App\Models\Company;
use App\Models\JobCategory;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class LinkTest extends DuskTestCase
{
    use DatabaseMigrations;

    /** @test */
    public function homepage_navigation_links_work()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                    ->assertSee('Home')
                    ->clickLink('Jobs')
                    ->assertPathIs('/jobs')
                    ->clickLink('Companies')
                    ->assertPathIs('/companies')
                    ->clickLink('About Us')
                    ->assertPathIs('/about-us')
                    ->clickLink('Contact')
                    ->assertPathIs('/contact');
        });
    }

    /** @test */
    public function footer_links_work()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                    ->scrollIntoView('footer')
                    ->assertSee('Quick Links')
                    ->clickLink('About Us')
                    ->assertPathIs('/about-us')
                    ->back()
                    ->scrollIntoView('footer')
                    ->clickLink('Contact')
                    ->assertPathIs('/contact')
                    ->back()
                    ->scrollIntoView('footer')
                    ->clickLink('Privacy Policy')
                    ->assertPathIs('/privacy-policy')
                    ->back()
                    ->scrollIntoView('footer')
                    ->clickLink('Terms & Conditions')
                    ->assertPathIs('/terms-conditions');
        });
    }

    /** @test */
    public function job_listing_links_work()
    {
        $jobCategory = JobCategory::factory()->create();
        $job = Job::factory()->create([
            'job_title' => 'PHP Developer',
            'job_category_id' => $jobCategory->id,
            'status' => Job::STATUS_OPEN,
        ]);

        $this->browse(function (Browser $browser) use ($job) {
            $browser->visit('/jobs')
                    ->assertSee('PHP Developer')
                    ->click('.job-listing-item[data-job-id="'.$job->id.'"]')
                    ->assertPathIs('/jobs/'.$job->id)
                    ->assertSee('PHP Developer')
                    ->assertSee('Apply Now');
        });
    }

    /** @test */
    public function company_listing_links_work()
    {
        $company = Company::factory()->create([
            'name' => 'Acme Inc',
            'is_active' => true,
        ]);

        $this->browse(function (Browser $browser) use ($company) {
            $browser->visit('/companies')
                    ->assertSee('Acme Inc')
                    ->click('.company-item[data-company-id="'.$company->id.'"]')
                    ->assertPathIs('/company/'.$company->id)
                    ->assertSee('Acme Inc')
                    ->assertSee('Company Profile');
        });
    }

    /** @test */
    public function job_category_links_work()
    {
        $category = JobCategory::factory()->create([
            'name' => 'Web Development',
        ]);
        
        Job::factory()->create([
            'job_category_id' => $category->id,
            'status' => Job::STATUS_OPEN,
        ]);

        $this->browse(function (Browser $browser) use ($category) {
            $browser->visit('/')
                    ->assertSee('Web Development')
                    ->clickLink('Web Development')
                    ->assertQueryStringHas('category', $category->id)
                    ->assertSee('Jobs in Web Development');
        });
    }

    /** @test */
    public function pagination_links_work()
    {
        // Create enough jobs to trigger pagination
        Job::factory()->count(20)->create([
            'status' => Job::STATUS_OPEN,
        ]);

        $this->browse(function (Browser $browser) {
            $browser->visit('/jobs')
                    ->assertSee('Next')
                    ->clickLink('2')
                    ->assertQueryStringHas('page', 2)
                    ->assertSee('Previous')
                    ->clickLink('Previous')
                    ->assertQueryStringHas('page', 1);
        });
    }

    /** @test */
    public function social_media_links_have_correct_targets()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                    ->assertPresent('.social-media-links')
                    ->assertAttribute('.facebook-link', 'href', 'https://facebook.com/')
                    ->assertAttribute('.twitter-link', 'href', 'https://twitter.com/')
                    ->assertAttribute('.linkedin-link', 'href', 'https://linkedin.com/')
                    ->assertAttribute('.instagram-link', 'href', 'https://instagram.com/');
        });
    }

    /** @test */
    public function candidate_dashboard_links_work()
    {
        $user = User::factory()->create([
            'email' => 'candidate@example.com',
            'password' => bcrypt('password123'),
            'user_type' => User::CANDIDATE,
            'is_active' => true,
        ]);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->visit('/login')
                    ->type('email', 'candidate@example.com')
                    ->type('password', 'password123')
                    ->press('Login')
                    ->assertPathIs('/dashboard')
                    ->clickLink('My Profile')
                    ->assertPathIs('/candidate/profile')
                    ->clickLink('Dashboard')
                    ->assertPathIs('/dashboard')
                    ->clickLink('Applied Jobs')
                    ->assertPathIs('/candidate/applied-jobs')
                    ->clickLink('Dashboard')
                    ->assertPathIs('/dashboard')
                    ->clickLink('Favorite Jobs')
                    ->assertPathIs('/candidate/favorite-jobs')
                    ->clickLink('Job Alerts')
                    ->assertPathIs('/candidate/job-alerts');
        });
    }

    /** @test */
    public function employer_dashboard_links_work()
    {
        $user = User::factory()->create([
            'email' => 'employer@example.com',
            'password' => bcrypt('password123'),
            'user_type' => User::EMPLOYER,
            'is_active' => true,
        ]);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->visit('/login')
                    ->type('email', 'employer@example.com')
                    ->type('password', 'password123')
                    ->press('Login')
                    ->assertPathIs('/dashboard')
                    ->clickLink('Company Profile')
                    ->assertPathIs('/employer/company')
                    ->clickLink('Dashboard')
                    ->assertPathIs('/dashboard')
                    ->clickLink('Post a Job')
                    ->assertPathIs('/employer/jobs/create')
                    ->clickLink('Dashboard')
                    ->assertPathIs('/dashboard')
                    ->clickLink('My Jobs')
                    ->assertPathIs('/employer/jobs')
                    ->clickLink('Applications')
                    ->assertPathIs('/employer/jobs/applications');
        });
    }
} 