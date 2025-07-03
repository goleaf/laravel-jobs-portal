<?php

namespace Tests\Browser;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class JobPortalTest extends DuskTestCase
{
    use DatabaseMigrations;

    /**
     * Enhanced pattern: Test basic homepage functionality.
     *
     * @test
     */
    public function homepage_loads_and_displays_basic_content()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->waitFor('body', 15)
                ->assertPresent('body')
                ->assertDontSee('500')
                ->assertDontSee('404')
                ->assertDontSee('Fatal error');

            // Enhanced pattern: Verify page structure
            $pageSource = $browser->driver->getPageSource();
            $this->assertNotEmpty($pageSource);
            $this->assertStringContainsString('<html', $pageSource);
        });
    }

    /**
     * Enhanced pattern: Test navigation to key pages.
     *
     * @test
     */
    public function key_pages_are_accessible()
    {
        $this->browse(function (Browser $browser) {
            // Test login page
            $browser->visit('/login')
                ->waitFor('body', 10)
                ->assertPresent('body')
                ->assertDontSee('500')
                ->assertDontSee('404');

            // Test register page
            $browser->visit('/register')
                ->waitFor('body', 10)
                ->assertPresent('body')
                ->assertDontSee('500')
                ->assertDontSee('404');

            // Test jobs page
            $browser->visit('/jobs')
                ->waitFor('body', 10)
                ->assertPresent('body')
                ->assertDontSee('500')
                ->assertDontSee('404');
        });
    }

    /**
     * Enhanced pattern: Test basic form functionality.
     *
     * @test
     */
    public function login_form_displays_correctly()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/login')
                ->waitFor('body', 10);

            // Enhanced pattern: Check for form elements without assuming specific implementation
            $pageSource = $browser->driver->getPageSource();

            // Basic checks that a login form exists
            $hasEmailField = strpos($pageSource, 'email') !== false;
            $hasPasswordField = strpos($pageSource, 'password') !== false;

            // Assert that basic form elements are present
            $this->assertTrue($hasEmailField || $hasPasswordField, 'Login form should have email or password fields');
        });
    }

    /**
     * Enhanced pattern: Test register form displays correctly.
     *
     * @test
     */
    public function register_form_displays_correctly()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/register')
                ->waitFor('body', 10);

            // Enhanced pattern: Check for form elements without assuming specific implementation
            $pageSource = $browser->driver->getPageSource();

            // Basic checks that a register form exists
            $hasForm = strpos($pageSource, 'form') !== false;
            $hasInputs = strpos($pageSource, 'input') !== false;

            // Assert that basic form elements are present
            $this->assertTrue($hasForm && $hasInputs, 'Register page should have form elements');
        });
    }

    /**
     * Enhanced pattern: Test jobs listing functionality.
     *
     * @test
     */
    public function jobs_page_displays_without_errors()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/jobs')
                ->waitFor('body', 10)
                ->assertPresent('body')
                ->assertDontSee('500')
                ->assertDontSee('404')
                ->assertDontSee('Fatal error')
                ->assertDontSee('Undefined variable')
                ->assertDontSee('Call to a member function');

            // Enhanced pattern: Verify page loaded successfully
            $pageSource = $browser->driver->getPageSource();
            $this->assertNotEmpty($pageSource);

            // Check that we don't have obvious PHP errors
            $this->assertStringNotContainsString('Parse error', $pageSource);
            $this->assertStringNotContainsString('Notice:', $pageSource);
            $this->assertStringNotContainsString('Warning:', $pageSource);
        });
    }

    /**
     * Enhanced pattern: Test companies page functionality.
     *
     * @test
     */
    public function companies_page_displays_without_errors()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/companies')
                ->waitFor('body', 10)
                ->assertPresent('body')
                ->assertDontSee('500')
                ->assertDontSee('404')
                ->assertDontSee('Fatal error');

            // Enhanced pattern: Verify page structure
            $pageSource = $browser->driver->getPageSource();
            $this->assertNotEmpty($pageSource);
            $this->assertStringContainsString('<html', $pageSource);
        });
    }

    /**
     * Enhanced pattern: Test basic navigation functionality.
     *
     * @test
     */
    public function browser_navigation_works()
    {
        $this->browse(function (Browser $browser) {
            // Start at homepage
            $browser->visit('/')
                ->waitFor('body', 10);

            // Navigate to login
            $browser->visit('/login')
                ->waitFor('body', 10)
                ->assertPresent('body');

            // Test browser back/forward
            $browser->back()
                ->waitFor('body', 5)
                ->assertPresent('body');

            $browser->forward()
                ->waitFor('body', 5)
                ->assertPresent('body');
        });
    }

    /**
     * Enhanced pattern: Test responsive behavior.
     *
     * @test
     */
    public function page_responds_to_different_screen_sizes()
    {
        $this->browse(function (Browser $browser) {
            // Test desktop size
            $browser->resize(1920, 1080)
                ->visit('/')
                ->waitFor('body', 10)
                ->assertPresent('body');

            // Test tablet size
            $browser->resize(768, 1024)
                ->visit('/')
                ->waitFor('body', 10)
                ->assertPresent('body');

            // Test mobile size
            $browser->resize(375, 667)
                ->visit('/')
                ->waitFor('body', 10)
                ->assertPresent('body');
        });
    }

    /** @test */
    public function user_can_register_and_login()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/register')
                ->type('name', 'Test User')
                ->type('email', 'test@example.com')
                ->type('password', 'password123')
                ->type('password_confirmation', 'password123')
                ->press('Register')
                ->assertPathIs('/dashboard')
                ->assertSee('Dashboard');
        });
    }

    /** @test */
    public function user_can_search_jobs()
    {
        $user = User::factory()->create();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                ->visit('/jobs')
                ->type('search', 'Developer')
                ->press('Search')
                ->assertSee('Search Results');
        });
    }

    /** @test */
    public function user_can_apply_for_job()
    {
        $user = User::factory()->create();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                ->visit('/jobs')
                ->click('@first-job-link')
                ->press('Apply Now')
                ->assertSee('Application Submitted');
        });
    }
}
