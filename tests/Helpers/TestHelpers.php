<?php

namespace Tests\Helpers;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class TestHelpers
{
    /**
     * Create a user with guaranteed unique email
     */
    public static function createUserWithUniqueEmail(array $attributes = []): User
    {
        $defaultAttributes = [
            'name' => 'Test User',
            'email' => 'test' . time() . random_int(1000, 9999) . '@example.com',
            'password' => Hash::make('password123'),
            'email_verified_at' => now(),
        ];

        return User::factory()->create(array_merge($defaultAttributes, $attributes));
    }

    /**
     * Create API authentication headers
     */
    public static function getApiAuthHeaders(User $user): array
    {
        $token = $user->createToken('test-token')->plainTextToken;
        
        return [
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ];
    }

    /**
     * Create basic test data using Context7 patterns
     */
    public static function createBasicTestData(): void
    {
        try {
            // Create minimal data needed for foreign key relationships
            // Using DB::table to avoid model constraints
            if (!\DB::table('marital_status')->exists()) {
                \DB::table('marital_status')->insert([
                    'id' => 1,
                    'marital_status' => 'Single',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
            
            if (!\DB::table('career_levels')->exists()) {
                \DB::table('career_levels')->insert([
                    'id' => 1, 
                    'level_name' => 'Entry Level',
                    'is_default' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
            
            if (!\DB::table('industries')->exists()) {
                \DB::table('industries')->insert([
                    'id' => 1,
                    'name' => 'Technology',
                    'is_default' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
            
            if (!\DB::table('functional_areas')->exists()) {
                \DB::table('functional_areas')->insert([
                    'id' => 1,
                    'name' => 'Software Development',
                    'is_default' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        } catch (\Exception $e) {
            // Silently continue if tables don't exist yet
        }
    }
}