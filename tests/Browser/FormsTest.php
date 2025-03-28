<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class FormsTest extends DuskTestCase
{
    use DatabaseMigrations;

    /** @test */
    public function it_can_visit_contact_form_page()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/contact')
                ->assertSee('Contact Us')
                ->assertPresent('form#contact-form');
        });
    }

    /** @test */
    public function it_can_submit_contact_form_with_valid_data()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/contact')
                ->type('name', 'Test User')
                ->type('email', 'test@example.com')
                ->select('subject', 'general')
                ->type('message', 'This is a test message from browser test.')
                ->check('newsletter')
                ->press('Send Message')
                ->waitForText('Your message has been sent')
                ->assertSee('Your message has been sent');
        });
    }

    /** @test */
    public function it_shows_validation_errors_for_empty_fields()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/contact')
                ->press('Send Message')
                ->waitForText('The name field is required')
                ->assertSee('The name field is required')
                ->assertSee('The email field is required')
                ->assertSee('The subject field is required')
                ->assertSee('The message field is required');
        });
    }

    /** @test */
    public function it_can_visit_validation_example_page()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/forms/validation')
                ->assertSee('Client-Side Validation Example')
                ->assertPresent('form#validation-form');
        });
    }

    /** @test */
    public function it_performs_client_side_validation()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/forms/validation')
                ->type('name', 'Jo') // Too short name
                ->assertSee('Your name must be at least 3 characters')
                ->type('name', 'John') // Fix the name
                ->assertDontSee('Your name must be at least 3 characters');
        });
    }

    /** @test */
    public function it_can_visit_alpine_example_page()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/forms/alpine')
                ->assertSee('Alpine.js Integration Example')
                ->assertPresent('form#alpine-form');
        });
    }

    /** @test */
    public function it_toggles_phone_field_with_alpine()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/forms/alpine')
                ->assertMissing('input[name="phone"]') // Phone field is hidden by default
                ->check('show_phone')
                ->waitFor('input[name="phone"]')
                ->assertVisible('input[name="phone"]') // Phone field is now visible
                ->uncheck('show_phone')
                ->waitUntilMissing('input[name="phone"]')
                ->assertMissing('input[name="phone"]'); // Phone field is hidden again
        });
    }

    /** @test */
    public function it_can_visit_binding_example_page()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/forms/binding')
                ->assertSee('Model Binding Example')
                ->assertPresent('form#binding-form')
                // Check that the form is pre-filled
                ->assertInputValue('name', 'John Doe')
                ->assertInputValue('email', 'john@example.com');
        });
    }

    /** @test */
    public function it_can_visit_errors_example_page()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/forms/errors')
                ->assertSee('Error Handling & Summary Example')
                ->assertPresent('form#errors-form');
        });
    }

    /** @test */
    public function it_can_visit_methods_example_page()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/forms/methods')
                ->assertSee('Method Spoofing Example')
                ->assertPresent('form#put-form')
                ->assertPresent('form#delete-form');
        });
    }
} 