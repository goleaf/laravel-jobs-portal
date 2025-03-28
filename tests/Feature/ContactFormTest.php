<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ContactFormTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** @test */
    public function contact_page_can_be_rendered()
    {
        $response = $this->get('/contact');

        $response->assertStatus(200);
        $response->assertViewIs('forms.contact');
        $response->assertSee('Contact Us');
    }

    /** @test */
    public function contact_form_validates_required_fields()
    {
        $response = $this->post(route('contact.submit'), []);

        $response->assertSessionHasErrors(['name', 'email', 'subject', 'message']);
    }

    /** @test */
    public function contact_form_validates_email_format()
    {
        $response = $this->post(route('contact.submit'), [
            'name' => 'Test User',
            'email' => 'not-an-email',
            'subject' => 'general',
            'message' => 'Test message'
        ]);

        $response->assertSessionHasErrors(['email']);
    }

    /** @test */
    public function contact_form_validates_subject_options()
    {
        $response = $this->post(route('contact.submit'), [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'subject' => 'invalid-subject',
            'message' => 'Test message'
        ]);

        $response->assertSessionHasErrors(['subject']);
    }

    /** @test */
    public function contact_form_submission_with_valid_data_is_successful()
    {
        $response = $this->post(route('contact.submit'), [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'subject' => 'general',
            'message' => 'This is a test message.'
        ]);

        $response->assertSessionHas('success');
        $response->assertRedirect();
    }

    /** @test */
    public function contact_form_can_include_newsletter_subscription()
    {
        $response = $this->post(route('contact.submit'), [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'subject' => 'general',
            'message' => 'This is a test message.',
            'newsletter' => '1'
        ]);

        $response->assertSessionHas('success');
        $response->assertRedirect();
    }
} 