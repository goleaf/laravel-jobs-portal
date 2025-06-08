<?php

namespace App\Http\Controllers\Api\Universal;

use App\Http\Controllers\UniversalBaseController;
use App\Models\Skill;
use App\Http\Resources\Universal\SkillResource;
use App\Http\Resources\Universal\SkillCollection;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

/**
 * Universal Skill API Controller
 * Implements MCP best practices for API endpoints
 */
class SkillApiController extends UniversalBaseController
{
    /**
     * Universal Pattern: Display a listing of the resource with caching
     */
    public function index(StoreRequest $request): JsonResponse
    {
        try {
            $cacheKey = $this->generateCacheKey($request, 'skill_index');
            
            $query = Skill::query();
            
            // Universal Pattern: Optimize with eager loading
            $query = $this->optimizeQuery($query, ['user'], ['applications']);
            
            // Universal Pattern: Apply filters
            if ($request->has('search')) {
                $query->where('name', 'like', '%' . $request->search . '%');
            }
            
            // Universal Pattern: Use cursor pagination for large datasets
            $skills = $this->paginateWithCursor($query);
            
            return $this->jsonResponse([
                'skills' => new App\Http\Resources\Universal\SkillCollection($skills)
            ]);
            
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to fetch skills', 500, [], [
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
            $skill = $this->findCached(Skill::class, $id, ['user']);
            
            if (!$skill) {
                return $this->errorResponse(ucfirst('skill') . ' not found', 404);
            }
            
            return $this->jsonResponse([
                'skill' => new App\Http\Resources\Universal\SkillResource($skill)
            ]);
            
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to fetch skill', 500, [], [
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
                    'skill' => new App\Http\Resources\Universal\SkillResource($skill)
                ], 'Created successfully', 201);
            });
            
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to create skill', 500, [], [
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
            $skill = Skill::findOrFail($id);
            
            // Universal Pattern: Rate limited action
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
                    'skill' => new App\Http\Resources\Universal\SkillResource($skill->fresh())
                ], 'Updated successfully');
            });
            
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to update skill', 500, [], [
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
            $skill = Skill::findOrFail($id);
            
            // Universal Pattern: Rate limited action
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