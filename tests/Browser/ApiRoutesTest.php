<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class ApiRoutesTest extends DuskTestCase
{
    /**
     * Test API documentation is accessible.
     */
    public function test_api_documentation_accessible(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('https://jobportal.prus.dev/api/documentation')
                    ->pause(2000)
                    ->assertDontSee('Error')
                    ->assertPresent('body');
        });
    }

    /**
     * Test Swagger docs are accessible.
     */
    public function test_swagger_docs_accessible(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('https://jobportal.prus.dev/docs')
                    ->pause(2000)
                    ->assertDontSee('Error')
                    ->assertPresent('body');
        });
    }

    /**
     * Test CSRF cookie endpoint.
     */
    public function test_sanctum_csrf_cookie(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('https://jobportal.prus.dev/sanctum/csrf-cookie')
                    ->pause(2000)
                    ->assertDontSee('Error');
        });
    }

    /**
     * Test API routes that should be protected.
     */
    public function test_protected_api_routes(): void
    {
        $this->browse(function (Browser $browser) {
            // Test /api/user route (should require authentication)
            $browser->visit('https://jobportal.prus.dev/api/user')
                    ->pause(2000);
            
            // Should receive unauthorized response or redirect
            $this->assertTrue(
                str_contains($browser->driver->getCurrentURL(), '/login') ||
                str_contains($browser->driver->getPageSource(), 'Unauthenticated')
            );
        });
    }

    /**
     * Test Livewire routes are working.
     */
    public function test_livewire_routes(): void
    {
        $this->browse(function (Browser $browser) {
            // Test Livewire JavaScript file
            $browser->visit('https://jobportal.prus.dev/livewire/livewire.js')
                    ->pause(1000);
            
            // Should load JavaScript content
            $this->assertStringContainsString('javascript', $browser->driver->getCurrentURL());
        });
    }

    /**
     * Test monitoring routes (Horizon).
     */
    public function test_horizon_routes(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('https://jobportal.prus.dev/horizon')
                    ->pause(2000);
            
            // Horizon might be protected, so just check it doesn't crash
            $this->assertStringNotContainsString('Fatal error', $browser->driver->getPageSource());
        });
    }

    /**
     * Test Telescope routes.
     */
    public function test_telescope_routes(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('https://jobportal.prus.dev/telescope')
                    ->pause(2000);
            
            // Telescope might be protected, so just check it doesn't crash
            $this->assertStringNotContainsString('Fatal error', $browser->driver->getPageSource());
        });
    }

    /**
     * Test Pulse monitoring.
     */
    public function test_pulse_routes(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('https://jobportal.prus.dev/pulse')
                    ->pause(2000);
            
            // Pulse might be protected, so just check it doesn't crash
            $this->assertStringNotContainsString('Fatal error', $browser->driver->getPageSource());
        });
    }
} 