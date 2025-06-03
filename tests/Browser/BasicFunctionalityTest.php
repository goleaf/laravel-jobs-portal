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
            $browser->visit('/')
                    ->assertStatus(200)
                    ->assertSee('Jobs')
                    ->assertSee('Find Jobs');
        });
    }

    /**
     * Test login page is accessible.
     */
    public function test_login_page_accessible(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/login')
                    ->assertStatus(200)
                    ->assertSee('Login')
                    ->assertSee('Email')
                    ->assertSee('Password');
        });
    }

    /**
     * Test register page is accessible.
     */
    public function test_register_page_accessible(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/register')
                    ->assertStatus(200)
                    ->assertSee('Register')
                    ->assertSee('Name')
                    ->assertSee('Email');
        });
    }
} 