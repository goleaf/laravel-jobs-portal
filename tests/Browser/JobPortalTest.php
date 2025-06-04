<?php

namespace Tests\Browser;

use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class JobPortalTest extends DuskTestCase
{
    /** @test */
    public function user_can_register_and_login()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/register')
                ->type('name', 'Test User')
                ->type('email', 'test@example.com')
                ->type('password', 'password123')
                ->type('password_confirmation', 'password123')
                ->press('Register')
                ->assertPathIs('/dashboard')
                ->assertSee('Dashboard');
        });
    }

    /** @test */
    public function user_can_search_jobs()
    {
        $user = User::factory()->create();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                ->visit('/jobs')
                ->type('search', 'Developer')
                ->press('Search')
                ->assertSee('Search Results');
        });
    }

    /** @test */
    public function user_can_apply_for_job()
    {
        $user = User::factory()->create();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                ->visit('/jobs')
                ->click('@first-job-link')
                ->press('Apply Now')
                ->assertSee('Application Submitted');
        });
    }
}