<?php

namespace Tests\Unit\Models;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Helpers\TestHelpers;
use Tests\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class UserModelOptimizedTest extends TestCase
{
    use RefreshDatabase;

    public function testUserCanBeCreated(): void
    {
        $user = TestHelpers::createUserWithUniqueEmail([
            'name' => 'John Doe',
            'email' => 'john@example.com',
        ]);

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals('John Doe', $user->name);
        $this->assertEquals('john@example.com', $user->email);
    }

    public function testUserHasCorrectFillableAttributes(): void
    {
        $user = new User();
        $fillable = $user->getFillable();

        $expectedFillable = [
            'first_name', 'last_name', 'name', 'email', 'password',
            'phone', 'dob', 'gender', 'region_code',
        ];

        foreach ($expectedFillable as $attribute) {
            $this->assertContains($attribute, $fillable, "Missing fillable attribute: {$attribute}");
        }
    }

    public function testUserHasCorrectHiddenAttributes(): void
    {
        $user = new User();
        $hidden = $user->getHidden();

        $this->assertContains('password', $hidden);
        $this->assertContains('remember_token', $hidden);
    }

    public function testPasswordIsHashed(): void
    {
        $user = TestHelpers::createUserWithUniqueEmail([
            'password' => 'plaintext',
        ]);

        $this->assertNotEquals('plaintext', $user->password);
        $this->assertTrue(\Hash::check('plaintext', $user->password));
    }

    public function testUserRelationships(): void
    {
        $user = TestHelpers::createUserWithUniqueEmail();

        // Test that relationships exist (methods are callable)
        $this->assertTrue(method_exists($user, 'jobs'));
        $this->assertTrue(method_exists($user, 'company'));
    }
}
