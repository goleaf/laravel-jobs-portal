<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Swagger;
use App\Http\Resources\SwaggerResource;
use App\Http\Requests\Api\StoreSwaggerRequest;
use App\Http\Requests\Api\UpdateSwaggerRequest;

/**
 * Context7 API Controller for Swagger
 * Generated for Level 4 Complex System Transformation
 * RESTful API following Laravel 12 best practices
 */
class SwaggerApiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Swagger::query();
        
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
            'message' => 'Swagger list retrieved successfully',
            'data' => SwaggerResource::collection($data->items()),
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
    public function store(StoreSwaggerRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();
            $item = Swagger::create($data);
            
            return response()->json([
                'success' => true,
                'message' => 'Swagger created successfully',
                'data' => new SwaggerResource($item)
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create swagger',
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
            $item = Swagger::findOrFail($id);
            
            return response()->json([
                'success' => true,
                'message' => 'Swagger retrieved successfully',
                'data' => new SwaggerResource($item)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Swagger not found',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Update the specified resource.
     */
    public function update(UpdateSwaggerRequest $request, $id): JsonResponse
    {
        try {
            $item = Swagger::findOrFail($id);
            $data = $request->validated();
            $item->update($data);
            
            return response()->json([
                'success' => true,
                'message' => 'Swagger updated successfully',
                'data' => new SwaggerResource($item)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update swagger',
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
            $item = Swagger::findOrFail($id);
            $item->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Swagger deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete swagger',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}