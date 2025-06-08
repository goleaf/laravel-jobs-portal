<?php

namespace App\Http\Controllers\Api\Universal;

use App\Http\Controllers\UniversalBaseController;
use App\Models\User;
use App\Http\Resources\Universal\UserResource;
use App\Http\Resources\Universal\UserCollection;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

/**
 * Universal User API Controller
 * Implements MCP best practices for API endpoints
 */
class UserApiController extends UniversalBaseController
{
    /**
     * Universal Pattern: Display a listing of the resource with caching
     */
    public function index(StoreRequest $request): JsonResponse
    {
        try {
            $cacheKey = $this->generateCacheKey($request, 'user_index');
            
            $query = User::query();
            
            // Universal Pattern: Optimize with eager loading
            $query = $this->optimizeQuery($query, ['user'], ['applications']);
            
            // Universal Pattern: Apply filters
            if ($request->has('search')) {
                $query->where('name', 'like', '%' . $request->search . '%');
            }
            
            // Universal Pattern: Use cursor pagination for large datasets
            $users = $this->paginateWithCursor($query);
            
            return $this->jsonResponse([
                'users' => new App\Http\Resources\Universal\UserCollection($users)
            ]);
            
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to fetch users', 500, [], [
                'exception' => $e->getMessage()
            ]);
        }
    }

    /**
     * Universal Pattern: Display the specified resource with caching
     */
    public function show($id): JsonResponse
    {
        try {
            $user = $this->findCached(User::class, $id, ['user']);
            
            if (!$user) {
                return $this->errorResponse(ucfirst('user') . ' not found', 404);
            }
            
            return $this->jsonResponse([
                'user' => new App\Http\Resources\Universal\UserResource($user)
            ]);
            
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to fetch user', 500, [], [
                'exception' => $e->getMessage()
            ]);
        }
    }

    /**
     * Universal Pattern: Store a newly created resource with validation
     */
    public function store(StoreRequest $request): JsonResponse
    {
        try {
            // Universal Pattern: Rate limited action
            return $this->rateLimitedAction($request, 'create_user', function () use ($request) {
                $validated = $request->validate([
                    'name' => 'required|string|max:255',
                    // Add more validation rules as needed
                ]);
                
                $user = $this->executeTransaction(function () use ($validated) {
                    return User::create($validated);
                });
                
                $this->clearModelCache(User::class, $user->id);
                
                return $this->jsonResponse([
                    'user' => new App\Http\Resources\Universal\UserResource($user)
                ], 'Created successfully', 201);
            });
            
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to create user', 500, [], [
                'exception' => $e->getMessage()
            ]);
        }
    }

    /**
     * Universal Pattern: Update the specified resource with optimistic locking
     */
    public function update(UpdateRequest $request, $id): JsonResponse
    {
        try {
            $user = User::findOrFail($id);
            
            // Universal Pattern: Rate limited action
            return $this->rateLimitedAction($request, 'update_user', function () use ($request, $user) {
                $validated = $request->validate([
                    'name' => 'sometimes|required|string|max:255',
                    // Add more validation rules as needed
                ]);
                
                $this->executeTransaction(function () use ($user, $validated) {
                    $user->update($validated);
                });
                
                $this->clearModelCache(User::class, $user->id);
                
                return $this->jsonResponse([
                    'user' => new App\Http\Resources\Universal\UserResource($user->fresh())
                ], 'Updated successfully');
            });
            
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to update user', 500, [], [
                'exception' => $e->getMessage()
            ]);
        }
    }

    /**
     * Universal Pattern: Remove the specified resource with soft delete
     */
    public function destroy($id): JsonResponse
    {
        try {
            $user = User::findOrFail($id);
            
            // Universal Pattern: Rate limited action
            return $this->rateLimitedAction($request ?? request(), 'delete_user', function () use ($user) {
                $this->executeTransaction(function () use ($user) {
                    $user->delete();
                });
                
                $this->clearModelCache(User::class, $user->id);
                
                return $this->jsonResponse([], 'Deleted successfully');
            });
            
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to delete user', 500, [], [
                'exception' => $e->getMessage()
            ]);
        }
    }
}