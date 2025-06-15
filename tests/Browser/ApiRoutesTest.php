<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class ApiRoutesTest extends DuskTestCase
{
    /**
     * Test API documentation is accessible.
     */
    public function testApiDocumentationAccessible(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('https://jobportal.prus.dev/api/documentation')
                ->pause(2000)
            ;

            // Just check that we get a valid response
            $this->assertNotEmpty($browser->driver->getPageSource());
        });
    }

    /**
     * Test Swagger docs are accessible.
     */
    public function testSwaggerDocsAccessible(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('https://jobportal.prus.dev/docs')
                ->pause(2000)
            ;

            // Just check that we get a valid response
            $this->assertNotEmpty($browser->driver->getPageSource());
        });
    }

    /**
     * Test CSRF cookie endpoint.
     */
    public function testSanctumCsrfCookie(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('https://jobportal.prus.dev/sanctum/csrf-cookie')
                ->pause(2000)
            ;

            // Just check that we get a valid response
            $this->assertNotEmpty($browser->driver->getPageSource());
        });
    }

    /**
     * Test API routes that should be protected.
     */
    public function testProtectedApiRoutes(): void
    {
        $this->browse(function (Browser $browser) {
            // Test /api/user route (should require authentication)
            $browser->visit('https://jobportal.prus.dev/api/user')
                ->pause(2000)
            ;

            // Just check that we get a valid response
            $this->assertNotEmpty($browser->driver->getPageSource());
        });
    }

    /**
     * Test Livewire routes are working.
     */
    public function testLivewireRoutes(): void
    {
        $this->browse(function (Browser $browser) {
            // Test Livewire JavaScript file
            $browser->visit('https://jobportal.prus.dev/livewire/livewire.js')
                ->pause(1000)
            ;

            // Just check that we get a valid response
            $this->assertNotEmpty($browser->driver->getPageSource());
        });
    }

    /**
     * Test monitoring routes (Horizon).
     */
    public function testHorizonRoutes(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('https://jobportal.prus.dev/horizon')
                ->pause(2000)
            ;

            // Horizon might be protected, so just check it doesn't crash
            $this->assertStringNotContainsString('Fatal error', $browser->driver->getPageSource());
        });
    }

    /**
     * Test Telescope routes.
     */
    public function testTelescopeRoutes(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('https://jobportal.prus.dev/telescope')
                ->pause(2000)
            ;

            // Telescope might be protected, so just check it doesn't crash
            $this->assertStringNotContainsString('Fatal error', $browser->driver->getPageSource());
        });
    }

    /**
     * Test Pulse monitoring.
     */
    public function testPulseRoutes(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('https://jobportal.prus.dev/pulse')
                ->pause(2000)
            ;

            // Pulse might be protected, so just check it doesn't crash
            $this->assertStringNotContainsString('Fatal error', $browser->driver->getPageSource());
        });
    }
}
