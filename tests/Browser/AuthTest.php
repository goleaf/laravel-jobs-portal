<?php

namespace Tests\Browser;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class AuthTest extends DuskTestCase
{
    use DatabaseMigrations;

    /** @test */
    public function visitor_can_register_as_candidate()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/register')
                ->assertSee('Register')
                ->type('name', 'Test Candidate')
                ->type('email', 'candidate@example.com')
                ->type('password', 'password123')
                ->type('password_confirmation', 'password123')
                ->select('user_type', User::CANDIDATE)
                ->check('agree_terms_policy')
                ->press('Register')
                ->waitForLocation('/candidate/profile/edit')
                ->assertPathIs('/candidate/profile/edit')
                ->assertSee('Complete your profile');
        });
    }

    /** @test */
    public function visitor_can_register_as_employer()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/register')
                ->assertSee('Register')
                ->type('name', 'Test Employer')
                ->type('email', 'employer@example.com')
                ->type('password', 'password123')
                ->type('password_confirmation', 'password123')
                ->select('user_type', User::EMPLOYER)
                ->check('agree_terms_policy')
                ->press('Register')
                ->waitForLocation('/employer/company/edit')
                ->assertPathIs('/employer/company/edit')
                ->assertSee('Complete your company profile');
        });
    }

    /** @test */
    public function user_can_login_with_correct_credentials()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
            'is_active' => true,
        ]);

        $this->browse(function (Browser $browser) {
            $browser->visit('/login')
                ->assertSee('Login')
                ->type('email', 'test@example.com')
                ->type('password', 'password123')
                ->press('Login')
                ->waitForLocation('/dashboard')
                ->assertPathIs('/dashboard')
                ->assertSee('Dashboard');
        });
    }

    /** @test */
    public function user_cannot_login_with_incorrect_credentials()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
            'is_active' => true,
        ]);

        $this->browse(function (Browser $browser) {
            $browser->visit('/login')
                ->assertSee('Login')
                ->type('email', 'test@example.com')
                ->type('password', 'wrongpassword')
                ->press('Login')
                ->assertPathIs('/login')
                ->assertSee('These credentials do not match our records');
        });
    }

    /** @test */
    public function user_can_logout()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
            'is_active' => true,
        ]);

        $this->browse(function (Browser $browser) {
            $browser->visit('/login')
                ->type('email', 'test@example.com')
                ->type('password', 'password123')
                ->press('Login')
                ->waitForLocation('/dashboard')
                ->assertAuthenticated()
                ->click('#user-dropdown-toggle')
                ->waitFor('#logout-form')
                ->click('#logout-button')
                ->waitForLocation('/')
                ->assertGuest()
                ->assertPathIs('/');
        });
    }

    /** @test */
    public function user_can_request_password_reset()
    {
        $user = User::factory()->create([
            'email' => 'reset@example.com',
            'is_active' => true,
        ]);

        $this->browse(function (Browser $browser) {
            $browser->visit('/login')
                ->assertSee('Login')
                ->click('Forgot your password?')
                ->waitForLocation('/password/reset')
                ->assertSee('Reset Password')
                ->type('email', 'reset@example.com')
                ->press('Send Password Reset Link')
                ->waitForText('We have emailed your password reset link')
                ->assertSee('We have emailed your password reset link');
        });
    }

    /** @test */
    public function inactive_user_cannot_login()
    {
        $user = User::factory()->create([
            'email' => 'inactive@example.com',
            'password' => bcrypt('password123'),
            'is_active' => false,
        ]);

        $this->browse(function (Browser $browser) {
            $browser->visit('/login')
                ->assertSee('Login')
                ->type('email', 'inactive@example.com')
                ->type('password', 'password123')
                ->press('Login')
                ->assertPathIs('/login')
                ->assertSee('Your account is not active');
        });
    }

    /** @test */
    public function candidate_sees_different_dashboard_than_employer()
    {
        $candidate = User::factory()->create([
            'email' => 'candidate@example.com',
            'password' => bcrypt('password123'),
            'user_type' => User::CANDIDATE,
            'is_active' => true,
        ]);

        $employer = User::factory()->create([
            'email' => 'employer@example.com',
            'password' => bcrypt('password123'),
            'user_type' => User::EMPLOYER,
            'is_active' => true,
        ]);

        $this->browse(function (Browser $browser) {
            // Login as candidate
            $browser->visit('/login')
                ->type('email', 'candidate@example.com')
                ->type('password', 'password123')
                ->press('Login')
                ->waitForLocation('/dashboard')
                ->assertSee('Candidate Dashboard')
                ->assertSee('My Profile')
                ->assertSee('Applied Jobs')
                ->logout();

            // Login as employer
            $browser->visit('/login')
                ->type('email', 'employer@example.com')
                ->type('password', 'password123')
                ->press('Login')
                ->waitForLocation('/dashboard')
                ->assertSee('Employer Dashboard')
                ->assertSee('Company Profile')
                ->assertSee('Post a Job');
        });
    }
}
