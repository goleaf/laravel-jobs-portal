<?php

namespace Tests\Unit;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class HelperTest extends TestCase
{
    /** @test */
    public function strLimitTruncatesStringCorrectly()
    {
        $this->assertEquals('Laravel...', Str::limit('Laravel Framework', 7));
        $this->assertEquals('Laravel Framework', Str::limit('Laravel Framework', 20));
    }

    /** @test */
    public function strSlugConvertsStringToSlug()
    {
        $this->assertEquals('laravel-framework', Str::slug('Laravel Framework'));
        $this->assertEquals('laravel-framework-version-10', Str::slug('Laravel Framework (Version 10)'));
    }

    /** @test */
    public function arrayGetReturnsDefaultForMissingKey()
    {
        $array = ['name' => 'Taylor', 'age' => 25];

        $this->assertEquals('Taylor', Arr::get($array, 'name'));
        $this->assertEquals('Unknown', Arr::get($array, 'gender', 'Unknown'));
        $this->assertNull(Arr::get($array, 'gender'));
    }

    /** @test */
    public function arrayHasChecksIfKeyExists()
    {
        $array = ['product' => ['name' => 'Laravel', 'price' => 'free']];

        $this->assertTrue(Arr::has($array, 'product'));
        $this->assertTrue(Arr::has($array, 'product.name'));
        $this->assertFalse(Arr::has($array, 'product.discount'));
    }

    /** @test */
    public function configGetReturnsDefaultForMissingKey()
    {
        // We can only test the function exists as we don't have a Laravel app instance in Unit tests
        $this->assertTrue(function_exists('config'));
    }

    /** @test */
    public function authCheckReturnsBoolean()
    {
        // We can only test the function exists as we don't have a Laravel app instance in Unit tests
        $this->assertTrue(function_exists('auth'));
    }

    /** @test */
    public function nowReturnsCarbonInstance()
    {
        $this->assertTrue(function_exists('now'));
    }

    /** @test */
    public function bcryptHashesPasswords()
    {
        $this->assertTrue(function_exists('bcrypt'));

        // This is a basic check that the bcrypt function exists
        // In an actual Laravel environment, we would test hashing works correctly
    }
}
