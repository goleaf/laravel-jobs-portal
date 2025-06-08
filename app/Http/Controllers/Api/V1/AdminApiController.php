<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Admin;
use App\Http\Resources\AdminResource;

/**
 * Context7 API Controller for Admin
 * Generated for Level 4 Complex System Transformation
 * RESTful API following Laravel 12 best practices
 */
class AdminApiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Admin::query();
        
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
            'message' => 'Admin list retrieved successfully',
            'data' => AdminResource::collection($data->items()),
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
    public function store(StoreAdminRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();
            $item = Admin::create($data);
            
            return response()->json([
                'success' => true,
                'message' => 'Admin created successfully',
                'data' => new AdminResource($item)
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create admin',
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
            $item = Admin::findOrFail($id);
            
            return response()->json([
                'success' => true,
                'message' => 'Admin retrieved successfully',
                'data' => new AdminResource($item)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Admin not found',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Update the specified resource.
     */
    public function update(UpdateAdminRequest $request, $id): JsonResponse
    {
        try {
            $item = Admin::findOrFail($id);
            $data = $request->validated();
            $item->update($data);
            
            return response()->json([
                'success' => true,
                'message' => 'Admin updated successfully',
                'data' => new AdminResource($item)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update admin',
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
            $item = Admin::findOrFail($id);
            $item->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Admin deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete admin',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}