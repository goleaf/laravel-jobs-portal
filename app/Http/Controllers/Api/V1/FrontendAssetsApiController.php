<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\FrontendAssets;
use App\Http\Resources\FrontendAssetsResource;

/**
 * Enhanced API Controller for FrontendAssets
 * Generated for Level 4 Complex System Transformation
 * RESTful API following Laravel 12 best practices
 */
class FrontendAssetsApiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $query = FrontendAssets::query();
        
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
            'message' => 'FrontendAssets list retrieved successfully',
            'data' => FrontendAssetsResource::collection($data->items()),
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
    public function store(StoreFrontendAssetsRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();
            $item = FrontendAssets::create($data);
            
            return response()->json([
                'success' => true,
                'message' => 'FrontendAssets created successfully',
                'data' => new FrontendAssetsResource($item)
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create frontendassets',
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
            $item = FrontendAssets::findOrFail($id);
            
            return response()->json([
                'success' => true,
                'message' => 'FrontendAssets retrieved successfully',
                'data' => new FrontendAssetsResource($item)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'FrontendAssets not found',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Update the specified resource.
     */
    public function update(UpdateFrontendAssetsRequest $request, $id): JsonResponse
    {
        try {
            $item = FrontendAssets::findOrFail($id);
            $data = $request->validated();
            $item->update($data);
            
            return response()->json([
                'success' => true,
                'message' => 'FrontendAssets updated successfully',
                'data' => new FrontendAssetsResource($item)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update frontendassets',
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
            $item = FrontendAssets::findOrFail($id);
            $item->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'FrontendAssets deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete frontendassets',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}