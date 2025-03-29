<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    /** @test */
    public function user_can_be_created()
    {
        $userData = [
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'password' => bcrypt('password'),
            'phone' => $this->faker->phoneNumber,
            'is_active' => true,
        ];

        $user = User::create($userData);

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals($userData['name'], $user->name);
        $this->assertEquals($userData['email'], $user->email);
        $this->assertEquals($userData['phone'], $user->phone);
        $this->assertTrue($user->is_active);
    }

    /** @test */
    public function user_can_be_updated()
    {
        $user = User::factory()->create();

        $updatedData = [
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
        ];

        $user->update($updatedData);
        $user->refresh();

        $this->assertEquals($updatedData['name'], $user->name);
        $this->assertEquals($updatedData['email'], $user->email);
    }

    /** @test */
    public function inactive_users_can_be_filtered()
    {
        User::factory()->count(3)->create(['is_active' => true]);
        User::factory()->count(2)->create(['is_active' => false]);

        $activeUsers = User::where('is_active', true)->get();
        $inactiveUsers = User::where('is_active', false)->get();

        $this->assertCount(3, $activeUsers);
        $this->assertCount(2, $inactiveUsers);
    }

    /** @test */
    public function user_can_have_candidate_profile()
    {
        $user = User::factory()->create();

        // Assuming the user can be linked to a candidate profile
        $candidate = $user->candidate()->create([
            'expected_salary' => $this->faker->randomNumber(5),
            'experience' => $this->faker->randomNumber(1),
            'career_level_id' => 1,
            'industry_id' => 1,
            'functional_area_id' => 1,
        ]);

        $this->assertNotNull($user->candidate);
        $this->assertEquals($user->id, $candidate->user_id);
    }

    /** @test */
    public function user_can_have_company_profile()
    {
        $user = User::factory()->create();

        // Assuming the user can be linked to a company profile
        $company = $user->company()->create([
            'name' => $this->faker->company,
            'website' => $this->faker->url,
            'location' => $this->faker->address,
            'industry_id' => 1,
            'size_id' => 1,
            'ownership_type_id' => 1,
        ]);

        $this->assertNotNull($user->company);
        $this->assertEquals($user->id, $company->user_id);
    }
}
