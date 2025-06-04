<?php

namespace Tests\Unit;

use Tests\TestCase;

class ConfigurationTest extends TestCase
{
    public function test_app_configuration_is_accessible(): void
    {
        $this->assertIsString(config('app.name'));
        $this->assertEquals('testing', config('app.env'));
    }

    public function test_database_configuration_for_testing(): void
    {
        $this->assertEquals('sqlite', config('database.default'));
        $this->assertEquals(':memory:', config('database.connections.sqlite.database'));
    }

    public function test_cache_configuration_for_testing(): void
    {
        $this->assertEquals('array', config('cache.default'));
    }

    public function test_session_configuration_for_testing(): void
    {
        $this->assertEquals('array', config('session.driver'));
    }

    public function test_mail_configuration_for_testing(): void
    {
        $this->assertEquals('array', config('mail.default'));
    }
}