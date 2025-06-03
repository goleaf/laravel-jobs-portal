<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class PublicPagesTest extends DuskTestCase
{
    /**
     * Test homepage loads and has proper content.
     */
    public function test_homepage_loads_successfully(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                    ->pause(3000)
                    ->assertDontSee('Unsupported cipher')
                    ->assertDontSee('Error')
                    ->assertPresent('body')
                    ->assertSee('Jobs');
        });
    }

    /**
     * Test about us page.
     */
    public function test_about_us_page(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/about-us')
                    ->pause(2000)
                    ->assertDontSee('Error')
                    ->assertPresent('body');
        });
    }

    /**
     * Test contact page.
     */
    public function test_contact_page(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/contact')
                    ->pause(2000)
                    ->assertDontSee('Error')
                    ->assertPresent('body');
        });
    }

    /**
     * Test privacy policy page.
     */
    public function test_privacy_policy_page(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/privacy-policy')
                    ->pause(2000)
                    ->assertDontSee('Error')
                    ->assertPresent('body');
        });
    }

    /**
     * Test terms and conditions page.
     */
    public function test_terms_conditions_page(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/terms-conditions')
                    ->pause(2000)
                    ->assertDontSee('Error')
                    ->assertPresent('body');
        });
    }

    /**
     * Test jobs listing page.
     */
    public function test_jobs_listing_page(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/jobs')
                    ->pause(2000)
                    ->assertDontSee('Error')
                    ->assertPresent('body')
                    ->assertSee('Jobs');
        });
    }

    /**
     * Test companies listing page.
     */
    public function test_companies_listing_page(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/companies')
                    ->pause(2000)
                    ->assertDontSee('Error')
                    ->assertPresent('body');
        });
    }

    /**
     * Test login page.
     */
    public function test_login_page(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/login')
                    ->pause(2000)
                    ->assertDontSee('Error')
                    ->assertPresent('body')
                    ->assertSee('Login');
        });
    }

    /**
     * Test register page.
     */
    public function test_register_page(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/register')
                    ->pause(2000)
                    ->assertDontSee('Error')
                    ->assertPresent('body')
                    ->assertSee('Register');
        });
    }

    /**
     * Test password reset page.
     */
    public function test_password_reset_page(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/password/reset')
                    ->pause(2000)
                    ->assertDontSee('Error')
                    ->assertPresent('body');
        });
    }
} 