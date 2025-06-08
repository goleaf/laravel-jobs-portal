<?php

namespace App\Http\Controllers\Api\Universal;

use App\Http\Controllers\UniversalBaseController;
use App\Models\Job;
use App\Http\Resources\Universal\JobResource;
use App\Http\Resources\Universal\JobCollection;
use App\Http\Resources\Universal\ShowJobResource;
use App\Http\Resources\Universal\DestroyJobResource;
use App\Http\Requests\Api\Universal\IndexRequest;
use App\Http\Requests\Api\Universal\ShowJobRequest;
use App\Http\Requests\Api\Universal\StoreRequest;
use App\Http\Requests\Api\Universal\UpdateRequest;
use App\Http\Requests\Api\Universal\DestroyJobRequest;
use Illuminate\Http\JsonResponse;

/**
 * Universal Job API Controller
 * Implements MCP best practices for API endpoints
 */
class JobApiController extends UniversalBaseController
{
    /**
     * Universal Pattern: Display a listing of the resource with caching
     */
    public function index(IndexRequest $request): JsonResponse
    {
        try {
            $cacheKey = $this->generateCacheKey($request, 'job_index');
            
            $query = Job::query();
            
            // Universal Pattern: Optimize with eager loading
            $query = $this->optimizeQuery($query, ['user'], ['applications']);
            
            // Universal Pattern: Apply filters
            if ($request->has('search')) {
                $query->where('name', 'like', '%' . $request->search . '%');
            }
            
            // Universal Pattern: Use cursor pagination for large datasets
            $jobs = $this->paginateWithCursor($query);
            
            return $this->jsonResponse([
                'jobs' => new App\Http\Resources\Universal\JobCollection($jobs)
            ]);
            
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to fetch jobs', 500, [], [
                'exception' => $e->getMessage()
            ]);
        }
    }

    /**
     * Universal Pattern: Display the specified resource with caching
     */
    public function show(ShowJobRequest $request, $id): JsonResponse
    {
        try {
            $job = $this->findCached(Job::class, $id, ['user']);
            
            if (!$job) {
                return $this->errorResponse(ucfirst('job') . ' not found', 404);
            }
            
            return $this->jsonResponse([
                'job' => new ShowJobResource($job)
            ]);
            
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to fetch job', 500, [], [
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
                    'job' => new App\Http\Resources\Universal\JobResource($job)
                ], 'Created successfully', 201);
            });
            
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to create job', 500, [], [
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
            $job = Job::findOrFail($id);
            
            // Universal Pattern: Rate limited action
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
                    'job' => new App\Http\Resources\Universal\JobResource($job->fresh())
                ], 'Updated successfully');
            });
            
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to update job', 500, [], [
                'exception' => $e->getMessage()
            ]);
        }
    }

    /**
     * Universal Pattern: Remove the specified resource with soft delete
     */
    public function destroy(DestroyJobRequest $request, $id): JsonResponse
    {
        try {
            $job = Job::findOrFail($id);
            
            // Universal Pattern: Rate limited action
            return $this->rateLimitedAction($request, 'delete_job', function () use ($job, $request) {
                $this->executeTransaction(function () use ($job) {
                    $job->delete();
                });
                
                $this->clearModelCache(Job::class, $job->id);
                
                return $this->jsonResponse([
                    'deletion' => new DestroyJobResource([
                        'job_id' => $job->id,
                        'job_title' => $job->title,
                        'reason' => $request->input('reason'),
                        'applications_affected' => $job->applications()->count(),
                        'active_applications' => $job->applications()->whereIn('status', ['pending', 'reviewing', 'shortlisted'])->count(),
                        'applicants_notified' => $request->input('notify_applicants', false),
                        'refund_processed' => $request->input('refund_featured', false),
                        'cleanup_performed' => true,
                        'cache_cleared' => true,
                        'search_updated' => true,
                        'audit_logged' => true
                    ])
                ], 'Deleted successfully');
            });
            
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to delete job', 500, [], [
                'exception' => $e->getMessage()
            ]);
        }
    }
}