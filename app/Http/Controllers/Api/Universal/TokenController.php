<?php

namespace App\Http\Controllers\Api\Universal;

use App\Http\Controllers\UniversalBaseController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

/**
 * Universal Token Authentication Controller
 * Implements Sanctum authentication with Universal best practices
 */
class TokenController extends UniversalBaseController
{
    /**
     * Universal Pattern: Login and issue API token
     */
    public function login(LoginRequest $request): JsonResponse
    {
        try {
            $request->validate([
                'email' => 'required|email',
                'password' => 'required',
                'device_name' => 'required|string|max:255',
            ]);

            $user = User::where('email', $request->email)->first();

            if (!$user || !Hash::check($request->password, $user->password)) {
                throw ValidationException::withMessages([
                    'email' => ['The provided credentials are incorrect.'],
                ]);
            }

            // Universal Pattern: Token with abilities
            $token = $user->createToken($request->device_name, [
                'user:read',
                'jobs:read',
                'jobs:create',
                'applications:create',
                'profile:update'
            ]);

            return $this->jsonResponse([
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                ],
                'token' => $token->plainTextToken,
                'abilities' => $token->accessToken->abilities,
            ], 'Authentication successful');

        } catch (ValidationException $e) {
            return $this->errorResponse('Invalid credentials', 401, $e->errors());
        } catch (\Exception $e) {
            return $this->errorResponse('Authentication failed', 500, [], [
                'exception' => $e->getMessage()
            ]);
        }
    }

    /**
     * Universal Pattern: Get authenticated user details
     */
    public function user(StoreRequest $request): JsonResponse
    {
        try {
            $user = $request->user();
            
            return $this->jsonResponse([
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'email_verified_at' => $user->email_verified_at,
                    'created_at' => $user->created_at,
                ],
                'token_abilities' => $user->currentAccessToken()?->abilities ?? [],
                'token_name' => $user->currentAccessToken()?->name,
            ], 'User details retrieved');

        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve user', 500, [], [
                'exception' => $e->getMessage()
            ]);
        }
    }

    /**
     * Universal Pattern: Logout and revoke token
     */
    public function logout(StoreRequest $request): JsonResponse
    {
        try {
            // Revoke current token
            $request->user()->currentAccessToken()->delete();

            return $this->jsonResponse([], 'Logged out successfully');

        } catch (\Exception $e) {
            return $this->errorResponse('Logout failed', 500, [], [
                'exception' => $e->getMessage()
            ]);
        }
    }

    /**
     * Universal Pattern: Revoke all tokens
     */
    public function logoutAll(StoreRequest $request): JsonResponse
    {
        try {
            // Revoke all tokens for the user
            $tokensCount = $request->user()->tokens()->count();
            $request->user()->tokens()->delete();

            return $this->jsonResponse([
                'revoked_tokens' => $tokensCount
            ], 'All tokens revoked successfully');

        } catch (\Exception $e) {
            return $this->errorResponse('Failed to revoke tokens', 500, [], [
                'exception' => $e->getMessage()
            ]);
        }
    }

    /**
     * Universal Pattern: List user tokens
     */
    public function tokens(StoreRequest $request): JsonResponse
    {
        try {
            $tokens = $request->user()->tokens()->get()->map(function ($token) {
                return [
                    'id' => $token->id,
                    'name' => $token->name,
                    'abilities' => $token->abilities,
                    'last_used_at' => $token->last_used_at,
                    'created_at' => $token->created_at,
                ];
            });

            return $this->jsonResponse([
                'tokens' => $tokens
            ], 'Tokens retrieved successfully');

        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve tokens', 500, [], [
                'exception' => $e->getMessage()
            ]);
        }
    }
}