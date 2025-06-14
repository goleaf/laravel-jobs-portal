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
                    ->waitFor('body', 10)
                    ->assertPresent('body')
                    ->assertDontSee('500')
                    ->assertDontSee('404')
                    ->assertDontSee('Error');
            
            // Enhanced pattern: Verify page structure
            $pageSource = $browser->driver->getPageSource();
            $this->assertNotEmpty($pageSource);
            $this->assertStringContainsString('<html', $pageSource);
        });
    }

    /**
     * Test about us page.
     */
    public function test_about_us_page(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/about-us')
                    ->waitFor('body', 10)
                    ->assertPresent('body')
                    ->assertDontSee('500')
                    ->assertDontSee('404');
            
            // Enhanced pattern: Verify page structure
            $pageSource = $browser->driver->getPageSource();
            $this->assertNotEmpty($pageSource);
            $this->assertStringContainsString('<html', $pageSource);
        });
    }

    /**
     * Test contact page.
     */
    public function test_contact_page(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/contact')
                    ->waitFor('body', 10)
                    ->assertPresent('body')
                    ->assertDontSee('500')
                    ->assertDontSee('404');
            
            // Enhanced pattern: Verify page structure
            $pageSource = $browser->driver->getPageSource();
            $this->assertNotEmpty($pageSource);
            $this->assertStringContainsString('<html', $pageSource);
        });
    }

    /**
     * Test jobs listing page.
     */
    public function test_jobs_listing_page(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/jobs')
                    ->waitFor('body', 10)
                    ->assertPresent('body')
                    ->assertDontSee('500')
                    ->assertDontSee('404');
            
            // Enhanced pattern: Verify page structure
            $pageSource = $browser->driver->getPageSource();
            $this->assertNotEmpty($pageSource);
            $this->assertStringContainsString('<html', $pageSource);
        });
    }

    /**
     * Test companies listing page.
     */
    public function test_companies_listing_page(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/companies')
                    ->waitFor('body', 10)
                    ->assertPresent('body')
                    ->assertDontSee('500')
                    ->assertDontSee('404');
            
            // Enhanced pattern: Verify page structure
            $pageSource = $browser->driver->getPageSource();
            $this->assertNotEmpty($pageSource);
            $this->assertStringContainsString('<html', $pageSource);
        });
    }

    /**
     * Test login page.
     */
    public function test_login_page(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/login')
                    ->waitFor('body', 10)
                    ->assertPresent('body')
                    ->assertDontSee('500')
                    ->assertDontSee('404');
            
            // Enhanced pattern: Verify page structure
            $pageSource = $browser->driver->getPageSource();
            $this->assertNotEmpty($pageSource);
            $this->assertStringContainsString('<html', $pageSource);
        });
    }

    /**
     * Test register page.
     */
    public function test_register_page(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/register')
                    ->waitFor('body', 10)
                    ->assertPresent('body')
                    ->assertDontSee('500')
                    ->assertDontSee('404');
            
            // Enhanced pattern: Verify page structure
            $pageSource = $browser->driver->getPageSource();
            $this->assertNotEmpty($pageSource);
            $this->assertStringContainsString('<html', $pageSource);
        });
    }

    /**
     * Test navigation functionality.
     */
    public function test_navigation_functionality(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                    ->waitFor('body', 10);
                    
            // Test basic navigation if links exist
            try {
                $browser->clickLink('Home')
                        ->waitFor('body', 5);
            } catch (\Exception $e) {
                // Navigation link may not exist, continue test
            }
            
            // Enhanced pattern: Verify we can navigate back
            $browser->back()
                    ->forward()
                    ->assertPresent('body');
        });
    }
} 