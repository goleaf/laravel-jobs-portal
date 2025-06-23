<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\DashboardResource;
use App\Models\Dashboard;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Enhanced API Controller for Dashboard
 * Generated for Level 4 Complex System Transformation
 * RESTful API following Laravel 12 best practices.
 */
class DashboardApiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Dashboard::query();

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
            'message' => 'Dashboard list retrieved successfully',
            'data' => DashboardResource::collection($data->items()),
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
    public function store(StoreDashboardRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();
            $item = Dashboard::create($data);

            return response()->json([
                'success' => true,
                'message' => 'Dashboard created successfully',
                'data' => new DashboardResource($item),
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create dashboard',
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
            $item = Dashboard::findOrFail($id);

            return response()->json([
                'success' => true,
                'message' => 'Dashboard retrieved successfully',
                'data' => new DashboardResource($item),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Dashboard not found',
                'error' => $e->getMessage(),
            ], 404);
        }
    }

    /**
     * Update the specified resource.
     *
     * @param mixed $id
     */
    public function update(UpdateDashboardRequest $request, $id): JsonResponse
    {
        try {
            $item = Dashboard::findOrFail($id);
            $data = $request->validated();
            $item->update($data);

            return response()->json([
                'success' => true,
                'message' => 'Dashboard updated successfully',
                'data' => new DashboardResource($item),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update dashboard',
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
            $item = Dashboard::findOrFail($id);
            $item->delete();

            return response()->json([
                'success' => true,
                'message' => 'Dashboard deleted successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete dashboard',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
