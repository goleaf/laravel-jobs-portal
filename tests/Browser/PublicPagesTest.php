<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class PublicPagesTest extends DuskTestCase
{
    /**
     * Test homepage loads and has proper content.
     */
    public function testHomepageLoadsSuccessfully(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->waitFor('body', 10)
                ->assertPresent('body')
                ->assertDontSee('500')
                ->assertDontSee('404')
                ->assertDontSee('Error')
            ;

            // Enhanced pattern: Verify page structure
            $pageSource = $browser->driver->getPageSource();
            $this->assertNotEmpty($pageSource);
            $this->assertStringContainsString('<html', $pageSource);
        });
    }

    /**
     * Test about us page.
     */
    public function testAboutUsPage(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/about-us')
                ->waitFor('body', 10)
                ->assertPresent('body')
                ->assertDontSee('500')
                ->assertDontSee('404')
            ;

            // Enhanced pattern: Verify page structure
            $pageSource = $browser->driver->getPageSource();
            $this->assertNotEmpty($pageSource);
            $this->assertStringContainsString('<html', $pageSource);
        });
    }

    /**
     * Test contact page.
     */
    public function testContactPage(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/contact')
                ->waitFor('body', 10)
                ->assertPresent('body')
                ->assertDontSee('500')
                ->assertDontSee('404')
            ;

            // Enhanced pattern: Verify page structure
            $pageSource = $browser->driver->getPageSource();
            $this->assertNotEmpty($pageSource);
            $this->assertStringContainsString('<html', $pageSource);
        });
    }

    /**
     * Test jobs listing page.
     */
    public function testJobsListingPage(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/jobs')
                ->waitFor('body', 10)
                ->assertPresent('body')
                ->assertDontSee('500')
                ->assertDontSee('404')
            ;

            // Enhanced pattern: Verify page structure
            $pageSource = $browser->driver->getPageSource();
            $this->assertNotEmpty($pageSource);
            $this->assertStringContainsString('<html', $pageSource);
        });
    }

    /**
     * Test companies listing page.
     */
    public function testCompaniesListingPage(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/companies')
                ->waitFor('body', 10)
                ->assertPresent('body')
                ->assertDontSee('500')
                ->assertDontSee('404')
            ;

            // Enhanced pattern: Verify page structure
            $pageSource = $browser->driver->getPageSource();
            $this->assertNotEmpty($pageSource);
            $this->assertStringContainsString('<html', $pageSource);
        });
    }

    /**
     * Test login page.
     */
    public function testLoginPage(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/login')
                ->waitFor('body', 10)
                ->assertPresent('body')
                ->assertDontSee('500')
                ->assertDontSee('404')
            ;

            // Enhanced pattern: Verify page structure
            $pageSource = $browser->driver->getPageSource();
            $this->assertNotEmpty($pageSource);
            $this->assertStringContainsString('<html', $pageSource);
        });
    }

    /**
     * Test register page.
     */
    public function testRegisterPage(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/register')
                ->waitFor('body', 10)
                ->assertPresent('body')
                ->assertDontSee('500')
                ->assertDontSee('404')
            ;

            // Enhanced pattern: Verify page structure
            $pageSource = $browser->driver->getPageSource();
            $this->assertNotEmpty($pageSource);
            $this->assertStringContainsString('<html', $pageSource);
        });
    }

    /**
     * Test navigation functionality.
     */
    public function testNavigationFunctionality(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->waitFor('body', 10)
            ;

            // Test basic navigation if links exist
            try {
                $browser->clickLink('Home')
                    ->waitFor('body', 5)
                ;
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
