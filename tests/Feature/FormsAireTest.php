<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FormsAireTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function validation_form_example_page_can_be_rendered()
    {
        $response = $this->get(route('forms.validation'));

        $response->assertStatus(200);
        $response->assertViewIs('forms.validation');
        $response->assertSee('Validation Example');
    }

    /** @test */
    public function alpine_form_example_page_can_be_rendered()
    {
        $response = $this->get(route('forms.alpine'));

        $response->assertStatus(200);
        $response->assertViewIs('forms.alpine');
        $response->assertSee('Alpine.js Integration');
    }

    /** @test */
    public function binding_form_example_page_can_be_rendered()
    {
        $response = $this->get(route('forms.binding'));

        $response->assertStatus(200);
        $response->assertViewIs('forms.binding');
        $response->assertSee('Data Binding Example');
    }

    /** @test */
    public function errors_form_example_page_can_be_rendered()
    {
        $response = $this->get(route('forms.errors'));

        $response->assertStatus(200);
        $response->assertViewIs('forms.errors');
        $response->assertSee('Error Handling Example');
    }

    /** @test */
    public function methods_form_example_page_can_be_rendered()
    {
        $response = $this->get(route('forms.methods'));

        $response->assertStatus(200);
        $response->assertViewIs('forms.methods');
        $response->assertSee('Method Spoofing Example');
    }

    /** @test */
    public function method_spoofing_put_works_correctly()
    {
        $response = $this->put(route('resource.update', 1), [
            'name' => 'Updated Resource',
            'description' => 'Updated description'
        ]);

        $response->assertSessionHas('success', 'Resource updated successfully!');
        $response->assertRedirect();
    }

    /** @test */
    public function method_spoofing_delete_works_correctly()
    {
        $response = $this->delete(route('resource.delete', 1));

        $response->assertSessionHas('success', 'Resource deleted successfully!');
        $response->assertRedirect();
    }
} 