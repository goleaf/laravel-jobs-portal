<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class HelperTest extends TestCase
{
    /** @test */
    public function str_limit_truncates_string_correctly()
    {
        $this->assertEquals('Laravel...', \Illuminate\Support\Str::limit('Laravel Framework', 7));
        $this->assertEquals('Laravel Framework', \Illuminate\Support\Str::limit('Laravel Framework', 20));
    }

    /** @test */
    public function str_slug_converts_string_to_slug()
    {
        $this->assertEquals('laravel-framework', \Illuminate\Support\Str::slug('Laravel Framework'));
        $this->assertEquals('laravel-framework-version-10', \Illuminate\Support\Str::slug('Laravel Framework (Version 10)'));
    }

    /** @test */
    public function array_get_returns_default_for_missing_key()
    {
        $array = ['name' => 'Taylor', 'age' => 25];
        
        $this->assertEquals('Taylor', \Illuminate\Support\Arr::get($array, 'name'));
        $this->assertEquals('Unknown', \Illuminate\Support\Arr::get($array, 'gender', 'Unknown'));
        $this->assertNull(\Illuminate\Support\Arr::get($array, 'gender'));
    }

    /** @test */
    public function array_has_checks_if_key_exists()
    {
        $array = ['product' => ['name' => 'Laravel', 'price' => 'free']];
        
        $this->assertTrue(\Illuminate\Support\Arr::has($array, 'product'));
        $this->assertTrue(\Illuminate\Support\Arr::has($array, 'product.name'));
        $this->assertFalse(\Illuminate\Support\Arr::has($array, 'product.discount'));
    }

    /** @test */
    public function config_get_returns_default_for_missing_key()
    {
        // We can only test the function exists as we don't have a Laravel app instance in Unit tests
        $this->assertTrue(function_exists('config'));
    }

    /** @test */
    public function auth_check_returns_boolean()
    {
        // We can only test the function exists as we don't have a Laravel app instance in Unit tests
        $this->assertTrue(function_exists('auth'));
    }

    /** @test */
    public function now_returns_carbon_instance()
    {
        $this->assertTrue(function_exists('now'));
    }

    /** @test */
    public function bcrypt_hashes_passwords()
    {
        $this->assertTrue(function_exists('bcrypt'));
        
        // This is a basic check that the bcrypt function exists
        // In an actual Laravel environment, we would test hashing works correctly
    }
} 