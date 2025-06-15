<?php

namespace Tests\Unit;

use Tests\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class ConfigurationTest extends TestCase
{
    public function testAppConfigurationIsAccessible(): void
    {
        $this->assertIsString(config('app.name'));
        $this->assertEquals('testing', config('app.env'));
    }

    public function testDatabaseConfigurationForTesting(): void
    {
        $this->assertEquals('sqlite', config('database.default'));
        $this->assertEquals(':memory:', config('database.connections.sqlite.database'));
    }

    public function testCacheConfigurationForTesting(): void
    {
        $this->assertEquals('array', config('cache.default'));
    }

    public function testSessionConfigurationForTesting(): void
    {
        $this->assertEquals('array', config('session.driver'));
    }

    public function testMailConfigurationForTesting(): void
    {
        $this->assertEquals('array', config('mail.default'));
    }
}
