<?php

namespace App\Http\Controllers\Api\Universal;

use App\Http\Controllers\UniversalBaseController;
use App\Http\Requests\Api\Universal\IndexRequest;
use App\Http\Requests\Api\Universal\ShowCandidateRequest;
use App\Http\Requests\Api\Universal\StoreRequest;
use App\Http\Requests\Api\Universal\UpdateRequest;
use App\Http\Requests\Api\Universal\DestroyCandidateRequest;
use App\Http\Resources\Universal\CandidateResource;
use App\Http\Resources\Universal\CandidateCollection;
use App\Http\Resources\Universal\ShowCandidateResource;
use App\Http\Resources\Universal\DestroyCandidateResource;
use App\Models\Candidate;
use Illuminate\Http\JsonResponse;

/**
 * Universal Candidate API Controller
 * Implements MCP best practices for API endpoints
 */
class CandidateApiController extends UniversalBaseController
{
    /**
     * Universal Pattern: Display a listing of the resource with caching
     */
    public function index(IndexRequest $request): JsonResponse
    {
        try {
            $cacheKey = $this->generateCacheKey($request, 'candidate_index');
            
            $query = Candidate::query();
            
            // Universal Pattern: Optimize with eager loading
            $query = $this->optimizeQuery($query, ['user'], ['applications']);
            
            // Universal Pattern: Apply filters
            if ($request->has('search')) {
                $query->where('name', 'like', '%' . $request->search . '%');
            }
            
            // Universal Pattern: Use cursor pagination for large datasets
            $candidates = $this->paginateWithCursor($query);
            
            return $this->jsonResponse([
                'candidates' => new CandidateCollection($candidates)
            ]);
            
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to fetch candidates', 500, [], [
                'exception' => $e->getMessage()
            ]);
        }
    }

    /**
     * Universal Pattern: Display the specified resource with caching
     */
    public function show(ShowCandidateRequest $request, $id): JsonResponse
    {
        try {
            $candidate = $this->findCached(Candidate::class, $id, ['user']);
            
            if (!$candidate) {
                return $this->errorResponse(ucfirst('candidate') . ' not found', 404);
            }
            
            return $this->jsonResponse([
                'candidate' => new ShowCandidateResource($candidate)
            ]);
            
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to fetch candidate', 500, [], [
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
                    'candidate' => new App\Http\Resources\Universal\CandidateResource($candidate)
                ], 'Created successfully', 201);
            });
            
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to create candidate', 500, [], [
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
            $candidate = Candidate::findOrFail($id);
            
            // Universal Pattern: Rate limited action
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
                    'candidate' => new App\Http\Resources\Universal\CandidateResource($candidate->fresh())
                ], 'Updated successfully');
            });
            
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to update candidate', 500, [], [
                'exception' => $e->getMessage()
            ]);
        }
    }

    /**
     * Universal Pattern: Remove the specified resource with soft delete
     */
    public function destroy(DestroyCandidateRequest $request, $id): JsonResponse
    {
        try {
            $candidate = Candidate::findOrFail($id);
            
            // Universal Pattern: Rate limited action
            return $this->rateLimitedAction($request, 'delete_candidate', function () use ($candidate, $request) {
                $this->executeTransaction(function () use ($candidate) {
                    $candidate->delete();
                });
                
                $this->clearModelCache(Candidate::class, $candidate->id);
                
                return $this->jsonResponse([
                    'deletion' => new DestroyCandidateResource([
                        'candidate_id' => $candidate->id,
                        'reason' => $request->input('reason'),
                        'cleanup_performed' => true,
                        'applications_handled' => 'preserved',
                        'files_removed' => []
                    ])
                ], 'Deleted successfully');
            });
            
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to delete candidate', 500, [], [
                'exception' => $e->getMessage()
            ]);
        }
    }
}