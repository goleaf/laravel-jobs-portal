<?php

namespace Tests\Feature;

use App\Http\Controllers\ContactController;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ContactControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** @test */
    public function it_can_display_contact_form()
    {
        $response = $this->get(route('contact'));
        
        $response->assertStatus(200);
        $response->assertViewIs('forms.contact');
    }

    /** @test */
    public function it_can_submit_valid_contact_form()
    {
        $formData = [
            'name' => 'John Doe',
            'email' => 'johndoe@example.com',
            'subject' => 'Test Subject',
            'message' => 'This is a test message',
        ];
        
        $response = $this->post(route('contact.submit'), $formData);
        
        $response->assertRedirect(route('contact'));
        $response->assertSessionHas('success', 'Your message has been sent!');
        
        $this->assertDatabaseHas('inquiries', [
            'name' => 'John Doe',
            'email' => 'johndoe@example.com',
            'subject' => 'Test Subject',
            'message' => 'This is a test message',
        ]);
    }

    /** @test */
    public function it_validates_contact_form_fields()
    {
        $response = $this->post(route('contact.submit'), [
            'name' => '',
            'email' => 'not-an-email',
            'subject' => '',
            'message' => '',
        ]);
        
        $response->assertSessionHasErrors(['name', 'email', 'subject', 'message']);
    }

    /** @test */
    public function it_can_display_validation_example()
    {
        $response = $this->get(route('forms.validation'));
        
        $response->assertStatus(200);
        $response->assertViewIs('forms.validation');
    }

    /** @test */
    public function it_can_display_alpine_example()
    {
        $response = $this->get(route('forms.alpine'));
        
        $response->assertStatus(200);
        $response->assertViewIs('forms.alpine');
    }

    /** @test */
    public function it_can_display_binding_example()
    {
        $response = $this->get(route('forms.binding'));
        
        $response->assertStatus(200);
        $response->assertViewIs('forms.binding');
    }

    /** @test */
    public function it_can_display_error_example()
    {
        $response = $this->get(route('forms.errors'));
        
        $response->assertStatus(200);
        $response->assertViewIs('forms.errors');
    }

    /** @test */
    public function it_can_display_method_example()
    {
        $response = $this->get(route('forms.methods'));
        
        $response->assertStatus(200);
        $response->assertViewIs('forms.methods');
    }
} 