<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class UserTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    protected $adminUser;

    protected function setUp(): void
    {
        parent::setUp();
        // Create an admin user for authentication in tests
        $this->adminUser = User::factory()->create(['user_type' => User::ADMIN]); // Assuming User::ADMIN constant exists
    }

    /** @test */
    public function userCanBeCreated()
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
    public function userCanBeUpdated()
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
    public function inactiveUsersCanBeFiltered()
    {
        User::factory()->count(3)->create(['is_active' => true]);
        User::factory()->count(2)->create(['is_active' => false]);

        $activeUsers = User::where('is_active', true)->get();
        $inactiveUsers = User::where('is_active', false)->get();

        $this->assertCount(3, $activeUsers);
        $this->assertCount(2, $inactiveUsers);
    }

    /** @test */
    public function userCanHaveCandidateProfile()
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
    public function userCanHaveCompanyProfile()
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

    /** @test */
    public function guestsCannotAccessAdminUsersSection()
    {
        $this->get('/admin/users')->assertRedirect('/login');
        $this->get('/admin/users/create')->assertRedirect('/login');
        $this->post('/admin/users')->assertRedirect('/login');
    }

    /** @test */
    public function nonAdminUsersCannotAccessAdminUsersSection()
    {
        $user = User::factory()->create(['user_type' => User::CANDIDATE]); // Or EMPLOYER
        $this->actingAs($user)->get('/admin/users')->assertStatus(403); // Or appropriate redirect/error
        $this->actingAs($user)->get('/admin/users/create')->assertStatus(403);
        // Add checks for other admin actions (store, edit, update, destroy)
    }

    /** @test */
    public function adminCanViewAdminUsersList()
    {
        $response = $this->actingAs($this->adminUser)->get('/admin/users');
        $response->assertStatus(200);
        $response->assertViewIs('admins.index'); // Assuming view name
    }

    /** @test */
    public function adminCanViewCreateAdminUserForm()
    {
        $response = $this->actingAs($this->adminUser)->get('/admin/users/create');
        $response->assertStatus(200);
        $response->assertViewIs('admins.create'); // Assuming view name
    }

    /** @test */
    public function adminCanCreateANewAdminUser()
    {
        $newAdminData = [
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'phone' => $this->faker->phoneNumber, // Add other required fields if necessary
            'is_active' => true,
            'user_type' => User::ADMIN,
        ];

        $response = $this->actingAs($this->adminUser)->post('/admin/users', $newAdminData);

        $response->assertRedirect('/admin/users'); // Assuming redirect to index
        $response->assertSessionHas('success'); // Check for flash message
        $this->assertDatabaseHas('users', [
            'email' => $newAdminData['email'],
            'user_type' => User::ADMIN,
        ]);
    }

    /** @test */
    public function adminCanViewEditAdminUserForm()
    {
        $adminToEdit = User::factory()->create(['user_type' => User::ADMIN]);

        $response = $this->actingAs($this->adminUser)->get("/admin/users/{$adminToEdit->id}/edit");
        $response->assertStatus(200);
        $response->assertViewIs('admins.edit'); // Assuming view name
        $response->assertSee($adminToEdit->name);
    }

    /** @test */
    public function adminCanUpdateAnAdminUser()
    {
        $adminToUpdate = User::factory()->create(['user_type' => User::ADMIN]);
        $updatedData = [
            'name' => 'Updated Name',
            'email' => $this->faker->unique()->safeEmail,
            'phone' => $this->faker->phoneNumber,
            'is_active' => false,
            'user_type' => User::ADMIN, // Required by UpdateAdminRequest
        ];

        $response = $this->actingAs($this->adminUser)->put("/admin/users/{$adminToUpdate->id}", $updatedData);

        $response->assertRedirect('/admin/users'); // Assuming redirect to index
        $response->assertSessionHas('success'); // Check for flash message
        $this->assertDatabaseHas('users', [
            'id' => $adminToUpdate->id,
            'name' => 'Updated Name',
            'email' => $updatedData['email'],
            'is_active' => false,
        ]);
    }

    /** @test */
    public function adminCanDeleteAnAdminUser()
    {
        $adminToDelete = User::factory()->create(['user_type' => User::ADMIN]);

        $response = $this->actingAs($this->adminUser)->delete("/admin/users/{$adminToDelete->id}");

        // Adjust assertions based on actual delete behavior (redirect vs JSON response)
        // If JSON response:
        $response->assertStatus(200); // or appropriate status
        $response->assertJson(['success' => true]); // Example JSON response check
        $this->assertDatabaseMissing('users', ['id' => $adminToDelete->id]);

        // If redirect:
        // $response->assertRedirect('/admin/users');
        // $response->assertSessionHas('success');
        // $this->assertDatabaseMissing('users', ['id' => $adminToDelete->id]);
    }

    /** @test */
    public function adminCannotDeleteSelf()
    {
        $response = $this->actingAs($this->adminUser)->delete("/admin/users/{$this->adminUser->id}");

        // Assert that the delete was forbidden or failed
        // This depends on implementation (e.g., status code, JSON error, no DB change)
        $response->assertStatus(403); // Or 500, or check JSON error
        $this->assertDatabaseHas('users', ['id' => $this->adminUser->id]);
    }
}
