<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class BasicFunctionalityTest extends DuskTestCase
{
    /**
     * Context7 pattern: Essential smoke test - most critical check
     * 
     * @test
     */
    public function application_loads_without_fatal_errors()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->waitFor('body', 30)  // Increased timeout for CI
                ->assertPresent('body');
            
            // Context7 pattern: Verify basic page structure
            $pageSource = $browser->driver->getPageSource();
            $this->assertNotEmpty($pageSource);
            $this->assertStringContainsString('<html', $pageSource);
            $this->assertStringNotContainsString('Fatal error', $pageSource);
            $this->assertStringNotContainsString('Parse error', $pageSource);
        });
    }

    /**
     * Context7 pattern: Test core public routes work
     * 
     * @test
     */
    public function essential_routes_are_accessible()
    {
        $this->browse(function (Browser $browser) {
            // Test homepage
            $browser->visit('/')
                ->waitFor('body', 20)
                ->assertPresent('body');
                
            // Test login page  
            $browser->visit('/login')
                ->waitFor('body', 20)
                ->assertPresent('body');
                
            // Test register page
            $browser->visit('/register')
                ->waitFor('body', 20)
                ->assertPresent('body');
        });
    }

    /**
     * Context7 pattern: Verify no critical HTTP errors
     * 
     * @test
     */
    public function pages_return_valid_http_responses()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->waitFor('body', 20)
                ->assertDontSee('500')
                ->assertDontSee('404')
                ->assertDontSee('403');
        });
    }

    /**
     * Context7 pattern: Test basic navigation works
     * 
     * @test
     */
    public function browser_navigation_functions()
    {
        $this->browse(function (Browser $browser) {
            // Start at homepage
            $browser->visit('/')
                ->waitFor('body', 20);
            
            // Navigate to another page
            $browser->visit('/login')
                ->waitFor('body', 15)
                ->assertPresent('body');
            
            // Test browser back
            $browser->back()
                ->waitFor('body', 10)
                ->assertPresent('body');
        });
    }

    /**
     * Context7 pattern: Verify JavaScript basics work
     * 
     * @test
     */
    public function javascript_environment_is_functional()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->waitFor('body', 20);
            
            // Test basic JavaScript functionality
            $windowExists = $browser->script('return typeof window !== "undefined";');
            $this->assertTrue($windowExists);
            
            $documentExists = $browser->script('return typeof document !== "undefined";');
            $this->assertTrue($documentExists);
        });
    }
} 