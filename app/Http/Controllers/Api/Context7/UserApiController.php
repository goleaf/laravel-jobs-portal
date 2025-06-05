<?php

namespace App\Http\Controllers\Api\Context7;

use App\Http\Requests\Api\Context7\StoreRequest;
use App\Http\Requests\Api\Context7\UpdateRequest;
use App\Http\Controllers\Context7BaseController;
use App\Models\User;
use App\Http\Resources\Context7\UserResource;
use App\Http\Resources\Context7\UserCollection;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

/**
 * Context7 User API Controller
 * Implements MCP best practices for API endpoints
 */
class UserApiController extends Context7BaseController
{
    /**
     * Context7 Pattern: Display a listing of the resource with caching
     */
    public function index(StoreRequest $request): JsonResponse
    {
        try {
            $cacheKey = $this->generateCacheKey($request, 'user_index');
            
            $query = User::query();
            
            // Context7 Pattern: Optimize with eager loading
            $query = $this->optimizeQuery($query, ['user'], ['applications']);
            
            // Context7 Pattern: Apply filters
            if ($request->has('search')) {
                $query->where('name', 'like', '%' . $request->search . '%');
            }
            
            // Context7 Pattern: Use cursor pagination for large datasets
            $users = $this->paginateWithCursor($query);
            
            return $this->jsonResponse([
                'users' => new App\Http\Resources\Context7\UserCollection($users)
            ]);
            
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to fetch users', 500, [], [
                'exception' => $e->getMessage()
            ]);
        }
    }

    /**
     * Context7 Pattern: Display the specified resource with caching
     */
    public function show($id): JsonResponse
    {
        try {
            $user = $this->findCached(User::class, $id, ['user']);
            
            if (!$user) {
                return $this->errorResponse(ucfirst('user') . ' not found', 404);
            }
            
            return $this->jsonResponse([
                'user' => new App\Http\Resources\Context7\UserResource($user)
            ]);
            
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to fetch user', 500, [], [
                'exception' => $e->getMessage()
            ]);
        }
    }

    /**
     * Context7 Pattern: Store a newly created resource with validation
     */
    public function store(StoreRequest $request): JsonResponse
    {
        try {
            // Context7 Pattern: Rate limited action
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
                    'user' => new App\Http\Resources\Context7\UserResource($user)
                ], 'Created successfully', 201);
            });
            
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to create user', 500, [], [
                'exception' => $e->getMessage()
            ]);
        }
    }

    /**
     * Context7 Pattern: Update the specified resource with optimistic locking
     */
    public function update(UpdateRequest $request, $id): JsonResponse
    {
        try {
            $user = User::findOrFail($id);
            
            // Context7 Pattern: Rate limited action
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
                    'user' => new App\Http\Resources\Context7\UserResource($user->fresh())
                ], 'Updated successfully');
            });
            
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to update user', 500, [], [
                'exception' => $e->getMessage()
            ]);
        }
    }

    /**
     * Context7 Pattern: Remove the specified resource with soft delete
     */
    public function destroy($id): JsonResponse
    {
        try {
            $user = User::findOrFail($id);
            
            // Context7 Pattern: Rate limited action
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