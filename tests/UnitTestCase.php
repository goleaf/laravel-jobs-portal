<?php

namespace Tests;

use PHPUnit\Framework\TestCase as BaseTestCase;

/**
 * Lightweight TestCase for unit tests that don't require database
 * Avoids memory overhead from Laravel application bootstrapping.
 */
abstract class UnitTestCase extends BaseTestCase
{
    /**
     * Set up the test environment without Laravel application.
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Only set up what's absolutely necessary for unit tests
        if (!defined('LARAVEL_START')) {
            define('LARAVEL_START', microtime(true));
        }
    }

    /**
     * Clean up after test.
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        // Force garbage collection to free memory
        if (function_exists('gc_collect_cycles')) {
            gc_collect_cycles();
        }
    }
}
