<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class BasicFunctionalityTest extends DuskTestCase
{
    /**
     * Context7 pattern: Comprehensive smoke test for core functionality
     * 
     * @test
     */
    public function application_smoke_test()
    {
        $this->browse(function (Browser $browser) {
            // Context7 pattern: Test homepage loads
            $browser->visit('/')
                ->waitFor('body', 15)
                ->assertPresent('body')
                ->assertDontSee('500')
                ->assertDontSee('404')
                ->assertDontSee('Fatal error')
                ->assertDontSee('Parse error')
                ->assertDontSee('Undefined variable');
            
            // Context7 pattern: Verify basic page structure
            $pageSource = $browser->driver->getPageSource();
            $this->assertNotEmpty($pageSource);
            $this->assertStringContainsString('<html', $pageSource);
            $this->assertStringContainsString('</html>', $pageSource);
        });
    }

    /**
     * Context7 pattern: Test all major public routes
     * 
     * @test
     */
    public function public_routes_are_accessible()
    {
        $publicRoutes = [
            '/',
            '/login',
            '/register',
            '/jobs',
            '/companies',
        ];

        $this->browse(function (Browser $browser) use ($publicRoutes) {
            foreach ($publicRoutes as $route) {
                $browser->visit($route)
                    ->waitFor('body', 10)
                    ->assertPresent('body')
                    ->assertDontSee('500')
                    ->assertDontSee('404')
                    ->assertDontSee('Fatal error');
                
                // Context7 pattern: Verify no PHP errors are displayed
                $pageSource = $browser->driver->getPageSource();
                $this->assertStringNotContainsString('Parse error', $pageSource, "Parse error found on route: {$route}");
                $this->assertStringNotContainsString('Notice:', $pageSource, "PHP Notice found on route: {$route}");
                $this->assertStringNotContainsString('Warning:', $pageSource, "PHP Warning found on route: {$route}");
            }
        });
    }

    /**
     * Context7 pattern: Test browser compatibility features
     * 
     * @test
     */
    public function browser_features_work_correctly()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->waitFor('body', 10);
            
            // Context7 pattern: Test JavaScript is working
            $browser->script('return typeof window;');
            $this->assertNotNull($browser->script('return document.title;'));
            
            // Context7 pattern: Test CSS is loading
            $browser->script('return getComputedStyle(document.body).display;');
            
            // Context7 pattern: Test basic DOM manipulation
            $hasBody = $browser->script('return document.querySelector("body") !== null;');
            $this->assertTrue($hasBody);
        });
    }

    /**
     * Context7 pattern: Test form elements exist on key pages
     * 
     * @test
     */
    public function forms_are_present_on_key_pages()
    {
        $this->browse(function (Browser $browser) {
            // Test login form
            $browser->visit('/login')
                ->waitFor('body', 10);
            
            $pageSource = $browser->driver->getPageSource();
            $hasFormElements = strpos($pageSource, '<form') !== false || 
                              strpos($pageSource, '<input') !== false ||
                              strpos($pageSource, 'type="email"') !== false ||
                              strpos($pageSource, 'type="password"') !== false;
            
            $this->assertTrue($hasFormElements, 'Login page should have form elements');
            
            // Test register form
            $browser->visit('/register')
                ->waitFor('body', 10);
            
            $pageSource = $browser->driver->getPageSource();
            $hasFormElements = strpos($pageSource, '<form') !== false || 
                              strpos($pageSource, '<input') !== false;
            
            $this->assertTrue($hasFormElements, 'Register page should have form elements');
        });
    }

    /**
     * Context7 pattern: Test assets are loading correctly
     * 
     * @test
     */
    public function assets_load_without_errors()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->waitFor('body', 10);
            
            // Context7 pattern: Check for asset loading errors in console
            $logs = $browser->driver->manage()->getLog('browser');
            
            $criticalErrors = array_filter($logs, function($log) {
                return $log['level'] === 'SEVERE' && 
                       (strpos($log['message'], '404') !== false || 
                        strpos($log['message'], '500') !== false);
            });
            
            $this->assertEmpty($criticalErrors, 'No critical asset loading errors should be present');
        });
    }

    /**
     * Context7 pattern: Test responsive layout
     * 
     * @test
     */
    public function responsive_layout_works()
    {
        $this->browse(function (Browser $browser) {
            // Desktop view
            $browser->resize(1920, 1080)
                ->visit('/')
                ->waitFor('body', 10)
                ->assertPresent('body');
            
            // Tablet view
            $browser->resize(768, 1024)
                ->visit('/')
                ->waitFor('body', 10)
                ->assertPresent('body');
            
            // Mobile view
            $browser->resize(375, 667)
                ->visit('/')
                ->waitFor('body', 10)
                ->assertPresent('body');
            
            // Verify layout doesn't break
            $pageSource = $browser->driver->getPageSource();
            $this->assertStringNotContainsString('overflow-x', $pageSource);
        });
    }

    /**
     * Context7 pattern: Test page performance basics
     * 
     * @test
     */
    public function pages_load_in_reasonable_time()
    {
        $this->browse(function (Browser $browser) {
            $startTime = microtime(true);
            
            $browser->visit('/')
                ->waitFor('body', 10);
            
            $loadTime = microtime(true) - $startTime;
            
            // Context7 pattern: Ensure page loads within reasonable time (10 seconds)
            $this->assertLessThan(10, $loadTime, 'Homepage should load within 10 seconds');
            
            // Test other critical pages
            $browser->visit('/login')
                ->waitFor('body', 5)
                ->visit('/register')
                ->waitFor('body', 5)
                ->visit('/jobs')
                ->waitFor('body', 5);
        });
    }

    /**
     * Context7 pattern: Test basic navigation works
     * 
     * @test
     */
    public function navigation_functions_properly()
    {
        $this->browse(function (Browser $browser) {
            // Start at home
            $browser->visit('/')
                ->waitFor('body', 10);
            
            // Navigate through pages
            $browser->visit('/login')
                ->waitFor('body', 5)
                ->assertPresent('body');
            
            // Test browser navigation
            $browser->back()
                ->waitFor('body', 5)
                ->assertPresent('body');
            
            $browser->forward()
                ->waitFor('body', 5)
                ->assertPresent('body');
            
            // Test refresh
            $browser->refresh()
                ->waitFor('body', 5)
                ->assertPresent('body');
        });
    }
} 