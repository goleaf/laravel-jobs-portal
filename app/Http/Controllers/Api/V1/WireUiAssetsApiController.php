<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\WireUiAssets;
use App\Http\Resources\WireUiAssetsResource;

/**
 * Enhanced API Controller for WireUiAssets
 * Generated for Level 4 Complex System Transformation
 * RESTful API following Laravel 12 best practices
 */
class WireUiAssetsApiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $query = WireUiAssets::query();
        
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
            'message' => 'WireUiAssets list retrieved successfully',
            'data' => WireUiAssetsResource::collection($data->items()),
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
    public function store(StoreWireUiAssetsRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();
            $item = WireUiAssets::create($data);
            
            return response()->json([
                'success' => true,
                'message' => 'WireUiAssets created successfully',
                'data' => new WireUiAssetsResource($item)
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create wireuiassets',
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
            $item = WireUiAssets::findOrFail($id);
            
            return response()->json([
                'success' => true,
                'message' => 'WireUiAssets retrieved successfully',
                'data' => new WireUiAssetsResource($item)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'WireUiAssets not found',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Update the specified resource.
     */
    public function update(UpdateWireUiAssetsRequest $request, $id): JsonResponse
    {
        try {
            $item = WireUiAssets::findOrFail($id);
            $data = $request->validated();
            $item->update($data);
            
            return response()->json([
                'success' => true,
                'message' => 'WireUiAssets updated successfully',
                'data' => new WireUiAssetsResource($item)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update wireuiassets',
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
            $item = WireUiAssets::findOrFail($id);
            $item->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'WireUiAssets deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete wireuiassets',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}