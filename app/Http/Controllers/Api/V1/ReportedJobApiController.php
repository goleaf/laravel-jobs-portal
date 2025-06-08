<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\ReportedJob;
use App\Http\Resources\ReportedJobResource;
use App\Http\Requests\Api\StoreReportedJobRequest;
use App\Http\Requests\Api\UpdateReportedJobRequest;

/**
 * Context7 API Controller for ReportedJob
 * Generated for Level 4 Complex System Transformation
 * RESTful API following Laravel 12 best practices
 */
class ReportedJobApiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $query = ReportedJob::query();
        
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
            'message' => 'ReportedJob list retrieved successfully',
            'data' => ReportedJobResource::collection($data->items()),
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
    public function store(StoreReportedJobRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();
            $item = ReportedJob::create($data);
            
            return response()->json([
                'success' => true,
                'message' => 'ReportedJob created successfully',
                'data' => new ReportedJobResource($item)
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create reportedjob',
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
            $item = ReportedJob::findOrFail($id);
            
            return response()->json([
                'success' => true,
                'message' => 'ReportedJob retrieved successfully',
                'data' => new ReportedJobResource($item)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'ReportedJob not found',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Update the specified resource.
     */
    public function update(UpdateReportedJobRequest $request, $id): JsonResponse
    {
        try {
            $item = ReportedJob::findOrFail($id);
            $data = $request->validated();
            $item->update($data);
            
            return response()->json([
                'success' => true,
                'message' => 'ReportedJob updated successfully',
                'data' => new ReportedJobResource($item)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update reportedjob',
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
            $item = ReportedJob::findOrFail($id);
            $item->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'ReportedJob deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete reportedjob',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}