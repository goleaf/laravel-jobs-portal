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
class AuthenticatedRoutesTest extends DuskTestCase
{
    use DatabaseMigrations;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
            'is_active' => true,
        ]);
    }

    /**
     * Test dashboard access for authenticated users.
     */
    public function test_dashboard_requires_authentication(): void
    {
        $this->browse(function (Browser $browser) {
            // Test unauthenticated access redirects to login
            $browser->visit('https://jobportal.prus.dev/dashboard')
                ->pause(2000)
                ->assertPathIs('/login');

            // Test authenticated access works
            $browser->visit('https://jobportal.prus.dev/login')
                ->type('email', 'test@example.com')
                ->type('password', 'password123')
                ->press('Login')
                ->pause(3000)
                ->assertPathIs('/dashboard')
                ->assertDontSee('Error')
                ->assertPresent('body');
        });
    }

    /**
     * Test candidate profile routes.
     */
    public function test_candidate_profile_routes(): void
    {
        $candidate = User::factory()->create([
            'email' => 'candidate@example.com',
            'password' => bcrypt('password123'),
            'is_active' => true,
            'user_type' => User::CANDIDATE,
        ]);

        $this->browse(function (Browser $browser) {
            $browser->visit('https://jobportal.prus.dev/login')
                ->type('email', 'candidate@example.com')
                ->type('password', 'password123')
                ->press('Login')
                ->pause(3000);

            // Test candidate profile
            $browser->visit('https://jobportal.prus.dev/candidate/profile')
                ->pause(2000)
                ->assertDontSee('Error')
                ->assertPresent('body');

            // Test candidate profile edit
            $browser->visit('https://jobportal.prus.dev/candidate/profile/edit')
                ->pause(2000)
                ->assertDontSee('Error')
                ->assertPresent('body');

            // Test applied jobs
            $browser->visit('https://jobportal.prus.dev/candidate/applied-jobs')
                ->pause(2000)
                ->assertDontSee('Error')
                ->assertPresent('body');

            // Test favorite jobs
            $browser->visit('https://jobportal.prus.dev/candidate/favorite-jobs')
                ->pause(2000)
                ->assertDontSee('Error')
                ->assertPresent('body');

            // Test job alerts
            $browser->visit('https://jobportal.prus.dev/candidate/job-alerts')
                ->pause(2000)
                ->assertDontSee('Error')
                ->assertPresent('body');
        });
    }

    /**
     * Test employer routes.
     */
    public function test_employer_routes(): void
    {
        $employer = User::factory()->create([
            'email' => 'employer@example.com',
            'password' => bcrypt('password123'),
            'is_active' => true,
            'user_type' => User::EMPLOYER,
        ]);

        $this->browse(function (Browser $browser) {
            $browser->visit('https://jobportal.prus.dev/login')
                ->type('email', 'employer@example.com')
                ->type('password', 'password123')
                ->press('Login')
                ->pause(3000);

            // Test employer company profile
            $browser->visit('https://jobportal.prus.dev/employer/company')
                ->pause(2000)
                ->assertDontSee('Error')
                ->assertPresent('body');

            // Test employer company edit
            $browser->visit('https://jobportal.prus.dev/employer/company/edit')
                ->pause(2000)
                ->assertDontSee('Error')
                ->assertPresent('body');

            // Test employer jobs
            $browser->visit('https://jobportal.prus.dev/employer/jobs')
                ->pause(2000)
                ->assertDontSee('Error')
                ->assertPresent('body');

            // Test job applications
            $browser->visit('https://jobportal.prus.dev/employer/jobs/applications')
                ->pause(2000)
                ->assertDontSee('Error')
                ->assertPresent('body');

            // Test create job
            $browser->visit('https://jobportal.prus.dev/employer/jobs/create')
                ->pause(2000)
                ->assertDontSee('Error')
                ->assertPresent('body');
        });
    }

    /**
     * Test company routes.
     */
    public function test_company_routes(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('https://jobportal.prus.dev/login')
                ->type('email', 'test@example.com')
                ->type('password', 'password123')
                ->press('Login')
                ->pause(3000);

            // Test company index
            $browser->visit('https://jobportal.prus.dev/company')
                ->pause(2000)
                ->assertDontSee('Error')
                ->assertPresent('body');
        });
    }

    /**
     * Test protected routes redirect unauthenticated users.
     */
    public function test_protected_routes_redirect_guests(): void
    {
        $protectedRoutes = [
            'https://jobportal.prus.dev/dashboard',
            'https://jobportal.prus.dev/candidate/profile',
            'https://jobportal.prus.dev/candidate/profile/edit',
            'https://jobportal.prus.dev/candidate/applied-jobs',
            'https://jobportal.prus.dev/candidate/favorite-jobs',
            'https://jobportal.prus.dev/candidate/job-alerts',
            'https://jobportal.prus.dev/employer/company',
            'https://jobportal.prus.dev/employer/company/edit',
            'https://jobportal.prus.dev/employer/jobs',
            'https://jobportal.prus.dev/employer/jobs/applications',
            'https://jobportal.prus.dev/employer/jobs/create',
        ];

        $this->browse(function (Browser $browser) use ($protectedRoutes) {
            foreach ($protectedRoutes as $route) {
                $browser->visit($route)
                    ->pause(1000);

                // Should redirect to login page
                $this->assertTrue(
                    str_contains($browser->driver->getCurrentURL(), '/login'),
                    "Route {$route} should redirect to login page"
                );
            }
        });
    }
}
