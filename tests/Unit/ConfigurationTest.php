<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Support\Facades\Config;

class ConfigurationTest extends TestCase
{
    /** @test */
    public function it_has_database_configuration()
    {
        $dbConnection = Config::get('database.default');
        $this->assertNotNull($dbConnection);
        $this->assertIsString($dbConnection);
    }

    /** @test */
    public function it_has_app_configuration()
    {
        $appName = Config::get('app.name');
        $appEnv = Config::get('app.env');
        $appDebug = Config::get('app.debug');
        
        $this->assertNotNull($appName);
        $this->assertIsString($appName);
        $this->assertNotNull($appEnv);
        $this->assertIsBool($appDebug);
    }

    /** @test */
    public function it_has_mail_configuration()
    {
        $mailDriver = Config::get('mail.default');
        $this->assertNotNull($mailDriver);
        $this->assertIsString($mailDriver);
    }

    /** @test */
    public function it_has_queue_configuration()
    {
        $queueConnection = Config::get('queue.default');
        $this->assertNotNull($queueConnection);
        $this->assertIsString($queueConnection);
    }

    /** @test */
    public function it_has_cache_configuration()
    {
        $cacheStore = Config::get('cache.default');
        $this->assertNotNull($cacheStore);
        $this->assertIsString($cacheStore);
    }

    /** @test */
    public function it_has_session_configuration()
    {
        $sessionDriver = Config::get('session.driver');
        $this->assertNotNull($sessionDriver);
        $this->assertIsString($sessionDriver);
    }
} 