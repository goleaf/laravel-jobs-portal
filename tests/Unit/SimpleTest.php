<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class SimpleTest extends TestCase
{
    /** @test */
    public function itCanRunBasicTest()
    {
        $this->assertTrue(true);
    }

    /** @test */
    public function itCanPerformBasicMath()
    {
        $this->assertEquals(4, 2 + 2);
    }
}
