<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\HeaderSlider;
use App\Http\Resources\HeaderSliderResource;

/**
 * Context7 API Controller for HeaderSlider
 * Generated for Level 4 Complex System Transformation
 * RESTful API following Laravel 12 best practices
 */
class HeaderSliderApiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $query = HeaderSlider::query();
        
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
            'message' => 'HeaderSlider list retrieved successfully',
            'data' => HeaderSliderResource::collection($data->items()),
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
    public function store(StoreHeaderSliderRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();
            $item = HeaderSlider::create($data);
            
            return response()->json([
                'success' => true,
                'message' => 'HeaderSlider created successfully',
                'data' => new HeaderSliderResource($item)
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create headerslider',
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
            $item = HeaderSlider::findOrFail($id);
            
            return response()->json([
                'success' => true,
                'message' => 'HeaderSlider retrieved successfully',
                'data' => new HeaderSliderResource($item)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'HeaderSlider not found',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Update the specified resource.
     */
    public function update(UpdateHeaderSliderRequest $request, $id): JsonResponse
    {
        try {
            $item = HeaderSlider::findOrFail($id);
            $data = $request->validated();
            $item->update($data);
            
            return response()->json([
                'success' => true,
                'message' => 'HeaderSlider updated successfully',
                'data' => new HeaderSliderResource($item)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update headerslider',
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
            $item = HeaderSlider::findOrFail($id);
            $item->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'HeaderSlider deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete headerslider',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}