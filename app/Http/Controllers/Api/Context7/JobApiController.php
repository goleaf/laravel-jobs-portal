<?php

namespace App\Http\Controllers\Api\Context7;

use App\Http\Requests\Api\Context7\StoreRequest;
use App\Http\Requests\Api\Context7\UpdateRequest;
use App\Http\Controllers\Context7BaseController;
use App\Models\Job;
use App\Http\Resources\Context7\JobResource;
use App\Http\Resources\Context7\JobCollection;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

/**
 * Context7 Job API Controller
 * Implements MCP best practices for API endpoints
 */
class JobApiController extends Context7BaseController
{
    /**
     * Context7 Pattern: Display a listing of the resource with caching
     */
    public function index(StoreRequest $request): JsonResponse
    {
        try {
            $cacheKey = $this->generateCacheKey($request, 'job_index');
            
            $query = Job::query();
            
            // Context7 Pattern: Optimize with eager loading
            $query = $this->optimizeQuery($query, ['user'], ['applications']);
            
            // Context7 Pattern: Apply filters
            if ($request->has('search')) {
                $query->where('name', 'like', '%' . $request->search . '%');
            }
            
            // Context7 Pattern: Use cursor pagination for large datasets
            $jobs = $this->paginateWithCursor($query);
            
            return $this->jsonResponse([
                'jobs' => new App\Http\Resources\Context7\JobCollection($jobs)
            ]);
            
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to fetch jobs', 500, [], [
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
            $job = $this->findCached(Job::class, $id, ['user']);
            
            if (!$job) {
                return $this->errorResponse(ucfirst('job') . ' not found', 404);
            }
            
            return $this->jsonResponse([
                'job' => new App\Http\Resources\Context7\JobResource($job)
            ]);
            
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to fetch job', 500, [], [
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
            return $this->rateLimitedAction($request, 'create_job', function () use ($request) {
                $validated = $request->validate([
                    'name' => 'required|string|max:255',
                    // Add more validation rules as needed
                ]);
                
                $job = $this->executeTransaction(function () use ($validated) {
                    return Job::create($validated);
                });
                
                $this->clearModelCache(Job::class, $job->id);
                
                return $this->jsonResponse([
                    'job' => new App\Http\Resources\Context7\JobResource($job)
                ], 'Created successfully', 201);
            });
            
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to create job', 500, [], [
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
            $job = Job::findOrFail($id);
            
            // Context7 Pattern: Rate limited action
            return $this->rateLimitedAction($request, 'update_job', function () use ($request, $job) {
                $validated = $request->validate([
                    'name' => 'sometimes|required|string|max:255',
                    // Add more validation rules as needed
                ]);
                
                $this->executeTransaction(function () use ($job, $validated) {
                    $job->update($validated);
                });
                
                $this->clearModelCache(Job::class, $job->id);
                
                return $this->jsonResponse([
                    'job' => new App\Http\Resources\Context7\JobResource($job->fresh())
                ], 'Updated successfully');
            });
            
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to update job', 500, [], [
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
            $job = Job::findOrFail($id);
            
            // Context7 Pattern: Rate limited action
            return $this->rateLimitedAction($request ?? request(), 'delete_job', function () use ($job) {
                $this->executeTransaction(function () use ($job) {
                    $job->delete();
                });
                
                $this->clearModelCache(Job::class, $job->id);
                
                return $this->jsonResponse([], 'Deleted successfully');
            });
            
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to delete job', 500, [], [
                'exception' => $e->getMessage()
            ]);
        }
    }
}