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
    public function itCanBeCreated()
    {
        $User = User::factory()->create();

        $this->assertInstanceOf(User::class, $User);
        $this->assertModelExists($User);
    }

    /** @test */
    public function itHasRequiredFillableFields()
    {
        $User = new User();
        $fillable = $User->getFillable();

        $this->assertIsArray($fillable);
        $this->assertNotEmpty($fillable);
    }

    /** @test */
    public function itCanBeSoftDeleted()
    {
        $User = User::factory()->create();
        $User->delete();

        $this->assertSoftDeleted($User);
    }
}
