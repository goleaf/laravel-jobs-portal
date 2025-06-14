<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\SwaggerAsset;
use App\Http\Resources\SwaggerAssetResource;

/**
 * Enhanced API Controller for SwaggerAsset
 * Generated for Level 4 Complex System Transformation
 * RESTful API following Laravel 12 best practices
 */
class SwaggerAssetApiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $query = SwaggerAsset::query();
        
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
            'message' => 'SwaggerAsset list retrieved successfully',
            'data' => SwaggerAssetResource::collection($data->items()),
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
    public function store(StoreSwaggerAssetRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();
            $item = SwaggerAsset::create($data);
            
            return response()->json([
                'success' => true,
                'message' => 'SwaggerAsset created successfully',
                'data' => new SwaggerAssetResource($item)
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create swaggerasset',
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
            $item = SwaggerAsset::findOrFail($id);
            
            return response()->json([
                'success' => true,
                'message' => 'SwaggerAsset retrieved successfully',
                'data' => new SwaggerAssetResource($item)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'SwaggerAsset not found',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Update the specified resource.
     */
    public function update(UpdateSwaggerAssetRequest $request, $id): JsonResponse
    {
        try {
            $item = SwaggerAsset::findOrFail($id);
            $data = $request->validated();
            $item->update($data);
            
            return response()->json([
                'success' => true,
                'message' => 'SwaggerAsset updated successfully',
                'data' => new SwaggerAssetResource($item)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update swaggerasset',
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
            $item = SwaggerAsset::findOrFail($id);
            $item->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'SwaggerAsset deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete swaggerasset',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}