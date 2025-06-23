<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\FunctionalAreaResource;
use App\Models\FunctionalArea;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Enhanced API Controller for FunctionalArea
 * Generated for Level 4 Complex System Transformation
 * RESTful API following Laravel 12 best practices.
 */
class FunctionalAreaApiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $query = FunctionalArea::query();

        // Apply filters
        if ($request->has('search')) {
            $query->where('name', 'like', '%'.$request->search.'%');
        }

        if ($request->has('status')) {
            $query->where('is_active', $request->boolean('status'));
        }

        // Pagination
        $perPage = min($request->integer('per_page', 15), 100);
        $data = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'message' => 'FunctionalArea list retrieved successfully',
            'data' => FunctionalAreaResource::collection($data->items()),
            'pagination' => [
                'current_page' => $data->currentPage(),
                'last_page' => $data->lastPage(),
                'per_page' => $data->perPage(),
                'total' => $data->total(),
            ],
        ]);
    }

    /**
     * Store a newly created resource.
     */
    public function store(StoreFunctionalAreaRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();
            $item = FunctionalArea::create($data);

            return response()->json([
                'success' => true,
                'message' => 'FunctionalArea created successfully',
                'data' => new FunctionalAreaResource($item),
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create functionalarea',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param mixed $id
     */
    public function show($id): JsonResponse
    {
        try {
            $item = FunctionalArea::findOrFail($id);

            return response()->json([
                'success' => true,
                'message' => 'FunctionalArea retrieved successfully',
                'data' => new FunctionalAreaResource($item),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'FunctionalArea not found',
                'error' => $e->getMessage(),
            ], 404);
        }
    }

    /**
     * Update the specified resource.
     *
     * @param mixed $id
     */
    public function update(UpdateFunctionalAreaRequest $request, $id): JsonResponse
    {
        try {
            $item = FunctionalArea::findOrFail($id);
            $data = $request->validated();
            $item->update($data);

            return response()->json([
                'success' => true,
                'message' => 'FunctionalArea updated successfully',
                'data' => new FunctionalAreaResource($item),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update functionalarea',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified resource.
     *
     * @param mixed $id
     */
    public function destroy($id): JsonResponse
    {
        try {
            $item = FunctionalArea::findOrFail($id);
            $item->delete();

            return response()->json([
                'success' => true,
                'message' => 'FunctionalArea deleted successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete functionalarea',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
