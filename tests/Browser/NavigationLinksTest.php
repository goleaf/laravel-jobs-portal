<?php

namespace Tests\Browser;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class NavigationLinksTest extends DuskTestCase
{
    use DatabaseMigrations;

    /** @test */
    public function it_can_navigate_home_page_links()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                    ->assertSee('Job Portal')
                    ->clickLink('Contact')
                    ->assertPathIs('/contact')
                    ->back()
                    ->clickLink('Jobs')
                    ->assertPathIs('/jobs')
                    ->back()
                    ->clickLink('Login')
                    ->assertPathIs('/login');
        });
    }

    /** @test */
    public function it_can_navigate_forms_page_links()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/forms/validation')
                    ->assertSee('Validation Form')
                    ->clickLink('Alpine.js Form')
                    ->assertPathIs('/forms/alpine')
                    ->clickLink('Model Binding')
                    ->assertPathIs('/forms/binding')
                    ->clickLink('Error Handling')
                    ->assertPathIs('/forms/errors')
                    ->clickLink('Method Spoofing')
                    ->assertPathIs('/forms/methods')
                    ->clickLink('Contact Form')
                    ->assertPathIs('/contact');
        });
    }

    /** @test */
    public function it_can_navigate_authenticated_user_links()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
            'is_active' => true,
        ]);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->visit('/login')
                    ->type('email', 'test@example.com')
                    ->type('password', 'password')
                    ->press('Login')
                    ->assertPathIs('/dashboard')
                    ->clickLink('Profile')
                    ->assertPathIs('/user/profile')
                    ->clickLink('Dashboard')
                    ->assertPathIs('/dashboard')
                    ->clickLink('Logout')
                    ->assertPathIs('/');
        });
    }

    /** @test */
    public function it_can_navigate_admin_dashboard_links()
    {
        $admin = User::factory()->create([
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'is_active' => true,
        ]);
        
        // Simulate admin role - this might need to be adjusted based on how role is stored
        $admin->is_admin = true;
        $admin->save();

        $this->browse(function (Browser $browser) use ($admin) {
            $browser->visit('/admin/login')
                    ->type('email', 'admin@example.com')
                    ->type('password', 'password')
                    ->press('Login')
                    ->assertPathIs('/admin/dashboard')
                    ->clickLink('Job Categories')
                    ->assertPathIs('/admin/job-categories')
                    ->clickLink('Skills')
                    ->assertPathIs('/admin/skills')
                    ->clickLink('Settings')
                    ->assertPathIs('/admin/settings');
        });
    }

    /** @test */
    public function it_can_navigate_job_listings_pagination_and_filters()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/jobs')
                    ->assertSee('Job Listings')
                    // Test pagination links
                    ->clickLink('2')
                    ->assertQueryStringHas('page', 2)
                    // Test filter links
                    ->select('category', '1')
                    ->press('Filter')
                    ->assertQueryStringHas('category', 1)
                    ->select('location', 'Remote')
                    ->press('Filter')
                    ->assertQueryStringHas('location', 'Remote');
        });
    }

    /** @test */
    public function it_can_navigate_job_details_page_links()
    {
        // Create a job first
        $job = \App\Models\Job::factory()->create([
            'status' => 'active',
            'title' => 'Software Developer'
        ]);

        $this->browse(function (Browser $browser) use ($job) {
            $browser->visit('/jobs/' . $job->id)
                    ->assertSee('Software Developer')
                    ->clickLink('Apply Now')
                    ->assertPathIs('/jobs/' . $job->id . '/apply')
                    ->back()
                    ->clickLink('Company Profile')
                    ->assertPathIs('/companies/' . $job->company_id);
        });
    }
} 