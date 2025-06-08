<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\ImageSlider;
use App\Http\Resources\ImageSliderResource;

/**
 * Context7 API Controller for ImageSlider
 * Generated for Level 4 Complex System Transformation
 * RESTful API following Laravel 12 best practices
 */
class ImageSliderApiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $query = ImageSlider::query();
        
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
            'message' => 'ImageSlider list retrieved successfully',
            'data' => ImageSliderResource::collection($data->items()),
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
    public function store(StoreImageSliderRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();
            $item = ImageSlider::create($data);
            
            return response()->json([
                'success' => true,
                'message' => 'ImageSlider created successfully',
                'data' => new ImageSliderResource($item)
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create imageslider',
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
            $item = ImageSlider::findOrFail($id);
            
            return response()->json([
                'success' => true,
                'message' => 'ImageSlider retrieved successfully',
                'data' => new ImageSliderResource($item)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'ImageSlider not found',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Update the specified resource.
     */
    public function update(UpdateImageSliderRequest $request, $id): JsonResponse
    {
        try {
            $item = ImageSlider::findOrFail($id);
            $data = $request->validated();
            $item->update($data);
            
            return response()->json([
                'success' => true,
                'message' => 'ImageSlider updated successfully',
                'data' => new ImageSliderResource($item)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update imageslider',
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
            $item = ImageSlider::findOrFail($id);
            $item->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'ImageSlider deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete imageslider',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}