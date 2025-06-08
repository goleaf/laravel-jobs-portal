<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

/**
 * Context7 API Authentication Controller
 * Level 4 Complex System - Vue3 SPA Authentication
 */
class AuthController extends Controller
{
    /**
     * Login user and create token
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $credentials = $request->validated();
        
        if (!Auth::attempt($credentials)) {
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
    public function register(RegisterRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['password'] = Hash::make($data['password']);
        
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
}