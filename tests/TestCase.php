<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Helpers\TestHelpers;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication, RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create basic test data
        TestHelpers::createBasicTestData();
        
        // Set up testing environment
        config(["app.env" => "testing"]);
        config(["cache.default" => "array"]);
        config(["session.driver" => "array"]);
        config(["queue.default" => "sync"]);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }
}