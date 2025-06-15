<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\CmsResource;
use App\Models\Cms;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Enhanced API Controller for Cms
 * Generated for Level 4 Complex System Transformation
 * RESTful API following Laravel 12 best practices.
 */
class CmsApiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Cms::query();

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
            'message' => 'Cms list retrieved successfully',
            'data' => CmsResource::collection($data->items()),
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
    public function store(StoreCmsRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();
            $item = Cms::create($data);

            return response()->json([
                'success' => true,
                'message' => 'Cms created successfully',
                'data' => new CmsResource($item),
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create cms',
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
            $item = Cms::findOrFail($id);

            return response()->json([
                'success' => true,
                'message' => 'Cms retrieved successfully',
                'data' => new CmsResource($item),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Cms not found',
                'error' => $e->getMessage(),
            ], 404);
        }
    }

    /**
     * Update the specified resource.
     *
     * @param mixed $id
     */
    public function update(UpdateCmsRequest $request, $id): JsonResponse
    {
        try {
            $item = Cms::findOrFail($id);
            $data = $request->validated();
            $item->update($data);

            return response()->json([
                'success' => true,
                'message' => 'Cms updated successfully',
                'data' => new CmsResource($item),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update cms',
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
            $item = Cms::findOrFail($id);
            $item->delete();

            return response()->json([
                'success' => true,
                'message' => 'Cms deleted successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete cms',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
