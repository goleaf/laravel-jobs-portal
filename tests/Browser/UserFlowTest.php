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
    public function user_can_visit_homepage()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->assertSee('Job Portal');
        });
    }

    /** @test */
    public function user_can_complete_login_flow()
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
                ->assertPathIs('/dashboard');
        });
    }
}
