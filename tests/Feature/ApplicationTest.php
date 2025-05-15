<?php

namespace Tests\Feature;

use Tests\TestCase;

class ApplicationTest extends TestCase
{
    /** @test */
    public function basic_application_loads()
    {
        $response = $this->get('/');
        
        $response->assertStatus(200);
    }
} 