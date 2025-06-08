<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Candidate;
use App\Http\Resources\CandidateResource;

/**
 * Context7 API Controller for Candidate
 * Generated for Level 4 Complex System Transformation
 * RESTful API following Laravel 12 best practices
 */
class CandidateApiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Candidate::query();
        
        // Apply filters
        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }
        
        if ($request->has('status')) {
            $query->where('is_active', $request->boolean('status'));
        }
        
        // Pagination
        $perPage = min($request->integer('per_page', 15), 100);
        $data = $query->paginate($perPage);
        
        return response()->json([
            'success' => true,
            'message' => 'Candidate list retrieved successfully',
            'data' => CandidateResource::collection($data->items()),
            'pagination' => [
                'current_page' => $data->currentPage(),
                'last_page' => $data->lastPage(),
                'per_page' => $data->perPage(),
                'total' => $data->total(),
            ]
        ]);
    }

    /**
     * Store a newly created resource.
     */
    public function store(StoreCandidateRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();
            $item = Candidate::create($data);
            
            return response()->json([
                'success' => true,
                'message' => 'Candidate created successfully',
                'data' => new CandidateResource($item)
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create candidate',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id): JsonResponse
    {
        try {
            $item = Candidate::findOrFail($id);
            
            return response()->json([
                'success' => true,
                'message' => 'Candidate retrieved successfully',
                'data' => new CandidateResource($item)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Candidate not found',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Update the specified resource.
     */
    public function update(UpdateCandidateRequest $request, $id): JsonResponse
    {
        try {
            $item = Candidate::findOrFail($id);
            $data = $request->validated();
            $item->update($data);
            
            return response()->json([
                'success' => true,
                'message' => 'Candidate updated successfully',
                'data' => new CandidateResource($item)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update candidate',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource.
     */
    public function destroy($id): JsonResponse
    {
        try {
            $item = Candidate::findOrFail($id);
            $item->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Candidate deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete candidate',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}