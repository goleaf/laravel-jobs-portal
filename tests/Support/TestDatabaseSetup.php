<?php
namespace Tests\Support;


use Illuminate\Support\Facades\Artisan;
use Spatie\Permission\Models\Role;
use App\Models\User;

class TestDatabaseSetup
{
    public static function setupRoles()
    {
        // Clear cache
        Artisan::call('cache:clear');
        
        // Create roles if they don't exist
        Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'Employer', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'Candidate', 'guard_name' => 'web']);
    }
    
    public static function createTestUser(string $role): User
    {
        $user = User::factory()->create();
        $user->assignRole($role);
        return $user;
    }
}