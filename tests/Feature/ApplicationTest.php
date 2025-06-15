<?php

namespace Tests\Feature;

use Tests\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class ApplicationTest extends TestCase
{
    /** @test */
    public function basicApplicationLoads()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }
}
