<?php

namespace Tests\Browser;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class UserFlowTest extends DuskTestCase
{
    use DatabaseMigrations;

    /** @test */
    public function userCanVisitHomepage()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->assertSee('Job Portal')
            ;
        });
    }

    /** @test */
    public function userCanCompleteLoginFlow()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
        ]);

        $this->browse(function (Browser $browser) {
            $browser->visit('/login')
                ->type('email', 'test@example.com')
                ->type('password', 'password123')
                ->press('Sign In')
                ->assertPathIs('/dashboard')
            ;
        });
    }
}
