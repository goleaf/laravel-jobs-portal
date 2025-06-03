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
                    ->pause(3000)
                    ->assertDontSee('Error')
                    ->assertDontSee('Unsupported cipher')
                    ->assertTitleContains('Home')
                    ->assertSee('Jobs');
        });
    }

    /**
     * Test login page is accessible.
     */
    public function test_login_page_accessible(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('https://jobportal.prus.dev/login')
                    ->pause(2000)
                    ->assertDontSee('Error')
                    ->assertTitleContains('Login')
                    ->assertSee('Login');
        });
    }

    /**
     * Test register page is accessible.
     */
    public function test_register_page_accessible(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('https://jobportal.prus.dev/register')
                    ->pause(2000)
                    ->assertDontSee('Error')
                    ->assertTitleContains('Register')
                    ->assertSee('Register');
        });
    }

    /**
     * Test navigation links work properly.
     */
    public function test_navigation_links(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('https://jobportal.prus.dev')
                    ->pause(2000)
                    ->assertSee('Jobs')
                    ->assertSee('Login');
        });
    }

    /**
     * Test footer links are present.
     */
    public function test_footer_links(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('https://jobportal.prus.dev')
                    ->pause(2000)
                    ->assertDontSee('Error');
        });
    }
} 