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
}