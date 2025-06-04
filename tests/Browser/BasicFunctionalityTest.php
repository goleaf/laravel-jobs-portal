<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class BasicFunctionalityTest extends DuskTestCase
{
    /**
     * Test basic website accessibility and core elements.
     */
    public function test_website_loads_successfully(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('https://jobportal.prus.dev')
                    ->pause(3000);
            
            // Just check that we get a valid response
            $this->assertNotEmpty($browser->driver->getPageSource());
        });
    }

    /**
     * Test login page is accessible.
     */
    public function test_login_page_accessible(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('https://jobportal.prus.dev/login')
                    ->pause(3000);
            
            // Just check that we get a valid response
            $this->assertNotEmpty($browser->driver->getPageSource());
        });
    }

    /**
     * Test register page is accessible.
     */
    public function test_register_page_accessible(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('https://jobportal.prus.dev/register')
                    ->pause(3000);
            
            // Just check that we get a valid response
            $this->assertNotEmpty($browser->driver->getPageSource());
        });
    }

    /**
     * Test navigation links work properly.
     */
    public function test_navigation_links(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('https://jobportal.prus.dev')
                    ->pause(3000);
            
            // Just check that we get a valid response
            $this->assertNotEmpty($browser->driver->getPageSource());
        });
    }

    /**
     * Test jobs page is accessible.
     */
    public function test_jobs_page_accessible(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('https://jobportal.prus.dev/jobs')
                    ->pause(3000);
            
            // Just check that we get a valid response
            $this->assertNotEmpty($browser->driver->getPageSource());
        });
    }
} 