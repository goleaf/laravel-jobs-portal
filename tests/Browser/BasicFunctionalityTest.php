<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class BasicFunctionalityTest extends DuskTestCase
{
    /**
     * Enhanced pattern: Essential smoke test - most critical check.
     *
     * @test
     */
    public function applicationLoadsWithoutFatalErrors()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->waitFor('body', 30)  // Increased timeout for CI
                ->assertPresent('body')
            ;

            // Enhanced pattern: Verify basic page structure
            $pageSource = $browser->driver->getPageSource();
            $this->assertNotEmpty($pageSource);
            $this->assertStringContainsString('<html', $pageSource);
            $this->assertStringNotContainsString('Fatal error', $pageSource);
            $this->assertStringNotContainsString('Parse error', $pageSource);
        });
    }

    /**
     * Enhanced pattern: Test core public routes work.
     *
     * @test
     */
    public function essentialRoutesAreAccessible()
    {
        $this->browse(function (Browser $browser) {
            // Test homepage
            $browser->visit('/')
                ->waitFor('body', 20)
                ->assertPresent('body')
            ;

            // Test login page
            $browser->visit('/login')
                ->waitFor('body', 20)
                ->assertPresent('body')
            ;

            // Test register page
            $browser->visit('/register')
                ->waitFor('body', 20)
                ->assertPresent('body')
            ;
        });
    }

    /**
     * Enhanced pattern: Verify no critical HTTP errors.
     *
     * @test
     */
    public function pagesReturnValidHttpResponses()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->waitFor('body', 20)
                ->assertDontSee('500')
                ->assertDontSee('404')
                ->assertDontSee('403')
            ;
        });
    }

    /**
     * Enhanced pattern: Test basic navigation works.
     *
     * @test
     */
    public function browserNavigationFunctions()
    {
        $this->browse(function (Browser $browser) {
            // Start at homepage
            $browser->visit('/')
                ->waitFor('body', 20)
            ;

            // Navigate to another page
            $browser->visit('/login')
                ->waitFor('body', 15)
                ->assertPresent('body')
            ;

            // Test browser back
            $browser->back()
                ->waitFor('body', 10)
                ->assertPresent('body')
            ;
        });
    }

    /**
     * Enhanced pattern: Verify JavaScript basics work.
     *
     * @test
     */
    public function javascriptEnvironmentIsFunctional()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->waitFor('body', 20)
            ;

            // Test basic JavaScript functionality
            $windowExists = $browser->script('return typeof window !== "undefined";');
            $this->assertTrue($windowExists);

            $documentExists = $browser->script('return typeof document !== "undefined";');
            $this->assertTrue($documentExists);
        });
    }
}
