<?php

namespace Tests\Unit\Models;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class UserModelTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_be_created()
    {
        $User = User::factory()->create();

        $this->assertInstanceOf(User::class, $User);
        $this->assertModelExists($User);
    }

    /** @test */
    public function it_has_required_fillable_fields()
    {
        $User = new User;
        $fillable = $User->getFillable();

        $this->assertIsArray($fillable);
        $this->assertNotEmpty($fillable);
    }

    /** @test */
    public function it_can_be_soft_deleted()
    {
        $User = User::factory()->create();
        $User->delete();

        $this->assertSoftDeleted($User);
    }
}
