<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Subscriber;
use App\Http\Resources\SubscriberResource;

/**
 * Context7 API Controller for Subscriber
 * Generated for Level 4 Complex System Transformation
 * RESTful API following Laravel 12 best practices
 */
class SubscriberApiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Subscriber::query();
        
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
            'message' => 'Subscriber list retrieved successfully',
            'data' => SubscriberResource::collection($data->items()),
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
    public function store(StoreSubscriberRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();
            $item = Subscriber::create($data);
            
            return response()->json([
                'success' => true,
                'message' => 'Subscriber created successfully',
                'data' => new SubscriberResource($item)
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create subscriber',
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
            $item = Subscriber::findOrFail($id);
            
            return response()->json([
                'success' => true,
                'message' => 'Subscriber retrieved successfully',
                'data' => new SubscriberResource($item)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Subscriber not found',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Update the specified resource.
     */
    public function update(UpdateSubscriberRequest $request, $id): JsonResponse
    {
        try {
            $item = Subscriber::findOrFail($id);
            $data = $request->validated();
            $item->update($data);
            
            return response()->json([
                'success' => true,
                'message' => 'Subscriber updated successfully',
                'data' => new SubscriberResource($item)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update subscriber',
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
            $item = Subscriber::findOrFail($id);
            $item->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Subscriber deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete subscriber',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}