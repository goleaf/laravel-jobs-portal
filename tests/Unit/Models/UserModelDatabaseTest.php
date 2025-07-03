<?php

namespace Tests\Unit\Models;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class UserModelDatabaseTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_create_user_via_factory()
    {
        $user = User::factory()->create([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@example.com',
            'user_type' => User::CANDIDATE,
        ]);

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals('John', $user->first_name);
        $this->assertEquals('Doe', $user->last_name);
        $this->assertEquals('john@example.com', $user->email);
        $this->assertEquals(User::CANDIDATE, $user->user_type);
        $this->assertTrue($user->exists);
    }

    /** @test */
    public function it_generates_full_name_attribute_from_database()
    {
        $user = User::factory()->create([
            'first_name' => 'Jane',
            'last_name' => 'Smith',
        ]);

        $this->assertEquals('Jane Smith', $user->full_name);
    }

    /** @test */
    public function it_can_create_different_user_types()
    {
        $admin = User::factory()->create(['user_type' => User::ADMIN]);
        $employer = User::factory()->create(['user_type' => User::EMPLOYER]);
        $candidate = User::factory()->create(['user_type' => User::CANDIDATE]);

        $this->assertEquals(User::ADMIN, $admin->user_type);
        $this->assertEquals(User::EMPLOYER, $employer->user_type);
        $this->assertEquals(User::CANDIDATE, $candidate->user_type);
    }

    /** @test */
    public function it_hashes_passwords_when_created()
    {
        $user = User::factory()->create([
            'password' => bcrypt('test123'),
        ]);

        $this->assertNotEquals('test123', $user->password);
        $this->assertTrue(password_verify('password', $user->password)); // Default factory password
    }

    /** @test */
    public function it_can_update_user_attributes()
    {
        $user = User::factory()->create();

        $user->update([
            'first_name' => 'Updated',
            'last_name' => 'Name',
        ]);

        $this->assertEquals('Updated', $user->first_name);
        $this->assertEquals('Name', $user->last_name);
        $this->assertEquals('Updated Name', $user->full_name);
    }

    /** @test */
    public function it_has_relationship_methods_that_return_correct_types()
    {
        $user = User::factory()->create();

        // Test that relationship methods exist and return the correct relationship types
        $this->assertInstanceOf(BelongsTo::class, $user->country());
        $this->assertInstanceOf(BelongsTo::class, $user->state());
        $this->assertInstanceOf(BelongsTo::class, $user->city());
        $this->assertInstanceOf(HasOne::class, $user->candidate());
        $this->assertInstanceOf(HasOne::class, $user->company());
        $this->assertInstanceOf(BelongsToMany::class, $user->candidateSkill());
        $this->assertInstanceOf(BelongsToMany::class, $user->candidateLanguage());
        $this->assertInstanceOf(HasMany::class, $user->followings());
    }

    /** @test */
    public function it_handles_boolean_casting_correctly()
    {
        $user = User::factory()->create([
            'is_active' => 1,
            'is_verified' => 0,
            'is_default' => true,
        ]);

        $this->assertTrue($user->is_active);
        $this->assertFalse($user->is_verified);
        $this->assertTrue($user->is_default);
        $this->assertIsBool($user->is_active);
        $this->assertIsBool($user->is_verified);
        $this->assertIsBool($user->is_default);
    }

    /** @test */
    public function it_handles_date_casting_correctly()
    {
        $user = User::factory()->create([
            'dob' => '1990-01-15',
            'email_verified_at' => '2023-01-01 10:00:00',
        ]);

        $this->assertInstanceOf(Carbon::class, $user->dob);
        $this->assertInstanceOf(Carbon::class, $user->email_verified_at);
        $this->assertEquals('1990-01-15', $user->dob->format('Y-m-d'));
    }

    /** @test */
    public function it_filters_sensitive_data_in_array_conversion()
    {
        $user = User::factory()->create();
        $userArray = $user->toArray();

        $this->assertArrayNotHasKey('password', $userArray);
        $this->assertArrayNotHasKey('remember_token', $userArray);
        $this->assertArrayHasKey('full_name', $userArray);
        $this->assertArrayHasKey('first_name', $userArray);
        $this->assertArrayHasKey('email', $userArray);
    }
}
