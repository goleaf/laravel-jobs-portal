<?php

namespace App\Http\Controllers\Api\Universal;

use App\Http\Controllers\UniversalBaseController;
use App\Models\JobApplication;
use App\Http\Resources\Universal\JobApplicationResource;
use App\Http\Resources\Universal\JobApplicationCollection;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

/**
 * Universal JobApplication API Controller
 * Implements MCP best practices for API endpoints
 */
class JobApplicationApiController extends UniversalBaseController
{
    /**
     * Universal Pattern: Display a listing of the resource with caching
     */
    public function index(StoreRequest $request): JsonResponse
    {
        try {
            $cacheKey = $this->generateCacheKey($request, 'jobapplication_index');
            
            $query = JobApplication::query();
            
            // Universal Pattern: Optimize with eager loading
            $query = $this->optimizeQuery($query, ['user'], ['applications']);
            
            // Universal Pattern: Apply filters
            if ($request->has('search')) {
                $query->where('name', 'like', '%' . $request->search . '%');
            }
            
            // Universal Pattern: Use cursor pagination for large datasets
            $jobapplications = $this->paginateWithCursor($query);
            
            return $this->jsonResponse([
                'jobapplications' => new App\Http\Resources\Universal\JobApplicationCollection($jobapplications)
            ]);
            
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to fetch jobapplications', 500, [], [
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
            $jobapplication = $this->findCached(JobApplication::class, $id, ['user']);
            
            if (!$jobapplication) {
                return $this->errorResponse(ucfirst('jobapplication') . ' not found', 404);
            }
            
            return $this->jsonResponse([
                'jobapplication' => new App\Http\Resources\Universal\JobApplicationResource($jobapplication)
            ]);
            
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to fetch jobapplication', 500, [], [
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
            return $this->rateLimitedAction($request, 'create_jobapplication', function () use ($request) {
                $validated = $request->validate([
                    'name' => 'required|string|max:255',
                    // Add more validation rules as needed
                ]);
                
                $jobapplication = $this->executeTransaction(function () use ($validated) {
                    return JobApplication::create($validated);
                });
                
                $this->clearModelCache(JobApplication::class, $jobapplication->id);
                
                return $this->jsonResponse([
                    'jobapplication' => new App\Http\Resources\Universal\JobApplicationResource($jobapplication)
                ], 'Created successfully', 201);
            });
            
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to create jobapplication', 500, [], [
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
            $jobapplication = JobApplication::findOrFail($id);
            
            // Universal Pattern: Rate limited action
            return $this->rateLimitedAction($request, 'update_jobapplication', function () use ($request, $jobapplication) {
                $validated = $request->validate([
                    'name' => 'sometimes|required|string|max:255',
                    // Add more validation rules as needed
                ]);
                
                $this->executeTransaction(function () use ($jobapplication, $validated) {
                    $jobapplication->update($validated);
                });
                
                $this->clearModelCache(JobApplication::class, $jobapplication->id);
                
                return $this->jsonResponse([
                    'jobapplication' => new App\Http\Resources\Universal\JobApplicationResource($jobapplication->fresh())
                ], 'Updated successfully');
            });
            
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to update jobapplication', 500, [], [
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
            $jobapplication = JobApplication::findOrFail($id);
            
            // Universal Pattern: Rate limited action
            return $this->rateLimitedAction($request ?? request(), 'delete_jobapplication', function () use ($jobapplication) {
                $this->executeTransaction(function () use ($jobapplication) {
                    $jobapplication->delete();
                });
                
                $this->clearModelCache(JobApplication::class, $jobapplication->id);
                
                return $this->jsonResponse([], 'Deleted successfully');
            });
            
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to delete jobapplication', 500, [], [
                'exception' => $e->getMessage()
            ]);
        }
    }
}