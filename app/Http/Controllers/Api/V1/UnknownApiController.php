<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Unknown;
use App\Http\Resources\UnknownResource;
use App\Http\Requests\Api\StoreUnknownRequest;
use App\Http\Requests\Api\UpdateUnknownRequest;

/**
 * Context7 API Controller for Unknown
 * Generated for Level 4 Complex System Transformation
 * RESTful API following Laravel 12 best practices
 */
class UnknownApiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Unknown::query();
        
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
            'message' => 'Unknown list retrieved successfully',
            'data' => UnknownResource::collection($data->items()),
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
    public function store(StoreUnknownRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();
            $item = Unknown::create($data);
            
            return response()->json([
                'success' => true,
                'message' => 'Unknown created successfully',
                'data' => new UnknownResource($item)
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create unknown',
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
            $item = Unknown::findOrFail($id);
            
            return response()->json([
                'success' => true,
                'message' => 'Unknown retrieved successfully',
                'data' => new UnknownResource($item)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Unknown not found',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Update the specified resource.
     */
    public function update(UpdateUnknownRequest $request, $id): JsonResponse
    {
        try {
            $item = Unknown::findOrFail($id);
            $data = $request->validated();
            $item->update($data);
            
            return response()->json([
                'success' => true,
                'message' => 'Unknown updated successfully',
                'data' => new UnknownResource($item)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update unknown',
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
            $item = Unknown::findOrFail($id);
            $item->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Unknown deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete unknown',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}