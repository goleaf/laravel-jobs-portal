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
class AdminRoutesTest extends DuskTestCase
{
    use DatabaseMigrations;

    protected $adminUser;
    protected $regularUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->adminUser = User::factory()->create([
            'email' => 'admin@example.com',
            'password' => bcrypt('password123'),
            'is_active' => true,
            'user_type' => User::ADMIN,
        ]);

        $this->regularUser = User::factory()->create([
            'email' => 'user@example.com',
            'password' => bcrypt('password123'),
            'is_active' => true,
            'user_type' => User::CANDIDATE,
        ]);
    }

    /**
     * Test admin dashboard access.
     */
    public function test_admin_dashboard_access(): void
    {
        $this->browse(function (Browser $browser) {
            // Test regular user cannot access admin dashboard
            $browser->visit('/login')
                ->type('email', 'user@example.com')
                ->type('password', 'password123')
                ->press('Login')
                ->pause(2000);

            $browser->visit('/admin/dashboard')
                ->pause(2000);

            // Should be redirected away from admin area
            $this->assertStringNotContainsString('/admin/dashboard', $browser->driver->getCurrentURL());

            // Logout and test admin access
            $browser->visit('/logout')
                ->pause(1000);

            // Test admin user can access admin dashboard
            $browser->visit('/login')
                ->type('email', 'admin@example.com')
                ->type('password', 'password123')
                ->press('Login')
                ->pause(3000);

            $browser->visit('/admin/dashboard')
                ->pause(2000)
                ->assertDontSee('Error')
                ->assertPresent('body');
        });
    }

    /**
     * Test admin candidates management.
     */
    public function test_admin_candidates_routes(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/login')
                ->type('email', 'admin@example.com')
                ->type('password', 'password123')
                ->press('Login')
                ->pause(3000);

            // Test candidates index
            $browser->visit('/admin/candidates')
                ->pause(2000)
                ->assertDontSee('Error')
                ->assertPresent('body');

            // Test candidates create
            $browser->visit('/admin/candidates/create')
                ->pause(2000)
                ->assertDontSee('Error')
                ->assertPresent('body');
        });
    }

    /**
     * Test admin jobs management.
     */
    public function test_admin_jobs_routes(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/login')
                ->type('email', 'admin@example.com')
                ->type('password', 'password123')
                ->press('Login')
                ->pause(3000);

            // Test jobs index
            $browser->visit('/admin/jobs')
                ->pause(2000)
                ->assertDontSee('Error')
                ->assertPresent('body');

            // Test jobs create
            $browser->visit('/admin/jobs/create')
                ->pause(2000)
                ->assertDontSee('Error')
                ->assertPresent('body');
        });
    }

    /**
     * Test admin transactions management.
     */
    public function test_admin_transactions_routes(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/login')
                ->type('email', 'admin@example.com')
                ->type('password', 'password123')
                ->press('Login')
                ->pause(3000);

            // Test transactions index
            $browser->visit('/admin/transactions')
                ->pause(2000)
                ->assertDontSee('Error')
                ->assertPresent('body');

            // Test transactions create
            $browser->visit('/admin/transactions/create')
                ->pause(2000)
                ->assertDontSee('Error')
                ->assertPresent('body');
        });
    }

    /**
     * Test admin routes are protected from non-admin users.
     */
    public function test_admin_routes_protected_from_regular_users(): void
    {
        $adminRoutes = [
            '/admin/dashboard',
            '/admin/candidates',
            '/admin/candidates/create',
            '/admin/jobs',
            '/admin/jobs/create',
            '/admin/transactions',
            '/admin/transactions/create',
        ];

        $this->browse(function (Browser $browser) use ($adminRoutes) {
            // Login as regular user
            $browser->visit('/login')
                ->type('email', 'user@example.com')
                ->type('password', 'password123')
                ->press('Login')
                ->pause(2000);

            foreach ($adminRoutes as $route) {
                $browser->visit($route)
                    ->pause(1000);

                // Should not be able to access admin routes
                $this->assertStringNotContainsString($route, $browser->driver->getCurrentURL());
            }
        });
    }

    /**
     * Test admin routes are protected from guests.
     */
    public function test_admin_routes_protected_from_guests(): void
    {
        $adminRoutes = [
            '/admin/dashboard',
            '/admin/candidates',
            '/admin/jobs',
            '/admin/transactions',
        ];

        $this->browse(function (Browser $browser) use ($adminRoutes) {
            foreach ($adminRoutes as $route) {
                $browser->visit($route)
                    ->pause(1000);

                // Should redirect to login page
                $this->assertTrue(
                    str_contains($browser->driver->getCurrentURL(), '/login'),
                    "Admin route {$route} should redirect guests to login page"
                );
            }
        });
    }
}
