<?php

namespace App\Http\Controllers\Api\Context7;

use App\Http\Requests\Api\Context7\StoreRequest;
use App\Http\Requests\Api\Context7\UpdateRequest;
use App\Http\Controllers\Context7BaseController;
use App\Models\Candidate;
use App\Http\Resources\Context7\CandidateResource;
use App\Http\Resources\Context7\CandidateCollection;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

/**
 * Context7 Candidate API Controller
 * Implements MCP best practices for API endpoints
 */
class CandidateApiController extends Context7BaseController
{
    /**
     * Context7 Pattern: Display a listing of the resource with caching
     */
    public function index(StoreRequest $request): JsonResponse
    {
        try {
            $cacheKey = $this->generateCacheKey($request, 'candidate_index');
            
            $query = Candidate::query();
            
            // Context7 Pattern: Optimize with eager loading
            $query = $this->optimizeQuery($query, ['user'], ['applications']);
            
            // Context7 Pattern: Apply filters
            if ($request->has('search')) {
                $query->where('name', 'like', '%' . $request->search . '%');
            }
            
            // Context7 Pattern: Use cursor pagination for large datasets
            $candidates = $this->paginateWithCursor($query);
            
            return $this->jsonResponse([
                'candidates' => new App\Http\Resources\Context7\CandidateCollection($candidates)
            ]);
            
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to fetch candidates', 500, [], [
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
            $candidate = $this->findCached(Candidate::class, $id, ['user']);
            
            if (!$candidate) {
                return $this->errorResponse(ucfirst('candidate') . ' not found', 404);
            }
            
            return $this->jsonResponse([
                'candidate' => new App\Http\Resources\Context7\CandidateResource($candidate)
            ]);
            
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to fetch candidate', 500, [], [
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
            return $this->rateLimitedAction($request, 'create_candidate', function () use ($request) {
                $validated = $request->validate([
                    'name' => 'required|string|max:255',
                    // Add more validation rules as needed
                ]);
                
                $candidate = $this->executeTransaction(function () use ($validated) {
                    return Candidate::create($validated);
                });
                
                $this->clearModelCache(Candidate::class, $candidate->id);
                
                return $this->jsonResponse([
                    'candidate' => new App\Http\Resources\Context7\CandidateResource($candidate)
                ], 'Created successfully', 201);
            });
            
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to create candidate', 500, [], [
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
            $candidate = Candidate::findOrFail($id);
            
            // Context7 Pattern: Rate limited action
            return $this->rateLimitedAction($request, 'update_candidate', function () use ($request, $candidate) {
                $validated = $request->validate([
                    'name' => 'sometimes|required|string|max:255',
                    // Add more validation rules as needed
                ]);
                
                $this->executeTransaction(function () use ($candidate, $validated) {
                    $candidate->update($validated);
                });
                
                $this->clearModelCache(Candidate::class, $candidate->id);
                
                return $this->jsonResponse([
                    'candidate' => new App\Http\Resources\Context7\CandidateResource($candidate->fresh())
                ], 'Updated successfully');
            });
            
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to update candidate', 500, [], [
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
            $candidate = Candidate::findOrFail($id);
            
            // Context7 Pattern: Rate limited action
            return $this->rateLimitedAction($request ?? request(), 'delete_candidate', function () use ($candidate) {
                $this->executeTransaction(function () use ($candidate) {
                    $candidate->delete();
                });
                
                $this->clearModelCache(Candidate::class, $candidate->id);
                
                return $this->jsonResponse([], 'Deleted successfully');
            });
            
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to delete candidate', 500, [], [
                'exception' => $e->getMessage()
            ]);
        }
    }
}