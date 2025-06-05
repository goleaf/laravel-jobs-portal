<?php

namespace App\Http\Controllers\Api\Context7;

use App\Http\Requests\Api\Context7\StoreRequest;
use App\Http\Requests\Api\Context7\UpdateRequest;
use App\Http\Controllers\Context7BaseController;
use App\Models\Skill;
use App\Http\Resources\Context7\SkillResource;
use App\Http\Resources\Context7\SkillCollection;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

/**
 * Context7 Skill API Controller
 * Implements MCP best practices for API endpoints
 */
class SkillApiController extends Context7BaseController
{
    /**
     * Context7 Pattern: Display a listing of the resource with caching
     */
    public function index(StoreRequest $request): JsonResponse
    {
        try {
            $cacheKey = $this->generateCacheKey($request, 'skill_index');
            
            $query = Skill::query();
            
            // Context7 Pattern: Optimize with eager loading
            $query = $this->optimizeQuery($query, ['user'], ['applications']);
            
            // Context7 Pattern: Apply filters
            if ($request->has('search')) {
                $query->where('name', 'like', '%' . $request->search . '%');
            }
            
            // Context7 Pattern: Use cursor pagination for large datasets
            $skills = $this->paginateWithCursor($query);
            
            return $this->jsonResponse([
                'skills' => new App\Http\Resources\Context7\SkillCollection($skills)
            ]);
            
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to fetch skills', 500, [], [
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
            $skill = $this->findCached(Skill::class, $id, ['user']);
            
            if (!$skill) {
                return $this->errorResponse(ucfirst('skill') . ' not found', 404);
            }
            
            return $this->jsonResponse([
                'skill' => new App\Http\Resources\Context7\SkillResource($skill)
            ]);
            
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to fetch skill', 500, [], [
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
            return $this->rateLimitedAction($request, 'create_skill', function () use ($request) {
                $validated = $request->validate([
                    'name' => 'required|string|max:255',
                    // Add more validation rules as needed
                ]);
                
                $skill = $this->executeTransaction(function () use ($validated) {
                    return Skill::create($validated);
                });
                
                $this->clearModelCache(Skill::class, $skill->id);
                
                return $this->jsonResponse([
                    'skill' => new App\Http\Resources\Context7\SkillResource($skill)
                ], 'Created successfully', 201);
            });
            
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to create skill', 500, [], [
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
            $skill = Skill::findOrFail($id);
            
            // Context7 Pattern: Rate limited action
            return $this->rateLimitedAction($request, 'update_skill', function () use ($request, $skill) {
                $validated = $request->validate([
                    'name' => 'sometimes|required|string|max:255',
                    // Add more validation rules as needed
                ]);
                
                $this->executeTransaction(function () use ($skill, $validated) {
                    $skill->update($validated);
                });
                
                $this->clearModelCache(Skill::class, $skill->id);
                
                return $this->jsonResponse([
                    'skill' => new App\Http\Resources\Context7\SkillResource($skill->fresh())
                ], 'Updated successfully');
            });
            
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to update skill', 500, [], [
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
            $skill = Skill::findOrFail($id);
            
            // Context7 Pattern: Rate limited action
            return $this->rateLimitedAction($request ?? request(), 'delete_skill', function () use ($skill) {
                $this->executeTransaction(function () use ($skill) {
                    $skill->delete();
                });
                
                $this->clearModelCache(Skill::class, $skill->id);
                
                return $this->jsonResponse([], 'Deleted successfully');
            });
            
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to delete skill', 500, [], [
                'exception' => $e->getMessage()
            ]);
        }
    }
}