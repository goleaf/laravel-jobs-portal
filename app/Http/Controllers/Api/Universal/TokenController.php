<?php

namespace App\Http\Controllers\Api\Universal;

use App\Http\Controllers\UniversalBaseController;
use App\Http\Requests\Api\Universal\LoginRequest;
use App\Http\Requests\Api\Universal\LogoutAllRequest;
use App\Http\Requests\Api\Universal\LogoutRequest;
use App\Http\Requests\Api\Universal\TokensRequest;
use App\Http\Requests\Api\Universal\UserRequest;
use App\Http\Resources\Universal\AuthUserResource;
use App\Http\Resources\Universal\LoginResource;
use App\Http\Resources\Universal\LogoutResource;
use App\Http\Resources\Universal\TokenCollection;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

/**
 * Universal Token Authentication Controller
 * Implements Sanctum authentication with Universal best practices.
 */
class TokenController extends UniversalBaseController
{
    /**
     * Universal Pattern: Login and issue API token.
     */
    public function login(LoginRequest $request): JsonResponse
    {
        try {
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
                'profile:update',
            ]);

            $data = [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                ],
                'token' => $token->plainTextToken,
                'abilities' => $token->accessToken->abilities,
                'device_name' => $request->device_name,
            ];

            return response()->json((new LoginResource($data))->toArray($request))
                ->setStatusCode(200)
            ;
        } catch (ValidationException $e) {
            return $this->errorResponse('Invalid credentials', 401, $e->errors());
        } catch (\Exception $e) {
            return $this->errorResponse('Authentication failed', 500, [], [
                'exception' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Universal Pattern: Get authenticated user details.
     */
    public function user(UserRequest $request): JsonResponse
    {
        try {
            $user = $request->user();

            return response()->json((new AuthUserResource($user))->toArray($request))
                ->setStatusCode(200)
            ;
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve user', 500, [], [
                'exception' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Universal Pattern: Logout and revoke token.
     */
    public function logout(LogoutRequest $request): JsonResponse
    {
        try {
            // Get current token info before deletion
            $currentToken = $request->user()->currentAccessToken();
            $remainingTokens = $request->user()->tokens()->count() - 1;

            // Revoke current token
            $currentToken->delete();

            $data = [
                'revoked_token' => true,
                'remaining_tokens' => $remainingTokens,
            ];

            return response()->json((new LogoutResource($data))->toArray($request))
                ->setStatusCode(200)
            ;
        } catch (\Exception $e) {
            return $this->errorResponse('Logout failed', 500, [], [
                'exception' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Universal Pattern: Revoke all tokens.
     */
    public function logoutAll(LogoutAllRequest $request): JsonResponse
    {
        try {
            // Revoke all tokens for the user
            $tokensCount = $request->user()->tokens()->count();
            $request->user()->tokens()->delete();

            return $this->jsonResponse([
                'revoked_tokens' => $tokensCount,
            ], 'All tokens revoked successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to revoke tokens', 500, [], [
                'exception' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Universal Pattern: List user tokens.
     */
    public function tokens(TokensRequest $request): JsonResponse
    {
        try {
            $query = $request->user()->tokens();

            // Apply filters
            if ($request->boolean('active_only')) {
                $query->whereNotNull('last_used_at');
            }

            // Apply sorting
            $query->orderBy($request->input('sort_by', 'created_at'), $request->input('sort_direction', 'desc'));

            // Apply limit
            $tokens = $query->limit($request->input('limit', 20))->get()->map(function ($token) {
                return [
                    'id' => $token->id,
                    'name' => $token->name,
                    'abilities' => $token->abilities,
                    'last_used_at' => $token->last_used_at,
                    'created_at' => $token->created_at,
                ];
            });

            return response()->json((new TokenCollection($tokens))->toArray($request))
                ->setStatusCode(200)
            ;
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve tokens', 500, [], [
                'exception' => $e->getMessage(),
            ]);
        }
    }
}
