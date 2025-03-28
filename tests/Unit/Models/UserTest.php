<?php

namespace Tests\Unit\Models;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_create_a_user()
    {
        $user = User::factory()->create();
        $this->assertDatabaseHas('users', ['id' => $user->id]);
    }

    /** @test */
    public function users_have_a_role()
    {
        $user = User::factory()->create(['is_active' => true]);
        $this->assertNotNull($user->role);
    }

    /** @test */
    public function it_can_check_if_a_user_is_active()
    {
        $activeUser = User::factory()->create(['is_active' => true]);
        $inactiveUser = User::factory()->create(['is_active' => false]);
        
        $this->assertTrue($activeUser->isActive());
        $this->assertFalse($inactiveUser->isActive());
    }

    /** @test */
    public function it_can_get_users_image_url()
    {
        $user = User::factory()->create(['image_url' => 'users/test-image.jpg']);
        $this->assertStringContainsString('users/test-image.jpg', $user->getImageUrl());
    }

    /** @test */
    public function it_can_set_a_default_image_when_no_image_url_is_provided()
    {
        $user = User::factory()->create(['image_url' => null]);
        $this->assertStringContainsString('assets/img/infyom-logo.png', $user->getImageUrl());
    }
} 