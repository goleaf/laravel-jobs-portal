<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use App\Models\User;

/**
 * Enhanced Authentication Controller for SPA
 * Handles login, logout, registration, and user management
 * Uses Laravel Sanctum for SPA authentication
 */
class AuthController extends Controller
{
    /**
     * User login with email and password
     */
    public function login(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
                'password' => 'required|string|min:6',
            ], [
                'email.required' => 'Email address is required',
                'email.email' => 'Please provide a valid email address',
                'password.required' => 'Password is required',
                'password.min' => 'Password must be at least 6 characters',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $credentials = $request->only('email', 'password');

            if (!Auth::attempt($credentials)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid email or password'
                ], 401);
            }

            $user = Auth::user();
            
            // Delete existing tokens
            $user->tokens()->delete();
            
            // Create new token
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'Login successful',
                'data' => [
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name ?? $user->first_name . ' ' . $user->last_name,
                        'email' => $user->email,
                        'email_verified_at' => $user->email_verified_at,
                        'is_admin' => $user->hasRole('admin') ?? false,
                        'role' => $user->getRoleNames()->first() ?? 'user',
                        'created_at' => $user->created_at?->toISOString(),
                    ],
                    'token' => $token,
                    'token_type' => 'Bearer'
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Login failed',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * User registration
     */
    public function register(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => ['required', 'confirmed', Password::min(8)],
                'role' => 'sometimes|string|in:admin,employer,candidate'
            ], [
                'name.required' => 'Name is required',
                'email.required' => 'Email address is required',
                'email.unique' => 'This email address is already registered',
                'password.required' => 'Password is required',
                'password.confirmed' => 'Password confirmation does not match',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Registration validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'email_verified_at' => now(), // Auto-verify for demo
            ]);

            // Assign role if provided
            $role = $request->get('role', 'candidate');
            if (class_exists('\Spatie\Permission\Models\Role')) {
                try {
                    $user->assignRole($role);
                } catch (\Exception $e) {
                    // Role assignment failed, continue without role
                }
            }

            // Create token
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'Registration successful',
                'data' => [
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'email_verified_at' => $user->email_verified_at,
                        'is_admin' => $user->hasRole('admin') ?? false,
                        'role' => $user->getRoleNames()->first() ?? $role,
                        'created_at' => $user->created_at?->toISOString(),
                    ],
                    'token' => $token,
                    'token_type' => 'Bearer'
                ]
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Registration failed',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Get current authenticated user
     */
    public function user(Request $request): JsonResponse
    {
        try {
            $user = $request->user();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }

            return response()->json([
                'success' => true,
                'message' => 'User retrieved successfully',
                'data' => [
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name ?? $user->first_name . ' ' . $user->last_name,
                        'email' => $user->email,
                        'email_verified_at' => $user->email_verified_at,
                        'is_admin' => $user->hasRole('admin') ?? false,
                        'role' => $user->getRoleNames()->first() ?? 'user',
                        'created_at' => $user->created_at?->toISOString(),
                        'updated_at' => $user->updated_at?->toISOString(),
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve user',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Logout user and revoke token
     */
    public function logout(Request $request): JsonResponse
    {
        try {
            $user = $request->user();

            if ($user) {
                // Revoke current token
                $request->user()->currentAccessToken()->delete();
                
                // Or revoke all tokens
                // $user->tokens()->delete();
            }

            return response()->json([
                'success' => true,
                'message' => 'Logout successful'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Logout failed',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Revoke all tokens for user
     */
    public function logoutAll(Request $request): JsonResponse
    {
        try {
            $user = $request->user();

            if ($user) {
                $user->tokens()->delete();
            }

            return response()->json([
                'success' => true,
                'message' => 'Logged out from all devices successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Logout all failed',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Refresh user token
     */
    public function refresh(Request $request): JsonResponse
    {
        try {
            $user = $request->user();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }

            // Delete current token
            $request->user()->currentAccessToken()->delete();
            
            // Create new token
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'Token refreshed successfully',
                'data' => [
                    'token' => $token,
                    'token_type' => 'Bearer'
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Token refresh failed',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Check if user has specific role
     */
    public function checkRole(Request $request, string $role): JsonResponse
    {
        try {
            $user = $request->user();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }

            $hasRole = $user->hasRole($role) ?? false;

            return response()->json([
                'success' => true,
                'message' => 'Role check completed',
                'data' => [
                    'has_role' => $hasRole,
                    'role' => $role,
                    'user_roles' => $user->getRoleNames() ?? []
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => true, // Don't fail on role check errors
                'message' => 'Role check completed with fallback',
                'data' => [
                    'has_role' => false,
                    'role' => $role,
                    'user_roles' => []
                ]
            ]);
        }
    }
} 