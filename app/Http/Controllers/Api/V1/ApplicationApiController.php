<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Application;
use App\Http\Resources\ApplicationResource;
use App\Http\Requests\Api\StoreApplicationRequest;
use App\Http\Requests\Api\UpdateApplicationRequest;

/**
 * Context7 API Controller for Application
 * Generated for Level 4 Complex System Transformation
 * RESTful API following Laravel 12 best practices
 */
class ApplicationApiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Application::query();
        
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
            'message' => 'Application list retrieved successfully',
            'data' => ApplicationResource::collection($data->items()),
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
    public function store(StoreApplicationRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();
            $item = Application::create($data);
            
            return response()->json([
                'success' => true,
                'message' => 'Application created successfully',
                'data' => new ApplicationResource($item)
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create application',
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
            $item = Application::findOrFail($id);
            
            return response()->json([
                'success' => true,
                'message' => 'Application retrieved successfully',
                'data' => new ApplicationResource($item)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Application not found',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Update the specified resource.
     */
    public function update(UpdateApplicationRequest $request, $id): JsonResponse
    {
        try {
            $item = Application::findOrFail($id);
            $data = $request->validated();
            $item->update($data);
            
            return response()->json([
                'success' => true,
                'message' => 'Application updated successfully',
                'data' => new ApplicationResource($item)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update application',
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
            $item = Application::findOrFail($id);
            $item->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Application deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete application',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}