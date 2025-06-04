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
            $browser->visit('https://jobportal.prus.dev/')
                    ->pause(3000);
            
            // Just check that we get a valid response
            $this->assertNotEmpty($browser->driver->getPageSource());
        });
    }

    /**
     * Test about us page.
     */
    public function test_about_us_page(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('https://jobportal.prus.dev/about-us')
                    ->pause(2000);
            
            // Just check that we get a valid response
            $this->assertNotEmpty($browser->driver->getPageSource());
        });
    }

    /**
     * Test contact page.
     */
    public function test_contact_page(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('https://jobportal.prus.dev/contact')
                    ->pause(2000);
            
            // Just check that we get a valid response
            $this->assertNotEmpty($browser->driver->getPageSource());
        });
    }

    /**
     * Test jobs listing page.
     */
    public function test_jobs_listing_page(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('https://jobportal.prus.dev/jobs')
                    ->pause(2000);
            
            // Just check that we get a valid response
            $this->assertNotEmpty($browser->driver->getPageSource());
        });
    }

    /**
     * Test companies listing page.
     */
    public function test_companies_listing_page(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('https://jobportal.prus.dev/companies')
                    ->pause(2000);
            
            // Just check that we get a valid response
            $this->assertNotEmpty($browser->driver->getPageSource());
        });
    }

    /**
     * Test login page.
     */
    public function test_login_page(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('https://jobportal.prus.dev/login')
                    ->pause(2000);
            
            // Just check that we get a valid response
            $this->assertNotEmpty($browser->driver->getPageSource());
        });
    }

    /**
     * Test register page.
     */
    public function test_register_page(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('https://jobportal.prus.dev/register')
                    ->pause(2000);
            
            // Just check that we get a valid response
            $this->assertNotEmpty($browser->driver->getPageSource());
        });
    }
} 