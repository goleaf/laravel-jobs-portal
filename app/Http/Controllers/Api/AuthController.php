<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\User;

/**
 * Enhanced API Authentication Controller
 * Level 4 Complex System - Vue3 SPA Authentication
 */
class AuthController extends Controller
{
    /**
     * Login user and create token
     */
    public function login(Request $request): JsonResponse
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
            'remember' => 'boolean'
        ]);
        
        if (!Auth::attempt(['email' => $credentials['email'], 'password' => $credentials['password']], $credentials['remember'] ?? false)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials'
            ], 401);
        }
        
        $user = Auth::user();
        $token = $user->createToken('api-token')->plainTextToken;
        
        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'data' => [
                'user' => $user,
                'token' => $token,
                'token_type' => 'Bearer'
            ]
        ]);
    }
    
    /**
     * Register new user
     */
    public function register(Request $request): JsonResponse
    {
        $data = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'user_type' => 'nullable|string|in:admin,employer,candidate'
        ]);
        
        $data['password'] = Hash::make($data['password']);
        $data['user_type'] = $data['user_type'] ?? 'candidate';
        
        $user = User::create($data);
        $token = $user->createToken('api-token')->plainTextToken;
        
        return response()->json([
            'success' => true,
            'message' => 'Registration successful',
            'data' => [
                'user' => $user,
                'token' => $token,
                'token_type' => 'Bearer'
            ]
        ], 201);
    }
    
    /**
     * Logout user and revoke token
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Logout successful'
        ]);
    }
    
    /**
     * Get authenticated user
     */
    public function user(Request $request): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'User retrieved successfully',
            'data' => $request->user()
        ]);
    }

    /**
     * Get login information for demo purposes
     * Provides user data and system status for login info component
     */
    public function getLoginInfo(): JsonResponse
    {
        try {
            // Check database connection
            DB::connection()->getPdo();
            
            // Get active users with basic information
            $users = User::where('is_active', 1)
                ->select(['id', 'first_name', 'last_name', 'email', 'user_type', 'is_active', 'created_at'])
                ->orderBy('id')
                ->get()
                ->map(function ($user) {
                    return [
                        'id' => $user->id,
                        'name' => trim($user->first_name . ' ' . $user->last_name),
                        'email' => $user->email,
                        'user_type' => $user->user_type,
                        'is_active' => $user->is_active,
                        'roles' => $user->user_type, // Simplified role display
                        'created_at' => $user->created_at?->format('Y-m-d H:i:s')
                    ];
                });

            // System information
            $systemInfo = [
                'laravel_version' => app()->version(),
                'php_version' => PHP_VERSION,
                'environment' => app()->environment(),
                'database_connection' => config('database.default'),
                'users_count' => $users->count(),
                'last_check' => now()->format('Y-m-d H:i:s')
            ];

            return response()->json([
                'success' => true,
                'message' => 'Login information retrieved successfully',
                'users' => $users,
                'system_info' => $systemInfo,
                'demo_credentials' => [
                    [
                        'role' => 'admin',
                        'email' => 'admin@jobportal.com',
                        'password' => 'password',
                        'description' => 'Super Admin Access'
                    ],
                    [
                        'role' => 'employer',
                        'email' => 'john@example.com',
                        'password' => 'password',
                        'description' => 'Employer Dashboard'
                    ],
                    [
                        'role' => 'candidate',
                        'email' => 'jane@example.com',
                        'password' => 'password',
                        'description' => 'Candidate Portal'
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Login info API error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Database connection error',
                'error' => app()->environment('local') ? $e->getMessage() : 'Internal server error',
                'users' => [],
                'system_info' => [
                    'laravel_version' => app()->version(),
                    'php_version' => PHP_VERSION,
                    'environment' => app()->environment(),
                    'database_connection' => 'Error',
                    'users_count' => 0,
                    'last_check' => now()->format('Y-m-d H:i:s')
                ]
            ], 500);
        }
    }

    /**
     * Verify login credentials (for testing purposes)
     */
    public function verifyCredentials(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string'
        ]);

        try {
            $user = User::where('email', $request->email)
                ->where('is_active', 1)
                ->first();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found or inactive',
                    'can_login' => false
                ]);
            }

            // Check if password matches (for demo, we assume 'password' is correct)
            $canLogin = $request->password === 'password' || 
                       Hash::check($request->password, $user->password);

            return response()->json([
                'success' => true,
                'message' => $canLogin ? 'Credentials valid' : 'Invalid password',
                'can_login' => $canLogin,
                'user' => [
                    'id' => $user->id,
                    'name' => trim($user->first_name . ' ' . $user->last_name),
                    'email' => $user->email,
                    'user_type' => $user->user_type,
                    'roles' => $user->user_type
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Credential verification error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Verification failed',
                'error' => app()->environment('local') ? $e->getMessage() : 'Internal server error',
                'can_login' => false
            ], 500);
        }
    }
} 